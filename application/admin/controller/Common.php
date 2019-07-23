<?php

namespace app\admin\controller;
use think\Controller;
use think\facade\Session;
use  think\Db;
use gmars\rbac\Rbac;
class Common extends Controller
{
   public function  initialize(){
       $name=Session::get('name');
       if (empty($name)){
           $this->error("请先登录","login/login");
       }
       $m=request()->module();
       $c=request()->controller();
       $f=request()->action();
       $carr=['Permission','Permissioncate','Role','User'];
       $farr=['index','add','update','datadel'];
       if (in_array($c,$carr)){
            if (in_array($f,$farr)){
                $rbac = new Rbac();
                $a="$m/$c/$f";
                $a=strtolower($a);
                $blo=$rbac->can($a);
                if ($blo==false){
                    $acc=["code"=>"1001","status"=>"no","message"=>"您没有权限!"];
                    echo  json_encode($acc);
                    die;
//                    $this->redirect("control/nocontrol");
//                    die;
                }
            }
       }

   }

    public function commonToken()
    {
        $token = $this->request->token('__token__', 'sha1');
        $acc=["token"=>$token];
        return json($acc);
    }

}
