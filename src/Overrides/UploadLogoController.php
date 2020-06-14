<?php namespace Artuu\Imager\Overrides;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AssertPermissionTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use League\Flysystem\FilesystemInterface;

class UploadLogoController extends \Flarum\Api\Controller\UploadLogoController
{
  use AssertPermissionTrait;

  /**
   * @var SettingsRepositoryInterface
   */
  protected $settings;

  /**
   * @var FilesystemInterface
   */
  protected $mount;

  /**
   * @param SettingsRepositoryInterface $settings
   * @param FilesystemInterface $mount
   */
  public function __construct(SettingsRepositoryInterface $settings, FilesystemInterface $mount)
  {
    $this->settings = $settings;
    $this->mount = $mount;
  }

  /**
   * {@inheritdoc}
   */
  public function data(ServerRequestInterface $request, Document $document)
  {
    $this->assertAdmin($request->getAttribute("actor"));

    $file = Arr::get($request->getUploadedFiles(), "logo");

    $manager = new ImageManager();

    $image = $manager
      ->make($file->getStream())
      ->heighten(60, function ($constraint) {
        $constraint->upsize();
      })
      ->encode("png");

    $imager = $this->imager($file->getStream());

    if ($path = $this->settings->get("original_logo_path") ?? $this->settings->get("logo_path")) {
      if ($this->mount->has($assets = "assets://$path")) {
        $this->mount->delete($assets);
      }

      if ($this->mount->has($imager = "imager://$path")) {
        $this->mount->delete($imager);
      }
    }

    $name = "logo-" . Str::lower(Str::random(8)) . ".png";

    $this->mount->write("assets://$name", $image);

    if (isset($imager)) {
      $this->mount->write("imager://$name", $imager);
    }

    $this->settings->set("logo_path", $name);

    return \Flarum\Api\Controller\ShowForumController::data($request, $document);
  }

  private function imager($file)
  {
    $maxSize = (int) $this->settings->get("artuu-imager.logo_size");

    $manager = new ImageManager();

    $imageSize = (int) $manager->make($file)->height();

    if ($imageSize <= 60) {
      return;
    }

    if ($imageSize > $maxSize) {
      return $manager
        ->make($file)
        ->heighten($maxSize)
        ->encode("png");
    }

    if ($imageSize <= $maxSize) {
      return $manager->make($file)->encode("png");
    }
  }
}
