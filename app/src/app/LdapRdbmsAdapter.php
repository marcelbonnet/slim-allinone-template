<?php
namespace DarthEv\Core\app;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

/**
 * Authenticate through LDAP
 * Authorize through RDBMS, using Doctrine ORM
 * @author marcelbonnet
 *
 */
class LdapRdbmsAdapter extends AbstractAdapter
{
	
	/**
	 * Used when authenticate through LDAP/AD
	 * when connected through intranet or VPN
	 * @var integer
	 */
	const AUTHENTICATE_LDAP = 1;
	
	/**
	 * Used when LDAP/AD is not available (like
	 * for users connected through the internet,
	 * thus authenticates using RDBMS
	 * @var integer
	 */
	const AUTHENTICATE_RDBMS = 2;
	
    protected static $configFile 		= null;
    
    protected $entityManager 			= null;
    protected $roleEntity 				= null;
    protected $roleAttribute 			= null;
    protected $roleAssociationAttribute = null;
    protected $identityAttribute 		= null;
    protected $userEntity 				= null;
    protected $credencialAttribute		= null;
    protected $authType 				= null;
    protected $pwdHashFactor			= null;
    protected $pwdHashAlgo			= null;

    /**
     * As we this Adapter uses Doctrine ORM, we suppose
     * that the User and Roles model both have an atrribute nammed
     * <em>id</em> annotated with
     * <pre> 
     * @Id 
	 * @GeneratedValue 
	 * @Column(type="integer") 
	 * protected $id;
     * </pre>
     * @param string $configIniFile ini file with LDAP settings
     * @param EntityManager $entityManager
     * @param string $roleFQCN Role Entity fully qualified class name
     * @param object $roleAttribute attribute of $roleFQCN holding roles
     * @param object $userEntity User Entity FQCN (fully qualified class name)
     * @param string $identityAttribute atrribute of $userEntity holding username
     * @param string $credencialAttribute atrribute of $userEntity holding password
     * @param integer $authType one of AUTHENTICATE_LDAP|AUTHENTICATE_RDBMS
     * @param number $pwdHashFactor if using AUTHENTICATE_RDBMS, than sets the password hash factor
     * @param string $pwdHashAlgo if using AUTHENTICATE_RDBMS, than sets the password algorithm
     */
    public function __construct(
    	$configIniFile,
        EntityManager $entityManager,
    	$roleFQCN = null,
    	$roleAttribute = null,
    	$userEntity,
    	$identityAttribute = NULL,
    	$credencialAttribute = null,
    	$authType = self::AUTHENTICATE_LDAP,
    	$pwdHashFactor = 10,
    	$pwdHashAlgo = PASSWORD_DEFAULT
    ) {
    	self::$configFile		= $configIniFile;
    	$this->entityManager 	= $entityManager;
    	$this->roleEntity		= $roleFQCN;
    	$this->roleAttribute	= $roleAttribute;
    	$this->userEntity		= $userEntity;
    	$this->identityAttribute= $identityAttribute;
    	$this->credencialAttribute= $credencialAttribute;
    	$this->authType			= $authType;
    	$this->pwdHashFactor	= $pwdHashFactor;
    	$this->pwdHashAlgo		= $pwdHashAlgo;
    }

    /**
     * Performs authentication.
     *
     * @return AuthenticationResult Authentication result
     */
    public function authenticate()
    {
    	$result = null;
    	if ($this->authType == self::AUTHENTICATE_LDAP){
    		$result = $this->authenticateLdap();
    	}
    	
    	if ($this->authType == self::AUTHENTICATE_RDBMS){
    		$result = $this->authenticateRdbms();
    	}
    	
    	
    	if (!$result->isValid()){
    		/*
    		 * TODO: log failure to file
    		 */
    		
    		return new AuthenticationResult(AuthenticationResult::FAILURE
    				, $result->getIdentity()
    				, $result->getMessages());
    	}
    	
    	$userRoles = $this->findUserRoles();
    	
    	$user = array(
    			"username" 	=> $this->getIdentity(),
    			"role"		=> $userRoles
    	);
    	return new AuthenticationResult(AuthenticationResult::SUCCESS, $user, array());
    }
    
    /**
     * Expects ini file's schema:
     * <pre>
     * [ldapauth]
     * ldap.[options]...
     * ldap.[options]...
     * ldap.[options]...
     * ...
     * </pre>
     */
    private function authenticateLdap()
    {
    	$configReader = new \Zend\Config\Reader\Ini();
    	$configData = $configReader->fromFile(self::$configFile);
    	$config = new \Zend\Config\Config($configData, false);
    	$options = $config->ldapauth->ldap->toArray();
    	$adapter = new \Zend\Authentication\Adapter\Ldap($options);
    	$adapter->setIdentity($this->getIdentity());
    	$adapter->setCredential($this->getCredential());
    	return $adapter->authenticate();
    }
    
    /**
     * password_hash("teste",PASSWORD_DEFAULT, [ "cost" => 15 ])
     * 
     * @throws Exception
     * @return \Zend\Authentication\Result
     */
    private function authenticateRdbms()
    {
    	try {
    		$user = $this->findUser($this->getIdentity());
    		
    		if(empty($user) ||
    				!password_verify($this->getCredential(), $user[$this->credencialAttribute])){
    			return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
    					array(),
    					array('Invalid username and/or password provided'));
    		}
    		
    		$currentHashAlgorithm   =  $this->pwdHashAlgo;
    		$currentHashOptions  =  array('cost'   => $this->pwdHashFactor ); 
    		$passwordNeedsRehash =  password_needs_rehash(
    				$user[$this->credencialAttribute],
    				$currentHashAlgorithm,
    				$currentHashOptions
    				);
    		
    		if($passwordNeedsRehash === true){
    			//try $em findby id , set e persist
    		}
    		
    		unset($user[$this->credencialAttribute]);
    		return new AuthenticationResult(AuthenticationResult::SUCCESS, $user, array('Authenticated through RDBMS'));
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
    
    /**
     * Finds a user by $username
     * @param string $username
     * @return an array with id, username and password (hashed)
     * @throws Exception
     */
    private function findUser($username)
    {
    	$dql = sprintf("SELECT u.id, u.%s, u.%s
    			FROM %s u
    			WHERE u.%s = :username",
    			$this->identityAttribute,
    			$this->credencialAttribute,
    			$this->userEntity,
    			$this->identityAttribute
    			);
    	 
    	try {
    		$query = $this->entityManager->createQuery($dql);
    		$query->setParameter("username", $this->getIdentity());
    		return $query->getSingleResult(Query::HYDRATE_ARRAY);
    	} catch (\Doctrine\ORM\NoResultException $e) {
    		return [];
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
    

    /**
     * Perform a search of user's roles.
     * @return array of roles
     */
    private function findUserRoles()
    {
    	$dql = sprintf("SELECT r.%s 
    			FROM %s r
    			JOIN %s u
    			WHERE u.%s = :username",
    			$this->roleAttribute,
    			$this->roleEntity,
    			$this->userEntity,
    			$this->identityAttribute
    			);
    	
    	try {
    		$query = $this->entityManager->createQuery($dql);
    		$query->setParameter("username", $this->getIdentity());
    		return $query->getResult(Query::HYDRATE_ARRAY);
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

    /**
     * Get tableName.
     *
     * @return string tableName
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get identityColumn.
     *
     * @return string identityColumn
     */
    public function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    /**
     * Get credentialColumn.
     *
     * @return string credentialColumn
     */
    public function getCredentialColumn()
    {
        return $this->credentialColumn;
    }
}
