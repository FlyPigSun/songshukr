<?php

namespace Songshukr\MainBundle\Service\Shop;

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
 * 处理购物相关的函数
 *
 * @author wanghaojie<haojie0429@126.com>
 * @since 2014-3-2
 */
class Shop extends Common
{
	/**
	 * 创建订单
	 * 
	 * @param int $uid
	 * @param array $cart
	 * @author wanghaojie<haojie0429@126.com>
	 * @since 2014-3-2
	 */
    public function createOrder($uid, $cart, $name, $cellphone, $address)
    {
    	$orderNo = md5(time().rand(0,1000));
        $o = new \Songshukr\MainBundle\Entity\Orders();
        $o->setUid($uid)
            ->setOrderNo($orderNo)
            ->setStatus(0)
            ->setName($name)
            ->setCellphone($cellphone)
            ->setAddress($address)
            ->setCtime(new \DateTime)
            ->setUtime(new \DateTime);
        $this->em->persist($o);
        $this->em->flush();
    	foreach($cart as $cid=>$number) {
    		$c = $this->em->getRepository('SongshukrMainBundle:Commodity')->find($cid);
    		if(!$c) continue;
    		$price = $c->getPrice();
            $name = $c->getName();
    		$oc = new \Songshukr\MainBundle\Entity\OrderCommodity();
    		$oc->setOrderNo($orderNo)
                ->setCid($cid)
                ->setName($name)
    			->setNumber($number)
    			->setPrice($price)
    			->setCtime(new \DateTime)
    			->setUtime(new \DateTime);
    		$this->em->persist($oc);
            $this->em->flush();
    	}

        $u = $this->em->getRepository('SongshukrMainBundle:User')->find($uid);
        if(!$u) {
            return array('errcode'=>113,'data'=>array());
        }
        $u->setAddress($address)->setUsername($username);
        $this->em->flush();

    	return array('errcode'=>100, 'data'=>array('orderNo'=>$orderNo));
    }

    /**
     * 确认订单
     * 
     * @param string $orderNo
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-4
     */
    public function comfirmOrder($orderNo)
    {
        $o = $this->em->getRepository('SongshukrMainBundle:Orders')->findOneBy(array('orderNo'=>$orderNo));
        if(!$o) {
            return array('errcode'=>111, 'data'=>array());
        }
        $o->setStatus(1)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100, 'data'=>array());
    }

    /**
     * 订单配送中
     * 
     * @param string $orderNo
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-4
     */
    public function sendingOrder($orderNo)
    {
        $o = $this->em->getRepository('SongshukrMainBundle:Orders')->findOneBy(array('orderNo'=>$orderNo));
        if(!$o) {
            return array('errcode'=>111, 'data'=>array());
        }
        $o->setStatus(2)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100, 'data'=>array());
    }

    /**
     * 结束订单
     * 
     * @param int $uid
     * @param string $orderNo
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-4
     */
    public function finishOrder($uid, $orderNo)
    {
        $o = $this->em->getRepository('SongshukrMainBundle:Orders')->findOneBy(array('orderNo'=>$orderNo, 'uid'=>$uid));
        if(!$o) {
            return array('errcode'=>111, 'data'=>array());
        }
        $o->setStatus(3)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100, 'data'=>array());
    }

    /**
     * 取消订单
     * 
     * @param int $uid
     * @param string $orderNo
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-4
     */
    public function cancelOrder($uid, $orderNo)
    {
        $os = $this->em->getRepository('SongshukrMainBundle:Orders')->findBy(array('orderNo'=>$orderNo, 'uid'=>$uid));
        if(!$o) {
            return array('errcode'=>111, 'data'=>array());
        }
        $o->setStatus(-1)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100, 'data'=>array());
    }

    /**
     * 获取订单列表
     * 
     * @param array $status
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-4
     */
    public function orderList($status)
    {
        $os = $this->em->getRepository('SongshukrMainBundle:Orders')->findBy(array('status'=>$status));
        $result = array();
        foreach($os as $o) {
            $ocs = $this->em->getRepository('SongshukrMainBundle:OrderCommodity')->findBy(array('orderNo'=>$o->getOrderNo()));
            $items = array();
            foreach($ocs as $oc) {
                $items[] = array(
                        'cid'=>$oc->getCid(),
                        'name'=>$oc->getName(),
                        'number'=>$oc->getNumber(),
                        'price'=>$oc->getPrice(),
                        'ctime'=>$oc->getCtime()->format('Y-m-d H:i:s'),
                    );
            }
            $result[] = array(
                    'orderNo'=>$o->getOrderNo(),
                    'ctime'=>$o->getCtime()->format('Y-m-d H:i:s'),
                    'status'=>$o->getStatus(),
                    'commodities'=>$items,
                );
        }
        return array('errcode'=>100, 'data'=>$result);
    }

    /**
     * 获取用户历史订单
     * 
     * @param int $uid
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-3-9
     */
    public function getOrderListByUid($uid)
    {
        $os = $this->em->getRepository('SongshukrMainBundle:Orders')->findBy(array('uid'=>$uid));
        $result = array();
        foreach($os as $o) {
            $ocs = $this->em->getRepository('SongshukrMainBundle:OrderCommodity')->findBy(array('orderNo'=>$o->getOrderNo()));
            $items = array();
            foreach($ocs as $oc) {
                $items[] = array(
                        'cid'=>$oc->getCid(),
                        'name'=>$oc->getName(),
                        'number'=>$oc->getNumber(),
                        'price'=>$oc->getPrice(),
                        'ctime'=>$oc->getCtime()->format('Y-m-d H:i:s'),
                    );
            }
            $result[] = array(
                    'orderNo'=>$o->getOrderNo(),
                    'ctime'=>$o->getCtime()->format('Y-m-d H:i:s'),
                    'status'=>$o->getStatus(),
                    'commodities'=>$items,
                );
        }
        return array('errcode'=>100, 'data'=>$result);
    }
}