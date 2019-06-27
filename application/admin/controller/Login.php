<?php
namespace app\admin\controller;
use think\Controller;
use think\captcha\Captcha;
use think\Db;
use think\facade\Session;
use gmars\rbac\Rbac;
class Login extends controller
{
    public function login()               //登录展示页面
    {
        return $this->fetch();
    }
    public function login_action(){       //登录判断页面
        $code=input("code");
        $user=input("user");
        $password=input("password");
        $captcha = new Captcha();
        if( !$captcha->check($code))      //首先判断验证码是否正确
        {
            $arr=["code"=>"1","status"=>"error","message"=>"验证码错误"];
            $type=json_encode($arr);
            echo $type;
            die;
        }else{                            //如果验证码正确则判断用户名密码
            $sel=Db::table("user")->where('user_name',$user)->where("password",md5($password))->find();
            if ($sel==false){
                $arr=["code"=>"2","status"=>"error","message"=>"用户名密码错误"];
            }else{
                $arr=["code"=>"0","status"=>"success","message"=>"用户名密码正确"];
                Session::set('name',$user);     //如果验证成功存session
                Session::set('name_id',$sel['id']);
                $rbac = new Rbac();
                $rbac->cachePermission($sel['id']);
            }
            $type=json_encode($arr);
            echo $type;
        }

    }

    public function  login_out(){
        Session::clear();         //清除所有Session
        $this->success("退出成功","login/login","1");
    }

}
