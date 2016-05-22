<?php
/**
 * Slim App's Front Controller
 * @author marcelbonnet
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;
use DarthEv\Core\ErrorLoggerHandler;
use DarthEv\Core\app\App;
use DarthEv\Core\app\ControllerArgsStrategy;

require_once 'vendor/autoload.php';

session_cache_limiter(false);
session_start();

/* ****************************************************************************
 * Slim App and Config
 * ****************************************************************************
 */
$config = require '../conf/config.php';
$app = new \Slim\App($config);

// Fetch DI Container
$container = $app->getContainer();

/* ****************************************************************************
 * Twig View helper
 * ****************************************************************************
 */
// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(
    		'templates', 
    		[
        		#'cache' => 'path/to/cache'
    		]
    );
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
    
    /*
     * Twig's Global Variables
     */
    //https://www.codecourse.com/forum/topics/share-data-in-view-with-slim3-middleware/1587
    $view->getEnvironment()->addGlobal('twigDate', 			DarthEv\Core\Config::get()["misc"]["twigDate"]);
    $view->getEnvironment()->addGlobal('twigTime', 			DarthEv\Core\Config::get()["misc"]["twigTime"]);
    $view->getEnvironment()->addGlobal('twigDateTime', 		DarthEv\Core\Config::get()["misc"]["twigDateTime"]);
    $view->getEnvironment()->addGlobal('twigFullDateTime', 	DarthEv\Core\Config::get()["misc"]["twigFullDateTime"]);
    return $view;
};
/* ****************************************************************************
 * Monolog Logger
 * ****************************************************************************
 */
$container['monolog'] = function($c) {
	$settings = $c->get('settings')['logger'];
	$logger = new Monolog\Logger($settings['name']);
	#$filename = __DIR__ . '/../logs/error.log';
	$filename = $settings['path'];
	$stream = new Monolog\Handler\StreamHandler($filename, Monolog\Logger::DEBUG);
	$fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
        $stream, Monolog\Logger::INFO);
    $logger->pushHandler($fingersCrossed);
	
	return $logger;
};
$container['logger'] = function ($c) {
	$settings = $c->get('settings')['logger'];
	$logger = new Monolog\Logger($settings['name']);
	$logger->pushProcessor(new Monolog\Processor\UidProcessor());
	$logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
	return $logger;
};
//$settings = $c->get('settings')['logger']	//retrieve config
$logger = $container['logger'];
#$logger->addWarning("testando logger");	//how to log


/* ****************************************************************************
 * Error Handling
 * @see http://www.slimframework.com/docs/handlers/error.html
 * ****************************************************************************
 */
/*
//ver um github do whoops para integrar com o Slim 3 e tentar manter o Monolog junto
unset($app->getContainer()['errorHandler']);
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();
*/
$container['errorHandler'] = function ($c) {
	//Para testar os errorHandler: lembrar de lançar a exceção de dentro de um router, não de fora!
	/*
	return function ($request, $response, $exception) use ($c) {
	return $c['response']->withStatus(500)
	->withHeader('Content-Type', 'text/html')
	->write('Something went wrong!');
	};
	*/
	return new ErrorLoggerHandler($c['settings']['displayErrorDetails'], $c['monolog']);
};

/*
 * Route Strategies
 * @see http://www.slimframework.com/docs/objects/router.html
 */
$container['foundHandler'] = function(){
	return new ControllerArgsStrategy();
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
	return function ($request, $response) use ($c) {
		if ( $c['environment']['REQUEST_METHOD'] == 'POST' ){
			/*
			 * Useful for Ajax POST calls
			 */
			return $c['response']
			->withStatus(404);//->withHeader('Content-Type', 'text/html;charset=utf-8')->write('404');
		} else {
			/*
			 * A custom 404:
			 * return $c->view->render($response, "error/404/theme/404.html");
			 */
			//or simply:
			return $c['response']
			->withStatus(404)
			->withHeader('Content-Type', 'text/html;charset=utf-8')
			->write('Not Found.');
			
		}
	};
};

/*
 * \DarthEv\Core\app\App permitirá o reuso do Slim\App com chamadas estáticas
 */
App::setup($app);

/*
 * Routes
 */
require_once "routes.php";
require_once "routes-cli.php";
require_once "routes-api.php";
require_once "routes-middleware.php";

$app->run();