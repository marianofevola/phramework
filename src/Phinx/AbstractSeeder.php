<?php


namespace Phramework\Phinx;

use Phalcon\Di;
use Phinx\Seed\AbstractSeed;
use Phramework\Modules\PhrameworkComponent;

class AbstractSeeder extends AbstractSeed
{

  /**
   * @param $name
   * @return PhrameworkComponent
   */
  public function getComponent($name)
  {
    return Di::getDefault()->get($name);
  }
}
