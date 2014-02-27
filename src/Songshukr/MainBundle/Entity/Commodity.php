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
    private $fid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $price;

    /**
     * @var integer
     */
    private $unit;

    /**
     * @var \DateTime
     */
    private $ctime;

    /**
     * @var \DateTime
     */
    private $utime;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $allowance;


    /**
     * Get fid
     *
     * @return integer 
     */
    public function getFid()
    {
        return $this->fid;
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
     * @param integer $unit
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
     * @return integer 
     */
    public function getUnit()
    {
        return $this->unit;
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
}
