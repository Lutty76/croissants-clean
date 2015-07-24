<?php

namespace CreativeData\CroissantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Doctrine\Common\Collections\ArrayCollection;

//TODO implement userInterface
/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="CreativeData\CroissantBundle\Entity\UserRepository")
 */
class User implements UserInterface{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", length=255, unique=true, nullable=true)
     */
    protected $googleId;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=true)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=32, nullable=true)
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=32, nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64)
     */

    private $email;

 
    /**
     * @var integer
     *
     * @ORM\Column(name="joker", type="integer")
     */
    private $joker = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="selected", type="boolean")
     */
    private $selected = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="coefficient", type="integer")
     */
    private $coefficient = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=128)
     */
    private $token = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastTrap", type="datetime", nullable=true)
     */
    private $lastTrap;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUp", type="datetime", nullable=true)
     */
    private $lastUp;

    /**
     * @ORM\OneToMany(targetEntity="History", mappedBy="user")
     */
    private $historys;
 
    
     public function __construct()
    {
        $this->historys = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
	return $this->id;
    }

    /**
     * @param string $googleId
     */
    public function setGoogleId($googleId) {
	$this->googleId = $googleId;
    }

    /**
     * @return string
     */
    public function getGoogleId() {
	return $this->googleId;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
	$this->username = $username;

	return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() {
	return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
	$this->password = $password;

	return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
	return $this->password;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles) {
	$this->roles = $roles;

	return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles() {
	//return $this->roles;
	return array('ROLE_USER');
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
	$this->salt = $salt;

	return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt() {
	return $this->salt;
    }

 

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
	$this->email = $email;

	return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
	return $this->email;
    }


    /**
     * Set joker
     *
     * @param integer $joker
     * @return User
     */
    public function setJoker($joker) {
	$this->joker = $joker;

	return $this;
    }

    /**
     * Get joker
     *
     * @return integer 
     */
    public function getJoker() {
	return $this->joker;
    }

    /**
     * Set premium
     *
     * @param boolean $premium
     * @return User
     */
    public function setPremium($premium) {
	$this->premium = $premium;

	return $this;
    }

    /**
     * Get premium
     *
     * @return boolean 
     */
    public function getPremium() {
	return $this->premium;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return User
     */
    public function setSelected($selected) {
	$this->selected = $selected;

	return $this;
    }

    /**
     * Get selected
     *
     * @return boolean 
     */
    public function getSelected() {
	return $this->selected;
    }

    /**
     * Set coefficient
     *
     * @param integer $coefficient
     * @return User
     */
    public function setCoefficient($coefficient) {
	$this->coefficient = $coefficient;

	return $this;
    }

    /**
     * Get coefficient
     *
     * @return integer 
     */
    public function getCoefficient() {
	return $this->coefficient;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token) {
	$this->token = $token;

	return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken() {
	return $this->token;
    }

	

    /**
     * Set lastTrap
     *
     * @param \DateTime  $lastTrap
     * @return User
     */
    public function setLastTrap($lastTrap) {
	$this->lastTrap = $lastTrap;

	return $this;
    }

    /**
     * Get lastTrap
     *
     * @return \DateTime  
     */
    public function getLastTrap() {
	return $this->lastTrap;
    }
	
    public function eraseCredentials() {
	
    }

    public function equals(UserInterface $user) {
	if (!$user instanceof WebserviceUser) {
	    return false;
	}

	if ($this->password !== $user->getPassword()) {
	    return false;
	}

	if ($this->getSalt() !== $user->getSalt()) {
	    return false;
	}

	if ($this->username !== $user->getUsername()) {
	    return false;
	}

	return true;
    }


    /**
     * Add historys
     *
     * @param \CreativeData\CroissantBundle\Entity\History $historys
     * @return User
     */
    public function addHistory(\CreativeData\CroissantBundle\Entity\History $historys)
    {
        $this->historys[] = $historys;

        return $this;
    }

    /**
     * Remove historys
     *
     * @param \CreativeData\CroissantBundle\Entity\History $historys
     */
    public function removeHistory(\CreativeData\CroissantBundle\Entity\History $historys)
    {
        $this->historys->removeElement($historys);
    }

    /**
     * Get Historys
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistorys()
    {
        return $this->historys;
    }
    
    
    /**
     * Get lastUp
     *
     * @return \DateTime  
     */
    function getLastUp() {
        return $this->lastUp;
    }

    
    /**
     * Set lastUp
     *
     * @param \DateTime  $lastUp
     * @return User
     */
    function setLastUp(\DateTime $lastUp) {
        $this->lastUp = $lastUp;
    }


}
