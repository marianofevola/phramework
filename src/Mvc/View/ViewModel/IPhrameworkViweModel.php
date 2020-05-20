<?php


namespace Phramework\Mvc\View\ViewModel;

interface IPhrameworkViweModel
{

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
