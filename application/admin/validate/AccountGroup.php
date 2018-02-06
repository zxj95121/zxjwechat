<?php
namespace app\admin\validate;
use think\Validate;
class AccountGroup extends Validate{
	protected $rule =   [
		'name'  => 'require|max:30',
		'description'  => 'require|max:30',
	];
	protected $message  =   [
		'name.require' => '名称必填',
		'name.max'     => '名称最多不能超过50个字符',
		'description.max'     => '密码最多不能超过200个字符',
	];
	protected $scene = [
		'add'  =>  [
			'name',
			'description',
		],
		'edit'  =>  [
			'name',
			'description',
		],
	];
}