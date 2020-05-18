<?php


namespace Phramework\Mvc\View\ViewModel;

use Phalcon\Html\Breadcrumbs;
use phpDocumentor\Reflection\DocBlock\Description;
use Phramework\Exception\ConfigException;


class AbstractViewModel
{

  const LAYOUT_DEFAULT = 'Default';

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
}
