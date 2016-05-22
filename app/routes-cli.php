<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;
use pavlakis\cli\CliRequest;
use DarthEv\Core\app\App;
use DarthEv\Core\app\Route;

/* ****************************************************************************
 * Middleware: CLI Request
 * ****************************************************************************
 */
App::add(new CliRequest());

Route::get("/cli/hello", function (SlimHttpRequest $request, SlimHttpResponse $response, $args) {
	/*
	 * USAGE:
	 * $ php index.php /cli/hello GET name=marcel
	 */
	if (PHP_SAPI !== 'cli'){
		return $response
		->withStatus(404)
		->withHeader('Content-Type', 'text/html')
		->write('<h1>Not Allowed</h1>');
	}
	$name = $request->getParam('name');
    $response->getBody()->write("Hello, : $name".PHP_EOL);
    return $response;
});