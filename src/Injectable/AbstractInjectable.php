<?php


namespace Phramework\Injectable;

use Phalcon\Di\DiInterface;
use Phalcon\Di\Injectable;
use Phramework\DI\AbstractDI;

abstract class AbstractInjectable extends Injectable
{
  /**
   * Type-hinting only
   * @return AbstractDI
   */
  public function getDI(): DiInterface
  {
    return parent::getDI();
  }
}
