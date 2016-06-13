<?php
namespace DarthEv\Core\app;

use PDO;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;
use Doctrine\ORM\EntityManager;

/**
 * Authenticate through LDAP
 * Authorize through RDBMS, using Doctrine ORM
 * @author marcelbonnet
 *
 */
class TesteAdapter extends AbstractAdapter
{
    

    public function __construct(
        /*EntityManager*/ $entityManager
    ) {
//         $this->db = $db;
//         $this->tableName = $tableName;
//         $this->identityColumn = $identityColumn;
//         $this->credentialColumn = $credentialColumn;
//         $this->passwordValidator = $passwordValidator;
    }

    /**
     * Performs authentication.
     *
     * @return AuthenticationResult Authentication result
     */
    public function authenticate()
    {
    	$user = array(
    			"id" 		=> 12345678,
    			"username" 	=> "test@example.com",
    			"role"		=> array("member","admin"),
    			"password" 	=> null
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
     * Finds user to authenticate.
     *
     * @return array|null Array of user data, null if no user found
     */
    private function findUser()
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = :identity',
            $this->getTableName(),
            $this->getIdentityColumn()
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('identity' => $this->getIdentity()));

        // Explicitly setting fetch mode fixes
        // https://github.com/jeremykendall/slim-auth/issues/13
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
