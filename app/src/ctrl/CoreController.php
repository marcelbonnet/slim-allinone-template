<?php

namespace DarthEv\Core\ctrl;

use \Slim\Http\Request as SlimHttpRequest;
use \Slim\Http\Response as SlimHttpResponse;
use DarthEv\Core\app\Controller;
use DarthEv\Core\cmd\HomeCommand;
use DarthEv\Core\cmd\HelloCommand;

/**
 * Controlador para funções de entrada ou de primeiro nível da aplicação
 * @author marcelbonnet
 *
 */
class CoreController extends Controller {
	
	/**
	 * Home page
	 * @param SlimHttpRequest $request
	 * @param SlimHttpResponse $response
	 * @param array $args
	 */
	public static function home(SlimHttpRequest $request, SlimHttpResponse $response, $args)
	{
		try {
			$cmd = new HomeCommand($request, $response, $args);
			$cmd->process();
			$cmd->respondWithHtml();
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public static function sayHello(SlimHttpRequest $request, SlimHttpResponse $response, $args)
	{
		try {
			$cmd = new HelloCommand($request, $response, $args);
			$cmd->process();
			$cmd->respondWithHtml();
		} catch (Exception $e) {
			throw $e;
		}
	}
}