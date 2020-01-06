<?php


namespace app\index\controller;

use think\App;
use think\Controller;
use think\facade\Log;
use think\facade\Session;

class CheckLogin extends Controller
{

    function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->checkLogin();

    }

    protected function checkLogin()
    {
        if (!Session::has('uid')) {
            $this->redirect(url('index/Login/index'),'', 0);
        }
    }

}