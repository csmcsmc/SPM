<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use  think\Db;
class Attribute extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    //属性页面渲染
    public function index()
    {
        return $this->fetch();
    }

    //添加时下拉框展示属性分类
    public function fenlei_show()
    {
        $arr=Db::query("select * from attr_category");
        $arr=['data'=>$arr];
        return json($arr);
    }
    //属性添加
    public function add(){
        $data=input();
        $validate = new \app\admin\validate\Attribute;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $name=$data['name'];
        $attrcate_id=$data['attrcate_id'];
        $sel=Db::query("select * from attribute where `name`='$name' and attrcate_id='$attrcate_id' ");
        if (empty($sel)){
            $data = ['name' => $name, 'attrcate_id' => $attrcate_id];
            $add=Db::name('attribute')->insert($data);
            if ($add==true){
                $acc=["status"=>"yes","message"=>"添加成功"];
                return json($acc);
            }else{
                $acc=["status"=>"no","message"=>"添加失败"];
                return json($acc);
            }
        }else{
            $acc=["status"=>"no","message"=>"该属性已存在！"];
            return json($acc);
        }

    }
    //属性分类及属性展示
    public function show(){
        $arr=Db::query("select ab.id,ab.`name` as abname,ab.attrcate_id,a.`name` as aname,a.id as aid from attribute as ab left join attr_category as a on ab.attrcate_id=a.id");
        $arr=['data'=>$arr];
        return json($arr);
    }
    //属性值添加
    public function adadd(){
        $data=input();
        $validate = new \app\admin\validate\Attribute;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $name=$data['name'];
        $aid=$data['aid'];
        $sel=Db::query("select * from attr_details where `name`='$name' and `attr_id` ='$aid' ");
        if (empty($sel)){
            $data = ['name' => $name, 'attr_id' => $aid];
            $add=Db::name('attr_details')->insert($data);
            if ($add==true){
                $acc=["status"=>"yes","message"=>"添加成功"];
                return json($acc);
            }else{
                $acc=["status"=>"no","message"=>"添加失败"];
                return json($acc);
            }
        }else{
            $acc=["status"=>"no","message"=>"该属性值已存在！"];
            return json($acc);
        }



    }
    //属性值展示
    public function adshow(){
        $id=input('id');
        $arr=Db::query("select * from attr_details where attr_id='$id'");
        $arr=['data'=>$arr];
        return json($arr);
    }
    //属性值删除
    public function addel(){
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

        $del = Db::table('attr_details')->where('id', 'in', $id)->delete();
        if ($del==true){
            $acc=["code"=>"0","status"=>"yes","message"=>"删除成功！"];
            return json($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
            return json($acc);
        }
    }
    public function abdel(){
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
        //删除属性要先删除属性下的属性分类
        $sel=Db::query("select * from attribute where  id in ($id) ");
        if (!empty($sel)){  //判断属性下是否有属性值
            foreach ($sel as $k=>$v){
                $dele = Db::table('attr_details')->where('attr_id', 'in', $v['id'])->delete();
            }

        }
        //删除属性
        $del = Db::table('attribute')->where('id', 'in', $id)->delete();
        if ($del==true){
            $acc=["code"=>"0","status"=>"yes","message"=>"删除成功！"];
            return json($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
            return json($acc);
        }


    }




}
