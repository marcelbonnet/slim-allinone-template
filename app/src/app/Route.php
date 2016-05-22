<?php

namespace DarthEv\Core\app;

/**
 * Class Route - from Andrew Smith's github repository, a developer of Slim Framework.
 * 
 * Allows the use of multiple controllers for one \Slim\App instance.
 * 
 * Allows static calls.
 * 
 * @see https://github.com/silentworks/madmin-api
 * @author andrew smith (Slim dev)
 *
 * @method static \Slim\App get()
 * @method static \Slim\App post()
 * @method static \Slim\App put()
 * @method static \Slim\App delete()
 * @method static \Slim\App group()
 */
class Route
{
	/**
	 * Staticfy o Route.
	 * 
	 * @param callable $method GET, POST, PUT ... 
	 * @param array $args  
	 * @return mixed
	 */
    public static function __callStatic($method, $args)
    {
        $instance = App::object();
        switch (count($args))
        {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}