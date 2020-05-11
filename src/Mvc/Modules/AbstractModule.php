<?php


namespace Phramework\Mvc\Modules;

use Phalcon\Config\Adapter\Yaml;
use Phalcon\DI;
use Phalcon\Mvc\View;

abstract class AbstractModule
{
	/**
   * AbstractModule constructor.
   */
  public function __construct()
  {
    // Define the module name
    define('APP_NAME', $this->getModuleName());
    define('APP_NAMESPACE', $this->getAppNamespace());
  }

  /**
   * Get the module name
   *
   * @return string
   */
  private function getModuleName()
  {
    // Define the Application name, so we know where the controllers are
    $moduleClassNames = explode('\\', get_called_class());
    $moduleName = $moduleClassNames[1];

    // Backwards compatability, old file structure
    if(defined('SRC_PATH'))
    {
      $moduleName = $moduleClassNames[2];
    }
    return $moduleName;
  }

	/**
   * @return mixed
   */
  private function getAppNamespace()
  {
    // Define the Application namespace, so we know where the controllers are
    $moduleClassNames = explode('\\', get_called_class());
    return $moduleClassNames[0];
  }

  public function registerAutoloaders() { }

  /**
   * Get the default dispatcher namespace
   *
   * @return string
   */
  protected function getDefaultDispatcherNamespace()
  {
    if(defined('SRC_PATH'))
    {
      return APP_NAMESPACE . "\\Modules\\" . APP_NAME . "\\Controllers";
    }

    return APP_NAMESPACE . "\\" . APP_NAME . "\\Controllers";
  }

	/**
   * @param DI $di
   */
  public function registerServices(DI $di)
  {
    $di['dispatcher']->setDefaultNamespace(
      $this->getDefaultDispatcherNamespace()
    );

    // Add module specific configuration
    $this->setModuleConfig($di);
  }

  /**
   * @param DI $di
   */
  private function setModuleConfig(DI $di)
  {
    $config = $di->get("config");
    $di
      ->set("config", function () use ($config)
      {
        $module = $this->getShared("dispatcher")->getModuleName();
        $action = $this->getShared("dispatcher")->getActionName();
        $namespace = APP_NAMESPACE;

        $filePathDefault = sprintf(
          APP_ROOT . "../config/%s/default.yaml",
          strtolower($namespace),
          strtolower($module)
        );

        $filePath = sprintf(
          APP_ROOT . "../config/%s/%s.yaml",
          strtolower($namespace),
          strtolower($module)
        );

        if (file_exists($filePathDefault))
        {
          $moduleConfig = new Yaml($filePathDefault);
          if ($moduleConfig)
          {
            $config->merge($moduleConfig);
          }
        }
        if (file_exists($filePath))
        {
          $moduleConfig = new Yaml($filePath);
          $actionConfig = $moduleConfig->get($action);
          if ($actionConfig)
          {
            $config->merge($actionConfig);
          }
        }

        return $config;
      });
  }
}
