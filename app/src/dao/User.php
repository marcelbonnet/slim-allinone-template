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
	 * @var integer
	 */
	protected $id;
	
	/**
	 * @Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	protected $username;
	
	/**
	 * If the user is authenticated through LDAP, it must be null, because
	 * we copy its username to this entity thus it is possible to associate its roles
	 * varchar(255) must be enought for years of evolution of the password hash factor (bcrypt)
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	protected $passwordHash;
	
	
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
	public function getPasswordHash() {
		return $this->passwordHash;
	}
	public function setPasswordHash($passwordHash) {
		$this->passwordHash = $passwordHash;
	}
	
	
	
}