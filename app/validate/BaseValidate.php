<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class BaseValidate extends Validate
{
    public function batchVerify($value,$rule,$data = [])
    {

    }
}
