<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property string $first_name
 * @property string $address1
 * @property string $phone
 * @property string $city
 * @property string $zip
 * @property string $province
 * @property string $country
 * @property string $last_name
 * @property string $address2
 * @property string $latitude
 * @property string $longitude
 * @property string $name
 * @property string $country_code
 * @property string $province_code
 * @property integer $order_id
 * @property integer $type
 * @property integer $is_default
 * @property string $address_message
 */
class Address extends BaseModel
{
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

    public function getAddressMessageAttr(): string
    {
        return sprintf(
            '%s,%s,%s,%s,%s,%s,%s',
            $this->name,$this->address1,
            $this->city,$this->province,
            $this->zip,$this->country_code,
            $this->phone
        );
    }
}
