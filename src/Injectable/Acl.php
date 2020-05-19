<?php


namespace Phramework\Injectable;

use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phramework\Exception\ConfigException;

/**
 * Vokuro\Acl\Acl
 */
class Acl extends Injectable
{

  /**
   * @return bool
   * @throws \Phramework\Exception\Exception
   */
  public function isEnabled()
  {
    /** @var Config $config */
    $config = $this
      ->getDI()
      ->get("config")
      ->get("acl");

    if (!$config)
    {
      throw (new ConfigException())->addCustomData(["acl" => "isEnabled"]);
    }

    $isEnabled = $config->get("isEnabled");
    if (is_null($isEnabled))
    {
      throw (new ConfigException())->addCustomData(["acl" => "isEnabled"]);
    }

    return (bool)$isEnabled;
  }

  /**
   * Checks if the current profile is allowed to access a resource
   *
   * @param string $userType
   * @param string $controller
   * @param string $action
   *
   * @return boolean
   */
  public function isAllowed(
    $userType,
    $controller,
    $action
  ): bool {
    $routes = $this
      ->getDI()
      ->get("config")
      ->get("routes")
      ->toArray();

    // get route
    $route = array_filter($routes, function ($route) use ($controller, $action, $userType)
    {
      return $route["controller"] == $controller
        && $route["action"] == $action
        && (
          !isset($route["types"])
          || isset($route["types"])
          && in_array($userType, $route["types"])
        );
    });

    return count($route) == 1;
  }
}
