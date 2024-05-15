<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class OrderLogs extends Model
{
    protected $field = [
        'checkout_id','logs','created_at'
    ];

    public $autoWriteTimestamp = false;

    public static function saveLogs($checkoutId,$logs)
    {
        return self::query()->insert(['checkout_id'=>$checkoutId,'logs'=>$logs,'created_at'=>date('Y-m-d H:i:s')]);
    }
}
