<?php
namespace DarthEv\Core\app;

use PDO;
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
class TesteAdapter extends AbstractAdapter
{
    protected static $configFile = null;
    
    protected $entityManager;
    protected $roleEntity;
    protected $roleAttribute;
    protected $roleAssociationAttribute;
    protected $identityAttribute;
    protected $userEntity;

    /**
     * 
     * @param string $configIniFile ini file with LDAP settings
     * @param EntityManager $entityManager
     * @param string $roleFQCN Role Entity fully qualified class name
     * @param string $identityAttribute of $roleFQCN
     */
    public function __construct(
    	$configIniFile,
        EntityManager $entityManager,
    	$roleFQCN = null,
    	$roleAttribute = null,
//     	$roleAssociationAttribute,
    	$userEntity,
    	$identityAttribute = NULL
    ) {
    	self::$configFile		= $configIniFile;
    	$this->entityManager 	= $entityManager;
    	$this->roleEntity		= $roleFQCN;
    	$this->roleAttribute	= $roleAttribute;
//     	$this->roleAssociationAttribute = $roleAssociationAttribute;
    	$this->userEntity		= $userEntity;
    	$this->identityAttribute= $identityAttribute;
    }

    /**
     * Performs authentication.
     *
     * @return AuthenticationResult Authentication result
     */
    public function authenticate()
    {
    	$result = $this->authenticateLdap();
    	var_dump($result);
    	if (!$result->isValid()){
    		/*
    		 * TODO: log failure to file
    		 */
    		
    		return new AuthenticationResult(AuthenticationResult::FAILURE
    				, $result->getIdentity()
    				, $result->getMessages());
    	}
    	
    	$userRoles = $this->findUserRoles();
    	
    	\Doctrine\Common\Util\Debug::dump($userRoles);
    	
    	$user = array(
//     			"id" 		=> 12345678,
    			"username" 	=> $this->getIdentity(),
//     			"role"		=> array("member","admin"),
    			"role"		=> $userRoles
    	);
    	return new AuthenticationResult(AuthenticationResult::SUCCESS, $user, array());
    	/*
        $user = $this->findUser();

        if ($user === false) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND,
                array(),
                array('User not found.')
            );
        }

        $validationResult = $this->passwordValidator->isValid(
            $this->credential, $user[$this->credentialColumn], $user[$this->identityColumn]
        );

        if ($validationResult->isValid()) {
            // Don't store password in identity
            unset($user[$this->getCredentialColumn()]);

            return new AuthenticationResult(AuthenticationResult::SUCCESS, $user, array());
        }

        return new AuthenticationResult(
            AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
            array(),
            array('Invalid username or password provided')
        );
        */
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
    	return $adapter->authenticate();
    }
    

    /**
     * Finds the authenticated LDAP's user from RDBMS and return
     * its roles.
     *
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
    		//return $rolesDAO = ... mode = FETCH_ARRAY
    	} catch (Exception $e) {
    		throw $e;
    	}
//         $sql = sprintf(
//             'SELECT * FROM %s WHERE %s = :identity',
//             $this->getTableName(),
//             $this->getIdentityColumn()
//         );
//         $stmt = $this->db->prepare($sql);
//         $stmt->execute(array('identity' => $this->getIdentity()));

//         // Explicitly setting fetch mode fixes
//         // https://github.com/jeremykendall/slim-auth/issues/13
//         return $stmt->fetch(PDO::FETCH_ASSOC);
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
