<?php


namespace Phramework\Mvc\View;


use Phalcon\Mvc\View;

class PhrameworkView extends View
{

  /**
   * @param $name
   * @return void
   */
  public function partialCommon($name)
  {
    $appRoot = SRC_PATH;
    return $this
      ->partial(
        sprintf(
          "../../../../Common/View/Partials/%s",
          $name
        )
      );
  }

}
