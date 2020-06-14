<?php namespace Artuu\Imager\Controllers;

use Flarum\Http\Exception\RouteNotFoundException;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

class FaviconController implements RequestHandlerInterface
{
  protected $settings;

  protected $assets;

  public function __construct(SettingsRepositoryInterface $settings, FilesystemInterface $assets)
  {
    $this->settings = $settings;
    $this->assets = $assets;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    $response = new Response();

    $size = (int) Arr::get($request->getQueryParams(), "s");
    $path = $this->settings->get("original_favicon_path");

    if ($this->assets->has("assets://$path") && $size == 64) {
      $file = $this->assets->read("assets://$path");

      $response->getBody()->write($file);
      return $response->withHeader("Content-Type", "image/png");
    }

    if ($this->assets->has("imager://$path")) {
      $file = $this->assets->read("imager://$path");
    } elseif ($this->assets->has("assets://$path")) {
      $file = $this->assets->read("assets://$path");
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
