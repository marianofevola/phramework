<?php


namespace Modules\Login;

use Carbon\Carbon;
use Modules\Login\Model\FailedLoginModel;
use Modules\Login\Model\RememberTokenModel;
use Modules\Login\Model\SuccessLoginModel;

class LoginRepository
{

	/** @var  LoginComponent */
	protected $loginComponent;

	/**
	 * @param LoginComponent $userComponent
	 */
	public function __construct(LoginComponent $userComponent)
	{
		$this->loginComponent = $userComponent;
	}

  /**
   * @param $userId
   * @param $ip
   */
  public function registerThrottlingAndSleep($userId, $ip)
  {
    $failedLogin = new FailedLoginModel();
    $failedLogin->userId = $userId;
    $failedLogin->ipAddress = $ip;
    $failedLogin->attempted = Carbon::now()->toDateTimeString();
    $failedLogin->save();

    $attempts = (new FailedLoginModel())->getAttemptsByIp($ip);

    switch ($attempts)
    {
      case 1:
      case 2:
        // no delay
        break;
      case 3:
      case 4:
        sleep(2);
        break;
      case 10:
        sleep(6);
        break;
      default:
        sleep(12);
        break;
    }
  }

  /**
   * @param $userId
   * @param $ip
   * @param $userAgent
   * @return bool
   */
  public function saveSuccessLogin($userId, $ip, $userAgent)
  {
    return (new SuccessLoginModel())->saveSuccess($userId, $ip, $userAgent);
  }

  /**
   * Persists and returns token
   *
   * @param $token
   * @param $userId
   * @param $userAgent
   * @return bool
   */
  public function saveToken($token, $userId, $userAgent)
  {
    return (new RememberTokenModel())->saveToken($token, $userId, $userAgent);
  }

  /**
   * @param $userId
   * @param $token
   * @return bool|RememberTokenModel
   */
  public function getRememberToken($userId, $token)
  {
    return (new RememberTokenModel())->getByUserIdAndToken($userId, $token);
  }


}

