<?php
namespace app\admin\controller\windows;
use app\common\controller\Backend;
use think\Weixin;
class Tools extends Account{
    function short_url(){
        return view();
    }
    function short_url_submit(){
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        if($account_id>0){
            $post=$this->server['post'];
            $post['longurl']=trim($post['longurl']);
            $Weixin=new Weixin($account_id);
            $data=$Weixin->getShortUrl($post['longurl']);
            if($data['errcode']==0){
                return $this->show($data['short_url']);
            }
        }
        return $this->console();
    }

}
