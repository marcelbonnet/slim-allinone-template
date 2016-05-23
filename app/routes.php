<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;
use DarthEv\Core\app\Route;


Route::get('/[/{chave:.+}]', 'DarthEv\Core\ctrl\CoreController:home' )->setName("home") ;

Route::get('/home', function (SlimHttpRequest $request, SlimHttpResponse $response, $args) use($container) {
	$container->get('router')->getNamedRoute('home')->run($request, $response);
});


Route::get('/hello[/{name}]', 'DarthEv\Core\ctrl\CoreController:sayHello')->setName('hello');
Route::get('/protected', 'DarthEv\Core\ctrl\CoreController:callProtectedResource')->setName('protected');