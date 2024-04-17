<?php

namespace app\libs\AsiabillSDK\builder;

use app\model\Orders;

class CustomerBuilder extends BuilderBase
{
    public function toArray()
    {
        return [
                'description' => $this->order->billingAddress->name,
                'email' => $this->order->contact_email,
                'firstName' => $this->order->billingAddress->first_name,
                'lastName' => $this->order->billingAddress->last_name,
                'phone' => $this->order->billingAddress->phone
        ];
    }

}