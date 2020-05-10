<?php


namespace Phramework\Exception;

class ConfigException extends Exception
{
  /** @var string  */
  protected $message = "Config key missing";
}
