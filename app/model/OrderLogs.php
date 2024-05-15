<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;
use think\Request;

/**
 * @property string $checkout_id
 * @property string $logs
 * @property string $created_at
 * @property string $draft_id
 * @property integer $order_id
 * @property integer $shop_id
 */
class OrderLogs extends Model
{
    protected $field = [
        'checkout_id','logs','created_at','shop_id','order_id','draft_id'
    ];

    public $autoWriteTimestamp = false;

    public static function saveLogs(Request $request)
    {
        return self::query()->insert([
            'checkout_id'=>$request->param('checkout_id'),
            'logs'=>$request->param('logs'),
            'shop_id'=>$request->middleware('x_shop_id'),
            'order_id'=>$request->middleware('order_id',0),
            'draft_id'=>$request->middleware('draft_id',''),
            'created_at'=>date('Y-m-d H:i:s'),
        ]);
    }
}
