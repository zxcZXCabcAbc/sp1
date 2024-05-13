<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property integer $quantity
 * @property string $price
 * @property string $title
 * @property string $image
 * @property string $variant_title
 * @property string $name
 */
class LineItems extends BaseModel
{
    public $autoWriteTimestamp = false;
    protected $field = [
        'order_id','admin_graphql_api_id','attributed_staffs',
        'current_quantity','fulfillable_quantity','fulfillment_service',
        'fulfillment_status','gift_card','grams',
        'name','price','product_exists',
        'product_id','properties','quantity',
        'requires_shipping','sku','taxable',
        'title','total_discount','variant_id',
        'variant_inventory_management','variant_title','vendor',
        'tax_lines','duties','discount_allocations','image'
    ];
    protected $json = ['attributed_staffs','duties','tax_lines','properties','discount_allocations'];
    protected $jsonAssoc = true;

    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }

    public function getTitleAttr()
    {
        if(!isJson($this->title)) return $this->title;
        $obj = json_decode($this->title,true);
        return $obj['title'];
    }

    public function getVariantTitleAttr()
    {
        if(!isJson($this->variant_title)) return $this->variant_title;
        $obj = json_decode($this->variant_title,true);
        return $obj['title'];
    }
    public function getNameAttr()
    {
        if(!isJson($this->name)) return $this->name;
        $obj = json_decode($this->name,true);
        return $obj['title'];
    }
}
