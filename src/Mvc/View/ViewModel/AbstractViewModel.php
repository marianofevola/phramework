<?php


namespace Phramework\Mvc\View\ViewModel;

use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Forms\Form;
use Phalcon\Html\Breadcrumbs;
use Phalcon\Http\Request\Exception;
use Phalcon\Paginator\Adapter\NativeArray;
use Phalcon\Paginator\RepositoryInterface;
use phpDocumentor\Reflection\DocBlock\Description;


class AbstractViewModel implements IPhrameworkViweModel
{
  /** @var string  */
  const LAYOUT_DEFAULT = 'Default';

  /** @var Di */
  private $di;

  /** @var Config */
  private $config;

  /** @var string */
  protected $title;

  /** @var Description */
  protected $_description;

  /** @var string */
  private $layout;

  /** @var string */
  private $layoutDir;

  /** @var Breadcrumbs  */
  private $breadcrumbs;

  /** @var RepositoryInterface[]  */
  private $paginators = [];

  private $forms = [];

  /**
   * Used to initiate the view model, config is available here
   * @return void
   */
  public function initiate() {}

  /**
   * @return \Phalcon\Di
   */
  public function getDi()
  {
    return $this->di;
  }

  /**
   * @param Di $di
   * @return $this|IPhrameworkViweModel
   */
  public function setDi(Di $di)
  {
    $this->di = $di;
    return $this;
  }

  /**
   * @param Config $config
   * @return $this
   */
  public function setConfig(Config $config)
  {
    $this->config = $config;
    return $this;
  }

  /**
   * @return Config
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    if (isset($this->title))
    {
      return $this->title;
    }

    return "";
  }

  /**
   * @param string $title
   *
   * @return string
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get Description Tag
   *
   * @return string
   */
  public function getDescription()
  {
    if (isset($this->_description))
    {
      return $this->_description;
    }

    return "";
  }

  /**
   * Set Description Tag
   *
   * @param $description
   *
   * @return string
   */
  public function setDescription($description)
  {
    $this->_description = $description;

    return $this;
  }

  /**
   * Return the required Template file (without .phtml)
   *
   * @return null
   */
  public function getTemplateName()
  {
    $splitClass = explode('\\', get_called_class());

    return str_replace('ViewModel', '', end($splitClass));
  }

  /**
   * Return the required Layout
   *
   * @return string
   */
  public function getLayout()
  {
    if (isset($this->layout))
    {
      return $this->layout;
    }

    return self::LAYOUT_DEFAULT;
  }

  /**
   * Get the layouts directory
   *
   * @return string
   */
  public function getLayoutDir()
  {
    if (isset($this->layoutDir))
    {
      return $this->layoutDir;
    }

    return SRC_PATH . '/Common/Layouts';
  }

  /**
   * Set the layout directory
   *
   * @param string $layoutDir
   *
   * @return static
   */
  public function setLayoutDir($layoutDir)
  {
    $this->layoutDir = $layoutDir;

    return $this;
  }

  /**
   * Set the layout name
   *
   * @param $layout
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  /**
   * @return Breadcrumbs
   */
  public function getBreadcrumbs()
  {
    return $this->breadcrumbs;
  }

  /**
   * @param $uri
   * @return AbstractViewModel
   */
  public function setBreadcrumbsByUri($uri)
  {
    if (!$this->breadcrumbs)
    {
      $this->breadcrumbs = new Breadcrumbs();
    }
    // /profile/change-password
    $explodedUri = explode("/", $uri);
    foreach ($explodedUri as $link)
    {
      if (empty($link))
      {
        $this->breadcrumbs->add("Home", "/");
        continue;
      }

      $label = ucwords(str_replace("-", " ", $link));
      $outputArray = [];
      preg_match('/(.*)\?/', $label, $outputArray);
      if (isset($outputArray[1]))
      {
        $label = $outputArray[1];
      }
      $this->breadcrumbs->add($label, sprintf("/%s", $link));
    }


    return $this;
  }

  /**
   * @return int
   */
  public function hasBreadCrumbs()
  {
    // Only Home itself is not a valid breadcrumb
    return count($this->breadcrumbs->toArray()) > 1;
  }

  /**
   * @param string $name
   * @param array $items
   * @param int $currentPage
   * @param int $limit
   * @return $this
   */
  public function addPaginator($name, $items, $currentPage = 1, $limit = 20)
  {
    $paginator = new NativeArray(
      [
        'data'  => $items,
        'limit' => $limit,
        'page'  => $currentPage,
      ]
    );

    $this->paginators[$name] = $paginator->paginate();

    return $this;
  }

  /**
   * @param string $name
   * @return RepositoryInterface
   * @throws \Exception
   */
  public function getPaginator($name)
  {
    if (!$this->hasPaginator($name))
    {
      throw new \Exception(
        sprintf(
          "Paginator %s not created in view %s",
          $name,
          __CLASS__
        )
      );
    }
    return $this->paginators[$name];
  }

  /**
   * @param string $name
   * @return bool
   */
  public function hasPaginator($name)
  {
    return isset($this->paginators[$name]);
  }

  /**
   * @param $name
   * @param bool $nameOverride = what will show in the url
   * @return string
   * @throws \Exception
   */
  public function getPaginatorTemplate($name, $nameOverride = false)
  {
    $myPaginator = $this
      ->getPaginator($name);

    // Disable if no need of showing it
    if ($myPaginator->getTotalItems() <= $myPaginator->getLimit())
    {
      return '';
    }
    $current = $myPaginator
      ->getCurrent();
    $first = $myPaginator
      ->getFirst();
    $previous = $myPaginator
      ->getPrevious();
    $next = $myPaginator
      ->getNext();
    $last = $myPaginator
      ->getLast();

    $previousDisabled = $current == $first;
    $nextDisabled = $last == 0 || $current == $last;
    $hasPrevious = $first != $current;
    $hasNext = $next != $current && $next != 0;

    /** @var Exception $request */
    $request = $this
      ->getDi()
      ->get("request");

    /**
     * Query parameters already expected is:
     * 1. _url
     * 2. page
     */
    $queries = $request->getQuery();
    unset($queries["_url"]);
    if (isset($queries["page"]))
    {
      unset($queries["page"]);
    }
    $hasGetQueries = count($queries) > 0;
    if ($hasGetQueries)
    {
      // concatene page to them
      $queries = array_map(function ($value, $query){
        return sprintf("%s=%s", $query, $value);
      },$queries, array_keys($queries));
      $queries = implode("&", $queries);
      // add page to them
      $queries = sprintf("?%s&page=", $queries);
    }

    $test = sprintf(
      '
<nav aria-label="...">
  <ul class="pagination">
    <li class="page-item %s">
      <a class="page-link" href="/%s%s%d" tabindex="-1">Previous</a>
    </li>
    %s
    <li class="page-item active">
      <a class="page-link" href="#">
        %d
        <span class="sr-only">(current)</span>
      </a>
    </li>
    %s
    <li
      class="page-item %s">
      <a class="page-link" href="/%s%s%d">Next</a>
    </li>
  </ul>
</nav>
      ',
      $previousDisabled ? "disabled" : "",
      $nameOverride ? $nameOverride : $name,
      $hasGetQueries ? $queries : "?page=",
      $previous,
      $hasPrevious
        ? sprintf(
        '<li class="page-item"><a class="page-link" href="/%s%s%d">%d</a></li>',
        $nameOverride ? $nameOverride : $name,
        $hasGetQueries ? $queries : "?page=",
        $previous,
        $previous
      ) : '',
      $current,
      $hasNext
        ? sprintf(
        '<li class="page-item"><a class="page-link" href="/%s%s%d">%d</a></li>',
        $nameOverride ? $nameOverride : $name,
        $hasGetQueries ? $queries : "?page=",
        $next,
        $next
      ) : '',
      $nextDisabled ? "disabled" : "",
      $nameOverride ? $nameOverride : $name,
      $hasGetQueries ? $queries : "?page=",
      $next
    );

    return $test;
  }

  /**
   * @param Form $form = MyClassForm (class name must finish with 'Form')
   * @return AbstractViewModel
   * @throws \Exception
   */
  public function setForm(Form $form)
  {
    $matchOutput = [];
    preg_match(
      '/Form\\\(.*)Form/',
      get_class($form),
      $matchOutput
    );;
    $formName = lcfirst($matchOutput[1]);
    if (!$formName)
    {
      $errorMessage = "The form you are adding does not follow the pattern \Form\MyClassForm";
      throw new \Exception($errorMessage);
    }
    $this->forms[$formName] = $form;

    return $this;
  }

  /**
   * @param $name = myClass when form class name is MyClassForm
   * @return Form
   * @throws \Exception
   */
  public function getForm($name)
  {
    if (!$this->hasForm($name))
    {
      throw new \Exception(
        sprintf(
          "Form %s not created in view %s",
          $name,
          __CLASS__
        )
      );
    }
    return $this->forms[$name];  }

  /**
   * @param string $name
   * @return bool
   */
  public function hasForm($name)
  {
    return isset($this->forms[$name]);
  }
}
