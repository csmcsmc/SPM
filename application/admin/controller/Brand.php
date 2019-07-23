<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Request;
use think\Db;
use app\admin\model\Brand as BrandModel;
class Brand extends Common
{

    public function show()
    {
        return $this->fetch();
    }
    public function add(){
        $data=input();
        $validate = new \app\admin\validate\Brand;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }
            $file = request()->file('log');
        if (empty($file)){
            $acc=["code"=>"0","status"=>"no","message"=>"上传失败！"];
            return json($acc);
        }
        // 移动到框架应用根目录/uploads/ 目录下
$info = $file->validate(['size'=>1024*1024,'ext'=>'jpg,png,gif'])->move( 'uploads');
        if($info){
             $file=$info->getSaveName();    //获取文件路径
            $name=$data['brand_name'];
            $sel=Db::query("select name from brand where name='$name'");
            if (empty($sel)){
                $file=str_replace("\\","/",$file);
                $data = ['name' =>$name , 'logo' => $file];
                $add=Db::name('brand')->insert($data);
                if ($add==true){
                    $acc=["code"=>"0","status"=>"ok","message"=>"添加成功"];
                    return json($acc);
                }
            }else{
                $acc=["code"=>"0","status"=>"no","message"=>"品牌已存在！"];
                return json($acc);
            }

        }else{
            $no=$file->getError();
            $acc=["status"=>"no","message"=>$no];
            return json($acc);
        }




    }
    public function showa(){
        $brand= new BrandModel;
        $arr=$brand->select();
        $a=json_decode($arr,true);
        $acc=["code"=>"0","status"=>"ok","message"=>$a];
        echo $b=json_encode($acc);
    }

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
            //先删除图片
            $sel=Db::table('brand')->where('id','in',$id)->select();
            foreach ($sel as $k=>$v){
                $a=$_SERVER['DOCUMENT_ROOT'];
                $picurl="$a/uploads/".$v['logo'];
                unlink($picurl);
            }
            //再删数据
            $del = Db::table('brand')->where('id', 'in', $id)->delete();
            if ($del==true){
                $acc=["code"=>"0","status"=>"ok","message"=>"删除成功！"];
                return json($acc);
            }else{
                $acc=["code"=>"0","status"=>"no","message"=>"删除失败！"];
                return json($acc);
            }

    }
    public function update(){
        $data = input();
        $validate = new \app\admin\validate\Brand;
        if (!$validate->check($data)) {
            $acc = [ "status" => "no", "message" => $validate->getError()];
            return json($acc);
        }
            $id=$data['id'];
            $name=$data['brand_name'];
            $img=$data['img'];
            $sel=Db::query("select * from brand where id!='$id' and name ='$name'");
            if (!empty($sel)){
                $acc = [ "status" => "no", "message" => "品牌名称已存在！"];
                return json($acc);
            }else{
            $upa = Db::name('brand')->where('id', $id)->update(['name' => $name]);
                    // 获取表单上传文件 例如上传了001.jpg
                    $file = request()->file('newlogo');
                    if (!empty($file)){
                        $a=$_SERVER['DOCUMENT_ROOT'];
                        $picurl=$a.$img;
                        unlink($picurl);
                        // 移动到框架应用根目录/uploads/ 目录下
                        $info = $file->validate(['size'=>1024*1024,'ext'=>'jpg,png,gif'])->move( 'uploads');
                        if($info){
                            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                            $file=$info->getSaveName();
                            $newlogo=str_replace("\\","/",$file);
                            $uplogo = Db::name('brand')->where('id', $id)->update(['logo' => $newlogo]);
                            if ($uplogo==true){
                                $acc = ["status" => "yes", "message" => "修改成功"];
                                return json($acc);
                            }
                        }else{
                            // 上传失败获取错误信息
                            $no=$file->getError();
                            $acc = ["status" => "no", "message" => $no];
                            return json($acc);
                        }
                    }else{
                        $acc = [ "status" => "yes"];
                        return json($acc);
                    }

            }


    }

}
