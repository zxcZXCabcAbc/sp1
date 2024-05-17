<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use think\captcha\facade\Captcha;
use think\facade\Cache;
use think\facade\Session;
use think\Request;

class AdminController extends BaseController
{

    public function index()
    {
        return view('admin/index',['title'=>'Shopify Admin']);
    }


    public function login(Request $request)
    {
        return view('admin/login');
    }


    public function checkLogin(Request $request)
    {
        $this->validate($request->post(),['username'=>'require','password'=>'require']);
        $password = 'ddhd@2024';
        $username = 'admin';
        if($request->param('username') != $username || $request->param('password') != $password){
            throw new \Exception('账号或者密码错误!');
        }
        Cache::set('uid',1);
        return redirect("/admin/home");

    }


    public function logout()
    {
        Cache::delete('uid');
        return redirect("/admin/login");
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
