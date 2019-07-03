<?php

namespace app\admin\validate;

use think\Validate;

class Permission extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
///[\s|\~|`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\||\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g
	protected $rule = [
        'name'  => 'require|max:50|min:1|token',

        'description'  => 'require|max:200|min:1',

        'path'  => 'require|max:100|min:1',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.regex' => '权限名不付符合规则',
        'name.require' => '权限名不能为空',
        'name.max'     => '权限名最多不能超过50个字符',
        'name.min'     => '权限名最少不能少于1个字符',

        'description.require' => '权限描述不能为空',
        'description.max'     => '权限描述最多不能超过200个字符',
        'description.min'     => '权限描述最少不能少于1个字符',

        'path.require' => '权限地址不能为空',
        'path.max'     => '权限地址最多不能超过100个字符',
        'path.min'     => '权限地址最少不能少于1个字符',
        

    ];
}
