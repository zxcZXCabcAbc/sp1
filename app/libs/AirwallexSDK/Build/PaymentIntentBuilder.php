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
        list($return_url,$cancel_url) = $this->getReturnUrl($this->order);
        return [
            'amount'=>0 + $this->order->total_money,
            'currency'=>$this->order->currency,
            'customer'=>$this->getCustomer(),
            'merchant_order_id'=>$this->order->payer_id,
            'order' => $this->getOrder(),
            'return_url'=>$return_url,
            'request_id'=>uniqid(time()),
            //'connected_account_id'=>'9562345678959589'

        ];
    }

    public function getCustomer()
    {
        $address = $this->order->address;
        $billing = $address['billing'];
        return [
            'email'=>$this->order->email,
            'first_name'=>$billing['first_name'],
            'last_name'=>$billing['last_name'],
            'phone_number'=>$billing['phone'],
            'merchant_customer_id'=>md5(time()),
            'address'=>[
                'city'=>$billing['city'],
                'country_code'=>$billing['country_code'],
                'postcode'=>$billing['postal_code'],
                'state'=>$billing['state'],
                'street'=>$billing['line1'],
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