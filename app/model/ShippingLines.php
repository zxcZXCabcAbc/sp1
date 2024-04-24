<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ShippingLines extends BaseModel
{
    protected $field = [
        'order_id','handle','custom',
        'price','title',

    ];

    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }
}
