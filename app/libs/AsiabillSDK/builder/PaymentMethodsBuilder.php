<?php

namespace app\libs\AsiabillSDK\builder;

class PaymentMethodsBuilder extends BuilderBase
{
    public function toArray()
    {
        return [
            'billingDetail'=>$this->getBillDetail(),
            'card'=>$this->getCard(),
            'customerId'=>$this->getCustomerId(),
        ];
    }

    public function getBillDetail()
    {
        return [
           'city'=>$this->order->billingAddress->city,
           'country'=>$this->order->billingAddress->country,
           'line1'=>$this->order->billingAddress->address1,
           'postalCode'=>$this->order->billingAddress->zip,
           'state'=>$this->order->billingAddress->province_code,
        ];
    }

    public function getCard()
    {
        return [
            'cardExpireMonth'=>request()->param('cardExpireMonth'),
            'cardExpireYear'=>request()->param('cardExpireYear'),
            'cardNo'=>request()->param('cardNo'),
            'cardSecurityCode'=>request()->param('cardSecurityCode'),
        ];
    }
}