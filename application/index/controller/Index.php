<?php
namespace app\index\controller;


use app\index\model\NvQrCode;
use app\index\model\NvUser;
use app\index\model\NvWxUser;
use app\model\WxUser;
use EasyWeChat\Factory;
use think\Controller;
use think\Db;
use think\facade\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Index  extends CheckLogin
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        $startTime = strtotime(request()->param('startTime'));
        $endTime = strtotime(request()->param('endTime'));
        Log::write('--->'.$startTime.';'.$endTime);

        if ($startTime && $endTime) {
            Log::write('查询');
            $wxUser = Db::table('nv_wx_user')
                ->where('subscribe_time', ['>', $startTime], ['<', $endTime], 'and')
                ->paginate(20, false, ['query' => request()->param()]);
            $this->assign("wxUser", $wxUser);
        }else {
            $wxUser = WxUser::paginate(20, false, ['query' => request()->param()]);
            $this->assign("wxUser", $wxUser);
        }


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
        Log::write(request()->param());
        $map['subscribe_time'] = array('between',$startTime.','.$endTime);
        $data['subscribe_time'] = array(array('egt', $startTime),array('elt', $endTime), 'and') ;
//        $map['subscribe_time'] = array(array('egt', $startTime), array('elt', $endTime));
        $wxUser = WxUser::where('subscribe_time', '=', '1')->paginate(20, false);
        //;paginate(20, false);
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
        $ret = $app->qrcode->temporary($name, 6 * 24 * 3600);
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
        $ret = $app->qrcode->temporary($channel, 6 * 24 * 3600);
        dump($ret);
    }

    public function ip()
    {
        $user = WxUser::get(1);
        $user->id = 0;
        for ($i = 0; $i < 50000; $i++) {
            $user->id=0;
            $user->isUpdate(false)->save();
        }
    }

    //导出excel
    public function outExcel()
    {
        $user = NvWxUser::all();
        Log::write('======================================');
        Log::write($user);
        foreach ($user as $key=>$value) {
            Log::write('key='.$key.';;;;'.$value);
        }
        $spreadsheet = new Spreadsheet();
        //add title
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', '微信昵称')
            ->setCellValue('C1', 'OPEN_ID')
            ->setCellValue('D1', '是否关注')
            ->setCellValue('E1', '性别')
            ->setCellValue('F1', '国家')
            ->setCellValue('G1', '省份')
            ->setCellValue('H1', '城市')
            ->setCellValue('I1', '关注时间')
            ->setCellValue('J1', '渠道来源');

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('关注用户');

        $i = 2;
        foreach ($user as $rs) {
            // add data
            // Add data
            $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, $rs['id'])
                ->setCellValue('B'.$i, $rs['nickname'])
                ->setCellValue('C'.$i, $rs['openid'])
                ->setCellValue('D'.$i, $rs['subscribe'] == 1 ? '是' : '否')
                ->setCellValue('E'.$i, $rs['sex'] == 1 ? '男' : '女')
                ->setCellValue('F'.$i, $rs['country'])
                ->setCellValue('G'.$i, $rs['province'])
                ->setCellValue('H'.$i, $rs['country'])
                ->setCellValue('I'.$i, date('Y-m-d H:i:s',$rs['subscribe_time']))
                ->setCellValue('J'.$i, $rs['qr_scene_str']);
            $i++;
        }

        //Set width
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(5);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(50);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(10);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(15);

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(20);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        return $this->exportExcel($spreadsheet, 'xls', '关注用户');
    }

    /**
     * 导出Excel
     * @param  object $spreadsheet  数据
     * @param  string $format       格式:excel2003 = xls, excel2007 = xlsx
     *
     * @param  string $savename     保存的文件名
     * @return filedownload         浏览器下载
     */
    public function exportExcel($spreadsheet, $format = 'xls', $savename = 'export')
    {
        if (!$spreadsheet) return false;
        if ($format == 'xls') {
            //输出Excel03版本
            header('Content-Type:application/vnd.ms-excel');
            $class = "\PhpOffice\PhpSpreadsheet\Writer\Xls";
        } elseif ($format == 'xlsx') {
            //输出07Excel版本
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $class = "\PhpOffice\PhpSpreadsheet\Writer\Xlsx";
        }
        //输出名称
        header('Content-Disposition: attachment;filename="'.$savename.'.'.$format.'"');
        //禁止缓存
        header('Cache-Control: max-age=0');
        $writer = new $class($spreadsheet);
        $filePath = env('runtime_path')."temp/".time().microtime(true).".tmp";
        $writer->save($filePath);
        readfile($filePath);
        unlink($filePath);
    }

}
