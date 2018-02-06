<?php
namespace app\admin\controller\windows;
use app\common\controller\Backend;
class Index extends Backend{
    public function index(){
        return view();
    }
    function user_group(){
    	return view();
    }
    function user_group_add(){
    	return view();
    }
    function user_group_add_submit(){
    	$post=$this->server['post'];
    	$validate=validate('AccountGroup');
    	$result = $validate->scene('add')->check($post);
    	if($result){
    		$AccountGroup=model('AccountGroup');
    		$AccountGroup->data($post)->save();
			return $this->show();
    	}else{
    		return $this->console($validate->getError());
    	}
    	return $this->show($post);
    }
	function user_group_edit(){
    	return view();
    }
    function user_group_edit_data(){
    	$Account=model('AccountGroup');
    	$post=$this->server['post'];
    	$data=$Account->getone($post['id']);
    	return $this->show($data);
    }
    function user_group_edit_submit(){
    	$post=$this->server['post'];
    	$validate=validate('AccountGroup');
    	$result = $validate->scene('edit')->check($post);
    	if($result){
    		$AccountGroup=model('AccountGroup');
    		$AccountGroup->save($post,['id'=>$post['id']]);
			return $this->show();
    	}else{
    		return $this->console($validate->getError());
    	}
    	return $this->show($post);
    }

    function user_group_list_count(){
    	$AccountGroup=model('AccountGroup');
    	$data=$AccountGroup->getCount();
    	return $this->show($data);
    }
    function user_group_list($page=1,$pagesize=10){	
    	$AccountGroup=model('AccountGroup');
    	$data=$AccountGroup->getList($page,$pagesize);
    	return $this->show($data);
    }
    function user_group_account(){
    	return view();
    }
    function user_groups(){
    	$AccountGroup=model('AccountGroup');
    	$user_groups=$AccountGroup->field('id,name')->select();
    	return $this->show($user_groups);
    }
    function account_list_count(){
    	$Account=model('WeixinAccount');
    	$data=$Account->getCount();
    	return $this->show($data);
    }
    function user_group_list_delete(){
    	$post=$this->server['post'];
    	$AccountGroup=model('AccountGroup');
    	$AccountGroup->deleteone($post['id']);
    	return $this->user_group_list($post['page'],$post['pagesize']);
    }
    function account_list($page=1,$pagesize=10){	
    	$post=$this->server['post'];
    	$post['id']=intval($post['id']);
    	$Account=model('WeixinAccount');
    	$data['data']=$Account->getList($page,$pagesize);
    	$Group=model('Group');
    	$data['checked']=$Group->getGroupAccount($post['id']);
    	return $this->show($data);
    }
    function chooseAccount(){
    	$post=$this->server['post'];
    	$Group=model('Group');
    	if($post['result']==1){
    		if($result=$Group->getByGroupAccount($post['groupid'],$post['id'])){
    			$data['deleted']=0;
    			$Group->save($data,['id'=>$result['id']]);
    		}else{
    			$data['groupid']=intval($post['groupid']);
    			$data['account_id']=intval($post['id']);
    			$Group->data($data)->save();
    		}
    	}else{
    		$data['deleted']=1;
    		$result=$Group->getByGroupAccount($post['groupid'],$post['id']);
    		$Group->save($data,['id'=>$result['id']]);
    	}
    	return $this->show();
    }
    function users(){
		return view();
    }
    function add_user(){
		return view();
    }
    public function add_user_submit(){
    	$post=$this->server['post'];
    	$post['password']=trim($post['password']);
    	$validate=validate('AdminUsers');
    	$result = $validate->scene('add')->check($post);
    	if($result){
    		$AdminUsers=model('AdminUsers');
    		$post['salt']=getRandomString(6);
    		$post['password']=md5($post['password'].$post['salt']);
    		$AdminUsers->data($post)->save();
			return $this->show();
    	}else{
    		return $this->console($validate->getError());
    	}
    }
    function edit_user(){
		return view();
    }
    function edit_user_submit(){
    	$post=$this->server['post'];
    	$post['password']=trim($post['password']);
    	$validate=validate('AdminUsers');
    	$AdminUsers=model('AdminUsers');
    	if($post['password']){
			$result = $validate->scene('add')->check($post);
			$user=$AdminUsers->getone($post['id']);
    		$post['salt']=$user['salt'];
			$post['password']=md5($post['password'].$post['salt']);
    	}else{
    		unset($post['password']);
    		$result = $validate->scene('edit')->check($post);
    	}
    	if($result){
    		$AdminUsers->save($post,['id'=>$result['id']]);
			return $this->show();
    	}else{
    		return $this->console($validate->getError());
    	}
    }
    function edit_user_data(){
    	$AdminUsers=model('AdminUsers');
    	$post=$this->server['post'];
    	$data=$AdminUsers->getInfo($post['id']);
    	return $this->show($data);
    }
    function user_list_count(){
    	$AdminUsers=model('AdminUsers');
    	$data=$AdminUsers->getCount();
    	return $this->show($data);
    }
    function user_list($page=1,$pagesize=10){	
    	$AdminUsers=model('AdminUsers');
    	$data=$AdminUsers->getList($page,$pagesize);
    	return $this->show($data);
    }
    // function login(){
    // 	return view();
    // }

}
