<?php

namespace app\libs\AirwallexSDK\Build;

use app\model\Orders;
use app\model\ShopsPayment;
use app\trait\PaymentTrait;

class PaymentIntentBuilder
{
    use PaymentTrait;
    protected $order;
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    public function toArray()
    {

        return [
            'amount'=>$this->order->total_price,
            'currency'=>$this->order->currency,
            'customer'=>$this->getCustomer(),
            'merchant_order_id'=>$this->order->transaction_id,
            'order' => $this->getOrder(),
            'return_url'=>$this->order->return_url,
            'request_id'=>uniqid(time()),

        ];
    }

    public function getCustomer()
    {
        return [
            'email'=>$this->order->contact_email,
            'first_name'=>$this->order->billingAddress->first_name,
            'last_name'=>$this->order->billingAddress->last_name,
            'phone_number'=>$this->order->billingAddress->phone,
            'merchant_customer_id'=>md5(time()),
            'address'=>[
                'city'=>$this->order->billingAddress->city,
                'country_code'=>$this->order->billingAddress->country_code,
                'postcode'=>$this->order->billingAddress->zip,
                'state'=>$this->order->billingAddress->province,
                'street'=>$this->order->billingAddress->address1,
            ],
        ];
    }


    public function getOrder()
    {

        return [
            'products'=>$this->getProducts(),
            'shipping'=>[
                'address'=>[
                    'city'=>$this->order->shippingAddress->city,
                    'country_code'=>$this->order->shippingAddress->country_code,
                    'postcode'=>$this->order->shippingAddress->zip,
                    'state'=>$this->order->shippingAddress->province,
                    'street'=>$this->order->shippingAddress->address1,
                ],
                'first_name'=>$this->order->shippingAddress->first_name,
                'last_name'=>$this->order->shippingAddress->last_name,
                'phone_number'=>$this->order->shippingAddress->phone,
                //'shipping_method'=>$this->order->shippings(),
            ]
        ];
    }

    protected function setRiskControlOptions(){
        return [
            'skip_risk_processing'=>true,
            'tra_applicable'=>true,
        ];
    }

    protected function setPaymentMethodOption()
    {
        return [
            'card'=>[
                'three_ds_action'=>'FORCE_3DS',
            ],
        ];
    }

    protected function getProducts()
    {
        $products = [];
        foreach ($this->order->items as $product){
            $products[] = [
                'sku'=>$product->sku ?: pathinfo($product->admin_graphql_api_id,PATHINFO_BASENAME),
                'name'=>substr($product->title,0,120),
                'unit_price'    => 0 + $product->price,
                'quantity'    => $product->quantity,
            ];
        }
        return $products;

    }


}