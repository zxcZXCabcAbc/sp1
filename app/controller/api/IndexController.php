<?php

namespace app\controller\api;

use app\BaseController;
use think\facade\Session;
use think\Request;


class IndexController extends BaseController
{
    public function index()
    {

        // 启动 Session
        Session::init();

        if (!Session::has('visits')) {
            Session::set('visits', 0);
        }

        Session::set('visits', Session::get('visits') + 1);

        return 'Visits: ' . Session::get('visits');
    }

    public function setSession($name = 'test')
    {
        Session::set($name,'ldy');
        dump(11);
    }


    public function getSession(Request$request,$name)
    {
       $r = \session($name);
       dd($r);
        //dump(Session::get($name));
    }
}
