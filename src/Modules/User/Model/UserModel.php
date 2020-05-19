<?php


namespace Phramework\Modules\User\Model;

use Phramework\Mvc\Model\AbstractModel;
use Phalcon\Mvc\Model;

class UserModel extends AbstractModel
{

  /**
   * Specify single-named table
   * @return string
   */
  public function getTableSource()
  {
    return "users";
  }

  /** @var int */
  public $id;

  /** @var string */
  public $name;

  /** @var string */
  public $email;

  /** @var string */
  public $password;

  /**
   * @param $memberId
   * @return UserModel|null
   */
  public static function getById($memberId)
  {
    $member = self::findFirst(
      [
        "conditions" => "id = :memberId:",
        "bind" => ["memberId" => $memberId]
      ]
    );

    return $member;
  }

  /**
   * @param $email
   * @param $password
   * @return UserModel|Model
   */
  public function getByEmailAndPassword($email, $password)
  {
    $user = self::findFirst([
      'conditions' => 'email=:email: and password=:password:',
      'bind' => [
        "email" => $email,
        "password" => md5($password)
      ],
    ]);

    return $user;
  }

  /**
   * @param $userAgent
   * @return string
   */
  public function generateToken($userAgent)
  {
    return md5($this->email . $this->password . $userAgent);
  }

  /**
   * Override as you need
   * @return array
   */
  public function toUserArray()
  {
    return $this->toArray(["id", "name", "email"]);
  }
}
