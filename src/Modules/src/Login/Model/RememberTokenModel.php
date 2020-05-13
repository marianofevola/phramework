<?php


namespace Phramework\Modules\Login\Model;

use Phramework\Mvc\Model\AbstractModel;

class RememberTokenModel extends AbstractModel
{

  /** @var int */
  public $id;

  /** @var string */
  public $userId;

  /** @var string */
  public $token;

  /** @var string */
  public $userAgent;

  /** @var string */
  public $created;

  /**
   * Persists and returns token
   * @param $token
   * @param $userId
   * @param $userAgent
   * @return bool
   */
  public function saveToken($token, $userId, $userAgent)
  {
    $remember = new self();
    $remember->userId = $userId;
    $remember->token = $token;
    $remember->userAgent = $userAgent;

    $isSaved = $remember->save();

    return $isSaved ? $token : false;
  }

  /**
   * @param $userId
   * @param $token
   * @return bool|RememberTokenModel
   */
  public function getByUserIdAndToken($userId, $token)
  {
    return self::findFirst([
      'userId = ?0 AND token = ?1',
      'bind' => [
        $userId,
        $token,
      ],
    ]);
  }

}
