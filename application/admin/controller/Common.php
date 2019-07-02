<?php

namespace app\admin\controller;
use think\Controller;
use think\facade\Session;
class Common extends Controller
{
   public function  initialize(){
       $name=Session::get('name');
       if (empty($name)){
           $this->error("请先登录","login/login");
       }
   }

    public function commonToken()
    {
        $token = $this->request->token('__token__', 'sha1');
        $acc=["token"=>$token];
        echo $b=json_encode($acc);
    }

}
