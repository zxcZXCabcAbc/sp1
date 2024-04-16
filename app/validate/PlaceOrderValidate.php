<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class PlaceOrderValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'payment_id'=>['require','number'],
        'billingAddress'=>['array'],
        'billingAddress.first_name'=>['string'],
        'billingAddress.last_name'=>['string'],
        'billingAddress.country_code'=>['string'],
        'billingAddress.country'=>['string'],
        'billingAddress.address1'=>['string'],
        'billingAddress.phone'=>['string'],
        'billingAddress.province_code'=>['string'],
        'billingAddress.province'=>['string'],
        'billingAddress.city'=>['string'],
        'billingAddress.zip'=>['string'],
        'card'=>['array'],
        'card.cardExpireMonth'=>['require','string'],
        'card.cardExpireYear'=>['require','string'],
        'card.cardNo'=>['require','string'],
        'card.cardSecurityCode'=>['require','string'],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];
}
