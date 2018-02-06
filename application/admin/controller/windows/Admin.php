<?php
namespace app\admin\controller\windows;
use think\Controller;
class Admin extends Controller{
    function login(){
    	return view();
    }
    function login_submit(){
    	$post=$this->server['post'];
    	$validate=validate('AdminUsers');
    	$result = $validate->scene('login')->check($post);
    	if($result){
    		$server=$this->server['server'];
    		$AdminUsers=model('AdminUsers');
    		if($user=$AdminUsers->login($post["username"],$post["password"],$server['REMOTE_ADDR'])){
    			$session['uid']=$user['id'];
    			$session['login_time']=time();
    			session('admin_user', $session);
    			return $this->show();
    		}
    		return $this->console('','用户名密码不正确');
    	}else{
    		return $this->console('',$validate->getError());
    	}
    }
}
