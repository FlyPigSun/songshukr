<?php
namespace Songshukr\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class ShopController extends Controller
{
    /**
     * 购物车
     *
     * @Route("/cart")
     */
    public function cartAction()
    {
        $request = $this->get('request');
        $cart = $request->request->get('cart');
        $session = $this->get('session');
        if($cart === null) {
            $cart = $session->get('cart');
        } else {
            $session->set('cart', $cart);
        }
        return new Response($session);
    }

    /**
     * 生成订单
     *
     * @Route("/order/create",name="_order_create")
     */
    public function orderCreateAction()
    {
        $session = $this->get('session');
        $uid = $session->get('user_id') ? $session->get('user_id') : 0;
        $cart = json_decode($session->get('sessioin'));
        if(!$cart) {
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $result = $this->get('common.shop')->createOrder($uid, $cart);
        return new Response($result);
    }
}