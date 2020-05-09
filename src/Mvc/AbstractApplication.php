<?php


namespace Phramework\Mvc;

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
  }
}
