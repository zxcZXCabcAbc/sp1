<?php

namespace app\libs\StripeSDK;


use app\model\ShopsPayment;
use Stripe\StripeClient;

class StripeAPI
{
    protected StripeClient $client;
    public function __construct(ShopsPayment $payment)
    {
        $config = $payment->config;
        $this->client = new StripeClient($config['app_secret']);
    }

    public function getStripe():StripeClient
    {
        return $this->client;
    }

}
