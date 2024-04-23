<?php

namespace app\libs\PaypalSDK\builder;

class PrePurchaseBuilder extends PaypalBuilder
{
    public function toArray()
    {
        return [
            'intent'=>'CAPTURE',
            'purchase_units'=>$this->getTransactions(),
            'application_context'=>[
                'return_url'=>sprintf('%s?%s',$this->order->return_url,http_build_query(['isPreOrder'=>1])),
                'cancel_url'=>$this->order->cancel_url,
            ],
        ];
    }
}