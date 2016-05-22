<?php
namespace DarthEv\Core;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;

/**
 * Invoke Slim's default Error Handler, but logs a message
 * to a file before it.
 * @author marcelbonnet
 *
 */
final class ErrorLoggerHandler extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct($displayErrorDetails = false, Logger $logger)
    {
    	$this->displayErrorDetails = (bool) $displayErrorDetails;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
    	#$dt = new \DateTime();
    	#$strData = $dt->format("Y-m-d H:i:s") . " ";
        // Log the message
        $this->logger->critical($exception->getMessage() . $exception->getTraceAsString());
		
        return parent::__invoke($request, $response, $exception);
    }
}