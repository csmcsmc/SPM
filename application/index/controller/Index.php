<?php
namespace app\index\controller;
use think\Db;
class Index
{
    public function index()
    {
        $sel=Db::table('user_live')->select();
        dump($sel);
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
