<?php

namespace app\admin\controller;
use app\admin\model\Permission as PermissionModel;
use app\admin\model\Permissioncate as PermissionCateModel;
use think\Controller;
use think\Db;

class Permission extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $PermissionCate= new PermissionCateModel;
        $arr=$PermissionCate->select();
        $a=json_decode($arr,true);
        $this->assign("cate",$a);
        return $this->fetch("permission/permission");
    }
    public function add(){
        $name=input("name");
        $cateid=input("cateid");
        $Permission           = new PermissionModel;
        $Permission->name     = $name;
        $Permission->category_id     = $cateid;
        $Permission->save();
    }
    public function showa(){
        $a=Db::query("select p.id,p.name,pc.name as pc_name from permission as p inner join permission_category as pc on p.category_id=pc.id");
        $acc=["code"=>"0","status"=>"ok","message"=>$a];
        echo $b=json_encode($acc);
    }

    public function del(){
        $id=input("del_id");
        $del=Db::table('permission')->where('id',$id)->delete();
        if ($del==true){
            $arr=["status"=>"ok"];
            echo $b=json_encode($arr);
        }
    }


}
