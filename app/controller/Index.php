<?php

namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 'three'], // 这里添加了一个非数字的元素
        ];

        $result = $this->validate([
            'data' => $data,
        ], [
            'data'  => 'require|array',
            'data.*.id' => 'require|number',
        ]);

        if (true !== $result) {
            return $result;
        }

        // 验证通过
        return '验证通过';
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }
}
