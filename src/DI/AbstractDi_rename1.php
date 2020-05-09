<?php


namespace Phramework\DI;

use Modules\User\UserComponent;
use Phalcon\Config\Adapter\Yaml;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\DI;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\MetaData\Memory;
use Phalcon\Security;
use Phalcon\Url;

/**
 * APP_ROOT is defined in index.php
 * Class AbstractDI
 * @package Framework\DI
 */
abstract class AbstractDi extends DI
{

  /**
   * AbstractDI constructor.
   */
  public function __construct()
  {
    parent::__construct();

    $config = $this->mergeConfig();

    $this->set("config", $config);
    $this->set(
      "db",
      function () use($config) {
        return new Mysql(
          $config->get("database")->toArray()
        );
      }
    );

    $this->set("security", function ()  {
      $security = new Security();

      return $security;
    });

    $this->set('url', function () {
      $url = new Url();
      $url->setBaseUri('/');
      return $url;
    });

    $this->set('modelsManager', function() {
      return new ModelsManager();
    });

    $this->set('modelsMetadata', function() {
      return new Memory();
    });

    $this->set('response', array(
      'className' => 'Phalcon\Http\Response'
    ));

    $this->set('request', array(
      'className' => 'Phalcon\Http\Request'
    ));

    // Set shared components
    $this->set(
      'userComponent',
      function ()
      {
        return new UserComponent();
      }
    );
  }

  /**
   * @return Yaml
   */
  protected function mergeConfig()
  {
    // Merge environmental config
    $env = $_SERVER["ENV"];
    $config = new Yaml(APP_ROOT . "../config/defaults.yaml");
    $config->merge(
      new Yaml(
        sprintf(
          APP_ROOT . "../config/%s.yaml",
          $env
        )
      )
    );

    // Merge module config
    $moduleName = $this->getModuleName();
    $config->merge(
      new Yaml(
        sprintf(
          APP_ROOT . "../config/%s/defaults.yaml",
          $moduleName
        )
      )
    );
    return $config;
  }

  /**
   * @return mixed
   */
  private function getModuleName()
  {
   return strtolower(explode("\\", get_called_class())[0]);
  }
}
