<?php

namespace app\controller\api;

use app\BaseController;


class IndexController extends BaseController
{
    public function index()
    {
        dump('success11');
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }
}
