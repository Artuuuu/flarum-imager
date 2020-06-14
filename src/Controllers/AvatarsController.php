<?php namespace Artuu\Imager\Controllers;

use Flarum\Http\Exception\RouteNotFoundException;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

class AvatarsController implements RequestHandlerInterface
{
  protected $assets;

  public function __construct(FilesystemInterface $assets)
  {
    $this->assets = $assets;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    $response = new Response();

    $size = (int) Arr::get($request->getQueryParams(), "s");
    $path = Arr::get($request->getQueryParams(), "path") . ".png";

    if ($this->assets->has("avatars://$path") && $size == 100) {
      $file = $this->assets->read("avatars://$path");

      $response->getBody()->write($file);
      return $response->withHeader("Content-Type", "image/png");
    }

    if ($this->assets->has("imager://$path")) {
      $file = $this->assets->read("imager://$path");
    } elseif ($this->assets->has("avatars://$path")) {
      $file = $this->assets->read("avatars://$path");
    } else {
      throw new RouteNotFoundException();
    }

    $manager = new ImageManager();
    $image = $manager->make($file);

    $imageSize = min((int) $image->height(), (int) $image->width());

    if (0 < $size and $size <= $imageSize) {
      $image = $image->resize($size, $size, function ($constraint) {
        $constraint->aspectRatio();
      });
    }

    $response->getBody()->write($image->encode("png"));
    return $response->withHeader("Content-Type", "image/png");
  }
}
