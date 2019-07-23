<?php

namespace app\admin\validate;

use think\Validate;

class Brand extends Validate
{
    protected $rule = [
        'brand_name'  => 'require|max:50|min:1|token',



    ];
    protected $message = [
        'brand_name.token' => '令牌错误',
        'brand_name.require' => '分类名不能为空',
        'brand_name.max'     => '分类名最多不能超过50个字符',
        'brand_name.min'     => '分类名最少不能少于1个字符',



    ];
}
