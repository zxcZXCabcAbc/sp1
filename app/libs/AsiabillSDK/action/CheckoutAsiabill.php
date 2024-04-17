<?php

namespace app\libs\AsiabillSDK\action;

use app\libs\AsiabillSDK\AsiabillClient;
use app\libs\AsiabillSDK\builder\CheckoutBuilder;

class CheckoutAsiabill extends AsiabillClient
{
    public function confirm_charge(string $customer_id,string $payment_id,CheckoutBuilder $builder)
    {
        $body = $builder->setCustomerId($customer_id)->setCustomerPaymentMethodId($payment_id)->toArray();
        return $this->setRequestType('confirmCharge')->setBody(compact('body'));
    }
}