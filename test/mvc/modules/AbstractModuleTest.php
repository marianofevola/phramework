<?php


use Assertify\Framework\TestCase;
use Phalcon\Config\Adapter\Yaml;

class AbstractModuleTest extends TestCase
{

  public function testConfig()
  {
    $this->markTestSkipped("todo");
    $di = \Phalcon\Di::getDefault();
    $this->assertInstanceOf(Yaml::class, $di->get("config"));
  }
}
