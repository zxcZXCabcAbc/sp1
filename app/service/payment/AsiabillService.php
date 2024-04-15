<?php

namespace app\service\payment;

use app\libs\AsiabillSDK\action\CustomerAsiabill;
use app\libs\AsiabillSDK\builder\CustomerBuilder;
use app\Request;

class AsiabillService extends PaymentBase implements PaymentInterface
{
    public function placeOrder()
    {
        #1.创建顾客
        $customer = new CustomerAsiabill($this->order->payment);
        $customerBuilder = new CustomerBuilder($this->order);
        $customerRes = $customer->create_customer($customerBuilder);




    }
}