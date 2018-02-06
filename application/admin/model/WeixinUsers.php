<?php
namespace app\admin\model;
use think\Model;
class WeixinUsers extends Model{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    function getCount(){
    	return $this->column('count(id)')[0];
    }
    function getList($page=1,$pagesize=10){
    	return $this->limit(getLimit($page,$pagesize))->select();
    }
    function getone($id=0){
    	$id=intval($id);
    	if($id>0){
    		return $this->where('id',$id)->find();
    	}else{
    		return [];
    	}
    }
}
