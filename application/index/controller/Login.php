<?php


namespace app\index\controller;

use app\index\model\NvUser;
use app\index\model\NvWxUser;
use EasyWeChat\Kernel\Messages\Message;
use think\Controller;
use think\facade\Log;
use think\facade\Session;
use think\facade\Validate;
use think\View;
use think\Db;

define("TOKEN", 'weixin123');

class Login extends Controller
{

public function testAdd()
    {

	$app = app('wechat.official_account');
	$wxUser = $app->user->get('oQYsAxIs6SqaLXJXj7Z7rOMTQvqY');
	Log::write($wxUser);
	
        $user = new NvWxUser();
        $ret = $user->save($wxUser);
	dump($ret);
    }

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

        $data = new \stdClass();
        if (!captcha_check($captcha)) {
            //验证失败
            $data->code="-1";
            $data->msg="验证码有误";
            return json($data);
        }

        Log::write('user'.$user);
        if (null == $user) {
//            $this->view->assign('errorMsg', '账号密码错误');
            $data->code="-1";
            $data->msg="账号密码有误";
            return json($data);
        }
        //设置session
        if (!Session::has('uid')) {
            Session::set('uid', $user->id);
            Session::set('loginUserName', $user->user_name);
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
        Log::write("valid");
//        $app->server->push(SubscribeMessageHandler::class, Message::EVENT);

        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    $this->handleSubscribe($message);
                    break;
                case 'text':
                    return 'success';
					//return '收到文字消息';
                    break;
                case 'image':
                    return 'success';
					//return '收到图片消息';
                    break;
                case 'voice':
                    return 'success';
					//return '收到语音消息';
                    break;
                case 'video':
                    return 'success';
					//return '收到视频消息';
                    break;
                case 'location':
                    return 'success';
					//return '收到坐标消息';
                    break;
                case 'link':
					return 'success';
                    //return '收到链接消息';
                    break;
                case 'file':
					return 'success';
                    //return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });
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

    public function handleSubscribe($message)
    {
        $app = app('wechat.official_account');
        Log::write($message);
        $openid = $message['FromUserName'];
        $event = $message['Event'];
        $eventKey = $message['EventKey'];
        Log::write("开始处理订阅消息： openid=".$openid ."; event=".$event);
        if ('unsubscribe' == $event) {
 //           Log::write("开始处理订阅消息： unsubscribe");
            //取消订阅
            $user = NvWxUser::get(['openid'=>$openid]);
			Log::write("debug_text 1： ");
			Log::write($user);
            if ($user) {
                //更新
                $user->subscribe=0;
                $user->save();
            }
        } else if ('subscribe' == $event) {
            Log::write("开始处理订阅消息： subscribe");
            //订阅
            $dbUser = NvWxUser::get(['openid'=>$openid]);
//			Log::write("debug_text 2： ");
			Log::write($dbUser);
            if (!$dbUser) {
                $dbUser = new NvWxUser();
                //为空的话，要插入
                $user = $app->user->get($openid);
//				Log::write("debug_text 3： ");
//				Log::write($user);

		$insertData = [
                    'openid'=>$user['openid'],
                    'subscribe'=>$user['subscribe'],
                    'nickname'=>$user['nickname'],
                    'sex'=>$user['sex'],
                    'province'=>$user['province'],
                    'country'=>$user['country'],
                    'headimgurl'=>$user['headimgurl'],
                    'subscribe_time'=>$user['subscribe_time'],
                    'unionid'=>0,
                    'subscribe_scene'=>$user['subscribe_scene'],
                    'qr_scene'=>$user['qr_scene'],
                    'qr_scene_str'=>$user['qr_scene_str'],
					'city'=>$user['city'],
                    'create_time'=>time()/1000,
                    'update_time'=>time()/1000
                ];
		Db::name('nv_wx_user')->insert($insertData, true);
            }else {
				$user = $app->user->get($openid);
                $dbUser->subscribe = 1;
		$qrStr = $eventKey;
				if (substr_count($eventKey, 'qrscene_') > 0) 
				{
					$qrStr = substr($eventKey, 8);
				}
				
                $dbUser->qr_scene_str = $qrStr;
                $dbUser->city=$user['city'];

                $dbUser->save();
            }
        }
    }

}

