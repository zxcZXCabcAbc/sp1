<?php

namespace app\libs\StripeSDK;


class StripeAPI
{
    protected $client;
    public function __construct(array $paymentCnf)
    {

        $this->client = $paymentCnf['app_secrect'];
    }

    public function getPrivateKey()
    {
        return $this->client;
    }
}
