<?php
namespace app\index\controller;


use app\index\model\NcUser;
use app\model\WxUser;
use think\Controller;
use think\facade\Config;
use think\facade\View;

class Index  extends Controller
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
        dump(NcUser::get(3));

        //查询构造器
        NcUser::field('id, name, email')
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
}
