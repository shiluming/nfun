<?php


namespace app\index\controller;

use app\index\model\NvWxUser;
use think\facade\Log;

/**
 *
 * 'ToUserName' => 'gh_601343ea897e',
* 'FromUserName' => 'oQYsAxIs6SqaLXJXj7Z7rOMTQvqY',
* 'CreateTime' => '1577319663',
* 'MsgType' => 'event',
* 'Event' => 'unsubscribe',
* 'EventKey' => NULL,
 * Class SubscribeMessageHandler
 * @package app\index\controller
 *
 *
 * {
"subscribe": 1,
"openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",
"nickname": "Band",
"sex": 1,
"language": "zh_CN",
"city": "广州",
"province": "广东",
"country": "中国",
"headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
"subscribe_time": 1382694957,
"unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
"remark": "",
"groupid": 0,
"tagid_list":[128,2],
"subscribe_scene": "ADD_SCENE_QR_CODE",
"qr_scene": 98765,
"qr_scene_str": ""
}
 *
 *
 *
 */
class SubscribeMessageHandler
{
    //订阅事件
    public function subscribeHandler($message)
    {
        $app = app('wechat.official_account');
        $user = $app->user->get($message['FromUserName']);
        Log::write($user);
        if ($message['MsgType'] == 'event' && $message['subscribe'])
        {
            $openId = $message['FromUserName'];

            //订阅消息
            $user = new NvWxUser();
            $user->nickname($message['nickname']);
        }

    }

    //取消订阅事件
    public function unSubscribeHandler($message)
    {

    }
}