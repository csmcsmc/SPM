<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use  think\Db;
class Fenlei extends Common
{
    public function index()
    {
        return $this->fetch();
    }
    public function show(){
        $sel=Db::table('fenlei')->select();
        $acc=["code"=>"0","status"=>"yes","data"=>$sel];
        return json($acc);
    }
    //添加时的展示  递归下拉列表
    function addshow(){
        $sel=Db::table('fenlei')->select(); //二维数组
        $res=$this->digui($sel);
        foreach ($res as $k =>$v){         //选好替换
            $res[$k]['f_name']=str_repeat('|--',$v['level']).$v['f_name'];

        }

        //var_dump($res);die;
        $acc = ["data" =>array_values($res)];
        return json($acc);
    }
    //递归方法
    function digui($as,$pid=0,$leve=0){
        static $b=[];                  //静态定义个空数组
        foreach ($as as $k => $v){     //循环这个一维数组
            if ($pid==$v['p_id']){
                $b[$k]=$v;
                $b[$k]["level"]=$leve;
                $this->digui($as,$v['f_id'],$leve+1);
            }
        }
        return $b;
    }
    //添加
    function  add(){
        $data=input();
        $f_name=$data['f_name'];
        $p_id=$data['p_id'];
        $validate = new \app\admin\validate\Fenlei;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{
            $sel=Db::query("select f_name from  fenlei where f_name='$f_name'");
            if (empty($sel)){
                $sele=Db::query("select leve from  fenlei where f_id='$p_id'");
                $leve=$sele[0]['leve']+1;
                $data = ['f_name' =>$f_name , 'p_id' => $p_id,'leve'=>$leve];
                $add=Db::name('fenlei')->insert($data);
                if ($add==true){
                    $acc=["code"=>"0","status"=>"yes","message"=>"添加成功！"];
                    return json($acc);
                }
            }else{
                $acc=["code"=>"0","status"=>"no","message"=>"您要添加的分类已存在！"];
                return json($acc);
            }
        }
    }
    //删除
    public function datadel(){
        $data=input();
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $id=$data['del_id'];
        $aid=[];
        if(is_array($id)){
            foreach ($id as $k=>$v){
                $aid[]=$v;
            }
            $id=implode(",",$aid);
        }
        $sel=Db::query("select * from fenlei where p_id in ($id)");
        if (empty($sel)){
            $del = Db::table('fenlei')->where('f_id', 'in', $id)->delete();
            if ($del==true){
                $acc=["code"=>"0","status"=>"yes","message"=>"删除成功！"];
                return json($acc);
            }else{
                $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
                return json($acc);
            }
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"您要删除的分类下有子分类请先删除子分类！"];
            return json($acc);
        }


    }
    //修改方法
    public function update(){
        $data=input();
        $validate = new \app\admin\validate\Fenlei;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $f_id=$data['f_id'];
        $f_name=$data['f_name'];
        $p_id=$data['p_id'];
        $sel=Db::query("select f_name from fenlei where f_id!='$f_id' and f_name='$f_name'");
        if (empty($sel)){
            $selone=Db::query("select * from fenlei ");
            $son=$this->getSon($f_id,$selone);
            $sonid=[];
            foreach ($son as $k=>$v){
                $sonid[]=$v['f_id'];
            }
            $result = array_search($p_id, $sonid);
            if ($result===false&&$f_id!=$p_id){               //可以修改
                //查询修改前的级别
                $selq=Db::query("select leve from fenlei where f_id='$f_id'");
                $one=$selq[0]['leve'];
                //查询修改后的级别
                $sebh=Db::query("select leve from fenlei where f_id='$p_id'");
                $tow=$sebh[0]['leve'];
                $a=$tow+1;
                $up=Db::name('fenlei')
                    ->where('f_id', $f_id)
                    ->update(['f_name' =>$f_name,'p_id'=>$p_id,'leve'=>$a ]);
                if ($up==true){
                    $onetow=$a+1;
                    $selone=Db::query("select * from fenlei ");
                    $son=$this->upgetSon($f_id,$selone,$onetow);

                    $acc=["status"=>"yes","message"=>"修改成功!"];
                    return json($acc);
                }
            }else{       //不可以
                $acc=["status"=>"no","message"=>"您所选择的上级分类不能是当前分类或者当前分类的下级分类!"];
                return json($acc);
            }

        }

    }

    //根据id获取所有子类
    function getSon($id,$array,$level=1)
    {
        $f_name=__FUNCTION__; // 定义当前函数名
        static $list;
        //$array=Db::table('table_name')->where('pid',$id)->select(); TP5
        foreach ($array as $k => $v)
        {
            if($v['p_id'] == $id)
            {
                //存放数组中
                $list[] = $v;
                unset($array[$k]);
                $this->$f_name($v['f_id'],$array,$level+1);
            }
        }
        return $list;
    }
    //根据id获取所有子类
    function upgetSon($id,$array,$onetow,$level=1)
    {
        $f_name=__FUNCTION__; // 定义当前函数名
        static $list;
        //$array=Db::table('table_name')->where('pid',$id)->select(); TP5
        foreach ($array as $k => $v)
        {
            if($v['p_id'] == $id)
            {
                $up=Db::name('fenlei')->where('f_id', $v['f_id'])
                    ->update(['leve' =>$onetow ]);
                //存放数组中
                $list[] = $v;
                unset($array[$k]);
                $this->$f_name($v['f_id'],$array,$onetow+1,$level+1);
            }
        }
        return $list;
    }


}
