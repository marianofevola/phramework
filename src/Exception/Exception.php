<?php


namespace Phramework\Exception;

/**
 * Phramework Exception
 * All exceptions should extend this class
 *
 * Class Exception
 */
class Exception extends \Exception
{
  /**
   * @param array $data
   * @return Exception
   */
  public function addCustomData(array $data)
  {
    if (!count($data))
    {
      return $this;
    }

    $this->message = sprintf(
      "%s - Data: %s",
      $this->getMessage(),
      json_encode($data)
    );

    return $this;
  }

}
