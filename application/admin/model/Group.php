<?php
namespace app\admin\model;
use think\Model;
class Group extends Model{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    function getGroupAccount($groupid=0){
        $groupid=intval($groupid);
        if($groupid>0){
            return $this->where('groupid',$groupid)->where('deleted',0)->column('account_id');
        }else{
            return [];
        }
    }
    // function getList($page=1,$pagesize=10){
    // 	return $this->limit(getLimit($page,$pagesize))->select();
    // }
    function getone($id=0){
    	$id=intval($id);
    	if($id>0){
    		return $this->where('id',$id)->find();
    	}else{
    		return [];
    	}
    }
    function getByGroupAccount($groupid=0,$accountid=0){
        $groupid=intval($groupid);
        $accountid=intval($accountid);
        if($groupid>0&&$accountid>0){
            return $this->where('groupid',$groupid)->where('account_id',$accountid)->find();
        }else{
            return [];
        }
    }
}
