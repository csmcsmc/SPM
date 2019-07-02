<?php

namespace app\admin\controller;
use app\admin\model\Permission as PermissionModel;
use app\admin\model\Permissioncate as PermissionCateModel;
use think\Controller;
use think\Db;
use gmars\rbac\Rbac;
class Permission extends Common
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
        $data=input();
        $validate = new \app\admin\validate\Permission;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            echo $b=json_encode($acc);
        }else{
            $sel=Db::table('permission')->where('name',$data['name'])->find();
            if (empty($sel)){
                $Permission           = new PermissionModel;
                $Permission->name     = $data['name'];
                $Permission->category_id     = $data['cateid'];
                $add=$Permission->save();
                if ($add==true){
                    $acc=["code"=>"0","status"=>"yes","message"=>"添加成功！"];
                    echo $b=json_encode($acc);
                }
            }else{
                $acc=["code"=>"0","status"=>"no","message"=>"改分类已存在"];
                echo $b=json_encode($acc);
            }

        }

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

    public function upd(){
        $data=input();
        $validate = new \app\admin\validate\Permission;

        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            echo $b=json_encode($acc);
        }else{
            $sel=Db::table('permission')->where('name',$data['name'])->find();
            if (!empty($sel)){
                $acc=["code"=>"0","status"=>"no","message"=>"修改的数据已存在！"];
                echo $b=json_encode($acc);
            }else{
                $up=Db::name('permission')
                    ->where('id', $data['id'])
                    ->update(['name' =>$data['name']]);
                if ($up==true){
                    $acc=["code"=>"0","status"=>"yes","message"=>"修改成功！"];
                    echo $b=json_encode($acc);
                }
            }
        }

    }

    public function datadel(){
        $id=input("id");
        if (empty($id)){
            $acc=["code"=>"0","status"=>"no","message"=>"未找到您要删除的信息！"];
            echo $b=json_encode($acc);
            die;
        }
        $arr=explode(",",$id);
        array_shift($arr);
        $rbac=new Rbac();
        $del=$rbac->delPermission($arr);
        if ($del==true){
            $acc=["code"=>"0","status"=>"yes","message"=>"删除成功！"];
            echo $b=json_encode($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
            echo $b=json_encode($acc);
        }


    }


}
