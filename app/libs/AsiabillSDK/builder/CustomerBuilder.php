<?php

namespace app\libs\AsiabillSDK\builder;

use app\model\Orders;

class CustomerBuilder extends BuilderBase
{
    public function toArray()
    {
        return [
                'description' => $this->order->shippingAddress->name,
                'email' => $this->order->contact_email,
                'firstName' => $this->order->shippingAddress->first_name,
                'lastName' => $this->order->shippingAddress->last_name,
                'phone' => $this->order->phone
        ];
    }

}