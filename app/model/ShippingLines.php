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
        'order_id','shipping_id','carrier_identifier','carrier_identifier',
        'code','discounted_price','is_removed','phone',
        'price','requested_fulfillment_service_id','source','title',
        'tax_lines','discount_allocations'
    ];
    protected $json = ['tax_lines','discount_allocations'];
    protected $jsonAssoc = true;

    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }
}
