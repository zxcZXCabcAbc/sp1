<?php

namespace app\libs\StripeSDK\Builder;

class CustomerBuilder extends StripeBaseBuilder
{
    public function toArray()
    {
        return [
            'address'=>$this->getBillingAddress(),
            'email'=>$this->order->contact_email,
            'phone'=>$this->order->shippingAddress->phone,
            'shipping'=>$this->getShippingAddress(),
            'name'=>$this->order->shippingAddress->name
        ];
    }

    public function getBillingAddress()
    {
        return [
            'city'=>$this->order->billingAddress->city,
            'country'=>$this->order->billingAddress->country_code,
            'line1'=>$this->order->billingAddress->address1,
            'postal_code'=>$this->order->billingAddress->zip,
            'state'=>$this->order->billingAddress->province,
        ];
    }

    public function getShippingAddress()
    {
        return [
            'address'=>[
                'city'=>$this->order->shippingAddress->city,
                'country'=>$this->order->shippingAddress->country_code,
                'line1'=>$this->order->shippingAddress->address1,
                'postal_code'=>$this->order->shippingAddress->zip,
                'state'=>$this->order->shippingAddress->province,
            ],
            'phone'=>$this->order->shippingAddress->phone,
            'name'=>$this->order->shippingAddress->name
        ];
    }
}