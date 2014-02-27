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
     * @Route("/", name="_index")
     */
    public function indexAction()
    {
        return $this->render('SongshukrMainBundle:index.html.twig');
    }
}
