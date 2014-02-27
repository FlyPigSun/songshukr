<?php

namespace Songshukr\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 */
class Cart
{
    /**
     * @var integer
     */
    private $cid;

    /**
     * @var integer
     */
    private $uid;

    /**
     * @var string
     */
    private $commodities;

    /**
     * @var \DateTime
     */
    private $ctime;

    /**
     * @var \DateTime
     */
    private $utime;


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
     * Set uid
     *
     * @param integer $uid
     * @return Cart
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set commodities
     *
     * @param string $commodities
     * @return Cart
     */
    public function setCommodities($commodities)
    {
        $this->commodities = $commodities;

        return $this;
    }

    /**
     * Get commodities
     *
     * @return string 
     */
    public function getCommodities()
    {
        return $this->commodities;
    }

    /**
     * Set ctime
     *
     * @param \DateTime $ctime
     * @return Cart
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
     * @return Cart
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
