<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Address extends BaseModel
{
    protected $table = 'address';
    protected $field = [
        'order_id','first_name','address1','phone',
        'city','zip','province','country',
        'last_name','address2','latitude','longitude',
        'name','country_code','province_code','type',
        'is_default'
    ];
    public $autoWriteTimestamp = false;
    const SHIPPING_ADDRESS = 1;
    const BILLING_ADDRESS = 2;
    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }
}
