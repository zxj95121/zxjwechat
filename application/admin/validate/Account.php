<?php
namespace app\admin\validate;
use think\Validate;
class Account extends Validate{
	protected $rule =   [
		'name'  => 'require|max:50',
		'description'   => 'max:200',
		'original' => 'max:50',
		'qrcode' => 'max:255',
		'account_type' => 'number|between:1,4',
		'token' => 'require|max:32',
		'encodingaeskey' => 'require|max:255',
		'appid' => 'require|max:50',
		'appsecret' => 'require|max:50',
		'avatar' => 'max:255',
	];
	protected $message  =   [
		'name.require' => '名称必填',
		'name.max'     => '名称最多不能超过50个字符',
		'description.max'  => '描述最多不能超过200个字符',
		'original.max'  => '原始ID最多不能超过50个字符',
		'qrcode.max'  => '地址最多不能超过255个字符',
		'account_type.number'  => '公众号类型只能是数字',
		'account_type.between'  => '公众号类型只能在1-4之间',
		'token.require'        => 'token必填',
		'token.max'        => 'token不能超过32个字符',
		'encodingaeskey.require'        => 'encodingaeskey必填',
		'encodingaeskey.max'        => 'encodingaeskey不能超过255个字符',
		'appid.require'        => 'appid必填',
		'appid.max'        => 'appid不能超过50个字符',
		'appsecret.require'        => 'appsecret必填',
		'appsecret.max'        => 'appsecret不能超过50个字符',
		'avatar.max'        => '地址不能超过255个字符',
	];
	protected $scene = [
		'add'  =>  [
			'name',
			'description',
			'original',
			'qrcode',
			'account_type',
			'token',
			'encodingaeskey',
			'appid',
			'appsecret',
			'avatar',
		],
		'edit'  =>  [
			'name',
			'description',
			'original',
			'qrcode',
			'account_type',
			'token',
			'encodingaeskey',
			'appid',
			'appsecret',
			'avatar',
		],
	];
}