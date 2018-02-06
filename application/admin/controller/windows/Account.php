<?php
namespace app\admin\controller\windows;
use app\common\controller\Backend;
class Account extends Backend{
    public function index(){
        return view();
    }
    public function add(){
        return view();
    }
    public function edit(){
        return view();
    }
    public function add_submit(){
    	$post=$this->server['post'];
    	$validate=validate('Account');
    	$result = $validate->scene('add')->check($post);
    	if($result){
    		$Account=model('WeixinAccount');
    		$Account->data($post)->save();
			return $this->show();
    	}else{
    		return $this->console($validate->getError());
    	}
    }
    function account_list_count(){
    	$Account=model('WeixinAccount');
    	$data=$Account->getCount();
    	return $this->show($data);
    }
    function account_list($page=1,$pagesize=10){	
    	$Account=model('WeixinAccount');
    	$data=$Account->getList($page,$pagesize);
    	return $this->show($data);
    }
    function edit_data(){
    	$Account=model('WeixinAccount');
    	$post=$this->server['post'];
    	$data=$Account->getone($post['id']);
    	return $this->show($data);
    }
    public function edit_submit(){
    	$post=$this->server['post'];
    	$validate=validate('Account');
    	$result = $validate->scene('add')->check($post);
    	if($result){
    		$Account=model('WeixinAccount');
    		$Account->save($post,['id'=>$post['id']]);
			return $this->show();
    	}else{
    		return $this->console($validate->getError());
    	}
	}
	function choose_account(){
		return view();
	}
	function choose_account_submit(){
		$post=$this->server['post'];
		$post['id']=intval($post['id']);
		if($post['id']>0){
			$Account=model('WeixinAccount');
			if($account_data=$Account->where('id',$post['id'])->find()){
				session('weixin_account',$account_data['id']);
				return $this->show($account_data['name']);
			}
		}
		return $this->console();
	}
	function this_account(){
		$session=$this->server['session'];
		$account_id=intval($session['weixin_account']);
		if($account_id>0){
			$Account=model('WeixinAccount');
			if($account_data=$Account->where('id',$account_id)->find()){
				return $this->show($account_data['name']);
			}
		}
		return $this->console();
	}
}
