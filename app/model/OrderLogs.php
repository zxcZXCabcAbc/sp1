<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;
use think\Request;

/**
 * @mixin \think\Model
 */
class OrderLogs extends Model
{
    protected $field = [
        'checkout_id','logs','created_at','shop_id'
    ];

    public $autoWriteTimestamp = false;

    public static function saveLogs(Request $request)
    {
        return self::query()->insert([
            'checkout_id'=>$request->param('checkout_id'),
            'logs'=>$request->param('logs'),
            'shop_id'=>$request->middleware('x_shop_id'),
            'created_at'=>date('Y-m-d H:i:s'),
        ]);
    }
}
