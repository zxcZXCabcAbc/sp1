<?php

namespace app\libs\StripeSDK\Builder;

use app\model\Orders;
use app\trait\PaymentTrait;

class StripeOrder extends StripeBaseBuilder
{

    public function toArray()
    {

        return [
            'amount'=>$this->order->total_price * 100,
            'currency' => $this->order->currency,
            'payment_method_types'=>['card'],
            'shipping'=>$this->getShipping(),
            'receipt_email'=>$this->order->contact_email,
            'confirm'=>true,
            'return_url'=>$this->order->return_url,
        ];
    }

    //货物收货信息
    public function getShipping()
    {
        $address = $this->order->address;
        $shipping = $address['shipping'];
        return [
            'address'=>[
                'country'=>$shipping['country_code'],
                'city'=>$shipping['city'],
                'line1'=>$shipping['line1'],
                'postal_code'=>$shipping['postal_code'],
                'state'=>$shipping['state'],
            ],
            'name'=>$shipping['first_name'] . $shipping['last_name'],
        ];
    }

    protected function getTransferData()
    {
        return [

        ];
    }

}
