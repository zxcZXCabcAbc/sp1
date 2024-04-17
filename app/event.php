<?php
// 事件定义文件
return [
    'bind'      => [
        'PushOrder'=>'app\event\PushOrder',
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'PushOrder'=>['app\listener\PushOrderListener'],
    ],

    'subscribe' => [
    ],
];
