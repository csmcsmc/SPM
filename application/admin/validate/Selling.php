<?php

namespace app\admin\validate;

use think\Validate;

class Selling extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'price'  => 'require|token',
        's_sum'  => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'price.token'     => '令牌不正确！',
        'price.require' => '商品价格不能为空！',
        's_sum.require'     => '库存不能为空！',
    ];
}
