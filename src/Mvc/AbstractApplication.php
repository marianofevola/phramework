<?php


namespace Phramework\Mvc;

use Phramework\Provider\AclProvider;
use Phramework\Provider\AuthProvider;
use Phalcon\Config;
use Phalcon\Mvc\Application;

abstract class AbstractApplication extends Application
{
  public function __construct($di)
  {
    parent::__construct($di);

    $moduleClassNames = explode('\\', get_called_class());
    $moduleName = $moduleClassNames[0];

    /** @var Config $config */
    $config = $this->getDI()->get("config");
    $modules = $config->get("modules")->get($moduleName)->toArray();

    $moduleArray = [];
    foreach ($modules as $module)
    {
      $module = ucfirst($module);
      $moduleArray[$module] = [
        "className" => sprintf("%s\\Modules\\%s\\Module", $moduleName, $module),
        "path"      => sprintf("%s/Modules/%s/Module.php", SRC_PATH,  $module)
      ];
    }
    $this->registerModules($moduleArray);

    // Register providers
    $authProvider = new AuthProvider();
    $authProvider->register($this->di);

    $aclProvider = new AclProvider();
    $aclProvider->register($this->di);

    if ($config->get("auth"))
    {
      $this
        ->handleWwwAuth(
          $config->get("auth")->get('username'),
          $config->get("auth")->get('password')
        );
    }
  }

  /**
   * @param $username
   * @param $password
   */
  private function handleWwwAuth($username, $password)
  {
    if (
      !isset($_SERVER["PHP_AUTH_USER"])
      ||
      (
        $_SERVER["PHP_AUTH_USER"] != $username
        || $_SERVER['PHP_AUTH_PW'] != $password
      )
    )
    {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      exit;
    }
  }
}
