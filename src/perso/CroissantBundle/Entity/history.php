<?php

namespace perso\CroissantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * history
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="perso\CroissantBundle\Entity\historyRepository")
 */
class history
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ok", type="boolean")
     */
    private $ok;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return history
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return history
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set ok
     *
     * @param boolean $ok
     * @return history
     */
    public function setOk($ok)
    {
        $this->ok = $ok;

        return $this;
    }

    /**
     * Get ok
     *
     * @return boolean 
     */
    public function getOk()
    {
        return $this->ok;
    }
}
