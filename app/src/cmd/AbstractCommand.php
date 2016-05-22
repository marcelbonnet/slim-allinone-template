<?php
namespace DarthEv\Core\cmd;

/**
 * Command para Delegar chamadas do FrontController do Slim Framework (o index.php).
 * Permite que uma requisição (request) do FrontController possa reutilizar os dados resultantes 
 * do processamento de um Command para alimentar outro(s) Command(s) antes de
 * responder (response) a requisição (request) - por exemplo com o envio do html ou de um json.
 * 
 * A vantagem de usar os Commands é que os objetos são instanciados somente quando forem realmente
 * necessários (após passarem do roteador para seu respectivo controlador e, então, delegado ao Command).
 * 
 * @author marcelbonnet
 *
 */
abstract class AbstractCommand
{

    /**
	 * Dados resultados do processamento do Command
	 * @var object[]
	 */
    protected $data = null;
    
    /**
     * Parâmetros da Query String, quando houver.
     * @var object[]
     */
    protected $args = null;
    
    /**
     * 
     * @var SlimHttpRequest
     */
    protected $request = null;
    
    /**
     *
     * @var SlimHttpResponse
     */
    protected $response = null;
    
    /**
     * Inicializa o Command
     * @param \Slim\App $app
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param object[] $args
     */
    public function __construct(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;
    }
    
	/**
	 * Método que executa o processamento e armazena o resultado
	 * em $data para aproveitamento por outros métodos
	 * @return DarthEv\Core\cmd\AbstractCommand ;
	 */
    public abstract function process();
    
    /**
     * Transforma os dados em $data, se preciso, e responde
     * com uma view HTML via Slim View
     */
    public abstract function respondWithHtml();
    
    /**
     * Transforma os dados em $data, se preciso, e responde
     * com uma view JSON via Slim View para responder chamadas
     * Ajax ou de API
     */
    public abstract function respondWithJson();
    
    /**
     * Dados resultados do método process
     */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * Argumentos (GET, query string) passados na request
	 */
	public function getArgs() {
		return $this->args;
	}
	
    
}
