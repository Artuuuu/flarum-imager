<?php namespace Artuu\Imager\Controllers;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AssertPermissionTrait;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;

class DeleteImagesController implements RequestHandlerInterface
{
  use AssertPermissionTrait;

  protected $validator;

  protected $assets;

  public function __construct(SettingsRepositoryInterface $settings, Factory $validator, FilesystemInterface $assets)
  {
    $this->settings = $settings;
    $this->validator = $validator;
    $this->assets = $assets;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    $this->assertAdmin($request->getAttribute("actor"));

    $validator = $this->validator->make($request->getParsedBody(), [
      "avatars" => "boolean",
      "favicon" => "boolean",
      "logo" => "boolean",
      "all" => "boolean",
    ]);

    if ($validator->fails()) {
      throw new ValidationException($validator);
    }

    if (Arr::get($request->getParsedBody(), "avatars")) {
      $this->assets->deleteDir("imager://avatars");
    }

    if (Arr::get($request->getParsedBody(), "favicon")) {
      $path = $this->settings->get("original_favicon_path");

      if ($this->assets->has("imager://$path")) {
        $this->assets->delete("imager://$path");
      }
    }

    if (Arr::get($request->getParsedBody(), "logo")) {
      $path = $this->settings->get("original_logo_path");

      if ($this->assets->has("imager://$path")) {
        $this->assets->delete("imager://$path");
      }
    }

    if (Arr::get($request->getParsedBody(), "all")) {
      $this->assets->deleteDir("assets://imager");
    }

    return new EmptyResponse(204);
  }
}
