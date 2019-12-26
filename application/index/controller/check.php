<?php


namespace app\index\controller;


use think\Controller;
use think\facade\Log;

class check extends Controller
{

    /**
     * 用于微信公众号里填写的URL的验证，
     * 如果合格则直接将"echostr"字段原样返回
     */
    public function valid()
    {
        Log::write('valid');
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()){
            echo $echoStr;
            exit;
        }
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

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature){
            return true;
        }else {
            return false;
        }
    }

}