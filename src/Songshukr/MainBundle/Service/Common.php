<?php
namespace Songshukr\MainBundle\Service;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Rice\MainBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Language;

/**
 * 基础通用类
 * 
 * @author wanghaojie<haojie0429@126.com>
 * @since 2013-11-26
 */
class Common{

	protected $em, $container, $logger;

	public function __construct(EntityManager $em, ContainerInterface $container, LoggerInterface $logger)
	{
		$this->em = $em;
		$this->container = $container;
		$this->logger = $logger;
	}

	public $units = array('元/斤', '元/个');

	public $orderStatus = array('-1'=>'已取消', '0'=>'已下单', '1'=>'已确认', '2'=>'正在配送', '3'=>'已收货');

	/**
	 * 是否管理员登录
	 * 
	 * @author wanghaojie<haojie0429@126.com>
	 * @since 2014-3-8
	 */
	public function adminIsLogin()
	{
		$session = $this->container->get('session');
        $admin = $session->get('admin');
        if($admin) {
            return true;
        } else {
            return false;
        }
	}
    	
}
