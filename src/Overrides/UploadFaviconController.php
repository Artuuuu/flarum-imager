<?php namespace Artuu\Imager\Overrides;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AssertPermissionTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UploadFaviconController extends \Flarum\Api\Controller\UploadFaviconController
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

    $file = Arr::get($request->getUploadedFiles(), "favicon");
    $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);

    if ($extension === "ico") {
      $image = $file->getStream();
    } else {
      $manager = new ImageManager();

      $image = $manager
        ->make($file->getStream())
        ->resize(64, 64, function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
        })
        ->encode("png");

      $imager = $this->imager($file->getStream());

      $extension = "png";
    }

    if ($path = $this->settings->get("original_favicon_path") ?? $this->settings->get("favicon_path")) {
      if ($this->mount->has($assets = "assets://$path")) {
        $this->mount->delete($assets);
      }

      if ($this->mount->has($imager = "imager://$path")) {
        $this->mount->delete($imager);
      }
    }

    $name = "favicon-" . Str::lower(Str::random(8)) . "." . $extension;

    $this->mount->write("assets://$name", $image);

    if (isset($imager)) {
      $this->mount->write("imager://$name", $imager);
    }

    $this->settings->set("favicon_path", $name);

    return \Flarum\Api\Controller\ShowForumController::data($request, $document);
  }

  private function imager($file)
  {
    $maxSize = (int) $this->settings->get("artuu-imager.favicon_size");

    $manager = new ImageManager();

    $imageSize = min((int) $manager->make($file)->height(), (int) $manager->make($file)->width());

    if ($imageSize <= 64) {
      return;
    }

    if ($imageSize > $maxSize) {
      return $manager
        ->make($file)
        ->resize($maxSize, $maxSize, function ($constraint) {
          $constraint->aspectRatio();
        })
        ->encode("png");
    }

    if ($imageSize <= $maxSize) {
      return $manager
        ->make($file)
        ->resize($imageSize, $imageSize, function ($constraint) {
          $constraint->aspectRatio();
        })
        ->encode("png");
    }
  }
}
