<?php


namespace Phramework\DI;

use Modules\Login\LoginComponent;
use Phalcon\Assets\Manager;
use Phalcon\Escaper;
use Phalcon\Filter;
use Phalcon\Http\Response\Cookies;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Session\Adapter\Stream as Stream;
use Phramework\Mvc\View\PhrameworkView;


class WebDi extends AbstractDi
{
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
      'view',
      function () {
        $view = new PhrameworkView();
        $view->setViewsDir(APP_ROOT . '/src/Modules/'. APP_NAME .'/Views/');
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

    $this
      ->setShared(
        'filter',
        function ()
        {
          $escaper = new Filter();

          return $escaper;
        }
      );

    $savePath = ROOT . $config->get("application")->get("sessionSavePath");
    if (true !== is_dir($savePath))
    {
      mkdir(
        $savePath,
        0777,
        true
      );
    }

    $sessionAdapter = new Stream(
      [
        'savePath' => $savePath
      ]
    );

    $this->setShared('session', function () use ($sessionAdapter) {
      $session = new SessionManager();
      $session->setAdapter($sessionAdapter);
      $session->start();
      return $session;
    });

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
}
