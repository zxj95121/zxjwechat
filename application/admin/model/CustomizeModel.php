<?php
namespace app\admin\model;
use think\Model;
class CustomizeModel extends Model{

    protected $table = 'rabbit_customize';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';

}
