<?php

namespace app\libs\PaypalSDK\action;

use app\libs\PaypalSDK\builder\PurchaseBuilder;
use app\libs\PaypalSDK\PaypalClient;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class PurchasePaypal extends PaypalClient
{
    public function purchase(PurchaseBuilder $builder)
    {
        $request = new OrdersCreateRequest();
        $request->body = $builder->toArray();
        return $this->send($request);
    }

    public function completePurchase($transactionReference,$body = [])
    {
        $request = new OrdersCaptureRequest($transactionReference);
        if($body)$request->body = $body;
        return $this->send($request);
    }
//
    public function fetchPurchase($transactionReference)
    {
        $request = new OrdersGetRequest($transactionReference);
        return $this->send($request);
    }

}