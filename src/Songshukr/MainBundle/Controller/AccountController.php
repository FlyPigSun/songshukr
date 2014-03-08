<?php
namespace Songshukr\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

class AccountController extends Controller
{
    /**
     * login頁面
     *
     * @Route("/account/login", name="_account_login")
     */
    public function accountLoginAction()
    {
        return $this->render('SongshukrMainBundle:account:login.html.twig');
    }

	/**
     * 登录验证
     * 
     * @method string $_POST['email']
     * @method string $_POST['password']
     * @method string $_POST['isAutoLogin']
     * @Route("/account/login/submit", name="_account_login_submit")
     */
    public function accountLoginSubmitAction()
    {
        $request = $this->get('request');
        $cellphone = urldecode($request->request->get('cellphone'));
        $password = urldecode($request->request->get('password'));
        $uid = $this->get('common.account')->getUidByCellphone($cellphone);
        if (0 == $uid) {
            return new Response(json_encode(array('errcode'=>113,'data'=>array())));
        }

        //判断用户名密码是否合法
        if ($this->get('common.account')->checkPassword($uid, $password)) {
            $data = array(
                'token'=> session_id(),
                'uid' => $this->get('session')->get('user_id'),
                'username' => $this->get('session')->get('username'),            
                'email' => $this->get('session')->get('email'),
                'avatar'=> $this->get('session')->get('avatar'),
                'cellphone' => $this->get('session')->get('cellphone'),
            );

            $response = new Response();
            if ('1' == $request->request->get('isAutoLogin')){
                $response->headers->setCookie(new Cookie('session_id',$this->get('session')->getId(),time()+ 13140000,'/',NULL,0));              
            }
            $response->headers->setCookie(new Cookie('username',$this->get('session')->get('username'),time()+ 13140000,'/',NULL,0,0));
            $response->setContent(json_encode(array('errcode'=>100,'data'=>$data)));
            return $response;
        }
        else{
            return new Response(json_encode(array('errcode'=>103,'data'=>array())));
        }
    }

    /**
     * 判断账号是否登录
     * 
     * @Route("/account/is_login", name="_account_is_login")
     */
    public function accountIsLoginAction()
    {
        $uid= $this->get('session')->get('user_id');
        if(!$uid) return new Response(json_encode(array('errcode'=>103, 'data'=>array()))); 
        else {
            return new Response(json_encode(array('errcode'=>100, 'data'=>array('uid'=>$uid))));
        }
    }

    /**
     * 登出
     * 
     * @Route("/account/logout", name="_account_logout")
     */
    public function accountLogoutAction()
    {
        $session = $this->get('session');
        $session->clear();
        $response=new Response();
        $response->headers->setCookie(new Cookie('session_id','',0,'/',NULL,0));
        $response->headers->setCookie(new Cookie('user_name','',0,'/',NULL,0));
        $response->setContent(json_encode(array('errcode'=>100,'data'=>'')));
        return $response;
    }

	/**
	 * 注册
	 * 
     * @method string $__POST['cellphone']
     * @method string $_POST['password']
     * @method string $__POST['username'] || null
     * @method string $_POST['email'] || null
	 * @Route("/account/register/submit",name="_account_register_submit")
	 */
    public function accountRegisterSubmitAction()
    {
        $request = $this->get('request');
        $email = urldecode($request->request->get('email'));
        $password = urldecode($request->request->get('password'));
        $username = urldecode($request->request->get('username'));
        $cellphone = urldecode($request->request->get('cellphone'));

        $result = $this->get('common.account')->register($cellphone, $password, $username, $email);

        if(100 == $result['errcode']) {
            $uid = $result['data']['uid'];
            $this->get('common.account')->checkPassword($uid, $password);
            $data = array(
                'token'=> session_id(),
                'uid' => $this->get('session')->get('user_id'),
                'username' => $this->get('session')->get('username'),            
                'email' => $this->get('session')->get('email'),
                'avatar'=> $this->get('session')->get('avatar'),
                'cellphone' => $this->get('session')->get('cellphone'),
            );

            $response = new Response();
            $response->headers->setCookie(new Cookie('session_id',$this->get('session')->getId(),time()+ 13140000,'/',NULL,0));        
            $response->headers->setCookie(new Cookie('username',$this->get('session')->get('username'),time()+ 13140000,'/',NULL,0,0));
            $response->setContent(json_encode(array('errcode'=>100,'data'=>$data)));
            return $response;
        } else{
            return new Response(json_encode($result));
        }
    }

    /**
     * 判断邮箱是否被绑定
     * 
     * @method string $_POST['email']
     * @return array(int,boolean) 此处不能直接返回uid,否则前台就可以直接从用户邮箱来得到用户id。若要得到某个邮箱对应的用户id，必须在登录状态下
     * @Route("/account/is_register/email",name="_account_is_register_email")
     */
    public function accountIsRegisterEmailActioin()
    {
        $request = $this->get('request');
        $email = urldecode($request->request->get('email'));
        if('' == $email){
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $isRegister = $this->get('common.account')->isRegisterEmail($email);
        return new Response(json_encode(array('errcode'=>100,'data'=>array('is_register'=>$isRegister))));
    }

    /**
     * 判断手机号是否被绑定
     *
     * @method string $POST['cellphone']
     * @Route("/account/is_register/cellphone",name="_account_is_register_cellphone")
     */
    public function accountIsRegisterCellphoneAction()
    {
        $request = $this->get('request');
        $cellphone = urldecode($request->request->get('cellphone'));
        if('' == $cellphone){
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $isRegister = $this->get('common.account')->isRegisterCellphone($cellphone);
        return new Response(json_encode(array('errcode'=>100,'data'=>array('is_register'=>$isRegister))));
    }

    /**
     * 绑定邮箱
     * 
     * @method string $_POST['email']
     * @Route("/account/bind/email",name="_account_bind_email")
     */
    public function accountBindEmailActioin()
    {
        $request = $this->get('request');
        $email = urldecode($request->request->get('email'));
        if('' == $email){
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $uid = $this->get('session')->get('user_id');
        $result = $this->get('common.account')->bindEmail($uid,$email);
        return new Response(json_encode($result));
    }

    /**
     * 绑定手机号
     *
     * @method string $POST['cellphone']
     * @Route("/account/bind/cellphone",name="_account_bind_cellphone")
     */
    public function accountBindCellphoneAction()
    {
        $request = $this->get('request');
        $cellphone = urldecode($request->request->get('cellphone'));
        if('' == $cellphone){
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $uid = $this->get('session')->get('user_id');
        $result = $this->get('common.account')->bindCellphone($uid,$cellphone);
        return new Response(json_encode($result));
    }

    /**
     * 发送密码找回邮件
     *
     * @method string $_POST['email']
     * @method string $_POST['checknum']
     * @Route("/account/forget_password/submit", name="_forget_password_submit")
     */
    public function accountForgetPasswordSubmitAction()
    {
        $request = $this->get('request');
        $email = urldecode($request->request->get('email'));
        $checknum = urldecode($request->request->get('checknum'));
        if ($checknum != $this->get('session')->get('checknum')) {
            return new Response(json_encode(
                Array(
                    'errcode'=>109,
                    'data'=>array(),
                )
            ));
        }
        else if (!$this->get('common.account')->getUidByEmail($email)) {
            return new Response(json_encode(
                Array(
                    'errcode'=>108,
                    'data'=>array(),
                )
            ));
        }
        else {
            return new Response(json_encode(
                $this->get('common.account')->sendResetPasswordEmail($email)
            ));
        }
    }

    /**
     * 重置密码页面
     *
     * @method string $_GET['token']
     * @return resource ('SongshukrMainBundle:account:reset_password.html.twig')
     * @Route("/account/reset_password", name="_account_reset_password")
     * @Template("SongshukrMainBundle:account:reset_password.html.twig")
     */
    public function resetPasswordAction()
    {
        $request = $this->get('request');
        $hl = $request->query->get('hl')?$request->query->get('hl'):$this->get('session')->get('_locale');
        $request->setLocale($hl);
        $token = urldecode($this->get('request')->query->get('token'));
        if (null == $token) {
            return new RedirectResponse('/');
        }
        $tokenInfo = $this->get('common.account')->getResetPasswordTokenInfo($token);
        if (100 != $tokenInfo['errcode']) {
            return new RedirectResponse('/');
        }
        return $this->render('SongshukrMainBundle:account:reset_password.html.twig', $tokenInfo);
    }

    /**
     * 重置密码submit
     *
     * @method string $_POST['token']
     * @method string $_POST['password']
     * @return resource ('SongshukrMainBundle:account:reset_password.html.twig')
     * @Route("/reset_password/submit", name="_reset_password_submit")
     */
    public function resetPasswordSubmitAction()
    {
        $request = $this->get('request');
        $token = urldecode($request->request->get('token'));
        $password = urldecode($request->request->get('password'));

        if ($token != null && $password != null) {
            $result = $this->get('common.profile')->resetPassword($token, $password);
            return new Response(json_encode($result));
        }
        else {
            return new Response(json_encode(
                array(
                    'errcode'=>199,
                    'data'=>array(),
                )));
        }
    }

    /**
     * 图片确认码
     *
     * @Route("/verify_code", name="_verify_code")
     */ 
    public function verifyCodeAction()
    {
        //随机生成一个4位数的数字验证码
        $num = '';
        for ($i=0;$i<4;$i++) {
            $num .= rand(0,9);
        }

        //4位验证码也可以用rand(1000,9999)直接生成
        //将生成的验证码写入session，备验证页面使用
        $this->get('session')->set('checknum',$num);
        //创建图片，定义颜色值
        Header('Content-type: image/PNG');
        srand((double)microtime()*1000000);
        $im = imagecreate(70,30);
        $black = ImageSongshukrAllocate($im, 0,0,0);
        $gray = ImageSongshukrAllocate($im, 200,200,200);
        $white = ImageSongshukrAllocate($im, 255,255,255);
        $textcolor = array();
        $textcolor[] = ImageSongshukrAllocate($im, 50,117,36);
        $textcolor[] = ImageSongshukrAllocate($im, 0,102,204);
        $textcolor[] = ImageSongshukrAllocate($im, 255,153,0);
        $textcolor[] = ImageSongshukrAllocate($im, 0,102,204);
        $textcolor[] = ImageSongshukrAllocate($im, 255,204,0);
        imagefill($im,0,0,$white);

        //随机绘制两条虚线，起干扰作用
        $style = array($black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
        imagesetstyle($im, $style);
        $y1 = rand(0,30);
        $y2 = rand(0,30);
        $y3 = rand(0,30);
        $y4 = rand(0,30);
        imageline($im, 0, $y1, 70, $y3, IMG_COLOR_STYLED);
        imageline($im, 0, $y2, 70, $y4, IMG_COLOR_STYLED);

        //在画布上随机生成大量黑点，起干扰作用;
        for ($i=0;$i<80;$i++) {
            imagesetpixel($im, rand(0,60), rand(0,30), $black);
        }

        $font = 'arial.ttf';
        //将四个数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成
        $strx = rand(4,8);

        for ($i=0;$i<4;$i++){
            $stry = rand(20,30);
            //          imagestring($im, 5,$strx ,$stry, substr($num,$i,1), $yellow);
            $index = rand(0,4);
            imagettftext($im, 15, 0, $strx, $stry, $textcolor[$index], $font, substr($num,$i,1));
            $strx += rand(12,16);
        }
        ImagePNG($im);
        ImageDestroy($im);
        return new Response();
    }

    /**
     * 设置用户详细信息
     *
     * @method string $_POST['description']
     * @Route("/account/set/description",name="_account_set_description")
     */
    public function accountSetDescription()
    {
        $uid = $this->get('session')->get('user_id');
        if(!$uid){
            return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        }
        $request = $this->get('request');
        $description = $request->request->get('description');
        if($description === null) {
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $result = $this->get('common.account')->setDescription($uid, $description);
        return new Response(json_encode($result));
    }

    /**
     * 设置用户名
     *
     * @method string $_POST['username']
     * @Route("/account/set/username",name="_account_set_username")
     */
    public function accountSetUsername()
    {
        $uid = $this->get('session')->get('user_id');
        if(!$uid){
            return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        }
        $request = $this->get('request');
        $username = urldecode($request->request->get('username'));
        if($username === null) {
            return new Response(json_encode(array('errcode'=>101,'data'=>array())));
        }
        $result = $this->get('common.account')->setUsername($uid, $username);
        return new Response(json_encode($result));
    }

    /**
     * 帐户设置
     * 
     * @method $_POST['username'] || null
     * @method $_POST['cellphone'] || null
     * @method $_POST['address'] || null
     * @Route("/account/set",name="_account_set")
     */
    public function accountSetAction()
    {
        $uid = $this->get('session')->get('user_id');
        if(!$uid){
            return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        }
        $request = $this->get('request');
        $config = array();
        $options = array('username','cellphone','address');
        foreach($options as $option) {
            if($request->request->get($option) !== null) {
                $config[$option] = urldecode($request->request->get($option));
            }
        }
        $result = $this->get('common.account')->accountSet($uid, $config);
        return new Response(json_encode($result));
    }

    /**
     * 获取帐户信息
     * 
     * @Route("/account/get",name="_account_get")
     */
    public function accountGetAction()
    {
        $uid = $this->get('session')->get('user_id');
        if(!$uid){
            return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        }
        $result = $this->get('common.account')->accountGet($uid);
        return new Response(json_encode($result));
    }

    /**
     * 管理员登录
     * 
     * @Route("/admin/login",name="_admin_login")
     */
    public function adminLoginAction()
    {
        $request = $this->get('request');
        $name = $request->get('name');
        $password = $request->get('password');
        if ($this->get('common.account')->adminLogin($name, $password)) {
            $data = array(
                'token'=> session_id(),
                'admin'=> $name,
            );
            $response = new Response();
            $response->headers->setCookie(new Cookie('admin',$this->get('session')->get('admin'),time()+ 13140000,'/',NULL,0,0));
            $response->setContent(json_encode(array('errcode'=>100,'data'=>$data)));
            return $response;
        }
        else{
            return new Response(json_encode(array('errcode'=>103,'data'=>array())));
        }
    }

    /**
     * 管理员是否登录
     * 
     * @Route("/admin/is_login")
     */
    public function adminIsloginAction()
    {
        $session = $this->get('session');
        $admin = $session->get('admin');
        if($admin) {
            return new Response(json_encode(array('errcode'=>100, 'data'=>array('admin'=>$admin))));
        } else {
            return new Response(json_encode(array('errcode'=>104, 'data'=>array())));
        }
    }
}