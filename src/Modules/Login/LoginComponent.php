<?php


namespace Phramework\Modules\Login;

use Phramework\Modules\PhrameworkComponent;

class LoginComponent extends PhrameworkComponent
{

	/** @var LoginRepository */
	private $loginRepository;

	/**
	 * @return LoginRepository
	 */
	private function getRepository()
	{
		if (!isset($this->loginRepository))
		{
			$this->loginRepository = new LoginRepository($this);
		}
		return $this->loginRepository;
	}

  /**
   * @param $userId
   * @param $ip
   */
  public function registerThrottlingAndSleep($userId, $ip)
  {
    $this->getRepository()->registerThrottlingAndSleep($userId, $ip);
  }

  /**
   * @param $userId
   * @param $ip
   * @param $userAgent
   * @return bool
   */
  public function saveSuccessLogin($userId, $ip, $userAgent)
  {
    return $this->getRepository()->saveSuccessLogin($userId, $ip, $userAgent);
  }

  /**
   * Persists and returns token
   *
   * @param int $userId
   * @param $userAgent
   * @return bool|string
   */
  public function saveRememberToken($token, $userId, $userAgent)
  {
    return $this->getRepository()->saveToken($token, $userId, $userAgent);
  }

  /**
   * @param $userOd
   * @param $token
   * @return bool|Model\RememberTokenModel
   */
  public function getRememberToken($userOd, $token)
  {
    return $this->getRepository()->getRememberToken($userOd, $token);
  }

  /**
   * @param (int) $userId
   */
  public function deleteRememberToken($userId)
  {
    $this->getRepository()->deleteRememberToken($userId);
  }
}
