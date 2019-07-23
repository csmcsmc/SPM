<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
class Product extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
       return $this->fetch();
    }
    public function show(){
        $sel=Db::query(" select goods.id,goods.`name`,brand.id as b_id,brand.`name` as b_name,fenlei.f_id,fenlei.f_name from goods inner join brand on goods.brand_id=brand.id inner join fenlei on goods.fenlei_id=fenlei.f_id ");
        $arr=['data'=>$sel];
        return json($arr);
    }
    public function addshow(){
        return $this->fetch();
    }
    //添加分类展示
    public function showfenlei(){
        $arr=Db::query("select * from fenlei");
        $sel=$this->getTree($arr);
        foreach ($sel as $k=>$v){
            $sel[$k]['f_name']=str_repeat('|--',$v['leve']).$v['f_name'];
        }

        $selbrand=Db::query("select id,name from brand");
        $arr=['data'=>$sel,'da'=>$selbrand];
        return json($arr);
    }


    //分类递归
    function getTree($array, $pid =0, $level = 0){

        $f_name=__FUNCTION__; // 定义当前的方法  也就是getTree这个递归方法

        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];

        foreach ($array as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['p_id'] == $pid){
                //把数组放到list中
                $list[] = $value;                        //放入空数组里
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->$f_name($array, $value['f_id'], $level+1);
            }
        }
        return $list;
    }
    //添加商品
    public function add(){
        $data=input();
        $validate = new \app\admin\validate\Product;
        if (!$validate->check($data)) {
            $arr=['status'=>'no','data'=>$validate->getError()];
            return json($arr);
        }
        $name=$data['name'];
        $brand_id=$data['brand_id'];
        $fenlei_id=$data['fenlei_id'];
        $data = ['name' => $name, 'brand_id' => $brand_id,'fenlei_id'=>$fenlei_id];
        $add=Db::name('goods')->insert($data);
        if ($add==true){
            $arr=['status'=>'ok','data'=>"添加成功"];
            return json($arr);
        }else{
            $arr=['status'=>'no','data'=>"添加失败"];
            return json($arr);
        }
    }
    public function imgshow(){  //添加的模板展示
         $id=input("id");
         $this->assign("goods_id",$id);
        return $this->fetch();
    }
    public function imgadd(){   //添加图片
            $goods_id=input('goods_id');
            // 获取表单上传文件
            $files = request()->file('file');
            foreach($files as $file){
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $file->move( 'uploads/goodsimg');
                if($info){
                $luj=$info->getFilename();
                $date=date("Ymd");
                $path="$date/$luj";
                $a=$_SERVER['DOCUMENT_ROOT'];

                $image = \think\Image::open("$a/uploads/goodsimg/$path");
                //大图
                $big_img="$date/big_$luj";
                $image->thumb(150, 150)->save("$a/uploads/goodsimg/".$big_img);
                //中图
                $middle_img="$date/middle_$luj";
                $image->thumb(100, 100)->save("$a/uploads/goodsimg/".$middle_img);
                //小图
                $small_img="$date/small_$luj";
                $image->thumb(50, 50)->save("$a/uploads/goodsimg/".$small_img);

                $data = ['old_img' => $path, 'goods_id' => $goods_id,'big_img'=>$big_img,'middle_img'=>$middle_img,'small_img'=>$small_img];
                    Db::name('goods_img')->insert($data);
                    $arr=['status'=>'ok','data'=>"添加成功"];
                }else{
                    // 上传失败获取错误信息
                    $arr=['status'=>'no','data'=> $file->getError()];
                }

            }
           return json($arr);
    }
    public function img_show(){  //展示图片
        $goods_id=input("goods_id");
        $sel=Db::query("select * from goods_img where goods_id='$goods_id'");
        $arr=['data'=> $sel];
        return json($arr);
    }
    //图片删除
    public function img_datadel(){
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
        //删除文件

        $a=$_SERVER['DOCUMENT_ROOT'];
        $sel=Db::query("select * from goods_img where id in ($id) ");
        foreach ($sel as $k=>$v){
            unlink($a."/uploads/goodsimg/".$v['big_img']);
            unlink($a."/uploads/goodsimg/".$v['middle_img']);
            unlink($a."/uploads/goodsimg/".$v['small_img']);
            unlink($a."/uploads/goodsimg/".$v['old_img']);
        }

        //删除数据库
        $del = Db::table('goods_img')->where('id', 'in', $id)->delete();
        if ($del==true){
            $acc=["code"=>"0","status"=>"ok","message"=>"删除成功！"];
            return json($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
            return json($acc);
        }
    }
    //删除商品并删除商品下的图片
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
        //删除商品下的图片
        $a=$_SERVER['DOCUMENT_ROOT'];
        $sel=Db::query("select * from goods_img where goods_id in ($id) ");
        if (!empty($sel)){  //判断删除商品有图片就删除图片
            foreach ($sel as $k=>$v){
                unlink($a."/uploads/goodsimg/".$v['big_img']);
                unlink($a."/uploads/goodsimg/".$v['middle_img']);
                unlink($a."/uploads/goodsimg/".$v['small_img']);
                unlink($a."/uploads/goodsimg/".$v['old_img']);
            }
            $del = Db::table('goods_img')->where('goods_id', 'in', $id)->delete();
        }


            $dele = Db::table('goods')->where('id', 'in', $id)->delete();

        if ($dele==true){
            $acc=["code"=>"0","status"=>"ok","message"=>"删除成功！"];
            return json($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
            return json($acc);
        }

    }
    public function update(){
        $data=input();
//        var_dump($data);die;
        $validate = new \app\admin\validate\Product;
        if (!$validate->check($data)) {
            $arr=['status'=>'no','message'=>$validate->getError()];
            return json($arr);
        }
        $id=$data['id'];
        $brand_name=$data['name'];
        $f_id=$data['f_id'];
        $b_id=$data['b_id'];
        $up=Db::name('goods')->where('id', $id)
        ->update(['name' =>$brand_name,'brand_id' =>$b_id,'fenlei_id' =>$f_id]);
        if ($up==true){
            $acc=["code"=>"0","status"=>"ok","message"=>"修改成功！"];
            return json($acc);
        }else{
            $acc=["code"=>"0","status"=>"no","message"=>"修改失败！"];
            return json($acc);
        }
    }
    //属性模板渲染
    public function attrshow(){
        $goodsid=input("id");
        $this->assign("goodsid",$goodsid);
        return $this->fetch();
    }
    //属性分类展示
    public function acshow(){
        $goodsid=input("goodsid");
        $selgoods=Db::query("select ac_id from goods where id='$goodsid'");

        $selas=Db::query("select * from attr_category");
        $acc=["data"=>$selas];
        if (!empty($selgoods)){
            $acc['def']=$selgoods;
        }
        return json($acc);
    }
    //属性展示
    public function sxshow(){
        $acid=input("acid");
        $goodsid=input("goodsid");
        $sel=Db::query("select ab.id as abid,ab.`name` as abname,ad.id as adid,ad.`name` as adname from attribute as ab  left join attr_details as ad on ab.id=ad.attr_id where ab.attrcate_id='$acid'");

        $arr=[];
        foreach ($sel as $k=>$v){
            $arr[$v['abname']][]=$v;
        }
        $acc=['status'=>$arr];

        $array=Db::query("select ad_id from goods_attr where goods_id='$goodsid'");
        if (!empty($array)){
            $acc['def']=$array;
        }
        return json($acc);

    }
    //添加属性
    public function addsx(){
        $goodsid=input("goodsid");
        $ac_id=input("ac_id");

        // 启动事务
        Db::startTrans();
        try {
            //添加前先删除商品表的属性分类字段  在删除属性关联表
            Db::name('goods')->where('id', $goodsid)->update(['ac_id' => $ac_id]);
            Db::table('goods_attr')->where('goods_id','in',$goodsid)->delete();
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }


        $sumArr=input("sumArr");
        if (!empty($sumArr)){              //判断数组是否为空
            foreach ($sumArr as $k=>$v){
                $abid=$v['abid'];
                $adid=$v['adid'];

                    $data = ['goods_id' => $goodsid, 'ab_id'=> $abid,'ad_id'=>$adid];
                    $init=Db::name('goods_attr')->insert($data);


            }
            if ($init==true){
                $acc=["status"=>"yes","message"=>"添加成功！"];
                return json($acc);
            }else{
                $acc=["status"=>"no","message"=>"添加失败！"];
                return json($acc);
            }
        }

    }
    //添加商品页面的模板渲染
     public function selling(){
         $gid=input("id");
        $this->assign("gid",$gid);
        return $this->fetch();
     }


}
