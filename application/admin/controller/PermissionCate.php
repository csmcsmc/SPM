<?php

namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\Controller;
use think\Db;
use app\admin\model\Permissioncate as PermissionCateModel;

class PermissionCate extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch("permissioncate/permissioncate");
    }

    public function add(){
        $name=input("name");
        $brand           = new PermissionCateModel;
        $brand->name     = $name;
        $brand->save();
    }
    public function showa(){
        $brand= new PermissionCateModel;
        $arr=$brand->select();
        $a=json_decode($arr,true);
        $acc=["code"=>"0","status"=>"ok","message"=>$a];
        echo $b=json_encode($acc);
    }
    public function del(){
        $id=input("del_id");
        $del=Db::table('permission_category')->where('id',$id)->delete();
        if ($del==true){
            $arr=["status"=>"ok"];
            echo $b=json_encode($arr);
        }
    }
}
