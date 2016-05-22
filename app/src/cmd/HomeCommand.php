<?php
namespace DarthEv\Core\cmd;

use DarthEv\Core\cmd\AbstractCommand;
use Slim\Http\MobileRequest;
use DarthEv\Core\app\App;

class HomeCommand extends AbstractCommand {
	
	public function __construct(\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
		parent::__construct ($request, $response, $args );
	}
	
	public function process() {
		$request  = new MobileRequest( $this->request );
		$isMobile = $request->isMobile();
		$this->data = array("isMobile" => $isMobile);
	}
	
	public function respondWithJson() {
		// TODO Auto-generated method stub
	}
	
	public function respondWithHtml() {
		return App::object()->getContainer()->view->render($this->response, 'home.html', $this->data);
	}
}