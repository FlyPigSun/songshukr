<?php

namespace Songshukr\MainBundle\Service\Account;

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
use Songshukr\MainBundle\Entity\User;

/**
 * 处理帐户相关的函数
 *
 * @author wanghaojie<haojie0429@126.com>
 * @since 2013-11-18
 */
class Account extends Common
{

    /**
     * 根据邮箱获取uid
     *
     * @param string $email
     * @return boolean
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function getUidByEmail($email)
    {
        if($email == '')return 0;
        $repository = $this->em->getRepository('SongshukrMainBundle:User');
        $user = $repository->findOneBy(array('email'=>$email));
        return ($user != null) ? $user->getUid() : 0;
    }

    /**
     * 判断邮箱是否注册
     *
     * @param string $email
     * @return boolean
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function isRegisterEmail($email)
    {
        return ($this->getUidByEmail($email) != 0);
    }

    /**
     * 根据手机号获取uid
     *
     * @param string $cellphone
     * @return boolean
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function getUidByCellphone($cellphone)
    {
        if($cellphone == '')return 0;
        $repository = $this->em->getRepository('SongshukrMainBundle:User');
        $user = $repository->findOneBy(array('cellphone'=>$cellphone));
        return ($user != null) ? $user->getUid() : 0;
    }

    /**
     * 判断手机号是否被绑定
     *
     * @param string $cellphone
     * @return boolean
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function isRegisterCellphone($cellphone)
    {
        return ($this->getUidByCellphone($cellphone) != 0);
    }

    /**
     * 绑定邮箱
     *
     * @param int $uid
     * @param string $email
     * @return 
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function bindEmail($uid,$email)
    {
        $user = $this->em->getRepository('SongshukrMainBundle:User')->find($uid);
        if(!$user)
            return array('errcode'=>111,'data'=>array());
        $user->setEmail($email);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 绑定手机号
     *
     * @param int $uid
     * @param string $cellphone
     * @return 
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function bindCellphone($uid,$cellphone)
    {
        $user = $this->em->getRepository('SongshukrMainBundle:User')->find($uid);
        if(!$user)
            return array('errcode'=>111,'data'=>array());
        $user->setCellphone($cellphone);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 检查uid与密码是否对应
     * 
     * @param int $uid
     * @param string $password
     * @return boolean
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25  
     */
    public function checkPassword($uid, $password)
    {
        $user = $this->em->getRepository('SongshukrMainBundle:User')
                ->findOneBy(array('uid' => $uid, 'password' => sha1($password)));
        if($user){
            $this->setLogin($user);
            return true;
        } 
        return false;
    }   

    /**
     * 设置用户登录的session
     * 
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function setLogin($user)
    {
        // $avatar = $this->formatAvatarLink($user->getAvatar());
        $this->container->get('session')->set('user_id',$user->getUid());
        $this->container->get('session')->set('email',$user->getEmail());
        $this->container->get('session')->set('username',$user->getUserName());
        // $this->container->get('session')->set('avatar',$avatar);
        $this->container->get('session')->set('cellphone',$user->getCellphone());
    }   

    /**
     * 注册
     *
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function register($email,$password,$username, $cellphone, $qq)
    {
        if($this->isRegisterEmail($email)){
            return array('errcode'=>110,'data'=>array());
        }

        $newUser = new \Songshukr\MainBundle\Entity\User();
        $newUser->setUsername($username)
            ->setPassword(sha1($password))
            ->setEmail($email)
            ->setCellphone($cellphone)
            ->setQq($qq)
            ->setCtime(new \DateTime)
            ->setUtime(new \DateTime);

        $this->em->persist($newUser);
        $this->em->flush();
        return array('errcode' => 100,'data' => array('uid' => $newUser->getUid()));
    }

    /**
     * 发送重置密码邮件
     *
     * @param string $email
     * @return array ('errcode', 'data')
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function sendResetPasswordEmail($email){
        $repository = $this->em->getRepository('SongshukrCommonBundle:User');
        $user = $repository->findOneBy(array('email'=>$email));
        $userName = $user->getUsername();
        $uid = $user->getUid();
        
        $token = md5(rand(0,10000).time());
        $expire = new \DateTime(date('Y-m-d H:i:s', time()+24*3600));  //链接24小时内有效
        $newToken = new \Songshukr\MainBundle\Entity\ResetPassword();
        $newToken->setToken($token)->setUid($uid)->setExpire($expire)->setUsername($userName)->setIsactive(0);
        $this->em->persist($newToken);
        $this->em->flush();

        $this->container->get('email.email')->resetPasswordEmail($email, $userName, $token);
        return Array(
                'errcode'=>100, 
                'data'=>array(),
            );
    }

    /**
     * 重置密码页面，根据Token获得密码重置信息
     *
     * @param string $token
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function getResetPasswordTokenInfo($token){
        $repository = $this->em->getRepository('ColorCommonBundle:ResetPassword');
        $ret = $repository->findOneBy(array('token'=>$token));
        if($ret){
            if(strtotime($ret->getExpire()->format('Y-m-d H:i:s')) > time()){
                return array(
                        'errcode'=>100,
                        'data'=>array(
                            'token' => $token,
                            'userName' => $ret->getUsername(),
                            'uid' => $ret->getUid(),
                            'expire' => $ret->getExpire()->format('Y-m-d H:i:s'),
                            'isActive' => $ret->getIsactive(),
                    ));
            }else{
                return array('errcode'=>150,'data'=>array());
            }
        }else{
            return array('errcode'=>150,'data'=>array());
        }
    }

    /**
     * 重置密码函数
     * 
     * @param string $token
     * @param string $newpasswd
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function resetPassword($token, $newpasswd){
        $tokenInfo = $this->getResetPasswordTokenInfo($token);
        if ($tokenInfo['errcode'] != 100)
            return $tokenInfo;
        $qryUser = $this->em->getRepository('SongshukrCommonBundle:User')->findOneBy(Array('uid'=>$tokenInfo['data']['uid']));
        $qryToken = $this->em->getRepository('SongshukrCommonBundle:ResetPassword')->findOneBy(Array('token'=>$token));
        if ($qryUser == null || $qryToken == null)
            return array('errcode'=>111,'data'=>array());
        $qryUser->setPassword(sha1($newpasswd));
        $qryToken->setIsactive('1');
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 设置用户名
     * 
     * @param int $uid
     * @param string $username
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function setUsername($uid, $username)
    {
        $u = $this->em->getRepository('SongshukrMainBundle:User')->find($uid);
        if(!$u) {
            return array('errcode'=>113,'data'=>array());
        }
        $u->setUsername($username)->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 通过邮箱获取管理员id
     * @param string $email
     * @return int
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function getAdminidByEmail($email)
    {
        if($email == '')return 0;
        $repository = $this->em->getRepository('SongshukrMainBundle:Admin');
        $admin = $repository->findOneBy(array('email'=>$email));
        return ($admin != null) ? $admin->getAdminid() : 0;
    }

    /**
     * 管理员登录密码检查
     * 
     * @param int $adminid
     * @param string $passwor
     * @return bool
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function checkAdminPassword($adminid, $password)
    {
        $admin = $this->em->getRepository('SongshukrMainBundle:Admin')
                ->findOneBy(array('adminid' => $adminid, 'password' => sha1($password)));
        if($admin){
            $this->setAdminLogin($admin);
            return true;
        } 
        return false;
    }

    /**
     * 设置管理员登录的session
     *
     * @param repository $admin
     * @return null
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function setAdminLogin($admin)
    {
        $this->container->get('session')->set('admin_id',$admin->getAdminid());
        $this->container->get('session')->set('admin_email',$admin->getEmail());
        $this->container->get('session')->set('admin_name',$admin->getAdminname());
        $this->container->get('session')->set('admin_cellphone',$admin->getCellphone());
    }

    /**
     * 根据键值对获取用户(管理员权限)
     * 
     * @param int $adminid
     * @param array $item
     * @return
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function searchUser($adminid, $item)
    {
        $admin = $this->em->getRepository('SongshukrMainBundle:Admin')
                ->find($adminid);
        if(!$admin){
            return array('errcode'=>103,'data'=>array());
        } 
        $u = $this->em->getRepository('SongshukrMainBundle:User')
                ->findOneBy($item);
        if($u) {
            $user = array(
                    'uid'=>$u->getUid(),
                    'username'=>$u->getUsername(),
                    'email'=>$u->getEmail(),
                    'cellphone'=>$u->getCellphone(),
                    'qq'=>$u->getQq(),
                    'avatar'=>$u->getAvatar(),
                    'description'=>$u->getDescription(),
                    'utime'=>$u->getUtime()->format('Y-m-d H:i:s'),
                    'ctime'=>$u->getCtime()->format('Y-m-d H:i:s'),
                    'wechat_id'=>$u->getWechatId(),
                );
            return array('errcode'=>100,'data'=>array('user'=>$user));
        } else {
            return array('errcode'=>113,'data'=>array());
        }
    }

    /**
     * 获取用户列表(管理员权限)
     * 
     * @param int $adminid 
     * @param int $page
     * @param int $limit 
     * @return
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function getUserList($adminid, $page, $limit)
    {
        $admin = $this->em->getRepository('SongshukrMainBundle:Admin')
                ->find($adminid);
        if(!$admin){
            return array('errcode'=>103,'data'=>array());
        } 
        $users = array();
        $offset = $limit * ($page-1);
        $query = $this->em
                    ->createQuery('
                            SELECT a.uid, a.username, a.email, a.cellphone, a.avatar, a.qq,
                                a.description, a.ctime, a.utime, a.wechat_id
                            FROM SongshukrMainBundle:User a
                            ORDER BY a.uid DESC'
                    )->setMaxResults($limit)
                    ->setFirstResult($offset);
        try {
            $result = $query->getResult();
            foreach($result as $item) {
                $item['ctime'] = $item['ctime']->format('Y-m-d H:i:s');
                $item['utime'] = $item['utime']->format('Y-m-d H:i:s');
                $users[] = $item;
            }
        } catch (\Doctrine\Orm\NoResultException $e) {
            
        }
        return array('errcode'=>100,'data'=>array('users'=>$users));
    }

    /**
     * 帐户设置
     *
     * @param int $uid
     * @param array $config
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function accountSet($uid, $config)
    {
        $u = $this->em->getRepository('SongshukrMainBundle:User')->find($uid);
        if(!$u) {
            return array('errcode'=>113,'data'=>array());
        }
        foreach($config as $key=>$item) {
            switch($key) {
            case 'cellphone':
                $u->setCellphone($item);
                break;
            case 'address':
                $u->setWebsite($item);
                break;
            case 'username';
                $u->setUsername($item);
                break;
            default:
                break;
            }
        }
        $u->setUtime(new \DateTime);
        $this->em->flush();
        return array('errcode'=>100,'data'=>array());
    }

    /**
     * 获取帐户信息
     * 
     * @param int $uid
     * @author wanghaojie<haojie0429@126.com>
     * @since 2014-2-25
     */
    public function accountGet($uid)
    {
        $u = $this->em->getRepository('SongshukrMainBundle:User')->find($uid);
        if(!$u) {
            return array('errcode'=>113,'data'=>array());
        }
        $account = array();
        $account['weibo_name'] = $u->getWeiboName();
        $account['weibo_url'] = $u->getWeiboUrl();
        $account['description'] = $u->getDescription();
        $account['qq'] = $u->getQq();
        $account['cellphone'] = $u->getCellphone();
        $account['website'] = $u->getWebsite();
        $account['username'] = $u->getUsername();
        return array('errcode'=>100,'data'=>array('account'=>$account));
    }
}