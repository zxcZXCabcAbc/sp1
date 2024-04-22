<?php

namespace app\libs\AirwallexSDK\Build;

use app\model\Orders;
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
        $address = $this->order->address;
        $shipping = $address['shipping'];
        return [
            'products'=>$this->transferProductsToThirdParty($this->order,Orders::PAY_METHOD_AIRWALLEX),
            'shipping'=>[
                'address'=>[
                    'city'=>$shipping['city'],
                    'country_code'=>$shipping['country_code'],
                    'postcode'=>$shipping['postal_code'],
                    'state'=>$shipping['state'],
                    'street'=>$shipping['line1'],
                ],
                'first_name'=>$shipping['first_name'],
                'last_name'=>$shipping['last_name'],
                'phone_number'=>$shipping['phone'],
                'shipping_method'=>$this->order->logistics_id,
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


}