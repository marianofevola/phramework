<?php


namespace Modules\User;

use Modules\User\Model\UserModel;

class UserComponent
{
	/** @var UserRepository */
	private $userRepository;

	/**
	 * @return UserRepository
	 */
	public function getRepository()
	{
		if (!isset($this->userRepository))
		{
			$this->userRepository = new UserRepository($this);
		}
		return $this->userRepository;
	}

  /**
   * @param $email
   * @return bool|UserModel|\Phalcon\Mvc\Model
   */
  public function getByEmail($email)
  {
    return $this->getRepository()->getByEmail($email);
	}

  /**
   * @param $id
   * @return bool|UserModel|\Phalcon\Mvc\Model
   */
  public function getById($id)
  {
    return $this->getRepository()->getById($id);
	}

  /**
   * @param $email
   * @param $password
   * @return UserModel|\Phalcon\Mvc\Model
   */
  public function getUser($email, $password)
  {
    return $this->getRepository()->getUser($email, $password);
	}

}
