<?php


namespace Phramework\Modules;


use Phalcon\Di;

class PhrameworkComponent
{
  /** @var Di */
  private $di;

  /**
   * UserComponent constructor.
   * @param Di $di
   */
  public function __construct(Di $di)
  {
    $this->di = $di;
  }

  /**
   * @return Di
   */
  public function getDi()
  {
    return $this->di;
  }
}
