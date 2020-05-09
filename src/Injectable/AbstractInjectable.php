<?php


namespace Phramework\Injectable;

use Phalcon\Di\DiInterface;
use Phalcon\Di\Injectable;
use Phramework\DI\AbstractDi;

abstract class AbstractInjectable extends Injectable
{
  /**
   * Type-hinting only
   * @return AbstractDi
   */
  public function getDI(): DiInterface
  {
    return parent::getDI();
  }
}
