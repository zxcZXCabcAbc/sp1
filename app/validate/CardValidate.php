<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class CardValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'account'=>'require|array',
        'account.holderName'=>'string|require',
        'account.number'=>'string|require',
        'account.expiryMonth'=>'string|require',
        'account.expiryYear'=>'string|require',
        'account.verificationCode'=>'string|require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];
}
