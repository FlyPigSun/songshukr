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
    public function createOrder($uid, $cart)
    {
    	$orderNo = md5(time().rand(0,1000));
    	foreach($cart as $cid=>$number) {
    		$c = $this->getRepository('SongshukrMainBundle:Commodity')->find($cid);
    		if(!$c) continue;
    		$price = $c->getPrice();
    		$o = new \Songshukr\MainBundle\Entity\Order();
    		$o->setOrderNo($orderNo)
    			->setCit($cid)
    			->setNumber($number)
    			->setPrice($price)
    			->setStatus(0)
    			->setCtime(new \DateTime)
    			->setUtime(new \DateTime);
    		$this->em->persist($o);
    	}
    	return array('errcode'=>100, 'data'=>array('orderNo'=>$orderNo));
    }
}