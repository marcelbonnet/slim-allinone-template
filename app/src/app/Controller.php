<?php
namespace DarthEv\Core\app;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container;

/**
 * Controller
 * 
 * @author marcelbonnet 
 */
abstract class Controller
{
	/**
	 * 
	 * @var Slim\Container
	 */
	protected $container;
	
	public function __construct(Container $c)
	{
		$this->container = $c;
		//\Doctrine\Common\Util\Debug::dump($c);
	}

}