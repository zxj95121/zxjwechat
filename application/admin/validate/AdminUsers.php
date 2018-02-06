<?php
namespace app\admin\validate;
use think\Validate;
class AdminUsers extends Validate{
	protected $rule =   [
		'username'  => 'require|max:30',
		'password'  => 'require|max:30|min:8',
		'remark'  => 'max:200',
	];
	protected $message  =   [
		'username.require' => '名称必填',
		'username.max'     => '名称最多不能超过30个字符',
		'password.require' => '密码必填',
		'password.max'     => '密码最多不能超过30个字符',
		'password.min'     => '密码不能低于8个字符',
		'remark.max'       => '备注最多不能超过30个字符',
	];
	protected $scene = [
		'login'  =>  [
			'username',
			'password',
		],
		'add'  =>  [
			'username',
			'password',
			'remark',
		],
		'edit'  =>  [
			'username',
			'remark',
		],
	];
}