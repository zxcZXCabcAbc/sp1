<?php

namespace app\controller;

use app\BaseController;


class Index extends BaseController
{
    public function index()
    {

        // 验证通过
        return '验证通过';
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }
}
