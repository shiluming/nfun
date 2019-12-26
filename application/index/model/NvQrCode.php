<?php


namespace app\index\model;


use think\Model;

class NvQrCode extends Model
{
    protected $pk = 'id';

    protected $table = 'nv_qr_code';

    protected $autoWriteTimestamp = true;

    protected $updateTime = 'update_time';

    protected $createTime = 'add_time';

}