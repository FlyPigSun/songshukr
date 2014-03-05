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
}
