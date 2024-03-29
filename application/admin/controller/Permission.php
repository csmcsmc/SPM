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
    public function index()      //展示方法页面
    {
        $PermissionCate= new PermissionCateModel;
        $arr=$PermissionCate->select();
        $a=json_decode($arr,true);
        $this->assign("cate",$a);
        return $this->fetch("permission/permission");
    }

    public function cate(){
        $PermissionCate= new PermissionCateModel;
        $arr=$PermissionCate->select();
        echo $a=json_encode($arr);
    }
    public function add(){      //添加方法
        $data=input();
        $validate = new \app\admin\validate\Permission;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{
            $rbac=new Rbac();
            $sela=$rbac->getPermission([['name', '=', $data['name']]]);
            $selb=$rbac->getPermission([['path', '=', $data['path']]]);
            if (empty($sela)&&empty($selb)){
                $rbac=new Rbac();
                $add=$rbac->createPermission([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => 1,
                    'type' => 1,
                    'category_id' => $data['cateid'],
                    'path' => $data['path'],
                ]);
                if ($add==true){
                    $acc=["code"=>"0","status"=>"yes","message"=>"添加成功！"];
                    return json($acc);
                }
            }else{
        $acc=["code"=>"0","status"=>"no","message"=>"此权限名称或权限路径已存在！"];
                return json($acc);
            }

        }

    }
    public function showa(){       //展示表单方法
        $a=Db::query("select p.id,p.name,p.description,p.path,p.category_id,pc.name as pc_name from permission as p inner join permission_category as pc on p.category_id=pc.id");
        $acc=["code"=>"0","status"=>"ok","message"=>$a];
        return json($acc);
    }

    public function upd(){     //权限名称的即点即改修改方法
        $data=input();
        $validate = new \app\admin\validate\Permission;

        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{
            $sel=Db::table('permission')->where('name',$data['name'])->find();
            if (!empty($sel)){
                $acc=["code"=>"0","status"=>"no","message"=>"修改的数据已存在！"];
                return json($acc);
            }else{
                $up=Db::name('permission')
                    ->where('id', $data['id'])
                    ->update(['name' =>$data['name']]);
                if ($up==true){
                    $acc=["code"=>"0","status"=>"yes","message"=>"修改成功！"];
                    return json($acc);
                }
            }
        }

    }


    public function update(){     //弹出层的修改方法
        $data=input();
        $validate = new \app\admin\validate\Permission;

        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{
            $id=$data['id'];
            $name=$data['name'];
            $path=$data['path'];
            $sel=DB::query("select * from permission where id!='$id' and name='$name'");
            $selb=DB::query("select * from permission where id!='$id' and path='$path'");
            if (empty($sel)&&empty($selb)){
                    $up=Db::name('permission')
                        ->where('id', $id)
                        ->update(['name' =>$name,'category_id'=>$data['category_id'], 'path'=>$path,
                            'description'=>$data['description']]);
                    if ($up==true){
                        $acc=["code"=>"0","status"=>"yes","message"=>"修改成功！"];
                        return json($acc);
                    }
            }else{
                    if (!empty($sel)){
                        $acc=["code"=>"1","status"=>"no","message"=>"您要修改的权限名称已存在！"];
                        return json($acc);
                    }
                    if (!empty($selb)){
                        $acc=["code"=>"1","status"=>"no","message"=>"您要修改的权限路径已存在！"];
                        return json($acc);
                    }
            }

        }

    }

    public function datadel(){      //删除方法
        $data=input();
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $id=$data['del_id'];
            if(is_array($id)){
                implode(',',$id);
            }

            $del = Db::table('permission')->where('id', 'in', $id)->delete();
            if ($del==true){
                $acc=["code"=>"0","status"=>"yes","message"=>"删除成功！"];
                return json($acc);
            }else{
                $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
                return json($acc);
            }

    }


}
