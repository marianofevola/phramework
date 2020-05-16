<?php


namespace Phramework\Injectable;

use Carbon\Carbon;
use Phramework\Modules\Login\LoginComponent;
use Phramework\Modules\User\Model\UserModel;
use Phramework\Modules\User\UserComponent;

/**
 * Manages Authentication/Identity
 */
class Auth extends AbstractInjectable
{
  /**
   * @return UserComponent
   */
  private function getUserComponent()
  {
    return $this->getDI()->get("userComponent");
  }

  /**
   * @return LoginComponent
   */
  private function getLoginComponent()
  {
    return $this->getDI()->get("loginComponent");
  }

  /**
   * Checks the user credentials
   *
   * @param string $email
   * @param string $password
   * @param string $remember
   *
   * @throws \Exception
   */
  public function check($email, $password, $remember)
  {
    // Check if the user exist
    $user = $this
      ->getUserComponent()
      ->getByEmail($email);

    if (!$user)
    {
      $this
        ->getLoginComponent()
        ->registerThrottlingAndSleep(
          0,
          $this->request->getClientAddress()
        );
      throw new \Exception('Wrong email/password combination');
    }

    // Check the password

    if (!$this->security->checkHash($password, $user->password))
    {
      $this
        ->getLoginComponent()
        ->registerThrottlingAndSleep(
          $user->id,
          $this->request->getClientAddress()
        );
      throw new \Exception('Wrong email/password combination');
    }

    // Register the successful login
    $this
      ->getLoginComponent()
      ->saveSuccessLogin(
        $user->id,
        $this->request->getClientAddress(),
        $this->request->getUserAgent()
      );

    // Check if the remember me was selected
    if (isset($remember))
    {
      $this->createRememberEnvironment($user);
    }

    $this->session->set('auth-identity', [
      'id' => $user->id,
      'name' => $user->name,
      "email" => $user->email
    ]);
  }

  /**
   * Creates the remember me environment settings the related cookies and
   * generating tokens
   *
   * @param UserModel $user
   */
  public function createRememberEnvironment(UserModel $user)
  {
    $userAgent = $this->request->getUserAgent();

    $token = $user->generateToken($userAgent);
    $token = $this
      ->getLoginComponent()
      ->saveRememberToken(
        $token,
        $user->id,
        $userAgent
      );

    if ($token != false)
    {
      $expire = Carbon::now()->addDays(8)->timestamp;
      $this->cookies->set('RMU', $user->id, $expire);
      $this->cookies->set('RMT', $token, $expire);
    }
  }

  /**
   * Check if the session has a remember me cookie
   *
   * @return boolean
   */
  public function hasRememberMe()
  {
    return $this->cookies->has('RMU');
  }

  /**
   * Logs on using the information in the cookies
   *
   * @return bool
   * @throws \Exception
   */
  public function loginWithRememberMe()
  {
    $userId = $this->cookies->get('RMU')->getValue();
    $cookieToken = $this->cookies->get('RMT')->getValue();

    $user = $this
      ->getUserComponent()
      ->getById($userId);

    if (!$user)
    {
      return false;
    }

    $userAgent = $this->request->getUserAgent();
    $token = $user->generateToken($userAgent);

    if ($cookieToken != $token)
    {
      $this->cookies->get('RMU')->delete();
      $this->cookies->get('RMT')->delete();

      return false;
    }

    $remember = $this
      ->getLoginComponent()
      ->getRememberToken($user->id, $token);

    if (!$remember)
    {
      $this->cookies->get('RMU')->delete();
      $this->cookies->get('RMT')->delete();

      return false;
    }

    // Check if the cookie has not expired
    if ((Carbon::parse($remember->created))->diffInHours(Carbon::now()) > 9)
    {
      // expired
      $this->cookies->get('RMU')->delete();
      $this->cookies->get('RMT')->delete();

      return false;
    }

    // Register identity
    $this
      ->session
      ->set('auth-identity', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
      ]);

    // Register the successful login
    $this
      ->getLoginComponent()
      ->saveSuccessLogin(
        $userId,
        $this->request->getClientAddress(),
        $userAgent
      );

    return true;
  }

  /**
   * Returns the current identity
   *
   * @return array|null
   */
  public function getIdentity()
  {
    return $this->session->get('auth-identity');
  }

  /**
   * Returns the current identity
   *
   * @return string
   */
  public function getName()
  {
    $identity = $this->session->get('auth-identity');
    return $identity['name'];
  }

  /**
   * Removes the user identity information from session
   */
  public function remove()
  {
    if ($this->cookies->has('RMU'))
    {
      $this->cookies->get('RMU')->delete();
    }
    if ($this->cookies->has('RMT'))
    {
      $token = $this->cookies->get('RMT')->getValue();

      $userId = $this->findFirstByToken($token);
      if ($userId)
      {
        $this->deleteToken($userId);
      }

      $this->cookies->get('RMT')->delete();
    }

    $this->session->remove('auth-identity');
  }

  /**
   * Auths the user by his/her id
   *
   * @param int $id
   *
   * @throws Exception
   */
  public function authUserById($id)
  {
    $user = Users::findFirstById($id);
    if ($user == false)
    {
      throw new Exception('The user does not exist');
    }

    $this->checkUserFlags($user);

    $this->session->set('auth-identity', [
      'id' => $user->id,
      'name' => $user->name,
      'profile' => $user->profile->name,
    ]);
  }

  /**
   * Get the entity related to user in the active identity
   *
   * @return Users
   * @throws Exception
   */
  public function getUser()
  {
    $identity = $this->session->get('auth-identity');

    if (!isset($identity['id']))
    {
      throw new Exception('Session was broken. Try to re-login');
    }

    $user = UserModel::findFirstById($identity['id']);
    if ($user == false)
    {
      throw new Exception('The user does not exist');
    }

    return $user;
  }

  /**
   * Returns the current token user
   *
   * @param string $token
   *
   * @return int|null
   */
  public function findFirstByToken($token)
  {
    $userToken = RememberTokens::findFirst([
      'conditions' => 'token = :token:',
      'bind' => [
        'token' => $token,
      ],
    ]);

    return $userToken ? $userToken->usersId : null;
  }

  /**
   * Delete the current user token in session
   *
   * @param int $userId
   */
  public function deleteToken(int $userId): void
  {
    $user = RememberTokens::find([
      'conditions' => 'usersId = :userId:',
      'bind' => [
        'userId' => $userId,
      ],
    ]);

    if ($user)
    {
      $user->delete();
    }
  }
}
