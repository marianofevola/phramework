<?php


namespace Modules\Login\Model;

use Phramework\Mvc\Model\AbstractModel;

class SuccessLoginModel extends AbstractModel
{
  /** @var int */
  public $id;

  /** @var string */
  public $userId;

  /** @var string */
  public $ipAddress;

  /** @var string */
  public $userAgent;

  /**
   * @param int $userId
   * @param string $ip
   * @param string $userAgent
   * @return bool
   */
  public function saveSuccess($userId, $ip, $userAgent)
  {
    $successLogin = new self();
    $successLogin->userId = $userId;
    $successLogin->ipAddress = $ip;
    $successLogin->userAgent = $userAgent;

    return $successLogin->save();
  }
}
