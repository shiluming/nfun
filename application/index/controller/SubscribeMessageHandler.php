<?php


namespace app\index\controller;

use app\index\model\NvWxUser;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use think\facade\Log;
use \EasyWeChat\Kernel\Contracts\EventHandlerInterface;
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
class SubscribeMessageHandler implements EventHandlerInterface
{


    public function handle($payload = null)
    {

        $app = app('wechat.official_account');
        $openid = $app['request']->get('FromUserName');
        $event = $app['request']->get('Event');
        Log::write("开始处理订阅消息： openid=".$openid ."; event=".$event);
        if ('unsubscribe' == $event) {
            Log::write("开始处理订阅消息： unsubscribe");
            //取消订阅
            $user = NvWxUser::get(['openid'=>$openid]);
            if ($user) {
                //更新
                $user->subscribe=0;
                $user->save();
            }
        } else if ('subscribe' == $event) {
            Log::write("开始处理订阅消息： subscribe");
            //订阅
            $user = NvWxUser::get(['openid'=>$openid]);
            //更新
            $user->subscribe=1;
            Log::write($user);
        }
    }
}