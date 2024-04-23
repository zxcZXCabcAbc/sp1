<?php
declare (strict_types = 1);

namespace app\model;

use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * @property integer $id
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
 * @property Customer $customer
 * @property string $notify_url
 * @property string $return_url
 * @property string $cancel_url
 * @property string $transaction_id
 * @property string $subtotal_price
 * @property string $total_discounts
 * @property string $total_shipping_price
 * @property string $total_tax
 * @property string $total_tip_received
 * @property integer $order_status
 * @property string $error_msg
 * @property string $name
 * @property string $order_id
 * @property string $last_order_name
 * @property string $webhook_url
 *
 */
class Orders extends BaseModel
{
    protected $dateFormat = 'U';
    protected $field = [
        'admin_graphql_api_id','app_id','browser_ip',
        'cancel_reason','cancelled_at','cart_token',
        'checkout_id','checkout_token','client_details',
        'created_at','contact_email','currency',
        'order_id','last_order_name',
        'customer_locale','discount_codes',
        'name','note','note_attributes',
        'order_status_url','payment_gateway_names','phone',
        'po_number','processed_at','subtotal_price',
        'tags','token','total_discounts',
        'total_line_items_price','total_outstanding','total_price',
        'total_shipping_price','total_tax','total_tip_received',
        'total_weight','updated_at','user_id','tax_lines','status','order_type',
        'shop_id','payment_id','transaction_id','error_msg'
    ];

    protected $json = ['client_details','note_attributes','payment_gateway_names','discount_codes','tax_lines'];
    protected $jsonAssoc = true;
    const ORDER_DRAFT = 1;//草稿订单
    const ORDER_NORMAL = 2;//正常订单
    const ORDER_STATUS_WAIT = 0;//待支付
    const ORDER_STATUS_COMPLETED = 1;//已完成
    const ORDER_STATUS_FAIL = 2;//失败
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
    public function getShippingAddressAttr() :Address|null
    {
        return $this->addresses()->where('type',Address::SHIPPING_ADDRESS)->find();
    }

    public function getBillingAddressAttr():Address|null
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

    public function getNotifyUrlAttr(): string
    {
        return domain(env('APP_HOST') . '/api/notify');
    }

    public function getCancelUrlAttr(): string
    {
        return domain($this->shop->host);
    }

    public function getReturnUrlAttr(): string
    {
       return domain(env('APP_HOST') . '/api/checkout/'. $this->id);
    }

    public function notifies(): HasMany
    {
        return $this->hasMany(Notify::class,'order_id');
    }

    public function getWebhookUrlAttr(): string
    {
        return domain(env('APP_HOST')) .'/api/webhook';
    }


}
