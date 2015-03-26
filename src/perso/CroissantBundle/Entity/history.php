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
     * @ORM\Column(name="dateCroissant", type="datetime")
     */
    private $dateCroissant;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="ok", type="integer")
     */
    private $ok = 1;


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
     * Set dateCroissant
     *
     * @param \DateTime $dateCroissant
     * @return history
     */
    public function setDateCroissant($dateCroissant)
    {
        $this->dateCroissant = $dateCroissant;

        return $this;
    }

    /**
     * Get dateCroissant
     *
     * @return \DateTime 
     */
    public function getDateCroissant()
    {
        return $this->dateCroissant;
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
     * @param integer $ok
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
     * @return integer 
     */
    public function getOk()
    {
        return $this->ok;
    }
}
