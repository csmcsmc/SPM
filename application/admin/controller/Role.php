<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use gmars\rbac\Rbac;
class Role extends Common
{

    public function index()
    {
        return $this->fetch("role/role");
    }

    public function  show(){
        $sel=Db::query("select p.id,p.name,p.description,p.path,p.category_id,pc.name as pc_name ,pc.id as pc_id from permission as p inner join permission_category as pc on p.category_id=pc.id");
        //var_dump($sel);
        $arr=[];
        foreach ($sel as $k=>$v){
            $arr[$v['pc_name']][]=$v;
        }
        $acc=['status'=>$arr];
        return json($acc);
    }
//    public function add(){
//        return $this->fetch();
//    }
    //角色添加方法
    public function add(){
        $data=input();
        $validate = new \app\admin\validate\Role;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else {
            $description=$data['description'];
            $name=$data['name'];
            $permission_id=$data['permission_id'];
            $arra=explode(',',$permission_id);
            array_shift($arra);
            Db::query("select * from role where name='$name'");
            if (empty($sela)) {
                $rbac = new Rbac();
                $add=$rbac->createRole([
                    'name' => $name,
                    'description' => $description,
                    'status' => 1
                ],$permission_id);
                if ($add == true) {
                    $acc = ["code" => "0", "status" => "yes", "message" => "添加成功！"];
                    return json($acc);
                }
            } else {
                $acc = ["code" => "0", "status" => "no", "message" => "您要添加的角色名称已存在！"];
                return json($acc);
            }
        }

    }

    public function role(){
        $rbac=new Rbac();
        $sel=$rbac->getRole([], true);
        if ($sel==true){
            $acc=["code"=>"0","status"=>"yes","data"=>$sel];
            return json($acc);
        }
    }
    public function mypermission(){
        $id=input("id");
        $sel=Db::table('role_permission')->where('role_id',$id)->select();
        if ($sel==true){
            $acc=["code"=>"0","status"=>"yes","data"=>$sel];
            return json($acc);
        }
    }
    public function datadel(){
        $data=input();
        $id=$data['del_id'];
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{
            if (is_array($id)){
                $id=implode(",",$id);
            }
            $dela=Db::table('role')->where('id','in',$id)->delete();
            $delb=Db::table('role_permission')->where('role_id','in',$id)->delete();
            $acc=["code"=>"0","status"=>"yes","message"=>"删除成功"];
            return json($acc);
        }

    }
    public function update(){
        $data=input();
        $id=$data['id'];
        $name=$data['name'];
        $description=$data['description'];
        $permission_id=$data['permission_id'];

        $validate = new \app\admin\validate\Role;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{
            $sel=Db::query("select * from role where name='$name'");
            if (empty($sel)||!empty($sel)&&$sel[0]['id']==$data['id']){

                $uprole=Db::name('role')
                    ->where('id', 1)
                    ->update(['name' => $name,'description' => $description]);

                //删除
                $del=Db::table('role_permission')->where('role_id',$id)->delete();

                $p_id=explode(",",$permission_id);
                array_shift($p_id);
                foreach ($p_id as $k=>$v){
                    $add=Db::query("insert into `role_permission` (`role_id`,`permission_id`) values ('$id','$v')");
                }
                $acc=["code"=>"0","status"=>"yes","data"=>"修改成功"];
            }else{
                $acc=["code"=>"0","status"=>"no","data"=>"您要修改的角色名或权限已存在！"];
            }
            return json($acc);
        }
    }

}





