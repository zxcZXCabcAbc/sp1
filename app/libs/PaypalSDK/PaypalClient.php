<?php

namespace app\libs\PaypalSDK;

use app\model\ShopsPayment;
use Omnipay\PayPal\RestGateway;

class PaypalClient
{
    protected RestGateway $gateway;
    public function __construct(public ShopsPayment $payment)
    {
        $config = $this->payment->config;
        $this->gateway = new RestGateway();
        $this->gateway->initialize(
            [
                'clientId'=>$config['app_key'],
                'secret'=>$config['app_secret'],
                'testMode'=>$this->payment->mode == ShopsPayment::MODE_SANDBOX
            ]
        );
    }
}