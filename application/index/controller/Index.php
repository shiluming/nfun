<?php
namespace app\index\controller;


use app\index\model\NvQrCode;
use app\index\model\NvUser;
use app\model\WxUser;
use EasyWeChat\Factory;
use think\Controller;
use think\facade\Log;


class Index  extends CheckLogin
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        $startTime = request()->param('startTime');
        $endTime = request()->param('endTime');

        $wxUser = WxUser::paginate(5, false, ['query' => request()->param()]);
        $this->assign("wxUser", $wxUser);
        return $this->view->fetch();
    }

    /**
     * 首页
     * @return string
     */
    public function search()
    {
        $startTime = request()->param('startTime');
        $endTime = request()->param('endTime');

        $wxUser = WxUser::paginate(10, false, ['query' => request()->param()]);
        $this->assign("wxUser", $wxUser);
        return $this->view->fetch('index');
    }


    public function sayHi2()
    {
        dump(NvUser::get(3));

        //查询构造器
        NvUser::field('id, name, email')
            ->where('id', 9)
            ->find();
    }

    public function test22()
    {
        //模板变量赋值
        //1.普通变量
        $this->view->assign('name', 'shiluming');
        $this->view->assign('age', '99');
        $this->view->assign([
            'sex' => 'name',
            'salary' => 33333
        ]);
        //在模板中输出数据， 默认目录


    }

    /**
     * 文件下载
     *
     * @return \think\response\Download
     * @author slm
     */
    public function download()
    {
        $zip = new \ZipArchive();

        $download = new \think\response\Download('111.txt');
//        return $download->name('my.jpg');
        // 或者使用助手函数完成相同的功能
        // download是系统封装的一个助手函数
        return download('111.txt', '111.txt');
    }

    /**
     * 文件下载
     *
     * @return \think\response\Download
     * @author slm
     */
    public function downloadQr($qr_numbers=5)
    {
        Log::write('下载二维码参数：num='.$qr_numbers);
        $filename = "test.zip";
        $zip = new \ZipArchive();
        if (file_exists($filename)) {
            unlink($filename);
        }
        $zip->open($filename,\ZipArchive::CREATE||\ZipArchive::OVERWRITE);   //打开压缩包
        Log::write('--------------------------------------------------');
        for ($i = 0; $i < $qr_numbers; $i++)
        {
            $this->temporaryQrcode('channel-'.$i, 'channel-'.$i);
            $path = "channel-".$i.'.jpg';
            $zip->addFile($path,basename($path));   //向压缩包中添加文件
            Log::write('下载二维码参数：num='.$qr_numbers.'; 循环次数i='.$i);
        }
        Log::write('--------------------------------------------------');

        $zip->close();  //关闭压缩包
//        if (file_exists($filename)) {
//            dump($zip);
//            exit();
//        }else {
//            dump('no exit');
//        }
        $download = new \think\response\Download('111.txt');
//        return $download->name('my.jpg');
        // 或者使用助手函数完成相同的功能
        // download是系统封装的一个助手函数
        return download($filename, '渠道二维码.zip');
    }

    public function wc()
    {
        $config = [
            'app_id' => 'wx12dbb75d4f31e33f',
            'secret' => 'db107170cbc972df6536e4beca38de8b',
            'token' => 'weixin123',
            'response_type' => 'array',
            //...
        ];
        //    先初始化微信
        $app = Factory::officialAccount($config);
        $app->server->push(function ($message) {
            return "您好！欢迎使用 EasyWeChat!";
        });

        $response = $app->server->serve();
        $response->send();
        exit;
//        $app->server->serve()->send();
//        $ip=$app->user->list();
//        dump($ip);
    }

    public function wc1()
    {
        //    先初始化微信
        $app = app('wechat.official_account');
        $ip = $app->user->list();
        dump($ip);
    }
    //创建二维码
    public function temporaryQrcode($name, $desc)
    {
        $app = app('wechat.official_account');
        $ret = $app->qrcode->temporary('foo', 6 * 24 * 3600);
        //保存到数据库
        $qrCode = new NvQrCode();
        $qrCode->ticket = $ret['ticket'];
        $qrCode->url = $ret['url'];
        $qrCode->expire_seconds = $ret['expire_seconds'];
        $qrCode->name = $name;
        $qrCode->desc = $desc;
        $qrCode->save();

        //保存文件到本地
        $url = $app->qrcode->url($ret['ticket']);
        $content = file_get_contents($url);
        file_put_contents($name.'.jpg', $content);
    }

    //创建二维码,虽然渠道一样，但是生成的二维码不一样了。
    public function foreverQrcode($channel = 'default')
    {
        $app = app('wechat.official_account');
        $ret = $app->qrcode->temporary('1', 6 * 24 * 3600);
        dump($ret);
    }

    public function ip()
    {
        $app = app('wechat.official_account');
        dump($app->base->getValidIps());
    }

}
