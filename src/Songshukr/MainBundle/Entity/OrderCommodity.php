<?php

namespace Songshukr\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderCommodity
 */
class OrderCommodity
{
    /**
     * @var integer
     */
    private $ocid;

    /**
     * @var string
     */
    private $orderNo;

    /**
     * @var integer
     */
    private $cid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $number;

    /**
     * @var float
     */
    private $price;

    /**
     * @var \DateTime
     */
    private $ctime;

    /**
     * @var \DateTime
     */
    private $utime;


    /**
     * Get ocid
     *
     * @return integer 
     */
    public function getOcid()
    {
        return $this->ocid;
    }

    /**
     * Set orderNo
     *
     * @param string $orderNo
     * @return OrderCommodity
     */
    public function setOrderNo($orderNo)
    {
        $this->orderNo = $orderNo;

        return $this;
    }

    /**
     * Get orderNo
     *
     * @return string 
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * Set cid
     *
     * @param integer $cid
     * @return OrderCommodity
     */
    public function setCid($cid)
    {
        $this->cid = $cid;

        return $this;
    }

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
     * @return OrderCommodity
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
     * Set number
     *
     * @param integer $number
     * @return OrderCommodity
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return OrderCommodity
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
     * Set ctime
     *
     * @param \DateTime $ctime
     * @return OrderCommodity
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
     * @return OrderCommodity
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
