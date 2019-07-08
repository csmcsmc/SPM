<?php

namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\Controller;
use think\Db;

class Permissioncate extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch("Permissioncate/Permissioncate");
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
        $a=$rbac->getPermissioncategory([['name', '=', $data['name']]]);
        //使用RBSC查询分类名是否存在数据库

        if (empty($a)){                      //判断如果数据库没有才能添加
            $rbac->savePermissioncategory([
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
        $data=input();
        $id=input("del_id");
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc = ["code" => "0", "status" => "no", "message" => $validate->getError()];
            return json($acc);
        }
        if (!is_array($id)){
            $id=explode(",",$id);
        }

        $rbac=new Rbac();
        $del=$rbac->delPermissionCategory($id);
        if ($del==true){
            $acc=["code"=>"0","status"=>"ok","message"=>"删除成功"];
            echo $b=json_encode($acc);
            die;
        }
    }
}
