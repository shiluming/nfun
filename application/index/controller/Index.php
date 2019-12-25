<?php
namespace app\index\controller;


use app\index\model\NvUser;
use app\model\WxUser;
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
        $wxUser = WxUser::paginate(5);
        $this->assign("wxUser", $wxUser);
        return $this->view->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function sayHi($name)
    {
        return 'hi ' . $name;
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
            'sex'=>'name',
            'salary'=>33333
        ]);
        //在模板中输出数据， 默认目录


    }

    /**
     * 文件下载
     *
     * @author slm
     * @return \think\response\Download
     */
    public function download()
    {
        $zip = new \ZipArchive();

        $download =  new \think\response\Download('111.txt');
//        return $download->name('my.jpg');
        // 或者使用助手函数完成相同的功能
        // download是系统封装的一个助手函数
        return download('111.txt', '111.txt');
    }
}
