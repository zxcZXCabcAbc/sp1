<?php
declare (strict_types = 1);

namespace app\validate;

use think\exception\ValidateException;
use think\Validate;

class BaseValidate extends Validate
{
    protected function checkLines($value,$rule,$data)
    {
        $isPass = true;
        foreach ($value as $vv){
            try {
                $validate = new Validate();
                $validate->rule($rule);
                $validate->failException()->check($vv);
            }catch (ValidateException $e){
                dump($e->getMessage());
                $isPass = false;
                break;
            }
        }

        return $isPass;
    }

    public function string($value,$rule,$data)
    {
        return is_string($value);
    }
}
