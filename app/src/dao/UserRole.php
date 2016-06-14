<?php
namespace DarthEv\Core\dao;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity()
 * @Table(name="core__user_roles", 
 		uniqueConstraints={
 			@UniqueConstraint(
 				name="role_idx", columns={"fk_user_id","role"})
 			}
 		)
 * @HasLifecycleCallbacks
 * @author marcelbonnet
 */
class UserRole {
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
	protected $role;
	
	
	/**
	 * @ManyToOne(targetEntity="User", inversedBy="userRoles", cascade={"persist"})
	 * @JoinColumn(name="fk_user_id", referencedColumnName="id", nullable=false)
	 */
	protected $user = null;
	
	
	
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
		return strval("[Class=UserRole"
				.", id=".$this->getId()
				.", role=".$this->getRole()
				."]");
	}
	
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getRole() {
		return $this->role;
	}
	public function setRole($role) {
		$this->role = $role;
	}
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
	}
	
}