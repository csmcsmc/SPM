<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class User extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
    }




}
