<?php namespace Artuu\Imager\Overrides;

use Flarum\User\User;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;

class AvatarUploader extends \Flarum\User\AvatarUploader
{
  protected $mount;

  public function __construct(FilesystemInterface $mount)
  {
    $this->mount = $mount;
  }

  /**
   * @param User $user
   * @param Image $image
   */
  public function upload(User $user, Image $file)
  {
    if (extension_loaded("exif")) {
      $file->orientate();
    }

    $name = Str::random() . ".png";

    if ($user->can("artuu-imager.resized_avatar")) {
      $imager = $this->imager($file);

      if (isset($imager)) {
        $this->mount->put("imager://$name", $imager);
      }
    }

    $image = $file->fit(100, 100)->encode("png");

    $this->remove($user);
    $user->changeAvatarPath($name);

    $this->mount->put("avatars://$name", $image);
  }

  public function remove(User $user)
  {
    $path = $user->getOriginal("avatar_url");

    $user->afterSave(function () use ($path) {
      if ($this->mount->has("avatars://$path")) {
        $this->mount->delete("avatars://$path");
      }

      if ($this->mount->has("imager://$path")) {
        $this->mount->delete("imager://$path");
      }
    });

    $user->changeAvatarPath(null);
  }

  private function imager($file)
  {
    $maxSize = (int) app("flarum.settings")->get("artuu-imager.avatars_size");

    $manager = new ImageManager();

    $imageSize = min((int) $manager->make($file)->height(), (int) $manager->make($file)->width());

    if ($imageSize <= 100) {
      return;
    }

    if ($imageSize > $maxSize) {
      return $manager
        ->make($file)
        ->fit($maxSize, $maxSize)
        ->encode("png");
    }

    if ($imageSize <= $maxSize) {
      return $manager
        ->make($file)
        ->fit($imageSize, $imageSize)
        ->encode("png");
    }
  }
}
