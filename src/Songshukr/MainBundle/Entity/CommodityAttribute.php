<?php

namespace Songshukr\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommodityAttribute
 */
class CommodityAttribute
{
    /**
     * @var integer
     */
    private $caid;

    /**
     * @var integer
     */
    private $cid;

    /**
     * @var integer
     */
    private $aid;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \DateTime
     */
    private $ctime;

    /**
     * @var \DateTime
     */
    private $utime;


    /**
     * Get caid
     *
     * @return integer 
     */
    public function getCaid()
    {
        return $this->caid;
    }

    /**
     * Set cid
     *
     * @param integer $cid
     * @return CommodityAttribute
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
     * Set aid
     *
     * @param integer $aid
     * @return CommodityAttribute
     */
    public function setAid($aid)
    {
        $this->aid = $aid;

        return $this;
    }

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
     * Set value
     *
     * @param string $value
     * @return CommodityAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set ctime
     *
     * @param \DateTime $ctime
     * @return CommodityAttribute
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
     * @return CommodityAttribute
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
