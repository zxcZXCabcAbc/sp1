<?php

namespace app\libs\PaypalSDK\builder;

class PurchaseBuilder extends PaypalBuilder
{
    public function toArray()
    {
        return [
            'intent'=>'CAPTURE',
            'purchase_units'=>$this->getTransactions(),
            'application_context'=>[
                'return_url'=>$this->order->return_url,
                'cancel_url'=>$this->order->cancel_url,
                'shipping_preference'=>'SET_PROVIDED_ADDRESS'
            ],
        ];
    }

}