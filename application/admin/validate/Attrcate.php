<?php

namespace app\admin\validate;

use think\Validate;

class Attrcate extends Validate
{
    protected $rule = [
        'name'  => 'require|token',



    ];
    protected $message = [
        'name.token' => '令牌错误',
        'name.require' => '分类名不能为空',
    ];
}
