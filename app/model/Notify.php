<?php
declare (strict_types = 1);

namespace app\model;

use Carbon\Carbon;

class Notify extends BaseModel
{
    public $autoWriteTimestamp = false;
    protected $field = [
        'order_id','params','created_at','type','pay_method'
    ];
    protected $json = ['params'];
    protected $jsonAssoc = true;
    const TYPE_SEND = 1;
    const TYPE_NOTIFY = 2;
    const TYPE_CHECKOUT = 3;

    public static function saveParams($order_id,$params,$type = self::TYPE_SEND,$pay_method = 0)
    {
        if(empty($params)) return false;
        return self::query()->insert(['order_id'=>$order_id,'params'=>$params,'type'=>$type,'created_at'=>Carbon::now()->toDateTimeString(),'pay_method'=>$pay_method]);
    }
}
