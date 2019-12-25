<?php


namespace app\model;


class WxUser extends \think\Model
{
    protected $pk = 'id';//默认主键

    //表名
    protected $table = 'nv_wx_user';

    //自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


}