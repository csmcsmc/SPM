<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
class Control extends Common
{
    public function savePermissionCategoryAdd()
    {
        $this->fetch();
    }
    public function savePermissionCategoryAddAction()//Permission_Category权限分类表添加数据
    {
        $rbac = new Rbac();
        $rbac->savePermissionCategory([
            'name' => '商品管理',
            'description' => '网站商品的管理',
            'status' => 1
        ]);
    }

    public function createPermissionAdd(){
        $this->fetch();
    }
    public function createPermissionAddAction(){     //Permission 权限表添加数据
        $rbac = new Rbac();
        $rbac->createPermission([
            'name' => '品牌添加权限',
            'description' => '品牌添加权限',
            'status' => 1,
            'type' => 1,
            'category_id' => 5,
            'path' => 'admin/brand/add',
        ]);
    }
    public function RbacAdd(){
        $this->fetch();
    }
    public function RbacAddAction(){    //role 角色表添加数据
        $rbac = new Rbac();
        $rbac->createRole([
            'name' => '内容管理员',
            'description' => '负责网站内容管理',
            'status' => 1
        ], '1');
    }

    public function  assignUserRole(){   // user_role用户角色表  添加数据
        $rbac = new Rbac();
        $rbac->assignUserRole(1, [1]);
    }

    public function nocontrol(){
        return $this->fetch();
    }

}
