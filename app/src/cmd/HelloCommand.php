<?php
namespace DarthEv\Core\cmd;

use DarthEv\Core\cmd\AbstractCommand;
use Slim\Http\MobileRequest;
use DarthEv\Core\app\App;

class HelloCommand extends AbstractCommand {
	
	public function __construct(\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
		parent::__construct ($request, $response, $args );
	}
	
	public function process() {
		$this->data = array("username" => @$this->args['name'] );
	}
	
	public function respondWithJson() {
		// TODO Auto-generated method stub
	}
	
	public function respondWithHtml() {
		return App::object()->getContainer()->view->render($this->response, 'hello.html', $this->data);
	}
}