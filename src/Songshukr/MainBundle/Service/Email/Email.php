<?php
namespace Rice\MainBundle\Service\Email;

use Symfony\Component\BrowserKit\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Finder\Comparator\NumberComparator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Rice\MainBundle\Service\Common;

class Email extends Common {
	private $username1 = 'postmaster@colormail.sendcloud.org', $username2 = 'postmaster@colormail2.sendcloud.org';
	private $password1 = 'sEleNLzS', $password2 = 'FNdZ78hC';
	protected $em,$container,$logger,$templateEngine;
	
	public function __construct(EntityManager $em,ContainerInterface $container,$templateEngine, LoggerInterface $logger)
	{
		$this->em = $em;
		$this->container = $container;
		$this->logger = $logger;
		$this->templateEngine = $templateEngine;
	}

	/**
	 * 邮件发送（使用SendCloud服务，http://sendcloud.sohu.com/send_data.do）
	 * $emails是必须是一个数组array(t1@gmail.com, t2@gmail.com)
	 * $type表示走哪个SendCloud账号
	 * 
	 * @author xuyang<skyang2009@gmail.com>
	 * @since 2013-8-13
	 */
	public function sendEmail($emails, $title, $body, $type=0, $tag=0)
	{
		if(empty($emails)) return array('errcode'=>100,'data'=>array());

		require_once __DIR__.'/sendcloud_php/SendCloudLoader.php';//导入SendCloud依赖
		try {
			if(0==$type) {
				if($tag) {
					$sendCloud = new \SendCloud($this->username1.'#'.$tag, $this->password1);
				}
				else {
					$sendCloud = new \SendCloud($this->username1, $this->password1);	
				}
			}
			else {
				if($tag) {
					$sendCloud = new \SendCloud($this->username2.'#'.$tag, $this->password2);
				}
				else {
					$sendCloud = new \SendCloud($this->username2, $this->password2);	
				}
			}
			
			$message = new \SendCloud\Message();
			$message->addRecipients($emails)	//添加批量接受地址
			->setReplyTo('noreply@mail.colorwork.com') // 添加回复地址
			->setFromName('Ricedonate') // 添加发送者称呼
			->setFromAddress('noreply@mail.colorwork.com') // 添加发送者地址
			->setSubject($title)  // 邮件主题
			->setBody($body); // 邮件正文html形式
		
			$sendCloud->send($message);
			
			return array('errcode'=>100,'data'=>array());
		} catch (Exception $e) {
			print $e->getMessage();
			return array('errcode'=>112,'data'=>array());
		}
	}

	
	/**
	 * 重置密码邮件
	 *
	 * @author wanghaojie<haojie0429@126.com>
	 * @since 2013-11-19
	 */
	public function resetPasswordEmail($mailto, $username, $token)
	{
		$request=$this->container->get('request');
		$hl=$this->container->get('session')->get('_locale');
		$request->setLocale($hl);
		
		$title = 'Colorwork重置密码';
		$body = $this->templateEngine->render(
					'ColorEmailBundle:Email:mail_reset_password.html.twig',
					array(
						'userName' => $username,
						'link'=>$this->container->getParameter('home').
						"/account/reset_password?token=$token",
						'time'=>date('Y-m-d'),
				));
		
		return $this->sendEmail(array($mailto), $title, $body);
	}
	
}
