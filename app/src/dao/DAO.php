<?php
namespace DarthEv\Core\dao;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DAO
{
    public static function em()
    {
        /* ao carregar novo ou mudar staus de um módulo
        * registrar isso numa classe/tabela core-modules do
        * sistema para saber onde dar o bootstrap:
        */
        
        $paths = array( __DIR__,
                # usar  . DIRECTORY_SEPARATOR . 
                       # __DIR__ . "/../mod/fiscalizacao/dao"
                );    //"./src/dao" , path to Managed Entities
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

        return $em;
    }
   
}
    
