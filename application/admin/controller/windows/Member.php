<?php
namespace app\admin\controller\windows;
use app\common\controller\Account;
use think\Weixin;
class Member extends Account{
    function index(){
        return view();
    }
    function member_group(){
        return view();
    }
    function member_group_user(){
        return view();
    }
    function synchMember(){
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $WeixinUsers=model('WeixinUsers');
        $Weixin=new Weixin($account_id);
        $member=$Weixin->getUserlist();
        $sqlpre="insert ignore  into ".tablename("weixin_users")."(openid,account_id,subscribe) values";
        $openids="";
        foreach($member['data']['openid'] as $v){
            $openids.="('".$v."','".$account_id."',1),";
        }
        $openids=trim($openids,',');
        while($member['next_openid']){
            if($openids){
                $WeixinUsers->execute($sqlpre.$openids);
            }
            $member=$Weixin->getUserlist($member['next_openid']);
            $openids="";
            foreach($member['data']['openid'] as $v){
                $openids.="('".$v."','".$account_id."',1),";
            }
            $openids=trim($openids,',');
        }
        if($openids){
            $WeixinUsers->execute($sqlpre.$openids);
        }
        return $this->show();
    }
    function member_group_user_count($member_group=0){
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $member_group=intval($member_group);
        $WeixinUsers=model('WeixinUsers');
        $data=$WeixinUsers->where('account_id',$account_id)->where('groupid',$member_group)->column('count(id)')[0];
        return $this->show($data);
    }
    function member_group_user_list($member_group=0,$page=1,$pagesize=10){    
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $member_group=intval($member_group);
        $WeixinUsers=model('WeixinUsers');
        $data=$WeixinUsers->where('account_id',$account_id)->where('groupid',$member_group)->limit(getLimit($page,$pagesize))->select();
        return $this->show($data);
    }
    function member_group_data(){
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $Weixin=new Weixin($account_id);
        $userGroups=$Weixin->getUserGroups()['groups'];
        return $this->show($userGroups);
    }
    function member_group_add(){
        return view();
    }
    function member_group_add_submit(){
        $post=$this->server['post'];
        $post['groupname']=trim($post['groupname']);
        if($post['groupname']){
            $session=$this->server['session'];
            $account_id=intval($session['weixin_account']);
            $Weixin=new Weixin($account_id);
            $result=$Weixin->createUserGroup($post['groupname']);
            if($result['group']){
                return $this->show();
            }
        }
        return $this->console();
    }
    function member_group_edit(){
        return view();
    }
    function member_group_edit_data(){
        $post=$this->server['post'];
        if($post['groupid']==intval($post['groupid'])){
            $post['groupid']=intval($post['groupid']);
            $session=$this->server['session'];
            $account_id=intval($session['weixin_account']);
            $Weixin=new Weixin($account_id);
            $userGroups=$Weixin->getUserGroups()['groups'];
            foreach($userGroups as $v){
                $userGroup[$v['id']]=$v['name'];
            }
            if(isset($userGroup[$post['groupid']])){
                $data['id']=$post['groupid'];
                $data['name']=$userGroup[$post['groupid']];
                return $this->show($data);
            }
        }
        return $this->console('','分组不存在');
    }
    function member_group_edit_submit(){
        $post=$this->server['post'];
        $post['groupname']=trim($post['groupname']);
        $post['id']=intval($post['id']);
        if($post['groupname']){
            $session=$this->server['session'];
            $account_id=intval($session['weixin_account']);
            $Weixin=new Weixin($account_id);
            $result=$Weixin->setUserGroupName($post['id'],$post['groupname']);
            if($result['errcode']==0){
                return $this->show();
            }
            return $this->console('',$result['errmsg']);
        }
        return $this->console('','名称不能为空');
    }
    function member_group_delete(){
        $post=$this->server['post'];
        $post['id']=intval($post['id']);
        if($post['id']==intval($post['id'])){
            $post['id']=intval($post['id']);
            $session=$this->server['session'];
            $account_id=intval($session['weixin_account']);
            $Weixin=new Weixin($account_id);
            $result=$Weixin->deleteUserGroupName($post['id']);
            if($result['errcode']==0){
                return $this->show();
            }
            return $this->console('',$result['errmsg']);
        }
        return $this->console('','分组不存在');
    }
    function member_user(){
        return view();
    }
    function member_user_count(){
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $WeixinUsers=model('WeixinUsers');
        $data=$WeixinUsers->where('account_id',$account_id)->column('count(id)')[0];
        return $this->show($data);
    }
    function member_user_list($page=1,$pagesize=10){    
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $WeixinUsers=model('WeixinUsers');
        $data=$WeixinUsers->where('account_id',$account_id)->limit(getLimit($page,$pagesize))->select();
        foreach ($data as &$value) {
            $value['nickname']=base64_decode($value['nickname']);
        }
        return $this->show($data);
    }
    function member_edit(){
        return view();
    }
    function member_edit_data(){
        $post=$this->server['post'];
        $post['member_id']=trim($post['member_id']);
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        if($account_id>0&&$post['member_id']){
            $Weixin=new Weixin($account_id);
            $WeixinUsers=model('WeixinUsers');
            $data=$WeixinUsers->where('account_id',$account_id)->where("openid",$post['member_id'])->find();
            if(!$data['get_full']){
                $user=$Weixin->getuserinfo($data['openid']);
                $userinfo['groupid']=$Weixin->getUserGroupId($data['openid'])['groupid'];
                $userinfo['gender']=$user['sex'];
                $userinfo['nickname']=base64_encode($user['nickname']);
                $userinfo['avatar']=$user['headimgurl'];
                $userinfo['remark']=$user['remark'];
                $userinfo['country']=$user['country'];
                $userinfo['province']=$user['province'];
                $userinfo['city']=$user['city'];
                $userinfo['subscribe_time']=$user['subscribe_time'];
                $userinfo['get_full']=1;
                $WeixinUsers->save($userinfo,[
                    'account_id'=>$account_id,
                    "openid"=>$user['openid'],
                ]);
                $data=$WeixinUsers->where('account_id',$account_id)->where("openid",$user['openid'])->find();
            }
            $data['nickname']=base64_decode($data['nickname']);
            $data['groupdata']=$Weixin->getUserGroups()['groups'];
            return $this->show($data);
        }
        return $this->console();
    }
    function member_edit_submit(){
        $session=$this->server['session'];
        $account_id=intval($session['weixin_account']);
        $post=$this->server['post'];
        $post['remark']=trim($post['remark']);
        $post['gender']=max(min(intval($post['gender']),2),0);
        $post['openid']=trim($post['openid']);
        $post['groupid']=intval($post['groupid']);
        $post['nickname']=base64_encode(trim($post['nickname']));
        $WeixinUsers=model('WeixinUsers');
        if($data=$WeixinUsers->where('account_id',$account_id)->where("openid",$post['openid'])->find()){
            $Weixin=new Weixin($account_id);
            if($post['remark']!=$data['remark']){
                $Weixin->setRemark($data['openid'],$post['remark']);
            }
            if($post['groupid']!=$data['groupid']){
                $Weixin->setUserGroupId($data['openid'],$post['groupid']);
            }
            if($WeixinUsers->save($post,[
                'account_id'=>$account_id,
                "openid"=>$data['openid'],
            ])){
                return $this->show();
            }
        }
        return $this->console();
    }
}
