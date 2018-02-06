<?php

namespace app\common\controller;
use think\Controller;
use think\Session;
/**
 * 后台控制器基类
 */
class Backend extends Controller{
    function _initialize(){
        parent::_initialize();
        if(!self::check_login()){
            if($this->request->isAjax()){
                $data['data']='';
                $data['message']='登录失效';
                $data['status']='-1';
                echo json_encode($data,JSON_UNESCAPED_UNICODE);die;
            }else{
                echo '<script>top.location.href="/windows/Admin/login";</script>';die;
            }
        }
    }
    function check_login(){
        $session=$this->server['session'];
        $server=$this->server['server'];
        if($session['admin_user']['uid']&&$server['REMOTE_ADDR']){
            $admin_user=model("AdminUsers"); 
            if($admin_user->check_login($session['admin_user']['uid'],$server['REMOTE_ADDR'])){
                return true;
            }
        }
        return false;
    }
}
