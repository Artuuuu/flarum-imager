<?php namespace Artuu\Imager\Overrides;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\AssertPermissionTrait;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ServerRequestInterface;
use League\Flysystem\FilesystemInterface;

class DeleteLogoController extends \Flarum\Api\Controller\DeleteLogoController
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
  protected function delete(ServerRequestInterface $request)
  {
    $this->assertAdmin($request->getAttribute("actor"));

    $path = $this->settings->get("original_logo_path");

    $this->settings->set("logo_path", null);

    if ($this->mount->has("assets://$path")) {
      $this->mount->delete("assets://$path");
    }

    if ($this->mount->has("imager://$path")) {
      $this->mount->delete("imager://$path");
    }

    return new EmptyResponse(204);
  }
}
