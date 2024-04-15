<?php

namespace app\libs\AsiabillSDK\action;

use app\libs\AsiabillSDK\AsiabillClient;
use app\libs\AsiabillSDK\builder\CustomerBuilder;
use app\libs\AsiabillSDK\builder\PaymentMethodsBuilder;

class CustomerAsiabill extends AsiabillClient
{
    public function create_customer(CustomerBuilder $builder)
    {
        return $this->setRequestType('customers')->setBody(['body'=>$builder->toArray()]);
    }

    public function create_customer_payment_id(string $customer_id,PaymentMethodsBuilder $builder)
    {

        return $this->setRequestType('paymentMethods')->setBody(['body'=>$builder->setCustomerId($customer_id)->toArray()]);
    }
}