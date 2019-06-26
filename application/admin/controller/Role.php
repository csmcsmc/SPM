<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Role extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch("role/role");
    }


}
