<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
if(!function_exists('getLimit')){
    function getLimit($page=1,$pagesize=10){
        $page=max(1,$page);
        $pagesize=max(1,$pagesize);
        return ' ' . (($page - 1) * $pagesize) . ',' . $pagesize;
    }
}
if(!function_exists('fullString')){
    function fullString(){
        $strings=func_get_args();
        $result=true;
        foreach($strings as $v){
            if(!(is_string($v)&&trim($v))){
               $result=false;
               break; 
            }
        }
        return $result;
    }
}
/**
 *  生成随机字符串
 *  $length 随机字符串
 *  $onlyNum    是否仅数字
 */
if (!function_exists('getRandomString')){
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
            $key=mt_rand(0,$maxKey);
            $data.=$result[$key];
        }
        return $data;
    }
}
if (!function_exists('tablename')){
    function tablename($tablename=''){
        $tablename=config()['database']['prefix'].$tablename;
        return $tablename;
    }
}