<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Request;
use app\admin\model\Brand as BrandModel;
class Brand extends Common
{
//    public function initialize()
//    {
//        parent::initialize();
//        $action = Request::action();      //获取当前方法
//        $action='admin/brand/'.$action;   //拼接
//        $rbac = new Rbac();
//        $can=$rbac->can($action);         //判断是否有权限
//        $html_type=input("html_type");
//
//        if ($can==false&&$html_type) {
//        $arr=["code"=>"0","status"=>"error","message"=>"不好意思，您没有权限操作！"];
//            echo json_encode($arr);
//            die;
//        }else if ($can==false){
//            $this->redirect("control/nocontrol");   //重定向跳转页面,直接跳转
//        }
//
//    }
    public function show()
    {
        return $this->fetch();
    }
    public function add(){
     $brand_name=input("brand_name");
        $brand           = new BrandModel;
        $brand->name     = $brand_name;
        $brand->save();
    }
    public function showa(){
        $brand= new BrandModel;
        $arr=$brand->select();
        $a=json_decode($arr,true);
        $acc=["code"=>"0","status"=>"ok","message"=>$a];
        echo $b=json_encode($acc);
    }

}
