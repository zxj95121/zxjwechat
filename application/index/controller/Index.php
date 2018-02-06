<?php
namespace app\index\controller;

use think\Session;
use think\Weixin;

use app\common\controller\Backend;
class Index extends Backend
{
    private $account_id;
    public function __construct()
    {
        parent::__construct();

        $account_id = Session::get('account_id');
        $this->account_id = 2;
    }

    /* 展示JSSDK用例 */
    public function index(){
        $weixin = new Weixin($this->account_id);
        $config = $weixin->getJsApiTicket();
        
        $this->assign('config', $config);
        return $this->fetch('/index');
    }
}
