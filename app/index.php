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
use JeremyKendall\Password\PasswordValidator;
use JeremyKendall\Slim\Auth\Adapter\Db\PdoAdapter;
use DarthEv\core\app\Bootstrap;
use DarthEv\Core\app\Acl;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use JeremyKendall\Slim\Auth\ServiceProvider\SlimAuthProvider;
use Zend\Authentication\Storage\Session as SessionStorage;
use JeremyKendall\Slim\Auth\Middleware\Authorization;
use JeremyKendall\Slim\Auth\Handlers\RedirectHandler;
use RKA\Session;
use DarthEv\Core\Config;
use Zend\Authentication\AuthenticationService;
use DarthEv\Core\app\SlimLdapAdapter;
use DarthEv\Core\dao\DAO;
use DarthEv\Core\app\LdapRdbmsAdapter;

require_once 'vendor/autoload.php';

// REMOVE: using rka-slim-session-*
// session_cache_limiter(false);
// session_start();

/* ****************************************************************************
 * Slim App and Config
 * ****************************************************************************
 */
$config = require '../conf/config.php';
$app = new \Slim\App($config);

// Fetch DI Container
$container = $app->getContainer();
/* ****************************************************************************
 * Session
 * ****************************************************************************
 */
$app->add(new RKA\SessionMiddleware(['name' => 'app.session']));

/* ****************************************************************************
 * Authentication/Authorization
 * ****************************************************************************
 */
$acl = new Acl();
/*
 * RDBMS Adapter
 */
// $validator = new PasswordValidator();
// $adapter = new PdoAdapter( (new \PDO("mysql:host=localhost;port=3306;dbname=slim_allinone_orm", "marcelbonnet", "" ))
// 		, 'users', 'username', 'password', $validator);

/*
 * LDAP Adapter
 */
// $configReader = new \Zend\Config\Reader\Ini();
// $configData = $configReader->fromFile(Config::CONFIG_FILE);
// $config = new \Zend\Config\Config($configData, false);
// $options = $config->ldapauth->ldap->toArray();
// $adapter = new Zend\Authentication\Adapter\Ldap($options);

/*
 * Custom Adapter
 * Auth  via LDAP
 * Authz via RDBMS
 */
/*
     * @param string $credencialAttribute atrribute of $userEntity holding password
     * @param integer $authType one of AUTHENTICATE_LDAP|AUTHENTICATE_RDBMS
 */
$adapter = new DarthEv\Core\app\LdapRdbmsAdapter(
		Config::CONFIG_FILE,
		DAO::em(),
		"DarthEv\Core\dao\UserRole",
		"role",
		"DarthEv\Core\dao\User",
		"username",
		"passwordHash",
		LdapRdbmsAdapter::AUTHENTICATE_RDBMS,
		15,
		PASSWORD_DEFAULT
		);

$container["authAdapter"] = $adapter;

// FIXME Check later how to properly config session parameters
// $sessionConfig = new SessionConfig();
// $sessionConfig->setOptions(array(
// 		'remember_me_seconds' => 60 * 60 * 24 * 7,
// 		'name' => 'slim-auth-impl',
// ));
// $sessionManager = new SessionManager($sessionConfig);
// $sessionManager->rememberMe();
// $storage = new SessionStorage('slim_auth', null, $sessionManager);

// $container["authStorage"] = $storage;
	

$slimAuthProvider = new SlimAuthProvider();
$slimAuthProvider->register($container);

$app->add(new Authorization( $container["auth"], $acl, new RedirectHandler("auth/notAuthenticated", "auth/notAuthorized") ));

/*
 * Slim Flash
 * requires slim/flash
 */
// Register provider
// $container['flash'] = function () {
// 	return new \Slim\Flash\Messages();
// };

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
    $view->getEnvironment()->addGlobal('username', 			(is_array(@$c["auth"]->getStorage()->read()))? @$c["auth"]->getStorage()->read()["username"] : @$c["auth"]->getStorage()->read());
    $view->getEnvironment()->addGlobal('userRoles', 		(is_array(@$c["auth"]->getStorage()->read()))? @$c["auth"]->getStorage()->read()["role"] : array());
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
require_once "routes-auth.php";
require_once "routes-cli.php";
require_once "routes-api.php";
require_once "routes-middleware.php";

/*
 * if needed while developing auth
 */
// $app->getContainer()["auth"]->hasIdentity();
// $app->getContainer()["auth"]->clearIdentity();

$app->run();