<?php

namespace perso\CroissantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;

//TODO implement userInterface
/**
 * user
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="perso\CroissantBundle\Entity\userRepository")
 */
class user implements UserInterface{

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
     * @ORM\Column(name="premium", type="boolean")
     */
    private $premium;

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
     * @return user
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
     * @return user
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
     * @return user
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
     * @return user
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
     * @return user
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
     * @return user
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
     * @return user
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
     * @return user
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

}
