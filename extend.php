<?php namespace Artuu\Imager;

use Flarum\Extend;
use Flarum\Foundation\Application;

return [
  (new Extend\Routes("forum"))
    ->get("/assets/avatars/{path}", "imager.avatars", Controllers\AvatarsController::class)
    ->get("/assets/logo", "imager.logo", Controllers\LogoController::class)
    ->get("/assets/favicon", "imager.favicon", Controllers\FaviconController::class),

  (new Extend\Routes("api"))->delete("/imager", "imager.delete", Controllers\DeleteImagesController::class),

  new Extend\Locales(__DIR__ . "/resources/locale"),

  (new Extend\Frontend("admin"))->js(__DIR__ . "/js/dist/admin.js")->css(__DIR__ . "/resources/less/ImagerModal.less"),

  function (Application $app) {
    // Register provider
    $app->register(Providers\ImagerProvider::class);
  },
];
