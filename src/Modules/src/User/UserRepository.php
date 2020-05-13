<?php


namespace Phramework\Modules\User;

use Modules\User\Model\UserModel;

class UserRepository
{
	/** @var UserModel */
	protected $model;

	/** @var  UserComponent */
	protected $userComponent;

	/**
	 * @param UserComponent $userComponent
	 */
	public function __construct(UserComponent $userComponent)
	{
		$this->userComponent = $userComponent;
		$this->model = new UserModel();
	}

  /**
   * @param $email
   *
   * @return bool|UserModel
   */
  public function getByEmail($email)
  {
    return $this->model->findFirstByEmail($email);
	}

  /**
   * @param $id
   * @return bool|UserModel
   */
  public function getById($id)
  {
    return $this->model->findFirstById($id);
	}

  /**
   * @param $email
   * @param $password
   * @return bool|UserModel
   */
  public function getUser($email, $password)
  {
    return $this
      ->model
      ->getByEmailAndPassword($email, $password);
  }


  /**
   * Saves and returns true if successful
   *
   * @param string $name
   * @param string $email
   * @param string $password
   * @return UserModel
   */
  public function saveFromPost($name, $email, $password)
  {
    $user = new UserModel([
      'name'       => $name,
      'email'      => $email,
      'password'   => $password
    ]);
    $user->save();

    return $user;
  }
}

