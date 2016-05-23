<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;
use DarthEv\Core\app\Route;
use DarthEv\Core\app\App;


Route::group('/auth', function() {
	Route::get('/notAuthenticated', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
		return $response
		->withStatus(401)
		->withHeader('Content-Type', 'text/html;charset=utf-8')
		->write('You are not authenticated.');
	})->setName("notAuthenticated");
	
	Route::get('/notAuthorized', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
		return $response
		->withStatus(403)
		->withHeader('Content-Type', 'text/html;charset=utf-8')
		->write('You are not authorized to this resource.');
	})->setName("notAuthorized");
});

Route::map(['GET','POST'], '/login', function () {
    $username = null;
    $app = App::$app;
    if ($app->request()->isPost()) {
        $username = $app->request->post('username');
        $password = $app->request->post('password');
        $result = $app->authenticator->authenticate($username, $password);
        if ($result->isValid()) {
            $app->redirect('/');
        } else {
            $messages = $result->getMessages();
            $app->flashNow('error', $messages[0]);
        }
    }
    $app->render('login.twig', array('username' => $username));
})->setName('login');

Route::get('/logout', function () {
	$app = App::$app;
    if ($app->auth->hasIdentity()) {
        $app->auth->clearIdentity();
    }
    $app->redirect('/');
});
