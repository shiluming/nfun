<?php
namespace app\index\controller;

class Index
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return 'index';
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
        return view('index');
    }
}
