<?php
namespace think;
class Weixin{
	/**
	 * AppID 			appid
	 * AppSecret 		AppSecret
	 * token 			token
	 * MchID 			商户号
	 * key 				商户MD5密钥
	 * apiclient_cert 	商户密钥文件apiclient_cert.pem路径
	 * apiclient_key 	商户密钥文件apiclient_key.pem路径
	 * rootca 			商户密钥文件rootca.pem路径
	 * wap_name 		商户Wap名称
	 */
	function __construct($account_id=0){
		$this->account_id=intval($account_id);
		$this->account=Db::name('weixin_account');
		$weixinConfig=$this->account->where('id',$this->account_id)->find();
		$this->AppID=$weixinConfig['appid'];
        $this->MchID=$weixinMerchConfig['MchID'];
        $this->key=$weixinMerchConfig['key'];
        $this->AppSecret=$weixinConfig['appsecret'];
        $this->apiclient_cert=$weixinMerchConfig['apiclient_cert'];
        $this->apiclient_key=$weixinMerchConfig['apiclient_key'];
        $this->rootca=$weixinMerchConfig['rootca'];
		$this->token=$weixinConfig['token'];
		$this->wap_name='';
	}
	/**
	 * 	更新参数
	 */
	function updateArgument($var,$value=''){
		if($this->$var){$this->$var=$value;}
	}
	/**
	 * url array互转
	 */
	function arrayurl($data){
		if(is_array($data)){
			$sign='';
			foreach($data as $k=>$v){
				$sign.=$k.'='.$v.'&';
			}
			return trim($sign,'&');
		}
		if(is_string($data)){
			$sign=trim($data,'&');
			$sign=explode('&',$sign);
			foreach($sign as $v){
				$cache=explode('=',$v);
				$result[$cache[0]]=$cache[1];
			}
			return $result;
		}
		return $data;
	}
	/**
	 * 	一维数组转xml
	 * 	$arraydata  传入的数组
	 * 	$rootDom  根标签
	 */
	public function arrayToXml($arraydata,$rootDom='xml'){
		if(!$rootDom){$rootDom='xml';}
    	$xml = '<'.$rootDom.'>';
    	foreach ($arraydata as $key=>$val){
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.='</'.$rootDom.'>';
        return $xml; 
	}
	//验证基础Token
	function verification(){
		$signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode('',$tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			echo $_GET['echostr'];
			return true;
		}
		return false;
	}
	/**
	 * 	curl提交
	 */
	function curlPost($url,$data,$header=array(),$sllkey=false){
        $curl=curl_init();
        if($header&&$header!=array()){
            curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
        }
        if($sllkey){
        	curl_setopt($curl,CURLOPT_SSLCERT,$this->apiclient_cert);
        	curl_setopt($curl,CURLOPT_SSLKEY,$this->apiclient_key);
        	curl_setopt($curl,CURLOPT_CAINFO,$this->rootca);
        }
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        $result=curl_exec($curl);
        curl_close($curl);
        return $result;
    }
	/**
	 * 	获取消息信息
	 */
	function getMessageinfo(){
		return json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 *  回复文本消息
	 */
	function textMsg($content='',$ToUserName=false){
		$data=$this->getMessageinfo();
		if($ToUserName){
			$data['FromUserName']=$ToUserName;
		}
		$textTpl = "<xml>
			<ToUserName><![CDATA[".$data['FromUserName']."]]></ToUserName>
			<FromUserName><![CDATA[".$data['ToUserName']."]]></FromUserName>
			<CreateTime>".time()."</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[{$content}]]></Content>
		</xml>";
		echo  $textTpl;
	}
	/**
	 * 回复图文消息
	 */
	function newsMsg($content,$ToUserName=false){
		$data=$this->getMessageinfo();
		if($ToUserName){
			$data['FromUserName']=$ToUserName;
		}
		$count=count($content);
		if($count>8){$count=8;}
		if($count<1){return false;}
		$Articles='';$i=1;
		foreach($content as $v){
			$Articles.='<item>
				<Title><![CDATA['.$v['title'].']]></Title> 
				<Description><![CDATA['.$v['description'].']]></Description>
				<PicUrl><![CDATA['.$v['picurl'].']]></PicUrl>
				<Url><![CDATA['.$v['url'].']]></Url>
			</item>';
			if($i==$count){
				break;
			}
			$i++;
		}
		$textTpl = "<xml>
			<ToUserName><![CDATA[".$data['FromUserName']."]]></ToUserName>
			<FromUserName><![CDATA[".$data['ToUserName']."]]></FromUserName>
			<CreateTime>".time()."</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>".$count."</ArticleCount>
			<Articles>".$Articles."</Articles>
		</xml>";
		echo  $textTpl;
	}
	/**
	 * 	获取accesstoken
	 */
	function getAccesstoken(){
		//-------------------------获取缓存中的access_token为$this->access_token-----
		$account=$this->account->where('id',$this->account_id)->find();
		if($account['access_token_overdue_time']>time()){
			$this->access_token=json_decode($account['access_token'],true);
		}
		//-------------------------------------------------------
		if(!@$this->access_token){
			$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->AppID.'&secret='.$this->AppSecret;
			$this->access_token=json_decode(file_get_contents($url),true);
			//------------------------保存 $this->access_token 为缓存----------------
			if($this->access_token['access_token']){
				$data['access_token']=json_encode($this->access_token);
				$data['access_token_overdue_time']=time()+4000;
				$this->account->where('id',$this->account_id)->update($data);
			}
			//------------------------保存 $this->access_token 为缓存----------------
		}
		return $this->access_token;
	}
	/**
	 * 	获取微信服务器ip
	 */
	function getweixinip(){
		$url='https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$this->getAccesstoken()['access_token'];
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	发送文本消息
	 */
	function toTextMsg($user,$content=''){
		$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccesstoken()['access_token'];
		$data['touser']=$user;
		$data['msgtype']='text';
		$data['text']['content']=$content;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	发送图文消息
	 */
	function toNewsMsg($user,$content){
		$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccesstoken()['access_token'];
		$data['touser']=$user;
		$data['msgtype']='news';
		foreach($content as $v){
			$arr[]=$v;
		}
		$data['news']['articles']=$arr;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	发送模板消息
	 * 	$user   用户openid
	 * 	$template_id    模板id
	 * 	$redirect    模板链接
	 * 	$content    数据
	 * 	$color    高粱颜色
	 */
	function templateMsg($user,$template_id,$redirect,$content,$color='#21B4E8'){
		$url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->getAccesstoken()['access_token'];
		$data['touser']=$user;
		$data['template_id']=$template_id;
		$data['url']=$redirect;
		foreach($content as $k=>$v){
			$cache[$k]['value']=$v;
			$cache[$k]['color']=$color;
		}
		$data['data']=$cache;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	一次性订阅消息授权
	 * 	$template_id    	模板id
	 * 	$redirect_url    	回调地址
	 * 	$scene    			订阅场景值
	 */
	function subscribeMsg($template_id,$redirect_url,$scene){
		$url='https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid='.$this->AppID.'&scene='.$scene.'&template_id='.$template_id.'&$redirect_url='.urlencode($redirect_url).'#wechat_redirect';
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	一次性订阅消息发送
	 * 	$user   			用户openid
	 * 	$template_id    	模板id
	 * 	$redirect    		模板链接
	 * 	$scene    			订阅场景值
	 * 	$content    		内容
	 */
	function subscribeMsgSend($user,$template_id,$title,$content,$redirect=false,$scene,$appid=false,$pagepath=false,$miniprogram=false){
		$url='https://api.weixin.qq.com/cgi-bin/message/template/subscribe?access_token='.$this->getAccesstoken()['access_token'];
		$data['touser']=$user;
		$data['template_id']=$template_id;
		if($redirect){$data['url']=$redirect;}
		if($appid){$data['appid']=$appid;}
		if($pagepath){$data['pagepath']=$pagepath;}
		if($miniprogram){$data['miniprogram']=$miniprogram;}
		$data['scene']=$scene;
		$data['title']=$title;
		$data['data']=$content;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	消息群发
	 * 	$openids   用户列表
	 * 	$content   内容
	 */
	function multiMsg($openids,$content=''){
		$url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->getAccesstoken()['access_token'];
		$data['msgtype']='text';
		$data['text']['content']=$content;
		foreach($openids as $v){
			$data['touser'][]=$v;
		}
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	获取用户列表
	 * 	$next_openid    从此用户之后查询,(空为从头查询)
	 */
	function getUserlist($next_openid=''){
		$url='https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->getAccesstoken()['access_token'].'&next_openid='.$next_openid;
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	获取用户信息
	 */
	function getuserinfo($openid){
		$url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->getAccesstoken()['access_token'].'&openid='.$openid.'&lang=zh_CN';
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	设置用户备注
	 */
	function setRemark($openid,$remark=''){
		$url='https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token='.$this->getAccesstoken()['access_token'];
		$data['openid']=$openid;
		$data['remark']=$remark;
		return json_decode($this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE)),true);
	}
	/**
	 * 	获取用户所在分组
	 */
	function getUserGroupId($openid=''){
		$url='https://api.weixin.qq.com/cgi-bin/groups/getid?access_token='.$this->getAccesstoken()['access_token'];
		$data['openid']=trim($openid);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	修改用户所在分组
	 */
	function setUserGroupId($openid='',$to_groupid=0){
		$url='https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token='.$this->getAccesstoken()['access_token'];
		$data['openid']=trim($openid);
		$data['to_groupid']=intval($to_groupid);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	获取用户分组
	 */
	function getUserGroups(){
		$url='https://api.weixin.qq.com/cgi-bin/groups/get?access_token='.$this->getAccesstoken()['access_token'];
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	创建用户分组
	 */
	function createUserGroup($name=''){
		$url='https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$this->getAccesstoken()['access_token'];
		$data['group']['name']=trim($name);
		return json_decode($this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE)),true);
	}
	/**
	 * 	修改用户分组名称
	 */
	function setUserGroupName($group_id,$name=''){
		$url='https://api.weixin.qq.com/cgi-bin/groups/update?access_token='.$this->getAccesstoken()['access_token'];
		$data['group']['id']=intval($group_id);
		$data['group']['name']=trim($name);
		return json_decode($this->curlPost($url,json_encode($data,JSON_UNESCAPED_UNICODE)),true);
	}
	/**
	 * 	删除用户分组
	 */
	function deleteUserGroupName($group_id){
		$url='https://api.weixin.qq.com/cgi-bin/groups/delete?access_token='.$this->getAccesstoken()['access_token'];
		$data['group']['id']=intval($group_id);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	语音识别
	 */
	function voiceRecognition(){
		return $thisg->getMessageinfo()['Recognition'];
	}
	function getMenuJson($menu1){
		if(!@$menu1['son']){
			$menuA=$menu1;
			if(@$menu1['key']){$menuA['type']='click';}
			if(@$menu1['url']){$menuA['type']='view';}
		}else{
			@$menuA['name']=$menu1['name'];
			foreach($menu1['son'] as $v){
				$cache=$v;
				if(@$v['key']){$cache['type']='click';}
				if(@$v['url']){$cache['type']='view';}
				@$menuA['sub_button'][]=$cache;
			}
		}
		return $menuA;
	}
	/**
	 * 	发布自定义菜单
	 */
	function Menu($menu1,$menu2=false,$menu3=false){
		$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccesstoken()['access_token'];
		$menuA=$this->getMenuJson($menu1);
		$data['button'][]=$menuA;
		if($menu2){
			$menuB=$this->getMenuJson($menu2);
			$data['button'][]=$menuB;
		}
		if($menu3){
			$menuC=$this->getMenuJson($menu3);
			$data['button'][]=$menuC;
		}
		return $this->curlPost($url, str_replace("\\/", "/", json_encode($data, JSON_UNESCAPED_UNICODE)));
	}
	/**
	 * 	获取自定义菜单数据
	 */
	function getMenu(){
		$url='https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->getAccesstoken()['access_token'];
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	删除自定义菜单(删除全部)
	 */
	function deleteMenu(){
		$url='https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->getAccesstoken()['access_token'];
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	按组发布个性化菜单
	 */
	function MenuByGroup($group_id,$menu1,$menu2=false,$menu3=false){
		$url='https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token='.$this->getAccesstoken()['access_token'];
		$menuA=$this->getMenuJson($menu1);
		$data['button'][]=$menuA;
		if($menu2){
			$menuB=$this->getMenuJson($menu2);
			$data['button'][]=$menuB;
		}
		if($menu3){
			$menuC=$this->getMenuJson($menu3);
			$data['button'][]=$menuC;
		}
		$data['matchrule']['group_id']=$group_id;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	按性别发布个性化菜单
	 * 	$sex 性别    1 :  男      2 :  女
	 */
	function MenuBySex($sex=1,$menu1,$menu2=false,$menu3=false){
		$url='https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token='.$this->getAccesstoken()['access_token'];
		$menuA=$this->getMenuJson($menu1);
		$data['button'][]=$menuA;
		if($menu2){
			$menuB=$this->getMenuJson($menu2);
			$data['button'][]=$menuB;
		}
		if($menu3){
			$menuC=$this->getMenuJson($menu3);
			$data['button'][]=$menuC;
		}
		$data['matchrule']['sex']=$sex;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	删除个性化菜单
	 *  $menuid   菜单id
	 */
	function deleteMenuGroup($menuid){
		$url='https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token='.$this->getAccesstoken()['access_token'];
		$data['menuid']=$menuid;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	查询用户正在使用的菜单
	 */
	function MenuForUser($openid){
		$url='https://api.weixin.qq.com/cgi-bin/menu/trymatch?access_token='.$this->getAccesstoken()['access_token'];
		$data['user_id']=$openid;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	发送图片消息
	 */
	function toImageMsg($user,$media_id){
		$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccesstoken()['access_token'];
		$data['touser']=$user;
		$data['msgtype']='image';
		$data['image']['media_id']=$media_id;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	回复图片消息
	 * 	$media_id   资源id
	 */
	function imageMsg($media_id='',$ToUserName=false){
		$data=$this->getMessageinfo();
		if($ToUserName){
			$data['FromUserName']=$ToUserName;
		}
		$textTpl = "<xml>
			<ToUserName><![CDATA[".$data['FromUserName']."]]></ToUserName>
			<FromUserName><![CDATA[".$data['ToUserName']."]]></FromUserName>
			<CreateTime>".time()."</CreateTime>
			<MsgType><![CDATA[image]]></MsgType>
			<Image>
				<MediaId><![CDATA[{$media_id}]]></MediaId>
			</Image>
		</xml>";
		echo  $textTpl;
	}
	/**
	 * 	获取用户地理位置
	 */
	function getUserLocation(){
		$message=$this->getMessageinfo();
		$result['Event']=$message['Event'];
		$result['user']=$message['FromUserName'];
		$result['Latitude']=$message['Latitude'];
		$result['Longitude']=$message['Longitude'];
		$result['Precision']=$message['Precision'];
		return $result;
	}
	/**
	 * 	发送语音消息
	 */
	function toVoiceMsg($user,$media_id){
		$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccesstoken()['access_token'];
		$data['touser']=$user;
		$data['msgtype']='voice';
		$data['voice']['media_id']=$media_id;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	回复语音消息
	 */
	function voiceMsg($media_id='',$ToUserName=false){
		$data=$this->getMessageinfo();
		if($ToUserName){
			$data['FromUserName']=$ToUserName;
		}
		$textTpl = "<xml>
			<ToUserName><![CDATA[".$data['FromUserName']."]]></ToUserName>
			<FromUserName><![CDATA[".$data['ToUserName']."]]></FromUserName>
			<CreateTime>".time()."</CreateTime>
			<MsgType><![CDATA[voice]]></MsgType>
			<Voice>
				<MediaId><![CDATA[{$media_id}]]></MediaId>
			</Voice>
		</xml>";
		echo  $textTpl;
	}
	/**
	 * 	分页获取永久素材列表
	 * 	$type 素材类型  图片（image）、视频（video）、语音 （voice）、图文（news）
	 * 	$offset 该页起始位置
	 * 	$count  每页条数
	 */
	function getMaterialList($type='image',$offset=0,$count=20){
		$url='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getAccesstoken()['access_token'];
		$data['type']=$type;
		$data['offset']=$offset;
		$data['count']=$count;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}

	/* 新增永久文件接口，不包括图文 */
	function newMaterial($type, $params)
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='. $this->getAccesstoken()['access_token']. '&type='. $type;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$response = json_decode(curl_exec($curl));
		return $response;
	}
	/**
	 * 	获取永久素材总数
	 */
	function getMaterialCount(){
		$url='https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token='.$this->getAccesstoken()['access_token'];
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	转储临时素材
	 * 	$media_id 素材id
	 * 	$file  保存的文件名(无则跳转至下载)
	 */
	function getTemporaryMaterial($media_id,$file=false){
		$url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccesstoken()['access_token'].'&media_id='.$media_id;
		if($file){
			file_put_contents($file,file_get_contents($url));
		}else{
			header('location:'.$url);
		} 
	} 
	/**
	 * 	删除永久素材
	 */
	function deletePerpetualMaterial($media_id){
		$url='https://api.weixin.qq.com/cgi-bin/material/del_material?access_token='.$this->getAccesstoken()['access_token'];
		$data['media_id']=$media_id;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 获取设备分组
	 */
	function getDeviceGroups($page=1,$pagesize=10){
		$url='https://api.weixin.qq.com/shakearound/device/group/getlist?access_token='.$this->getAccesstoken()['access_token'];
		$page=max(1,$page);
        $pagesize=min(max(1,$pagesize),1000);
        $data['begin']=($page-1)*$pagesize;
        $data['count']=$pagesize;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 获取设备分组详情
	 */
	function getDeviceGroupDetail($group_id,$page=1,$pagesize=10){
		$url='https://api.weixin.qq.com/shakearound/device/group/getdetail?access_token='.$this->getAccesstoken()['access_token'];
		$page=max(1,$page);
        $pagesize=min(max(1,$pagesize),1000);
        $data['group_id']=intval($group_id);
        $data['begin']=($page-1)*$pagesize;
        $data['count']=$pagesize;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 创建设备分组
	 */
	function createDeviceGroup($group_name=''){
		$url='https://api.weixin.qq.com/shakearound/device/group/add?access_token='.$this->getAccesstoken()['access_token'];
        $data['group_name']=trim($group_name);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 修改设备分组名称
	 */
	function setDeviceGroupName($group_id,$group_name=''){
		$url='https://api.weixin.qq.com/shakearound/device/group/update?access_token='.$this->getAccesstoken()['access_token'];
        $data['group_id']=intval($group_id);
        $data['group_name']=trim($group_name);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 删除设备分组
	 */
	function deleteDeviceGroup($group_id){
		$url='https://api.weixin.qq.com/shakearound/device/group/delete?access_token='.$this->getAccesstoken()['access_token'];
        $data['group_id']=intval($group_id);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 设置设备所在分组(单)
	 */
	function setDeviceGroup($group_id,$device_id=false,$uuid=false,$major=false,$minor=false){
		$url='https://api.weixin.qq.com/shakearound/device/group/adddevice?access_token='.$this->getAccesstoken()['access_token'];
        $data['group_id']=intval($group_id);
        if(trim($device_id)){$device_identifiers['device_id']=trim($device_id);}
        if(trim($uuid)){$device_identifiers['uuid']=trim($uuid);}
        if(trim($major)){$device_identifiers['major']=trim($major);}
        if(trim($minor)){$device_identifiers['minor']=trim($minor);}
        $data['device_identifiers']=[$device_identifiers];
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 设置设备所在分组(多)
	 */
	function deviceGroup($device_id=false,$uuid=false,$major=false,$minor=false){
        if(trim($device_id)){$device_identifiers['device_id']=trim($device_id);}
        if(trim($uuid)){$device_identifiers['uuid']=trim($uuid);}
        if(trim($major)){$device_identifiers['major']=trim($major);}
        if(trim($minor)){$device_identifiers['minor']=trim($minor);}
        if($device_identifiers){
        	$this->device_identifiers[]=$device_identifiers;
        }
        return $this;
	}
	function setDeviceGroups($group_id){
		$url='https://api.weixin.qq.com/shakearound/device/group/adddevice?access_token='.$this->getAccesstoken()['access_token'];
		$data['group_id']=intval($group_id);
		$data['device_identifiers']=$this->device_identifiers;
		$this->device_identifiers=[];
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	生成短链接
	 */
	function getShortUrl($long_url=''){
		$url='https://api.weixin.qq.com/cgi-bin/shorturl?access_token='.$this->getAccesstoken()['access_token'];
		$data['action']='long2short';
		$data['long_url']=trim($long_url);
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	语义理解
	 * 	$query  关键词
	 * 	$city   城市
	 * 	$category   类型
	 * 	$uid   用户openid
	 */
	function autoQuery($query,$city,$category,$uid=false){
		$url='https://api.weixin.qq.com/semantic/semproxy/search?access_token='.$this->getAccesstoken()['access_token'];
		$data['query']=$query;
		$data['category']=$category;
		$data['city']=$city;
		$data['appid']=$this->AppID;
		if($uid){
			$data['uid']=$uid;
		}
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	获取新增用户分析数据
	 */
	function getUserSummary($begin_date,$end_date=false){
		$url='https://api.weixin.qq.com/datacube/getusersummary?access_token='.$this->getAccesstoken()['access_token'];
		$data['begin_date']=date('Y-m-d',$begin_date);
		if(!$end_date){
			$end_date=date('Y-m-d',strtotime($data['begin_date'])+6*24*3600);
		}else{
			$end_date=date('Y-m-d',$end_date);
		}
		$data['end_date']=$end_date;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	获取用户总量分析数据
	 */
	function getUserCumulate($begin_date,$end_date=false){
		$url='https://api.weixin.qq.com/datacube/getusercumulate?access_token='.$this->getAccesstoken()['access_token'];
		$data['begin_date']=date('Y-m-d',$begin_date);
		if(!$end_date){
			$end_date=date('Y-m-d',strtotime($data['begin_date'])+6*24*3600);
		}else{
			$end_date=date('Y-m-d',$end_date);
		}
		$data['end_date']=$end_date;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 下载媒体文件
	 */
	function downloadMedia($media_id,$file=false){
		$url='http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccesstoken()['access_token'].'&media_id='.$media_id;
		if($file){
			file_put_contents($file,file_get_contents($url));
		}else{
			header('location:'.$url);
		}
	}
	/**
	 * 	获取接口使用分析数据
	 */
	function getInterfaceSummary($begin_date,$end_date=false){
		$url='https://api.weixin.qq.com/datacube/getinterfacesummary?access_token='.$this->getAccesstoken()['access_token'];
		$data['begin_date']=date('Y-m-d',$begin_date);
		if(!$end_date){
			$end_date=date('Y-m-d',strtotime($data['begin_date'])+6*24*3600);
		}else{
			$end_date=date('Y-m-d',$end_date);
		}
		$data['end_date']=$end_date;
		return json_decode($this->curlPost($url,json_encode($data)),true);
	}
	/**
	 * 	扫码登录
	 * 	$redirect_uri  确认登录回调地址
	 */
	function qrcodeLogin($redirect_uri,$state=1){
		$url='https://open.weixin.qq.com/connect/qrconnect?appid='.$this->AppID.'&redirect_uri='.$redirect_uri.'&scope=snsapi_login&state='.$state;
		header('location:'.$url);
	}
	/**
	 * 	静默授权登录
	 */
	function quietLogin($redirect_uri,$state=1){
		$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->AppID.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state='.$state.'#wechat_redirect';
		header('location:'.$url);
	}
	/**
	 * 	确认式授权登录
	 */
	function confirmLogin($redirect_uri,$state=1){
		$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->AppID.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
		header('location:'.$url);
	}
	/**
	 * 	获取用户access_token
	 */
	function getUserToken($code){
		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->AppID.'&secret='.$this->AppSecret.'&code='.$code.'&grant_type=authorization_code';
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	获取用户信息
	 * 	$access_token  获取的用户access_token
	 */
	function getWebUserinfo($access_token,$openid){
		$url="https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}";
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	刷新用户的access_token
	 * 	$refresh_token   获取的refresh_token
	 */
	function refreshToken($refresh_token){
		$url='https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->AppID.'&grant_type=refresh_token&refresh_token='.$refresh_token;
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	检测获取的用户access_token是否还有效
	 */
	function detectionToken($access_token,$openid){
		$url='https://api.weixin.qq.com/sns/auth?access_token='.$access_token.'&openid='.$openid;
		return json_decode(file_get_contents($url),true);
	}
	/**
	 * 	生成随机字符串
	 * 	$length	随机字符串
	 * 	$onlyNum	是否仅数字
	 */
	function getRandomString($length=4,$onlyNum=false){
		$number=range(0,9);
		$letterChar=range('A','Z');
		$lowerChar=range('a','z');
		if(!$onlyNum){
			$result=array_merge_recursive($number,$lowerChar,$letterChar);
		}else{
			$result=$number;
		}
		$maxKey=count($result)-1;
		$data='';
		for($i=1;$i<=$length;$i++){
			$key=rand(0,$maxKey);
			$data.=$result[$key];
		}
		return $data;
	}
	/**
	 * 	获取当前URL
	 */
	function getThisUrl(){
		if(!$_SERVER['REQUEST_SCHEME']){
			if($_SERVER['SERVER_PROTOCOL']=='HTTP/1.1'){
				$_SERVER['REQUEST_SCHEME']='http';
			}elseif($_SERVER['HTTPS']=='on'){
				$_SERVER['REQUEST_SCHEME']='https';
			}
		}
		return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}

	/* 获取js所有参数 */
	function getJsApiTicket(){
		
		
		/* 第一步：getJsApiTicket */
		//-------------------------获取缓存中的jsapi_ticket为$this->jsapi_ticket-----
		$account=$this->account->where('id',$this->account_id)->find();
		if($account['jsapi_ticket_overdue_time']>time()){
			$this->jsapi_ticket=json_decode($account['jsapi_ticket'],true);
		}
		//-------------------------------------------------------
		if(!@$this->jsapi_ticket){
			$url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->getAccesstoken()['access_token'].'&type=jsapi';
			$this->jsapi_ticket=json_decode(file_get_contents($url),true);
			//------------------------保存 $this->jsapi_ticket 为缓存----------------
			if($this->jsapi_ticket['ticket']){
				$data['jsapi_ticket']=json_encode($this->jsapi_ticket);
				$data['jsapi_ticket_overdue_time']=time()+4000;
				$this->account->where('id',$this->account_id)->update($data);
			}
			//------------------------保存 $this->jsapi_ticket 为缓存----------------
		}

		$jsapi_ticket = $this->jsapi_ticket['ticket'];

		/* 第二步：获取jsapi_config */
		$data['noncestr']=$this->getRandomString(16);
		$data['jsapi_ticket']=$jsapi_ticket;
		$data['url']=$this->getThisUrl();
		$data['timestamp']=time();
		ksort($data);
		$url='';
		foreach($data as $k=>$v){
			$url.=$k.'='.$v.'&';
		}
		$data['signature']=sha1(trim($url,'&'));
		$data['appid']=$this->AppID;

		return $data;
	}
	/* 获取jsapiconfig，已废弃 */
	function getJsApiConfig($jsapi_ticket){
		$data['noncestr']=$this->getRandomString(16);
		$data['jsapi_ticket']=$jsapi_ticket;
		$data['url']=$this->getThisUrl();
		$data['timestamp']=time();
		ksort($data);
		$url='';
		foreach($data as $k=>$v){
			$url.=$k.'='.$v.'&';
		}
		$data['signature']=sha1(trim($url,'&'));
		$data['appid']=$this->AppID;
		return $data;
	}
	/**
	 * 	获取支付所需数据
	 * 	$out_trade_no 	 订单号
	 * 	$body 	 商品描述
	 * 	$total_fee 	 商品金额
	 * 	$notify_url 	 付款回调地址 (回调数据以HTTP_RAW_POST_DATA格式传递)
	 */
	function getCodeUrl($out_trade_no,$body,$total_fee,$notify_url){
		$url='https://api.mch.weixin.qq.com/pay/unifiedorder';
		if(!$total_fee||$total_fee<0.01){
			$total_fee=1;
		}else{
			$total_fee=$total_fee*100;
		}
		$total_fee=intval($total_fee);
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['body']=$body;
		$data['out_trade_no']=$out_trade_no;
		$data['total_fee']=$total_fee;
		$data['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
		$data['notify_url']=$notify_url;
		$data['trade_type']='NATIVE';
		$data['product_id']=$out_trade_no;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	通过交易单号查询订单信息
	 * 	$transaction_id		获取的transaction_id(交易单号)
	 */
	function getOrderByTransactionId($transaction_id){
		$url='https://api.mch.weixin.qq.com/pay/orderquery';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['transaction_id']=$transaction_id;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	通过订单号查询订单信息
	 * 	$out_trade_no	订单号
	 */
	function getOrderByOutTradeNo($out_trade_no){
		$url='https://api.mch.weixin.qq.com/pay/orderquery';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['out_trade_no']=$out_trade_no;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	关闭订单
	 * 	$out_trade_no   订单号
	 */
	function closeOrder($out_trade_no){
		$url='https://api.mch.weixin.qq.com/pay/closeorder';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['out_trade_no']=$out_trade_no;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 订单退款
	 * $out_trade_no	订单号
	 * $total_fee		订单金额
	 */
	function refundOrder($out_trade_no,$total_fee){
		$url='https://api.mch.weixin.qq.com/secapi/pay/refund';
		if(!$total_fee||$total_fee<0.01){
			$total_fee=1;
		}else{
			$total_fee=$total_fee*100;
		}
		$data['appid']=$this->AppID;
        $data['mch_id']=$this->MchID;
        $data['nonce_str']=$this->getRandomString(16);
        $data['out_trade_no']=$out_trade_no;
        $data['out_refund_no']=$out_trade_no;
        $data['total_fee']=$total_fee;
        $data['refund_fee']=$total_fee;
        ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data,false,true),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	通过交易单号查询退款信息
	 * 	$transaction_id		获取的transaction_id(交易单号)
	 */
	function getRefundByTransactionId($transaction_id){
		$url='https://api.mch.weixin.qq.com/pay/refundquery';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['transaction_id']=$transaction_id;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	通过订单号查询退款信息
	 * 	$out_trade_no		订单号
	 */
	function getRefundByOutTradeNo($out_trade_no){
		$url='https://api.mch.weixin.qq.com/pay/refundquery';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['out_trade_no']=$out_trade_no;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	通过商户退款单号查询退款信息
	 * 	$out_refund_no		商户退款单号
	 */
	function getRefundByOutRefundNo($out_refund_no){
		$url='https://api.mch.weixin.qq.com/pay/refundquery';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['out_refund_no']=$out_refund_no;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
	 * 	通过微信退款单号查询退款信息
	 * 	$refund_id		微信退款单号
	 */
	function getRefundByRefundId($refund_id){
		$url='https://api.mch.weixin.qq.com/pay/refundquery';
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['refund_id']=$refund_id;
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	/**
     * 获取微信小程序支付参数
     */
    function getAppletPayParams($out_trade_no,$body,$total_fee,$notify_url,$openid){
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        if(!$total_fee||$total_fee<0.01){
            $total_fee=1;
        }else{
            $total_fee=$total_fee*100;
        }
        $total_fee=intval($total_fee);
        $data['appid']=$this->AppID;
        $data['mch_id']=$this->MchID;
        $data['nonce_str']=$this->getRandomString(16);
        $data['body']=$body;
        $data['out_trade_no']=$out_trade_no;
        $data['total_fee']=$total_fee;
        $data['openid']=$openid;
        $data['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
        $data['notify_url']=$notify_url;
        $data['trade_type']='JSAPI';
        ksort($data);
        $signStr=$this->arrayurl($data).'&key='.$this->key;
        $data['sign']=strtoupper(md5($signStr));
        $data=$this->arrayToXml($data);
        $result=json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
        $data=array();
        $data['appId']=$this->AppID;
        $data['signType']='MD5';
        $data['package']='prepay_id='.$result['prepay_id'];
        $data['nonceStr']=$this->getRandomString(16);
        $data['timeStamp']=''.time().'';
        ksort($data);
        $signStr=$this->arrayurl($data).'&key='.$this->key;
        $data['paySign']=strtoupper(md5($signStr));
        return $data;
    }
	/**
     * 获取微信app支付参数
     */
    function getAppPayParams($out_trade_no,$body,$total_fee,$notify_url){
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        if(!$total_fee||$total_fee<0.01){
            $total_fee=1;
        }else{
            $total_fee=$total_fee*100;
        }
        $total_fee=intval($total_fee);
        $data['appid']=$this->AppID;
        $data['mch_id']=$this->MchID;
        $data['nonce_str']=$this->getRandomString(16);
        $data['body']=$body;
        $data['out_trade_no']=$out_trade_no;
        $data['total_fee']=$total_fee;
        $data['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
        $data['notify_url']=$notify_url;
        $data['trade_type']='APP';
        $data['product_id']=$out_trade_no;
        ksort($data);
        $signStr=$this->arrayurl($data).'&key='.$this->key;
        $data['sign']=strtoupper(md5($signStr));
        $data=$this->arrayToXml($data);
        $result=json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
        $data=array();
        $data['appid']=$this->AppID;
        $data['partnerid']=$this->MchID;
        $data['prepayid']=$result['prepay_id'];
        $data['package']='Sign=WXPay';
        $data['noncestr']=$this->getRandomString(16);
        $data['timestamp']=''.time().'';
        ksort($data);
        $signStr=$this->arrayurl($data).'&key='.$this->key;
        $data['sign']=strtoupper(md5($signStr));
        return $data;
    }
    /**
     * 获取微信Js支付参数
     */
    function getJsPayParams($out_trade_no,$body,$total_fee,$notify_url,$openid){
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        if(!$total_fee||$total_fee<0.01){
            $total_fee=1;
        }else{
            $total_fee=$total_fee*100;
        }
        $total_fee=intval($total_fee);
        $data['appid']=$this->AppID;
        $data['mch_id']=$this->MchID;
        $data['nonce_str']=$this->getRandomString(16);
        $data['body']=$body;
        $data['out_trade_no']=$out_trade_no;
        $data['openid']=$openid;
        $data['total_fee']=$total_fee;
        $data['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
        $data['notify_url']=$notify_url;
        $data['trade_type']='JSAPI';
        $data['product_id']=$out_trade_no;
        ksort($data);
        $signStr=$this->arrayurl($data).'&key='.$this->key;
        $data['sign']=strtoupper(md5($signStr));
        $data=$this->arrayToXml($data);
        $result=json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
        $data=array();
        $data['appId']=$this->AppID;
        $data['package']='prepay_id='.$result['prepay_id'];
        $data['signType']='MD5';
        $data['nonceStr']=$this->getRandomString(16);
        $data['timeStamp']=''.time().'';
        ksort($data);
        $signStr=$this->arrayurl($data).'&key='.$this->key;
        $data['paySign']=strtoupper(md5($signStr));
        return $data;
    }
    /**
     * 	获取微信H5支付参数
     */
    function getH5PayParams($out_trade_no,$body,$total_fee,$notify_url){
		$url='https://api.mch.weixin.qq.com/pay/unifiedorder';
		if(!$total_fee||$total_fee<0.01){
			$total_fee=1;
		}else{
			$total_fee=$total_fee*100;
		}
		$total_fee=intval($total_fee);
		$data['appid']=$this->AppID;
		$data['mch_id']=$this->MchID;
		$data['nonce_str']=$this->getRandomString(16);
		$data['body']=$body;
		$data['out_trade_no']=$out_trade_no;
		$data['total_fee']=$total_fee;
		$data['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
		$data['notify_url']=$notify_url;
		$data['trade_type']='MWEB';
		$data['product_id']=$out_trade_no;
		$scene_info['h5_info']=array(
			'type'=>"Wap",
			"wap_url"=>$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'],
			"wap_name"=>$this->wap_name
		);
		$data['scene_info']=json_encode($scene_info);
		ksort($data);
		$signStr=$this->arrayurl($data).'&key='.$this->key;
		$data['sign']=strtoupper(md5($signStr));
		$data=$this->arrayToXml($data);
		return json_decode(json_encode(simplexml_load_string(@$this->curlPost($url,$data),'SimpleXMLElement', LIBXML_NOCDATA)),true);
	}
	function verifyWxCallback(){
		$result=json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"),'SimpleXMLElement', LIBXML_NOCDATA)),true);
		if($result['return_code']=='SUCCESS'){
			if($result['appid']==$this->AppID&&$result['mch_id']==$this->MchID){
				$data['status']=1;
				$data['out_trade_no']=$result['out_trade_no'];
				$data['openid']=$result['openid'];
				$data['total_fee']=$result['total_fee'];
				$data['successStr']='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
			}
		}else{
			$data['status']=0;
			$data['return_code']=$result['return_code'];
			$data['return_msg']=$result['return_msg'];
			$data['successStr']='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA['.$result['return_msg'].']]></return_msg></xml>';
		}
		return $data;
	}
}
$Weixin=new Weixin();
// echo "<pre>";
// print_r($Weixin->getCodeUrl('6633663','opens','1','http://ceshi.dianyun.hk/caidan/open/weixin.class.php'));
// print_r($Weixin->getOrderByOutTradeNo('ME20170927173717644857'));
// print_r($Weixin->refundOrder('ME20170927173717644857'));
// print_r($Weixin->getRefundByOutTradeNo('dawwaw'));
// print_r($Weixin->deviceGroup('0001','0001','1','1')->deviceGroup('0002','0002','2','2')->setDeviceGroups('15'));

// print_r($Weixin->closeOrder('6633663'));
// print_r($Weixin->getCodeUrl('6633663','asd','0.01','asd'));
// file_put_contents('filename.php',json_encode($Weixin->getMessageinfo()));