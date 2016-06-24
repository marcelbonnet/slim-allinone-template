<?php
namespace DarthEv\Core\app;

use Zend\Permissions\Acl\Acl as ZendAcl;
use DarthEv;

/**
 * ACL for Slim Auth Implementation Example.
 * @see https://github.com/marcelbonnet/slim-allinone-template
 */
class Acl extends ZendAcl
{
	protected $defaultPrivilege = array('GET');
	
	public function __construct()
	{
		// APPLICATION ROLES
		$this->addRole('guest');
		// member role "extends" guest, meaning the member role will get all of
		// the guest role permissions by default
		$this->addRole('member', 'guest');
		$this->addRole('admin');
		// APPLICATION RESOURCES
		// Application resources == Slim route patterns
		$this->addResource('/');
		$this->addResource('/login');
		$this->addResource('/logout');
		$this->addResource('/member');
		$this->addResource('/admin');
		
		$this->addResource('/home');
		$this->addResource('/hello[/{name}]');
		
		$this->addResource('/protected');
		$this->addResource('/auth/notAuthenticated');
		$this->addResource('/auth/notAuthorized');
		// APPLICATION PERMISSIONS
		// Now we allow or deny a role's access to resources.
		// The third argument is 'privilege'. In Slim Auth privilege == HTTP method
		$this->allow('guest', '/', $this->defaultPrivilege);
		$this->allow('guest', '/login', array('GET', 'POST'));
		$this->allow('guest', '/logout', $this->defaultPrivilege);
		$this->allow('member', '/member', $this->defaultPrivilege);
		
		$this->allow('guest', '/home' , $this->defaultPrivilege);
		$this->allow('guest', '/hello[/{name}]' , $this->defaultPrivilege);
		$this->allow('guest', '/auth/notAuthenticated' , $this->defaultPrivilege);
		$this->allow('guest', '/auth/notAuthorized' , $this->defaultPrivilege);
// 		$this->allow('member', '/protected' , $this->defaultPrivilege);
		$this->allow('admin', '/protected' , $this->defaultPrivilege);
		
		// This allows admin access to everything
		$this->allow('admin');
	}
	
	/**
	 * http://stackoverflow.com/questions/14540445/zend-acl-how-to-check-a-user-with-multiple-roles-for-resource-access
	 * {@inheritDoc}
	 * @see \Zend\Permissions\Acl\Acl::isAllowed()
	 */
	public function isAllowed($roleOrUser = null, $resource = null, $privilege = null)
	{
		if (empty($roleOrUser)) {
			return parent::isAllowed("guest", $resource, $privilege);
		} elseif (is_array($roleOrUser)) {
			foreach ($roleOrUser as $role) {
				if (parent::isAllowed($role['role'], $resource, $privilege)) {
					return true;
				}
			}
		} else {
			return parent::isAllowed($roleOrUser, $resource, $privilege);
		}
	}
	
}