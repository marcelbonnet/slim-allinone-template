<?php
namespace DarthEv\Core\dao;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity()
 * @Table(name="core__users", 
 		uniqueConstraints={
 			@UniqueConstraint(
 				name="username_idx", columns={"username"})
 			}
 		)
 * @HasLifecycleCallbacks
 * @author marcelbonnet
 */
class User {
	/**
	 * @Id 
	 * @GeneratedValue 
	 * @Column(type="integer")
	 * @var string
	 */
	protected $id;
	
	/**
	 * @Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	protected $username;
	
	
	/**
	 * @OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"persist"})
	 * @var UserRole $userRoles
	 */
	protected $userRoles = null;
	
	/*
	 * other properties like:
	 * email, full name, telephone
	 * are intended to be taken from LDAP search by username
	 */
	
	
	public function __construct() {
		$this->userRoles = new ArrayCollection();
	}
	
	
	/**
	 * @PrePersist
	 */
	public function doChain(){
		//set created datetime
	}
	
	/**
	 * @PreUpdate
	 */
	public function doTokens(){
		//set updated datetime
	}
	

	public function __toString()
	{
		return strval("[Class=User"
				.", id=".$this->getId()
				.", username=".$this->getUsername()
				."]");
	}
	
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getUsername() {
		return $this->username;
	}
	public function setUsername($username) {
		$this->username = $username;
	}
	public function getUserRoles() {
		return $this->userRoles;
	}
	public function setUserRoles($userRoles) {
		$this->userRoles = $userRoles;
	}
	
	
}