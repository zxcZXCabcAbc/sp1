<?php
declare (strict_types = 1);

namespace app\model;

use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * @property string $admin_graphql_api_id
 * @property string $browser_ip
 * @property string $total_price
 * @property string $currency
 * @property Address $shippingAddress
 * @property Address $billingAddress
 * @property string $contact_email
 * @property string $phone
 * @property integer $shop_id
 * @property Shops $shop
 * @property ShopsPayment $payment
 * @property integer $payment_id
 */
class Orders extends BaseModel
{
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
        'total_weight','updated_at','user_id','tax_lines','status','order_type',
        'shop_id','payment_id',
    ];

    protected $json = ['client_details','note_attributes','payment_gateway_names','discount_codes','tax_lines'];
    protected $jsonAssoc = true;
    const ORDER_DRAFT = 1;//草稿订单
    const ORDER_NORMAL = 2;//正常订单
    public function addresses()
    {
        return $this->hasMany(Address::class,'order_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class,'order_id');
    }

    public function items():HasMany
    {
        return $this->hasMany(LineItems::class,'order_id');
    }

    public function shippings()
    {
        return $this->hasMany(ShippingLines::class,'order_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shops::class,'shop_id');
    }

    public function payment()
    {
        return $this->belongsTo(ShopsPayment::class,'payment_id');
    }

    /**
     * @return Address
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @property string $address1
     */
    public function getShippingAddressAttr() :Address
    {
        return $this->addresses()->where('type',Address::SHIPPING_ADDRESS)->find();
    }

    public function getBillAddress():Address
    {
        return $this->addresses()->where('type',Address::BILLING_ADDRESS)->find();
    }

    public function getShopAttr():Shops
    {
        return $this->shop()->find();
    }

    public function getCustomerAttr(): Customer
    {
        return $this->customer()->find();
    }

    public function getPaymentAttr(): ShopsPayment
    {
        return $this->payment()->find();
    }

}
