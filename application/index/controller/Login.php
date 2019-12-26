<?php


namespace app\index\controller;

use app\index\model\NvUser;
use app\index\model\NvWxUser;
use think\Controller;
use think\facade\Log;
use think\facade\Session;
use think\facade\Validate;
use think\View;


define("TOKEN", 'weixin123');

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



    /**
     * 用于微信公众号里填写的URL的验证，
     * 如果合格则直接将"echostr"字段原样返回
     */
//    public function valid()
//    {
//        Log::write('valid '. TOKEN);
//        $echoStr = $_GET["echostr"];
//        $ret = $this->checkSignature();
//        Log::write('ret ='.$ret);
//        if ($ret){
//            echo $echoStr;
//            exit;
//        }
//        Log::write('ret after='.$ret);
//    }

    public function valid()
    {
        $app = app('wechat.official_account');
//        $app->server->push(Mediamess)
//        $app->server->push(function ($message) {
//            switch ($message['MsgType']) {
//                case 'event':
//                    Log::write($message);
//                    return '收到事件消息';
//                    break;
//                case 'text':
//                    return '收到文字消息';
//                    break;
//                case 'image':
//                    return '收到图片消息';
//                    break;
//                case 'voice':
//                    return '收到语音消息';
//                    break;
//                case 'video':
//                    return '收到视频消息';
//                    break;
//                case 'location':
//                    return '收到坐标消息';
//                    break;
//                case 'link':
//                    return '收到链接消息';
//                    break;
//                case 'file':
//                    return '收到文件消息';
//                // ... 其它消息
//                default:
//                    return '收到其它消息';
//                    break;
//            }
//        });
        $app->server->serve()->send();
    }

    /**
     * 用于验证是否是微信服务器发来的消息
     * @return bool
     */
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        Log::write('signature='.$signature .', time='.$timestamp .', nonce='.$nonce);
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        Log::write('tmpStr='. $tmpStr .'sign='.$signature);
        if ($tmpStr == $signature){
            return true;
        }else {
            return false;
        }
    }

}