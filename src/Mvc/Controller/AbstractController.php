<?php


namespace Phramework\Mvc\Controller;

use Phramework\Injectable\Auth;
use Phramework\Mvc\ViewModel\AbstractViewModel;
use Phalcon\Assets\Manager;
use Phalcon\Config\Adapter\Yaml;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;

/**
 * view hierarchical: @link https://docs.phalcon.io/3.4/en/views
 *
 * Class AbstractController
 * @package Framework\Mvc\Controller
 *
 * @property Auth auth
 */
abstract class AbstractController extends Controller
{
  /** @var Yaml */
  private $config;

  /**
   * @var array
   */
  private $unsecuredRoutes = [
    ['controller' => 'login', 'action' => 'login'],
    ['controller' => 'login', 'action' => 'post'],
  ];

  /**
   * @link https://docs.phalcon.io/4.0/en/assets
   */
  public function onConstruct()
  {
    $this->setAssets();
  }

  private function setJsCollections($jsArray)
  {
    $assetsManager = $this->assets;
    $jsCollection = $assetsManager->collection("js");

    $jsCollection->addJs('js/jquery-3.2.1.slim.min.js');
    $jsCollection->addJs('js/jquery-slim.min.js');
    $jsCollection->addJs('js/popper.min.js');
    $jsCollection->addJs('js/bootstrap.min.js');

    foreach ($jsArray as $js)
    {
      $jsCollection->addJs(sprintf("css/%s", $js));
    }
  }

  private function addCssCollection($cssArray)
  {
    /** @var Manager $assetsManager */
    $assetsManager = $this->assets;
    $cssCollection = $assetsManager->collection("css");
    foreach ($cssArray as $css)
    {
      $cssCollection->addCss(sprintf("css/%s", $css));
    }
  }

  /**
   * @param Dispatcher $dispatcher
   *
   */
  public function beforeExecuteRoute(Dispatcher $dispatcher)
  {
  }

  /**
   * @param Dispatcher $dispatcher
   *
   * @return bool
   */
  public function afterExecuteRoute(Dispatcher $dispatcher)
  {
  }

  /**
   * @param Dispatcher $dispatcher
   *
   * @return bool
   */
  private function isUnsecuredRoute(Dispatcher $dispatcher)
  {
    foreach ($this->unsecuredRoutes as $route)
    {
      if ($route['controller'] == $dispatcher->getControllerName()
        && $route['action'] == $dispatcher->getActionName()
      )
      {
        return true;
      }
    }

    return false;
  }


  private function setAssets()
  {
    $config = $this->getConfig();

    $assets = $config->get("assets");
    if ($assets->has("css"))
    {
      $this->addCssCollection($assets->get("css")->getValues());
    }
    if ($assets->has("js"))
    {
      $this->setJsCollections($assets->get("js"));
    }
  }

  /**
   * @return Yaml
   */
  private function getConfig()
  {
    if (!$this->config)
    {
      return $this->config = $this
        ->getDI()
        ->get("config");
    }

    return $this->config;
  }


  /**
   * Set a view model
   *
   * @param AbstractViewModel $view
   *
   * @return static
   */
  public function setView(AbstractViewModel $view)
  {
    // Set the Main Layout
    $this->view->setLayout($view->getLayout());

    // Set the layouts directory
    $layoutDir = $view->getLayoutDir();
    $this->view->setLayoutsDir($layoutDir);

    // Send Main Views
    $this->view->setVar('viewModel', $view);

    // Set the Views Phtml
    $template = $view->getTemplateName();
    if ($template)
    {
      // Do not render anything after the ViewModel template
      $this->view->setRenderLevel(View::LEVEL_AFTER_TEMPLATE);
      $this->view->setLayoutsDir("../../../Common/View/Layout/");
      $this->view->setTemplateBefore(sprintf("%sLayout", APP_NAMESPACE));
    }

    return $this;
  }

  /**
   * Get the view template file path
   *
   * @param                      $basePath
   * @param                      $viewName
   *
   * @return string
   */
  protected function getViewTemplatePath(
    $basePath,
    $viewName
  )
  {
    $basePath = rtrim($basePath, '/');

    return '../../Modules' . $basePath . '/' . $viewName;
  }

  /**
   * @param $subDomain
   * @return \Phalcon\Http\ResponseInterface
   */
  protected function redirectToSubDomain($subDomain)
  {
    /** @var Yaml $config */
    $config = $this
      ->getDI()
      ->get("config")
      ->get("application");

    $location = sprintf(
      "%s://%s.%s",
      $_SERVER["REQUEST_SCHEME"],
      $subDomain,
      $config->get("domain")
    );

    return $this->response->redirect($location, true);
  }

}
