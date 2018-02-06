<?php
namespace app\common\controller;
use app\common\controller\Backend;
/**
 * 后台控制器基类
 */
class Account extends Backend{
    function _initialize(){
        parent::_initialize();
        $session=$this->server['session'];
        if(!$session['weixin_account']){
            if($this->request->isAjax()){
                $data['data']='';
                $data['message']='选择公众号';
                $data['status']='-101';
                echo json_encode($data,JSON_UNESCAPED_UNICODE);die;
            }else{
                echo '<script>top.chooseAccount();</script>';die;
            }
        }
        
    }
}
