<?php

namespace app\libs\PaypalSDK\action;

use app\libs\PaypalSDK\builder\PurchaseBuilder;
use app\libs\PaypalSDK\PaypalClient;

class PurchasePaypal extends PaypalClient
{
    public function purchase(PurchaseBuilder $builder)
    {
        return $this->gateway->purchase($builder->toArray())->send();
    }

    public function completePurchase($transactionReference,$payerId)
    {
        return $this->gateway->completePurchase(compact('transactionReference','payerId'))->send();
    }

}