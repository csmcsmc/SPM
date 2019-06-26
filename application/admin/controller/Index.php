<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

//    public function rbac(){
//        $rbac = new Rbac();
//        $rbac->createTable();
//    }
}
