<?php

namespace Songshukr\MainBundle\Service\Commodity;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Songshukr\MainBundle\Service\Common;
use Songshukr\MainBundle\Entity\User;

/**
 * 处理商品相关的函数
 *
 * @author wanghaojie<haojie0429@126.com>
 * @since 2014-2-28
 */
class Commodity extends Common
{
    /**
     * 获取商品列表
     * 
     * @param int $page
     * @param int $limit
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-28
     */
    public function commodityList($page, $limit)
    {
        $offset = ($page - 1) * $limit;
        $query = $this->em->createQuery('
                        SELECT a.cid, a.name, a.description, a.price, a.unit, 
                                a.ctime, a.utime, a.status, a.allowance
                        FROM SongshukrMainBundle:Commodity a'
                    )->setMaxResults($limit)
                    ->setFirstResult($offset);
        try {
            $res = $query->getResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = array();
        }
        $result = array();
        foreach($res as $item) {
            $item['ctime'] = $item['ctime']->format('Y-m-d H-i-m');
            $item['utime'] = $item['utime']->format('Y-m-d H-i-m');
            $cid = $item['cid'];
            $attributes = $this->__getAttributesByCid($cid);
            $item['attributes'] = $attributes;
            $result[] = $item;
        }
        return array('errcode'=>100,'data'=>$result);
    }

    /**
     * 根据cid获取商品属性
     *
     * @param int $cid
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-28
     */
    private function __getAttributesByCid($cid)
    {
        $cas = $this->em->getRepository('SongshukrMainBundle:CommodityAttribute')
                ->findBy('cid'=>$cid);
        $attributes = array();
        foreach($cas as $ca) {
            $attributes[$ca->getName()] = $ca->getValue();
        }
        return $attributes;
    }

    /**
     * 获取单个商品
     *
     * @param int $cid
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-28
     */
    public function getCommodityByCid($cid) 
    {
        $query = $this->em->createQuery('
                        SELECT a.cid, a.name, a.description, a.price, a.unit, 
                                a.ctime, a.utime, a.status, a.allowance
                        FROM SongshukrMainBundle:Commodity a
                        WHERE a.cid=:cid'
                    )->setParameters(array(
                        'cid'=>$cid,
                    ));
        try {
            $item = $query->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            return array('errcode'=>111,'data'=>array());
        }
        $item['ctime'] = $item['ctime']->format('Y-m-d H-i-m');
        $item['utime'] = $item['utime']->format('Y-m-d H-i-m');
        $cid = $item['cid'];
        $attributes = $this->__getAttributesByCid($cid);
        $item['attributes'] = $attributes;
        return array('errcode'=>100,'data'=>$item);
    }

    /**
     * 添加/修改商品(cid为0时为添加)
     * 
     * @param int $cid
     * @param array $config
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-1
     */
    public function setCommodity($cid, $config)
    {
        if($cid == 0) {
            if(!isset($config['name']))
                return array('errcode'=>101, 'data'=>array());
            $c = new \Songshukr\MainBundle\Entity\Commodity();
            $c->setCtime(new \DateTime);
        } else {
            $c = $this->em->getRepository('SongshukrMainBundle:Commodity')->find($cid);
        }
        if(!$c) {
            return array('errcode'=>111, 'data'=>array());
        }
        foreach($config as $key=>$item) {
            switch($key) {
            case 'name':
                $c->setName($item);
                break;
            case 'description':
                $c->setDescription($item);
                break;
            case 'price':
                $c->setPrice($item);
                break;
            case 'unit':
                $c->setUnit($item);
                break;
            case 'status':
                $c->setStatus($item);
                break;
            case 'allowance':
                $c->setAllowance($item);
                break;
            default:
                break;
            }
            $c->setUtime(new \DateTime);
            $this->em->persist($c);
            $this->em->flush();
            return array('errcode=>100', 'data'=>array('cid'=>$c->getCid()));
        }
    }

    /**
     * 删除商品
     * 
     * @param int $cid
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-1
     */
    public function removeCommodity($cid)
    {
        $c = $this->em->getRepository('SongshukrMainBundle:Commodity')->find($cid);
        if(!$c) {
            return array('errcode'=>111, 'data'=>array());
        }
        $this->em->remove();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 添加属性
     * 
     * @param string $name
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-1
     */
    public function attributeAdd($name)
    {
        $a = new \Songshukr\MainBundle\Entity\Attribute();
        $a->setName($name)->setCtime(new \DateTime)->setUtime(new \DateTime);
        $this->em->persist($a);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array('aid'=>$a->getAid()));
    }

    /**
     * 修改属性
     *
     * @param int $aid
     * @param string $name
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-1
     */
    public function attributeEdit($aid, $name)
    {
        $a = $this->em->getRepository('SongshukrMainBundle:Attribute')->find($aid);
        if(!$a) {
            return array('errcode'=>111,'data'=>array());
        }
        $a->setName($name)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }
}