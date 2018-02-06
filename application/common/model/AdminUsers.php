<?php
namespace app\common\model;
use think\Model;
use think\Session;
class AdminUsers extends Model{
	// 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
   	function check_login($userid=0,$userip='127.0.0.1'){
   		return $this->where("joinip",$userip)->where("id",intval($userid))->find();
   	}
   	function login($username='',$password='',$userip='127.0.0.1'){
   		$username=trim($username);
   		$password=trim($password);
   		if(fullString($username,$password)){
   			if($user=$this->where("username",$username)->find()){
   				if(md5($password.$user['salt'])==$user['password']){
   					$data['lastdate']=$user['joindate'];
   					$data['lastip']=$user['joinip'];
   					$data['joindate']=date("Ymd",time());
   					$data['joinip']=$userip;
   					$this->save($data,['id'=>$user['id']]);
   					return $user;
   				}
   			}
   		}
   		return false;
   	}
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
    function getInfo($id=0){
        $data=$this->getone($id);
        unset($data['password']);
        unset($data['salt']);
        return $data;
    }
    function deleteone($id=0){
        $id=intval($id);
        if($id>0){
            return $this->where('id',$id)->delete();
        }else{
            return false;
        }
    }
}