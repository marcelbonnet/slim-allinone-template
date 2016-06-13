<?php
namespace DarthEv\Core\app;

use Zend\Authentication\Adapter\Ldap;
use DarthEv\Core\Config;

class SlimLdapAdapter extends Ldap{


	public function __construct(array $options=array() ,$identity=null,$credential=null)
	{ 
		parent::__construct( $options , $identity=null, $credential=null);
	}

	
	public function authenticate($username=null, $password=null)
	{
		$configReader = new \Zend\Config\Reader\Ini();
// 		$configData = $configReader->fromFile(__DIR__ . '/../../../conf/' . "ldap.ini");
		$configData = $configReader->fromFile(Config::CONFIG_FILE);
		$config = new \Zend\Config\Config($configData, false);
		$options = $config->ldapauth->ldap->toArray();
		$this->setOptions($options);
		$this->setUsername($username);
		$this->setPassword($password);
// 		var_dump(array("user" => $username));
// 		die("OK");
		return parent::authenticate();
	}
}