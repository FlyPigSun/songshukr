<?php

namespace Songshukr\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commodity
 */
class Commodity
{
    /**
     * @var integer
     */
    private $cid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var float
     */
    private $oprice;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $unit;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $allowance;

    /**
     * @var \DateTime
     */
    private $utime;

    /**
     * @var \DateTime
     */
    private $ctime;


    /**
     * Get cid
     *
     * @return integer 
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Commodity
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
     * Set description
     *
     * @param string $description
     * @return Commodity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Commodity
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set oprice
     *
     * @param float $oprice
     * @return Commodity
     */
    public function setOprice($oprice)
    {
        $this->oprice = $oprice;

        return $this;
    }

    /**
     * Get oprice
     *
     * @return float 
     */
    public function getOprice()
    {
        return $this->oprice;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Commodity
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return Commodity
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Commodity
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set allowance
     *
     * @param integer $allowance
     * @return Commodity
     */
    public function setAllowance($allowance)
    {
        $this->allowance = $allowance;

        return $this;
    }

    /**
     * Get allowance
     *
     * @return integer 
     */
    public function getAllowance()
    {
        return $this->allowance;
    }

    /**
     * Set utime
     *
     * @param \DateTime $utime
     * @return Commodity
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

    /**
     * Set ctime
     *
     * @param \DateTime $ctime
     * @return Commodity
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
}
