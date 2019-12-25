<?php


namespace app\admin\controller;


use think\facade\Config;

class User
{

    public function get()
    {
        //获取全部配置项目
//        dump(Config::get());
        //获取一级配置项目
//        dump(Config::get('app.'));

        //获取一级配置项目, 推荐使用
//        dump(Config::pull('app'));

        //获取二级配置项目
        dump(Config::get('app.app_debug'));
        //app是默认的一级配置前缀
        dump(Config::get('app_debug'));

        dump(Config::has('sdf'));

    }

    public function set()
    {
        //动态设置，静态设置就是直接修改配置文件
        Config::set('app_name', 'sdkfjsdkjfksdjfksdjfksdjf');

        dump(Config::get('app_name'));
    }

    //助手函数
    public function helper()
    {
//        dump(config());
//        dump(config('app_name'));
        dump(\config('?database.username'));
        dump(\config('database.username'));
    }

}