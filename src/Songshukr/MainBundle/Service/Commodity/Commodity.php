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
                        SELECT a.cid, a.name, a.description, a.oprice, a.price, a.unit, 
                                a.ctime, a.utime, a.status, a.allowance, a.logo
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
        $item['unit'] = $this->units[$item['unit']];
            $labels = $this->__getLabelsByCid($cid);
            $item['labels'] = $labels;
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
    private function __getLabelsByCid($cid)
    {
        $ls = $this->em->getRepository('SongshukrMainBundle:Label')
                ->findBy(array('cid'=>$cid));
        $labels = array();
        foreach($ls as $l) {
            $labels[] = $l->getValue();
        }
        return $labels;
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
                        SELECT a.cid, a.name, a.description, a.oprice, a.price, a.unit, 
                                a.ctime, a.utime, a.status, a.allowance, a.logo
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
        $item['unit'] = $this->units[$item['unit']];
        $cid = $item['cid'];
        $labels = $this->__getLabelsByCid($cid);
        $item['labels'] = $labels;
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
            case 'oprice':
                $c->setOprice($item);
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
            case 'logo':
                $c->setLogo($item);
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
        $this->em->remove($c);
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 添加标签
     * 
     * @param string $value
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-1
     */
    public function addLabel($cid, $value)
    {
        $l = new \Songshukr\MainBundle\Entity\Label();
        $l->setCid($cid)->setValue($value)->setCtime(new \DateTime)->setUtime(new \DateTime);
        $this->em->persist($l);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array('lid'=>$l->getLid()));
    }

    /**
     * 修改标签
     *
     * @param int $lid
     * @param string $value
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-1
     */
    public function editLabel($lid, $value)
    {
        $l = $this->em->getRepository('SongshukrMainBundle:Label')->find($lid);
        if(!$l) {
            return array('errcode'=>111,'data'=>array());
        }
        $l->setValue($value)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 删除标签
     *
     * @param int $lid
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-2
     */
    public function removeLabel($lid)
    {

        $l = $this->em->getRepository('SongshukrMainBundle:Label')->find($lid);
        if(!$l) {
            return array('errcode'=>111,'data'=>array());
        }
        $this->em->remove($l);
        return array('errcode'=>100,'data'=>array());
    }
}