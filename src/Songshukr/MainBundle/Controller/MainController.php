<?php
namespace Songshukr\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class MainController extends Controller
{
	/**
     * 首页
     *
     * @Route("/", name="_")
     * @Route("/index", name="_index")
     */
    public function indexAction()
    {
        return $this->render('SongshukrMainBundle::index.html.twig');
    }

    /**
     * 登录页面
     *
     * @Route("/login", name="_login")
     */
    public function loginAction()
    {
        return $this->render('SongshukrMainBundle:account:login.html.twig');
    }

    /**
     * 注册页面
     *
     * @Route("/register", name="_register")
     */
    public function registerAction()
    {
        return $this->render('SongshukrMainBundle:account:register.html.twig');
    }

    /**
     * 购物车页面
     *
     * @Route("/cart", name="_cart")
     */
    public function cartAction()
    {
        return $this->render('SongshukrMainBundle:shop:cart.html.twig');
    }

    /**
     * 商品詳情頁面
     * 
     * @Route("/commodity/detail/{cid}", name="_commodity_detail")
     */
    public function commodityDetailAction($cid)
    {
        $result = $this->get('common.commodity')->getCommodityByCid($cid);
        if($result['errcode'] == 100) {
            $commodity = $result['data'];
            return $this->render('SongshukrMainBundle:shop:detail.html.twig',$commodity);
        } else {
            return $this->render('SongshukrMainBundle::index.html.twig');
        }
    }

    /**
     * 订单詳情頁面
     * 
     * @Route("/order", name="_order")
     */
    public function orderAction()
    {
        $session = $this->get('session');
        $uid = $session->get('user_id');
        if(!$uid) {
            return $this->redirect('/login?url=_order');
        }
        $cart = json_decode($session->get('cart'));
        if(count((array)$cart) == 0) {
            return $this->redirect('/index');
        }
        $result = $this->get('common.account')->getUserInfo($uid);
        if($result['errcode'] == 100) $userInfo = $result['data'];
        else $userInfo = array();
        return $this->render('SongshukrMainBundle:shop:order.html.twig', $userInfo);
    }
}
