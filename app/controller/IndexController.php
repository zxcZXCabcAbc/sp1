<?php

namespace app\controller;

use app\BaseController;
use app\exception\BusinessException;


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
