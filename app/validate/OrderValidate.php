<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class OrderValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'currency'=>['require','alpha'],
        'phone'=>['require',],
        'total_discounts'=>['require','number'],
        'total_line_items_price'=>['require','number'],
        'total_outstanding'=>['require','number'],
        'total_price'=>['require','number'],
        'total_shipping_price'=>['require','number'],
        'total_tax'=>['require','number'],
        'total_tip_received'=>['require','number'],
        'shipping_address'=>[],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];
}
