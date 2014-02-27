<?php

namespace Songshukr\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute
 */
class Attribute
{
    /**
     * @var integer
     */
    private $aid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $ctime;

    /**
     * @var \DateTime
     */
    private $utime;


    /**
     * Get aid
     *
     * @return integer 
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ctime
     *
     * @param \DateTime $ctime
     * @return Attribute
     */
    public function setCtime($ctime)
    {
        $this->ctime = $ctime;

        return $this;
    }

    /**
     * Get ctime
     *
     * @return \DateTime 
     */
    public function getCtime()
    {
        return $this->ctime;
    }

    /**
     * Set utime
     *
     * @param \DateTime $utime
     * @return Attribute
     */
    public function setUtime($utime)
    {
        $this->utime = $utime;

        return $this;
    }

    /**
     * Get utime
     *
     * @return \DateTime 
     */
    public function getUtime()
    {
        return $this->utime;
    }
}
