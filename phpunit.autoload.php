<?php

use Phalcon\Di;
use Phalcon\Loader;
use Phramework\DI\WebDi;

ini_set("display_errors", 1);
error_reporting(E_ALL);

// /backend paths
define('ROOT', sprintf("%s/", __DIR__));

/**
 * On other environment the ENV is set from nginx
 */
$_SERVER["ENV"] = "test";

// Use the application autoloader to autoload the classes
// Autoload the dependencies found in composer
$loader = new Loader();

$loader->register();

$di = new WebDi();

Di::reset();

// Add any needed services to the DI here

Di::setDefault($di);
