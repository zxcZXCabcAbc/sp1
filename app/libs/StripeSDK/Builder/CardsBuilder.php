<?php

namespace app\libs\StripeSDK\Builder;

use think\Request;

class CardsBuilder extends StripeBaseBuilder
{
    public function toArray()
    {
        $account =  $this->request instanceof Request ?  $this->request->param('account') : $this->request;
        return [
            'type'=> 'card',
            'card'=>[
                'exp_month'=>0 + $account['expiryMonth'],
                'exp_year'=> 0 + $account['expiryYear'],
                'number'=> $account['number'],
                'cvc'=> $account['verificationCode'],
            ],
            'billing_details'=>[
                'address'=>[
                    'city'=> $this->order->billingAddress->city,
                    'country'=> $this->order->billingAddress->country_code,
                    'line1'=> $this->order->billingAddress->address1,
                    'state'=> $this->order->billingAddress->province,
                    'postal_code'=> $this->order->billingAddress->zip,
                ],
                'email'=> $this->order->contact_email,
                'phone'=>$this->order->billingAddress->phone,
                'name'=>$this->order->billingAddress->username
            ],
        ];
    }
}