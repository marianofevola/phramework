<?php


namespace Phramework\Mvc\View\ViewModel;

use Phalcon\Config;

interface IPhrameworkViweModel
{
  /**
   * Used to initiate the view model, config is available here
   * @return void
   */
  public function initiate();

  /**
   * @param Config $config
   * @return $this
   */
  public function setConfig(Config $config);

  /**
   * @return Config
   */
  public function getConfig();

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @param string $title
   *
   * @return string
   */
  public function setTitle($title);

  /**
   * Get Description Tag
   *
   * @return string
   */
  public function getDescription();

  /**
   * Set Description Tag
   *
   * @param $description
   *
   * @return string
   */
  public function setDescription($description);

}
