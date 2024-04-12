<?php
declare (strict_types = 1);

namespace app\model;

class Orders extends BaseModel
{
    protected $table = 'orders';
    protected $dateFormat = 'U';
    protected $field = [
        'admin_graphql_api_id','app_id','browser_ip',
        'cancel_reason','cancelled_at','cart_token',
        'checkout_id','checkout_token','client_details',
        'created_at','contact_email','currency',
        'current_subtotal_price','current_total_discounts','current_total_price',
        'current_total_tax','customer_locale','discount_codes',
        'name','note','note_attributes',
        'order_status_url','payment_gateway_names','phone',
        'po_number','processed_at','subtotal_price',
        'tags','token','total_discounts',
        'total_line_items_price','total_outstanding','total_price',
        'total_shipping_price','total_tax','total_tip_received',
        'total_weight','updated_at','user_id','tax_lines','status'
    ];

    protected $json = ['client_details','note_attributes','payment_gateway_names','discount_codes','tax_lines'];
    protected $jsonAssoc = true;

    public function addresses()
    {
        return $this->hasMany(Address::class,'order_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class,'order_id');
    }

    public function items()
    {
        return $this->hasMany(LineItems::class,'order_id');
    }

    public function shippings()
    {
        return $this->hasMany(ShippingLines::class,'order_id');
    }

}
