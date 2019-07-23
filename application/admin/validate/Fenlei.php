<?php

namespace app\admin\validate;

use think\Validate;

class Fenlei extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'f_name'  => 'require|max:50|min:1|token',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'f_name.require' => '分类名不能为空',
        'f_name.token' => '令牌不正确',
        'f_name.max'     => '分类名最多不能超过50个字符',
        'f_name.min'     => '分类名最少不能少于1个字符',

    ];
}
