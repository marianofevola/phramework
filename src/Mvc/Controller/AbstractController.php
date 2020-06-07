<?php


namespace Phramework\Mvc\Controller;

use Phramework\Injectable\Acl;
use Phramework\Modules\Login\LoginComponent;
use Phramework\Modules\User\UserComponent;
use Phramework\Exception\ConfigException;
use Phramework\Injectable\Auth;
use Phramework\Mvc\View\ViewModel\AbstractViewModel;
use Phalcon\Assets\Manager;
use Phalcon\Config\Adapter\Yaml;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phramework\Mvc\View\ViewModel\IPhrameworkViweModel;

/**
 * view hierarchical: @link https://docs.phalcon.io/3.4/en/views
 *
 * Class AbstractController
 * @package Framework\Mvc\Controller
 *
 * @property Auth auth
 * @property Manager $assets
 * @property Acl $acl
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

  private function setJsCollections($jsArray, $root = null)
  {
    $assetsManager = $this->assets;
    $jsCollection = $assetsManager->collection("js");

    foreach ($jsArray as $js)
    {
      if (!is_null($root))
      {
        $jsCollection
          ->addJs(
            sprintf(
              "%s/js/%s",
              $root,
              $js
            )
          );
        continue;
      }
      $jsCollection->addJs(sprintf("js/%s", $js));
    }
  }

  private function addCssCollection($cssArray, $root = null)
  {
    /** @var Manager $assetsManager */
    $assetsManager = $this->assets;
    $cssCollection = $assetsManager->collection("css");
    foreach ($cssArray as $css)
    {
      if (!is_null($root))
      {
        $cssCollection
          ->addCss(
            sprintf(
              "%s/css/%s",
              $root,
              $css
            )
          );
        continue;
      }
      $cssCollection->addCss(sprintf("css/%s", $css));
    }
  }

  /**
   * @param Dispatcher $dispatcher
   * @return \Phalcon\Http\ResponseInterface|void
   * @throws \Phramework\Exception\Exception
   */
  public function beforeExecuteRoute(Dispatcher $dispatcher)
  {
    if (!$this->acl->isEnabled())
    {
      return;
    }

    $identity = $this->auth->getIdentity();
    if (!is_array($identity))
    {
      // no user, redirect to www
      return $this
        ->redirectToSubDomain("www");
    }

    $isAllowed = $this
      ->acl
      ->isAllowed(
        $identity["typeEnum"]->getValue(),
        $dispatcher->getControllerName(),
        $dispatcher->getActionName()
      );

    if (!$isAllowed)
    {
      return $this
        ->response
        ->redirect("/");
    }
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

  /**
   * Config example:
   * assets:
   *  css:
   *    - bootstrap.min.css
   */
  private function setAssets()
  {
    $config = $this->getConfig();

    $assets = $config->get("assets");

    if (!$assets)
    {
      return;
    }

    $root = $assets->get("root");

    if ($assets->has("css"))
    {
      $this
        ->addCssCollection(
          $assets
            ->get("css")
            ->getValues(),
          $root
        );
    }
    if ($assets->has("js"))
    {
      $this
        ->setJsCollections(
          $assets
            ->get("js")
            ->getValues(),
          $root
        );
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
  public function setView(IPhrameworkViweModel $view)
  {

    // Set breadcrumbs
    $config = $this->getDI()->get("config");
    $view->setConfig($config);
    if (
      $config->get("breadcrumbs")
      && $config->get("breadcrumbs")->get("isEnabled")
      && $config->get("breadcrumbs")->get("isEnabled") == true
    )
    {
      $view->setBreadcrumbsByUri($this->request->getURI());
    }

    // Set the Main Layout
    $this->view->setLayout($view->getLayout());

    // Set the layouts directory
    $layoutDir = $view->getLayoutDir();
    $this->view->setLayoutsDir($layoutDir);

    $view->initiate();

    // Send Main Views
    $this->view->setVar('viewModel', $view);

    // Set partials folder
    $this->view->setPartialsDir("partials/");

    // Set the Views Phtml
    $template = $view->getTemplateName();
    $layoutsDir = "../../../Common/View/layout/";
    $layoutName = sprintf("%slayout", lcfirst(APP_NAMESPACE));

    $layoutPath = sprintf(
      "%s/Common/View/layout/%s.phtml",
      SRC_PATH,
      $layoutName
    );

    if (!file_exists($layoutPath))
    {
      throw new \Exception(sprintf("Create a layout in %s", $layoutPath));
    }

    if ($template)

    {
      // Do not render anything after the ViewModel template
      $this->view->setRenderLevel(View::LEVEL_AFTER_TEMPLATE);
      $this->view->setLayoutsDir($layoutsDir);
      $this->view->setTemplateBefore($layoutName);
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

    $domain = $config->get("domain");
    if (!$domain)
    {
      throw (new ConfigException())->addCustomData(["application.domain"]);
    }
    $location = sprintf(
      "%s://%s.%s",
      $_SERVER["REQUEST_SCHEME"],
      $subDomain,
      $domain
    );

    return $this->response->redirect($location, true);
  }

  /**
   * @return LoginComponent
   */
  protected function getLoginComponent()
  {
    return $this->getDI()->get("loginComponent");
  }

  /**
   * @return UserComponent
   */
  protected function getUserComponent()
  {
    return $this->getDI()->get("userComponent");
  }

}
