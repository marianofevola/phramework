<?php


namespace Phramework\Provider;

use Phramework\Injectable\Auth;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class AuthProvider implements ServiceProviderInterface
{
  /**
   * @var string
   */
  protected $providerName = 'auth';

  /**
   * @param DiInterface $di
   *
   * @return void
   */
  public function register(DiInterface $di): void
  {
    $di->setShared($this->providerName, Auth::class);
  }
}
