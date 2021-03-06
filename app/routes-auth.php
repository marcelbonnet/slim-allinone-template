<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;
use DarthEv\Core\app\Route;
use DarthEv\Core\app\App;


Route::group('/auth', function() {
	Route::get('/notAuthenticated', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
// 		return $response
// 		->withStatus(401)
// 		->withHeader('Content-Type', 'text/html;charset=utf-8')
// 		->write('You are not authenticated.');
		//redirect:
		$route = App::object()->getContainer()->get('router')->getNamedRoute('login');
		$route->setArgument("message" , "You are not authenticated" );
		$route->run($request, $response );
	})->setName("notAuthenticated");
	
	Route::get('/notAuthorized', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
		return $response
		->withStatus(403)
		->withHeader('Content-Type', 'text/html;charset=utf-8')
		->write('You are not authorized to this resource.');
	})->setName("notAuthorized");
});

Route::map(['GET','POST'], '/login', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
    $username = null;
    $app = App::object();
    /*
     * require: slim/flash
     * don't know if slim/flash is not stable or I'm a fool
     */
//     $app->getContainer()["flash"]->addMessage('error', 'testando novo');
//     var_dump( $app->getContainer()["flash"]->storage["slimFlash"]["error"] );
//     var_dump( $app->getContainer()["flash"]->getMessages()["error"] );
	$message = array_key_exists("message", $args) ? $args["message"] : null;
    if ($request->isPost()) {
        $username = $request->getParsedBody()['slimUsername'];
        $password = $request->getParsedBody()['slimPassword']; //(new PasswordValidator())->rehash($request->getParsedBody()['slimPassword']);
        $result = $app->getContainer()["authenticator"]->authenticate($username, $password);

        if ($result->isValid()) {
   			return $app->getContainer()->view->render($response, 'home.html');
        } else {
        	$messages = $result->getMessages();
            $message = $messages[0]; //message to presentation layer
//             $app->getContainer()["flash"]->addMessage('error', $messages[0]);
			$logger = $app->getContainer()["logger"];
        	foreach ($messages as $i => $msg) {
					$messages[$i] = str_replace("\n", "\n  ", $msg);
			}
			
			$logger->addWarning("Authentication failure for $username .", $messages);
            
        }
    }
    return $app->getContainer()->view->render($response, 'login.html', array('username' => @$username, "message" => $message));
})->setName('login');

Route::get('/logout', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
	$app = App::object();
    if ($app->getContainer()["auth"]->hasIdentity()) {
        $app->getContainer()["auth"]->clearIdentity();
    }
    //redirect:
    $app->getContainer()->get('router')->getNamedRoute('home')->run($request, $response);
})->setName('logout');
