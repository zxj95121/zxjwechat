<?php

namespace app\admin\controller\windows;

use think\Controller;
use think\Request;
use think\Weixin;
use think\Session;
use think\Db;

use app\admin\model\CustomizeModel;

class Customize extends Controller{

    private $account_id;

    public function __construct()
    {
        parent::__construct();
        $account_id = Session::get('weixin_account');
        $this->account_id = 2;
    }

    //首页
    public function index(){
        $menu1 = CustomizeModel::where('pid', 0)
            ->where('status', 1)
            ->select();
        
        $menu2 = CustomizeModel::where('pid', '<>', 0)
            ->where('status', 1)
            ->order('pid')
            ->select();
        $this->assign('menu1', $menu1);
        $this->assign('menu2', $menu2);
        $this->assign('menu2_json', json_encode($menu2));
    	return view();
    }

    /* 添加一级自定义菜单 */
    public function addMenu1()
    {
        $request = Request::instance()->param();
        
        $flight = new CustomizeModel();
        $flight->name = $request['name'];
        $flight->type = $request['type'];
        if ($flight->type == 2) {
            $flight->url = $request['url'];
        }
        $flight->save();

        return json(['errcode' => 0, 'id' => $flight->id]);
    }

    /* 添加二级自定义菜单 */
    public function addMenu2()
    {
        $request = Request::instance()->param();
        
        $flight = new CustomizeModel();
        $flight->name = $request['name'];
        $flight->type = $request['type'];
        $flight->pid = $request['mid'];
        if ($flight->type == 2) {
            $flight->url = $request['url'];
        }
        $flight->save();

        return json(['errcode' => 0, 'id' => $flight->id]);
    }

    /* ajax请求发布自定义菜单 */
    public function ajaxShowMenu()
    {
        $menu1 = CustomizeModel::where('pid', 0)
            ->where('status', 1)
            ->order('id')
            ->select();

        $data = array();
        $param['prev'] = 0;
        $param['key'] = 0;
        foreach ($menu1 as $key => $value) {
            if ($value['id'] != $param['prev']) {
                // $data[$param['key']] = array('name');
                $param['prev'] = $value['id'];
                
                //根据pid去查二级菜单
                $menu2 = CustomizeModel::where('pid', $value['id'])
                    ->where('status', 1)
                    ->order('id')
                    ->select();
                $data[$param['key']]['name'] = $value['name'];
                if(count($menu2) != 0) {
                    //有二级子菜单
                    foreach ($menu2 as $k => $v) {
                        $data[$param['key']]['son'][$k]['name'] = $v['name'];
                        if ($v['type'] == 2) {
                            $data[$param['key']]['son'][$k]['url'] = $v['url'];
                        } else if ($v['type'] == 1) {
                            $data[$param['key']]['son'][$k]['key'] = $v['name'];//key暂且就用key值代替
                        }
                    }
                } else {
                    //无二级子菜单
                    if ($value['type'] == 2) {
                        $data[$param['key']]['url'] = $value['url'];
                    } else if ($value['type'] == 1) {
                        $data[$param['key']]['key'] = $value['name'];
                    }
                }

                $param['key']++;
            }
        }
        $data[$param['key']++] = false;
        $data[$param['key']] = false;

        $weixin = new Weixin($this->account_id);
        $result = $weixin->Menu($data[0], $data[1], $data[2]);
        
        echo $result;
    }

    /* ajax请求删除自定义菜单 */
    public function deleteMenu()
    {
        $request = Request::instance()->param();

        $mid = $request['mid'];

        $result = CustomizeModel::where(['id' => $mid, 'status' => 1])
            ->whereOr(['pid' => $mid])
            ->select();
        try {
            $result = Db::query("update rabbit_customize set status = 0 where (id={$mid} or pid = {$mid}) and status=1");
            $json['errcode'] = 0;
        } catch (Exception $e) {
            $json['errcode'] = 1;
            $json['reason'] = $e->getMessage();  
        }
        
        return json($json);
    }
}
