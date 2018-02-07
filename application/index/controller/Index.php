<?php
namespace app\index\controller;

use think\Session;
use think\Weixin;
use think\Request;

use app\common\controller\FrontBase;
class Index extends FrontBase
{
    private $account_id;
    public function __construct()
    {
        parent::__construct();

        $account_id = Session::get('account_id');
        $this->account_id = 2;
    }

    /* 展示JSSDK用例 */
    public function index()
    {
        $weixin = new Weixin($this->account_id);
        $config = $weixin->getJsApiTicket();
        
        $this->assign('config', $config);
        return $this->fetch('/index');
    }

    /* 调用百度API获取地理位置 */
    public function getPosition()
    {
        $request = Request::instance()->param();
        $lat = $request['lat'];
        $long = $request['long'];
        $url = 'http://api.map.baidu.com/geocoder?location='.$lat.','.$long.'&output=json&key=aibHOrzNpo585L0P3HIa3lGK';

        // $weixin = new Weixin($this->account_id);
        // $weixin->curlPost($url);
        $result = file_get_contents($url);
        echo $result;
    }
}
