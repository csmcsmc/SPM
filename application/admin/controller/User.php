<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
class User extends Common
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
    public function  show(){
        $sel=Db::query("select u.id as u_id,u.user_name,u.password,u.mobile,u.last_login_time,u.status,u_r.id,u_r.user_id,u_r.role_id,r.id as r_id,r.name  from user_role as u_r inner join user as u  on u_r.user_id=u.id inner join role as r on u_r.role_id=r.id ");
        $acc=["code"=>"0","status"=>"yes","data"=>$sel];
        return json($acc);
    }
    public function addshow(){
        $sel=Db::query("select * from role ");
        $acc=["code"=>"0","status"=>"yes","data"=>$sel];
        return json($acc);
    }

    public function add(){
        $data=input();
        $name=$data['name'];
        $Role=$data['Role'];
        $phone=$data['phone'];
        $validate = new \app\admin\validate\UserRole;
        if (!$validate->check($data)) {
            $acc=["code"=>"0","status"=>"no","message"=>$validate->getError()];
            return json($acc);
        }else{

            //添加之前要先进行判断 是否重复
            $sele=Db::query("select * from user where user_name='$name' ");

            $seleb=Db::query("select * from user where mobile='$phone'");
            if (empty($sele)&&empty($seleb)){
                    // 启动事务
                    Db::startTrans();
                    try {
                        $data = ['user_name' => $name, 'status' => 1, 'password' => md5($data['password']),'mobile' => $data['phone']];
                        $add=Db::name('user')->insertGetId($data); //获取添加数据的自增ID
                        $data = ['user_id' => $add,'role_id' => $Role];
                        $add_role=Db::name('user_role')->insert($data);
                        // 提交事务
                        Db::commit();
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();

                    }
                        if ($add_role==true){
                            $acc=["code"=>"0","status"=>"yes","message"=>"添加成功"];
                            return json($acc);
                        }
            }else{
                if (!empty($sele)){
                    $acc=["code"=>"0","status"=>"no","message"=>"您要添加的用户已存在"];
                    return json($acc);
                }

                if (!empty($seleb)){
                    $acc=["code"=>"0","status"=>"no","message"=>"您要添加的电话已被他人绑定"];
                    return json($acc);
                }

            }

        }

    }
    public function update()
    {
        $data = input();
        $id = $data['id'];
        $name = $data['name'];
        $phone = $data['phone'];
        $password = $data['password'];
        $r_id = $data['r_id'];

        $validate = new \app\admin\validate\UserRole;
        if (!$validate->check($data)) {
            $acc = ["code" => "0", "status" => "no", "message" => $validate->getError()];
            return json($acc);
        } else {
            $sel=Db::query("select * from user where id !='$id' and user_name = '$name' ");
            if (!empty($sel)){
                $acc=["code"=>"0","status"=>"no","message"=>"您要修改的用户已存在"];
                return json($acc);
            }
            $sela=Db::query("select * from user where id !='$id' and mobile = '$phone' ");
            if (!empty($sela)){
                $acc=["code"=>"0","status"=>"no","message"=>"修改的电话已被他人绑定"];
                return json($acc);
            }
            // 启动事务
            Db::startTrans();
            try {
                $upa = Db::name('user')
                    ->where('id', $id)
                    ->update(['user_name' => $name, 'password' => md5($password), 'mobile' => $phone]);
                $upb = Db::name('user_role')
                    ->where('user_id', $id)
                    ->update(['role_id' => $r_id]);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

                $acc = ["code" => "0", "status" => "yes", "message" => "修改成功"];
                return json($acc);

        }

    }

    public function datadel()
    {
        $data = input();
        $id = $data['del_id'];
        $validate = new \app\admin\validate\Delete;
        if (!$validate->check($data)) {
            $acc = ["code" => "0", "status" => "no", "message" => $validate->getError()];
            return json($acc);
        } else {
            if (is_array($id)) {
                $id = implode(",", $id);
            }
// 启动事务
            Db::startTrans();
            try {
                $dela = Db::table('user')->where('id', 'in', $id)->delete();
                $delb = Db::table('user_role')->where('user_id', 'in', $id)->delete();
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();

            }
            if ($dela == true && $delb == true) {
                $acc = ["code" => "0", "status" => "yes", "message" => "删除成功"];
            } else {
                $acc = ["code" => "0", "status" => "no", "message" => "删除失败"];

            }
            return json($acc);
        }
    }

}
