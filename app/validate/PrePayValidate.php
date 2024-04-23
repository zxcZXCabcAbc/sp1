<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class PrePayValidate extends BaseValidate
{

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
        ]
    ];


    protected $message = [];
}
