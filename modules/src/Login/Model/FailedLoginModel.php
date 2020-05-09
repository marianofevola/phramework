<?php


namespace Modules\Login\Model;

use Carbon\Carbon;
use Phramework\Mvc\Model\AbstractModel;

class FailedLoginModel extends AbstractModel
{

  /** @var int */
  public $id;

  /** @var string */
  public $userId;

  /** @var string */
  public $ipAddress;

  /** @var string */
  public $attempted;

  /**
   * @param $ip
   * @return int
   */
  public function getAttemptsByIp($ip)
  {
    return self::count([
      'ipAddress = ?0 AND attempted >= ?1',
      'bind' => [
        $ip,
        Carbon::now()->subHour(6)
      ],
    ]);
  }
}
