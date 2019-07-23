<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
class Selling extends Controller
{

    public function index()
    {
         $gid=input('gid');
         $selgoods=Db::query("select ac_id from goods where id='$gid'");
         $gsid=$selgoods[0]['ac_id'];
         if (empty($gsid)){
             $acc=["data"=>'no'];
         }else{
             $acc=["data"=>'ok'];
         }
        return json($acc);
    }
    //添加下拉框展示
    public function addshow(){
        $gid=input("gid");
        $sel=Db::query("select ga.ab_id,ga.ad_id,ad.name as adname,ab.name as abname from goods_attr as ga  join attr_details as ad on ga.ad_id=ad.id join attribute as ab on ga.ab_id=ab.id  where ga.goods_id='$gid'   ");
        $arr=[];
        foreach ($sel as $k=>$v){
            $arr[$v['abname']][]=$v;
        }

        $acc=["data"=>$arr];
        return json($acc);
    }
    //添加
    public function add(){
        $data=input();
        $validate = new \app\admin\validate\Selling();
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $adid=$data['adid'];
        $adid=implode(',',$adid);
        $selse=Db::query("select goods_attr_id from selling where goods_attr_id= '$adid'");
       if (!empty($selse)){
           $acc=["status"=>"no","message"=>"该属性您已添加过了！"];
           return json($acc);
       }
        $gid=$data['gid'];
        $s_sum=$data['s_sum'];
        $price=$data['price'];

        $data = ['goods_id' => $gid, 'goods_attr_id'=>$adid,'price'=>$price,'s_sum'=>$s_sum];
        $add=Db::name('selling')->insert($data);
        if ($add==true){
            $acc=["code"=>"0","status"=>"yes","message"=>"添加成功！"];
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"添加失败！"];
        }
        return json($acc);
    }
    //展示页面
    public function show(){
        $gid=input("gid");
        $sel=Db::query("select * from selling inner join goods on selling.goods_id=goods.id where goods_id='$gid'");
        foreach ($sel as $key=>$val) {
            $adida = $val['goods_attr_id'];
            $arr=explode(',',$adida);
            $selad=[];
            foreach ($arr as $ka=>$va){
                $selad[]= Db::query("select name from attr_details where id =$va");
            }

            $adid = [];
            foreach ($selad as $k => $v) {
                foreach ($v as $ke=>$ve){
                    $adid[] = $ve['name'];
                }

            }
            $a=implode(',',$adid);
            $ase=substr_replace($adida, $a, 0);//替换
            $sel[$key]['goods_attr_id']=$ase;    //数组值替换
        }

        $acc = ["data" => $sel];
        return json($acc);
    }
    //修改下拉框展示
    public function upshow(){
        $gid=input("gid");
        $sel=Db::query("select ga.ab_id,ga.ad_id,ad.name as adname,ab.name as abname from goods_attr as ga  join attr_details as ad on ga.ad_id=ad.id join attribute as ab on ga.ab_id=ab.id  where ga.goods_id='$gid'   ");
        $arr=[];
        foreach ($sel as $k=>$v){        //已熟悉name分组
            $arr[$v['abname']][]=$v;
        }
        $acc=["data"=>$arr];
        return json($acc);
    }
    public function update(){
        $data=input();
        $validate = new \app\admin\validate\Selling();
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $s_id=input('s_id');
        $price=input('price');
        $s_sum=input('s_sum');
        $adid=input('adid');
        $adid=implode(',',$adid);
        $upda=Db::name('selling')->where('s_id', $s_id)
            ->update(['goods_attr_id' => $adid,'price' => $price,'s_sum' => $s_sum]);
        if ($upda==true){
            $acc=["status"=>"yes","message"=>"修改成功！"];
        }else{
            $acc=["status"=>"no","message"=>"修改失败！"];
        }
        return json($acc);
    }
    //删除
    public function datadel(){      //删除方法
        $data=input();
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
        $id=$data['del_id'];
        var_dump($id);
        if(is_array($id)){
            $id=implode(',',$id);
        }
        $del = Db::table('selling')->where('s_id', 'in', $id)->delete();
        if ($del==true){
            $acc=["code"=>"0","status"=>"yes","message"=>"删除成功！"];
            return json($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
            return json($acc);
        }

    }



}
