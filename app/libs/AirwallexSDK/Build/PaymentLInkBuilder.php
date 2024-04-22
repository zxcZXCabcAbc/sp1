<?php

namespace app\libs\AirwallexSDK\Build;

use app\model\Orders;

class PaymentLInkBuilder
{
    protected $order;
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    public function toArray()
    {
        return [
            'amount'=>$this->order->total_price,
            'collectable_shopper_info'=>$this->collectable_shopper_info(),
            'currency'=>$this->order->currency,
            //'default_currency'=>"USD",
            'metadata'=>$this->metadata(),
            'reusable'=>true,
            //'supported_currencies'=>["EUR",'USD'],
            'title'=>$this->order->name,
        ];
    }


    protected function collectable_shopper_info()
    {
        return [
            'message'=>true,
            'phone_number'=>true,
            'shipping_address'=>true,
        ];
    }

    public function metadata()
    {
        return [
            'email'=>$this->order->contact_email,
            'order_sn'=>$this->order->transaction_id
        ];
    }
}