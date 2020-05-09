<?php


namespace Phramework\DI;

use Modules\Login\LoginComponent;
use Modules\User\UserComponent;
use Phalcon\Assets\Manager;
use Phalcon\Config\Adapter\Yaml;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\DI;
use Phalcon\Escaper;
use Phalcon\Http\Response\Cookies;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\MetaData\Memory;
use Phalcon\Mvc\Router;
use Phalcon\Security;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Url;
use Phalcon\Mvc\View;

/**
 * APP_ROOT is defined in index.php
 * Class AbstractDI
 * @package Framework\DI
 */
abstract class AbstractDI extends DI
{

  /**
   * AbstractDI constructor.
   */
  public function __construct()
  {
    parent::__construct();

    $config = $this->mergeConfig();

    $this->set("config", $config);

    $this->set('router', function () use ($config){
      $router = new Router(false);
      if (!$config->has("routes"))
      {
        return $router;
      }

      $toArray = $config->get("routes")->toArray();
      foreach ($toArray as $path => $route)
      {
        if ($path == "not-found")
        {
          // Add 404
          $router->notFound($route);
        }
        $router->add($path, $route);
      }

      return $router;
    });

    $this->setShared(
      'dispatcher',
      function ()
      {
        return new Dispatcher();
      }
    );

    $this->set(
      "db",
      function () use($config) {
        return new Mysql(
          $config->get("database")->toArray()
        );
      }
    );
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

    $this->set("security", function ()  {
      $security = new Security();

      return $security;
    });

    $this->set('url', function () {
      $url = new Url();
      $url->setBaseUri('/');
      return $url;
    });

    $this->setShared(
      'view',
      function ()
      {
        $view = new View();

        return $view;
      }
    );

    $this
      ->setShared(
        'assets',
        function ()
        {
          $assets = new Manager();

          return $assets;
        }
      );

    $this
      ->setShared(
        'escaper',
        function ()
        {
          $escaper = new Escaper();

          return $escaper;
        }
      );

    $sessionAdapter = new Stream(
      [
        'savePath' => ROOT . $config->get("application.sessionSavePath")
      ]
    );

    $this->setShared('session', function () use ($config, $sessionAdapter) {
      $session = new SessionManager();
      $session->setAdapter($sessionAdapter);
      $session->start();
      return $session;
    });

    // Set shared components
    $this->set(
      'userComponent',
      function ()
      {
        return new UserComponent();
      }
    );
    $this->set(
      'loginComponent',
      function ()
      {
        return new LoginComponent();
      }
    );

    // Set up the flash service
    $this
      ->set("flashSession", function () use ($sessionAdapter) {

        $session = new \Phalcon\Session\Manager();
        $session->setAdapter($sessionAdapter);

        $escaper = new Escaper();
        $flash = new \Phalcon\Flash\Session($escaper, $session);
        $flash->setCssClasses([
          'error'   => 'alert alert-danger',
          'success' => 'alert alert-success',
          'notice'  => 'alert alert-info',
          'warning' => 'alert alert-warning',
        ]);

        return $flash;
      });

    $this
      ->set(
        'cookies',
        function (){
          $cookies = new Cookies();

          $cookies->useEncryption(false);

          return $cookies;
        }
      );
  }

  /**
   * @return Yaml
   */
  private function mergeConfig()
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
          APP_ROOT . "../config/%s/default.yaml",
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
   return explode("\\", get_called_class())[0];
  }
}
