<?php

namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\Controller;
use think\Db;

class PermissionCate extends Common
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

    public function add(){       //添加权限分类
        $data=input();           //判断输入框的值是否合法 类似正则  使用框架自带验证
        $validate = new \app\admin\validate\permissioncate;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            echo $b=json_encode($acc);
            die;
        }
        $rbac=new Rbac();
        $a=$rbac->getPermissionCategory([['name', '=', $data['name']]]);
        //使用RBSC查询分类名是否存在数据库

        if (empty($a)){                      //判断如果数据库没有才能添加
            $rbac->savePermissionCategory([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 1
            ]);
            $acc=["code"=>"0","status"=>"ok","message"=>"添加成功"];
            echo $b=json_encode($acc);
        }else{
            $acc=["code"=>"1","status"=>"no","message"=>"已有该分类"];
            echo $b=json_encode($acc);
        }

    }

    public function showa(){          //查询所有分类
        $rbac=new Rbac();
        $a=$rbac->getPermissionCategory([['status', '=', 1]]);
        //rbac自带权限分组查询

        $acc=["code"=>"0","status"=>"ok","message"=>$a];
        echo $b=json_encode($acc);
    }
    public function del(){          //删除选中分类
        $id=input("del_id");
        $del=Db::table('permission_category')->where('id',$id)->delete();
        if ($del==true){
            $arr=["status"=>"ok"];
            echo $b=json_encode($arr);
        }
    }
    public function update(){
        $data=input();           //判断输入框的值是否合法 类似正则  使用框架自带验证
        $validate = new \app\admin\validate\permissioncate;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            echo $b=json_encode($acc);
            die;
        }

        $rbac=new Rbac();        //查询要修改的值是否重复
        $sel=$rbac->getPermissionCategory([['name', '=', $data['name']]]);
        if (empty($sel)){
            $update=Db::name('permission_category')
                ->where('id', $data['id'])
                ->data(['name' => $data['name'],'description'=>$data['description']])
                ->update();
            if ($update==true){
                $acc=["code"=>"0","status"=>"ok","message"=>"修改成功"];
                echo $b=json_encode($acc);
            }
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"该权限分类已存在"];
            echo $b=json_encode($acc);

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
        $del=$rbac->delPermissionCategory($arr);
        if ($del==true){
            $acc=["code"=>"0","status"=>"ok","message"=>"删除成功"];
            echo $b=json_encode($acc);
            die;
        }
    }
}
