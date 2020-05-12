<?php


namespace Phramework\Form;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Callback;

abstract class AbstractForm extends Form
{
  /**
   * Adds csrf token for validation
   */
  public function addCsrf()
  {
    // CSRF
    $csrf = new Hidden('csrf');
    $csrf->addValidator(new Callback([
      "message" => "CSRF validation failed",
      "callback" => $this->security->checkToken()
    ]));
    $csrf->clear();

    $this->add($csrf);
  }
}
