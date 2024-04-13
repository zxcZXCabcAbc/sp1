<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class OrderValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'line_items'=>[
            'array',
            'checkLines'=>
                [
                    'price'=>'require|float',
                    'quantity'=>'require|number',
                    'title'=>'string',
                    'variant_id'=>'number'
                ]
        ],
        'email'=>['email'],
        'shipping_address'=>['array'],
        'shipping_line'=>['array'],
        'applied_discount'=>['array'],
        'shipping_address.address1'=>['string'],
        'shipping_address.city'=>['string'],
        'shipping_address.company'=>['string'],

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];
}
