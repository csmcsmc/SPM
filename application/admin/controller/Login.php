<?php
namespace app\admin\controller;
use think\Controller;
use think\captcha\Captcha;
use think\Db;

class Login extends controller
{
    public function login()
    {
        return $this->fetch();
    }
    public function login_action(){
        $code=input("code");
        $user=input("user");
        $password=input("password");
        $captcha = new Captcha();
        if( !$captcha->check($code))
        {
            $arr=["code"=>"1","status"=>"error","message"=>"验证码错误"];
            $type=json_encode($arr);
            echo $type;
            die;
        }else{
            $sel=Db::table("admin_user")->where('a_user',$user)->where("a_password",$password)->find();
            if ($sel==false){
                $arr=["code"=>"2","status"=>"error","message"=>"用户名密码错误"];
                $type=json_encode($arr);
                echo $type;
                die;
            }else{
                $arr=["code"=>"0","status"=>"success","message"=>"用户名密码正确"];
                $type=json_encode($arr);
                echo $type;
                die;
            }
        }

    }

}
