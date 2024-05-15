<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $order_id
 * @property string $handle
 * @property string $title
 * @property float $price
 * @property integer $custom
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

    public function getTitleAttr($title)
    {
        return get_json_key($title);
    }
}
