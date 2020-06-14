<?php namespace Artuu\Imager\Providers;

use Artuu\Imager\Controllers;
use Artuu\Imager\Overrides;
use Flarum\Api\Controller;
use Flarum\Api\Event\Serializing as ApiSerializing;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Settings\OverrideSettingsRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AvatarUploader;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Filesystem\Factory;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

class ImagerProvider extends AbstractServiceProvider
{
  public function register()
  {
    $this->registerNewFilesystem();
    $this->registerOverrides();
    $this->overrideSettings();
    $this->overrideUsersAvatars();
  }

  protected function overrideSettings()
  {
    // Override settings: logo and favicon path
    $settings = $this->app->make(SettingsRepositoryInterface::class)->all();
    $overrides = [];

    if (isset($settings["logo_path"])) {
      $overrides["original_logo_path"] = $settings["logo_path"];
      $overrides["logo_path"] = "logo?s=60";
    }
    if (isset($settings["favicon_path"])) {
      $overrides["original_favicon_path"] = $settings["favicon_path"];
      $overrides["favicon_path"] = "favicon?s=64";
    }

    $this->app->extend(SettingsRepositoryInterface::class, function ($settings) use ($overrides) {
      return new OverrideSettingsRepository($settings, $overrides);
    });
  }

  protected function overrideUsersAvatars()
  {
    // Override user avatar
    $this->app->make(Dispatcher::class)->listen(ApiSerializing::class, function (ApiSerializing $event) {
      if ($event->isSerializer(BasicUserSerializer::class)) {
        $event->attributes["avatarUrl"] = str_replace(".png", "?s=100", $event->attributes["avatarUrl"]);
      }
    });
  }

  protected function registerNewFilesystem()
  {
    $this->app->make("config")->set("filesystems.disks.flarum-imager", [
      "driver" => "local",
      "root" => $this->app->publicPath() . "/assets/imager",
    ]);

    $assets = function (Container $app) {
      return new MountManager([
        "assets" => $app
          ->make(Factory::class)
          ->disk("flarum-assets")
          ->getDriver(),
        "imager" => $app
          ->make(Factory::class)
          ->disk("flarum-imager")
          ->getDriver(),
      ]);
    };

    $this->app
      ->when([
        Controllers\DeleteImagesController::class,
        Controllers\FaviconController::class,
        Controllers\LogoController::class,
        Overrides\DeleteFaviconController::class,
        Overrides\DeleteLogoController::class,
        Overrides\UploadFaviconController::class,
        Overrides\UploadLogoController::class,
      ])
      ->needs(FilesystemInterface::class)
      ->give($assets);

    $this->app->make("config")->set("filesystems.disks.flarum-imager-avatars", [
      "driver" => "local",
      "root" => $this->app->publicPath() . "/assets/imager/avatars",
    ]);

    $avatars = function (Container $app) {
      return new MountManager([
        "avatars" => $app
          ->make(Factory::class)
          ->disk("flarum-avatars")
          ->getDriver(),
        "imager" => $app
          ->make(Factory::class)
          ->disk("flarum-imager-avatars")
          ->getDriver(),
      ]);
    };

    $this->app
      ->when([Controllers\AvatarsController::class, Overrides\AvatarUploader::class])
      ->needs(FilesystemInterface::class)
      ->give($avatars);
  }

  protected function registerOverrides()
  {
    $settings = $this->app->make(SettingsRepositoryInterface::class);

    /* Avatars */
    $this->app->extend(AvatarUploader::class, function () {
      return $this->app->make(Overrides\AvatarUploader::class);
    });

    /* Logo */
    $this->app->extend(Controller\DeleteLogoController::class, function () {
      return $this->app->make(Overrides\DeleteLogoController::class);
    });

    if ((int) $settings->get("artuu-imager.logo_size")) {
      $this->app->extend(Controller\UploadLogoController::class, function () {
        return $this->app->make(Overrides\UploadLogoController::class);
      });
    }

    /* Favicon */
    $this->app->extend(Controller\DeleteFaviconController::class, function () {
      return $this->app->make(Overrides\DeleteFaviconController::class);
    });

    if ((int) $settings->get("artuu-imager.favicon_size")) {
      $this->app->extend(Controller\UploadFaviconController::class, function () {
        return $this->app->make(Overrides\UploadFaviconController::class);
      });
    }
  }
}
