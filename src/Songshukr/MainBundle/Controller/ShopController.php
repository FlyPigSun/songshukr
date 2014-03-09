<?php
namespace Songshukr\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use \stdClass;

class ShopController extends Controller
{
    /**
     * 购物车
     *
     * @Route("/cart/set",name="_cart_set")
     */
    public function cartSetAction()
    {
        $request = $this->get('request');
        $cid = $request->request->get('cid');
        $number = $request->request->get('number');
        $session = $this->get('session');
        $cart = json_decode($session->get('cart'));
        if(!$cart) {
            $cart = new stdClass();
        }
        if(is_numeric($cid) && is_numeric($number) && $cid > 0) {
            if($number > 0) {
                $cart->$cid = $number;
            } else {
                unset($cart->$cid);
            }
        }
        $session->set('cart', json_encode($cart));
        return new Response(json_encode($cart));
    }

    /**
     * 获取购物车
     * 
     * @Route("/cart/list",name="_cart_list")
     */
    public function cartListAction()
    {
        $session = $this->get('session');
        $cart = json_decode($session->get('cart'));
        $result = array();
        foreach($cart as $cid=>$number) {
            $res = $this->get('common.commodity')->getCommodityByCid($cid);
            if($res['errcode'] != 100) continue;
            $commodity = $res['data'];
            $commodity['number'] = $number;
            $result[] = $commodity;
        }
        return new Response(json_encode(array('errcode'=>100, 'data'=>$result)));
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
        if($uid == 0) return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        $cart = json_decode($session->get('sessioin'));
        if(!$cart) {
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $result = $this->get('common.shop')->createOrder($uid, $cart);
        return new Response(json_encode($result));
    }

    /**
     * 订单确认
     * 
     * @Route("/order/confirm",name="_order_confirm")
     */
    public function orderConfirmAction()
    {
        if(!$this->get('common.common')->adminIsLogin()) {
            return new Response(json_encode(array('errcode'=>103, data=>array())));
        }

        $request = $this->get('request');
        $orderNo = $request->request->get('orderNo');
        if(!$orderNo) return new Response(json_encode(array('errcode'=>101, 'data'=>array())));
        $reuslt = $this->get('common.shop')->comfirmOrder($orderNo);
        return new Response(json_encode($result));
    }

    /**
     * 订单配送中
     * 
     * @Route("/order/sending",name="_order_sending")
     */
    public function orderSendingAction()
    {
        if(!$this->get('common.common')->adminIsLogin()) {
            return new Response(json_encode(array('errcode'=>103, data=>array())));
        }

        $request = $this->get('request');
        $orderNo = $request->request->get('orderNo');
        if(!$orderNo) return new Response(json_encode(array('errcode'=>101, 'data'=>array())));
        $reuslt = $this->get('common.shop')->sendingOrder($orderNo);
        return new Response(json_encode($result));
    }

    /**
     * 订单完成
     * 
     * @Route("/order/finish",name="_order_finish")
     */
    public function orderFinishAction()
    {
        $session = $this->get('session');
        $uid = $session->get('user_id') ? $session->get('user_id') : 0;
        if($uid == 0) return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        $request = $this->get('request');
        $orderNo = $request->request->get('orderNo');
        if(!$orderNo) return new Response(json_encode(array('errcode'=>101, 'data'=>array())));
        $result = $this->get('common.shop')->finishOrder($uid, $orderNo);
        return new Response(json_encode($result));
    }

    /**
     * 订单取消
     * 
     * @Route("/order/cancel",name="_order_cancel")
     */
    public function orderCancelAction()
    {
        $session = $this->get('session');
        $uid = $session->get('user_id') ? $session->get('user_id') : 0;
        if($uid == 0) return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        $request = $this->get('request');
        $orderNo = $request->request->get('orderNo');
        if(!$orderNo) return new Response(json_encode(array('errcode'=>101, 'data'=>array())));
        $result = $this->get('common.shop')->cancelOrder($uid, $orderNo);
        return new Response(json_encode($result));
    }

    /**
     * 获取订单列表
     * 
     * @method array $_POST['status']
     * @Route("/order/list",name="_order_list")
     */
    public function orderListAction()
    {
        if(!$this->get('common.common')->adminIsLogin()) {
            return new Response(json_encode(array('errcode'=>103, data=>array())));
        }

        $request = $this->get('request');
        $status = json_decode($request->request->get('status'));
        if(!$status) return new Response(json_encode(array('errcode'=>101, 'data'=>array())));
        $result = $this->get('common.shop')->orderList($status);
        return new Response(json_encode($result));
    }
}