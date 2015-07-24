<?php

namespace CreativeData\CroissantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * history
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CreativeData\CroissantBundle\Entity\HistoryRepository")
 */
class History
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="historys")
     */
    private $user;
    
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
     * @return History
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
     * Set ok
     *
     * @param integer $ok
     * @return History
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

    /**
     * Set User
     *
     * @param \CreativeData\CroissantBundle\Entity\User $user
     * @return History
     */
    public function setUser(\CreativeData\CroissantBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get User
     *
     * @return \CreativeData\CroissantBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
