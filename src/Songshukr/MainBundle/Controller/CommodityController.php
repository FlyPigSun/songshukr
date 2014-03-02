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
     * 获取商品列表
     *
     * @Route("/commodity/list",name="_commodity_list")
     */
    public function commodityListAction()
    {
        $request = $this->get('request');
        $page = $request->query->get('page') ? $request->query->get('page') : 0;
        $limit = $request->query->get('limit') ? $request->query->get('limit') : 0;
        $result = $this->get('common.commodity')->commodityList($page, $limit);
        return new Response(json_encode($result));
    }

    /**
     * 获取单个商品
     *
     * @Route("/commodity/get/{cid}",name="_commodity_get")
     */
    public function commodityGetAction($cid)
    {
        $result = $this->get('common.commodity')->getCommodityByCid($cid);
        return new Response(json_encode($result));
    }

    /**
     * 添加/修改商品(cid为0时为添加)
     *
     * @Route("/commodity/set/{cid}",name="_commodity_set")
     */
    public function commoditySetActoin($cid)
    {
        //is admin
        
        $request = $this->get('request');
        $config = array();
        $options = array('name', 'description', 'price', 'unit', 'status', 'allowance');
        foreach($options as $option) {
            if($request->request->get($option) !== null) {
                $config[$option] = urldecode($request->request->get($option));
            }
        }
        $result = $this->get('common.commodity')->setCommodity($cid, $config);
        return new Response(json_encode($result));
    }


    /**
     * 删除商品
     *
     * @Route("/commodity/remove/{cid}",name="_commodity_remove")
     */
    public function commoditySetActoin($cid)
    {
        //is admin
        
        $result = $this->get('common.commodity')->removeCommodity($cid);
        return new Response(json_encode($result));
    }

    /**
     * 添加标签
     *
     * @Route("/label/add/{cid}",name="_label_add")
     */
    public function labelAddAction($cid)
    {
        //is admin
        
        $request = $this->get('request');
        $value = urldecode($request->request->get('value'));
        if(!$value) {
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $result = $this->get('common.commodity')->addLabel($cid, $value);
        return new Response(json_encode($result));
    }

    /**
     * 修改标签
     *
     * @Route("/label/edit/{lid}",name="_label_edit")
     */
    public function labelAddAction($lid)
    {
        //is admin
        
        $request = $this->get('request');
        $value = urldecode($request->request->get('value'));
        if(!$value) {
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $result = $this->get('common.commodity')->editLabel($lid, $value);
        return new Response(json_encode($result));
    }

    /**
     * 删除标签
     *
     * @Route("/label/remove/{lid}",name="_label_remove")
     */
    public function labelRemoveAction($lid)
    {
        //is admin
        
        $result = $this->get('common.commodity')->removeLabel($lid);
        return new Response(json_encode($result));
    }
}