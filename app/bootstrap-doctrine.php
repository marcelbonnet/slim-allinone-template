<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

$paths = array(__DIR__."/src/dao");
$isDevMode = \DarthEv\Core\Config::get()["doctrine"]["devel"];

// the connection configuration
$dbParams = array(
    'driver'    => \DarthEv\Core\Config::get()["database"]["doctrine_driver"],
    'user'      => \DarthEv\Core\Config::get()["database"]["user"],
    'password'  => \DarthEv\Core\Config::get()["database"]["password"],
    'host'      => \DarthEv\Core\Config::get()["database"]["host"],
    'dbname'    => \DarthEv\Core\Config::get()["database"]["db"],
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$em = EntityManager::create($dbParams, $config);