<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        //测试shopify
        'shopify:test'=>'app\command\Shopify\ShopifyTest',
        //lfx专用测试
        'lfx:test'=>\app\command\Test\LfxTest::class,
    ],
];
