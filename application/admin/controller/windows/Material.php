<?php

namespace app\admin\controller\windows;

use think\Controller;
use think\Weixin;
use think\Session;
use think\Request;

use app\common\controller\MaterialImages;

class Material extends Controller{

    //首页
    private $account_id;

    public function __construct()
    {
        parent::__construct();
        $account_id = Session::get('weixin_account');
        $this->account_id = 2;
    }
    public function index()
    {
    	return view();
    }


    /* 图片素材部分 */
    public function images()
    {
        $weixin = new Weixin($this->account_id);
        $material_count = $weixin->getMaterialCount();
        // 'voice_count' => int 0
        // 'video_count' => int 0
        // 'image_count' => int 0
        // 'news_count' => int 0
        $this->assign('image_count', $material_count['image_count']);
        var_dump($material_count);
        return view();
    }

    /* 图片上传 */
    public function imagesUpload()
    {
        $file = Request::instance()->file('file');
        // $extension = $file->getExtension();
        // $fileName = md5($file->getFilename());
        // $file->move(ROOT_PATH. 'public/uploads/')
        $info = $file->getInfo();
        $extension = substr($info['type'], 6);
        var_dump($extension);
        if (!in_array($extension, array('jpeg', 'jpg', 'png', 'gif', 'bmp'))) {
            return json(['errcode' => 0, 'reason' => '不是正确的图片文件', 'ext' => $extension]);
        }

        $filename = ROOT_PATH. 'public/uploads/material/'. md5($info['type'].time()). '.'. $extension;

        //try 保存图片文件到服务器
        try{
            move_uploaded_file($info['tmp_name'], $filename);  
            // touch($directory. $filename,time()+$k);
            $json['errcode'] = 0;
            $json['reason'] .= $filename.'上传成功';

            /* 将图片上传到微信服务器 */
            $params = array(
                'media' => '@'. $filename
            );
            
            $account_id = Session::get('weixin_account');
            $weixin = new Weixin($this->account_id);
            $result = $weixin->newMaterial('image', $params);
            var_dump($result);
            exit;
            
            $json['cc'] = $response;
        } catch (Exception $e) {
            $json['errcode'] = 1;
            $json['reason'] = $e->getMessage();  
        }
        return json($json);
    }
}
