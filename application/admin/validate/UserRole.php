<?php

namespace app\admin\validate;

use think\Validate;

class UserRole extends Validate
{

    protected $rule = [
        'name'  => 'require|max:50|min:1|token',

        'password'  => 'require|length:6,20',

        'phone'  => 'require|/^1[3-8]{1}[0-9]{9}$/',


    ];


    protected $message = [
        'name.require' => '角色名称不能为空',
        'name.max'     => '角色名称最多不能超过50个字符',
        'name.min'     => '角色名称最少不能少于1个字符',

        'password.require' => '密码不能为空',
        'password.length' => '密码长度在6位到20位之间',

        'phone.require' => '手机号不能为空',
        'phone' => '手机号格式有误',


    ];
}
