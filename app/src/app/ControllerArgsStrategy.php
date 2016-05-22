<?php

namespace DarthEv\Core\app;

use Slim\Interfaces\InvocationStrategyInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * It's just a copy of Slim's RequestResponse Strategy.
 * To be evolved in future.
 * @author marcelbonnet
 *        
 */
class ControllerArgsStrategy implements InvocationStrategyInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Slim\Interfaces\InvocationStrategyInterface::__invoke()
	 */
	public function __invoke(callable $callable, ServerRequestInterface $request, ResponseInterface $response, array $routeArguments) {
		foreach ($routeArguments as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }
        return call_user_func($callable, $request, $response, $routeArguments);
	}
}