<?php
declare (strict_types = 1);

namespace app\validate;

use think\exception\ValidateException;
use think\Validate;

class DraftOrderValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'line_items'=>[
            'require',
            'array',
            'checkLines'=>
                [
                    'price'=>'require|float',
                    'quantity'=>'require|number',
                    'title'=>'require',
                ]
        ],
    ];

    protected $message = [
        'line_items.checkLines'=>'line_items format is error',
    ];



}
