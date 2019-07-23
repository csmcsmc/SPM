<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use  think\Db;
class Attrcate extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    //属性分类渲染页面
    public function index()
    {
        return $this->fetch();
    }
    //属性分类展示
    public function show(){
        $sel=Db::query("select * from attr_category");
        $acc=["data"=>$sel];
        return json($acc);
    }

    //属性分类添加
    public function add()
    {
        $data=input();
        $validate = new \app\admin\validate\Attrcate;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $name=$data['name'];
        $sel=Db::query("select * from attr_category where `name`='$name' ");
        if (empty($sel)){
            $data = ['name' => $name, ];
            $add=Db::name('attr_category')->insert($data);
            if ($add==true){
                $acc=["status"=>"yes","message"=>"添加成功"];
                return json($acc);
            }else{
                $acc=["status"=>"no","message"=>"添加失败"];
                return json($acc);
            }
        }else{
            $acc=["status"=>"no","message"=>"属性分类已存在！"];
            return json($acc);
        }
    }
    //删除属性分类
    public function datadel(){
        $data=input();
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
         $id=$data['del_id'];
        if(is_array($id)){
            $id=implode(',',$id);
        }
        $selab=Db::query("select * from attribute where  attrcate_id in ($id) ");
        if (!empty($selab)){

            $acc=["status"=>"no","message"=>"该分类下有属性，请先删除属性后删除分类！"];
            return json($acc);
        }
        //删除属性分类
        $delac = Db::table('attr_category')->where('id', 'in', $id)->delete();
        if ($delac==true){
            $acc=["status"=>"yes","message"=>"删除成功"];
            return json($acc);
        }
    }




}
