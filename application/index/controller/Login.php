<?php


namespace app\index\controller;

use app\index\model\NvUser;
use app\index\model\NvWxUser;
use think\Controller;
use think\facade\Log;
use think\facade\Session;
use think\facade\Validate;
use think\View;

class Login extends Controller
{

    public function index()
    {
        Log::write('login view');
        return $this->view->fetch('login');
    }

    public function login()
    {
        $request = request();
        $username = $request->param('username');
        $password = $request->param('password');
        $captcha = $request->param('captcha');
        Log::write('username='.$username . " ;password=". $password);
        $user = NvUser::get(['user_name'=>$username, 'password'=>$password]);
        Log::write('user'.$user);
        if (null == $user) {
//            $this->view->assign('errorMsg', '账号密码错误');
            $data = '账号密码有误';
            return json($data, 500);
        }
        //设置session
        if (!Session::has('uid')) {
            Session::set('uid', $user->id);
        }
//        return $this->redirect('Index/index');
        return json();
    }

    public function logout()
    {
        if (Session::has('uid')) {
            Session::set('uid', null);
        }
        return $this->redirect('login/index');
    }

}