<?php


namespace Phramework\Provider;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phramework\Injectable\Acl;

class AclProvider implements ServiceProviderInterface
{
  /**
   * @var string
   */
  protected $providerName = "acl";

  /**
   * @param DiInterface $di
   *
   * @return void
   */
  public function register(DiInterface $di): void
  {
    $di->setShared($this->providerName, function ()
    {
      $acl = new Acl();

      return $acl;
    });
  }
}
