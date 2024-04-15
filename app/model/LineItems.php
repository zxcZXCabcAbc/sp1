<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property integer $quantity
 * @property string $price
 * @property string $title
 */
class LineItems extends BaseModel
{
    protected $table = 'line_items';
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
        'tax_lines','duties','discount_allocations',
    ];
    protected $json = ['attributed_staffs','duties','tax_lines','properties','discount_allocations'];
    protected $jsonAssoc = true;

    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }
}
