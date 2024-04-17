<?php

namespace app\service\payment;

use app\libs\AsiabillSDK\action\CheckoutAsiabill;
use app\libs\AsiabillSDK\action\CustomerAsiabill;
use app\libs\AsiabillSDK\builder\CustomerBuilder;
use app\libs\AsiabillSDK\builder\PaymentMethodsBuilder;
use app\Request;

class AsiabillService extends PaymentBase implements PaymentInterface
{
    public function placeOrder()
    {
        #1.创建顾客
        $customer = new CustomerAsiabill($this->payment);
        $customerBuilder = new CustomerBuilder($this->order);
        $customerRes = $customer->create_customer($customerBuilder);
        $customerId = $customerRes['data']['customerId'] ?? '';
        if(empty($customerId)) throw new \Exception('create asiabill customer error');
        //创建支付方式
        $paymentBuilder = new PaymentMethodsBuilder($this->order);
        $paymentRes = $customer->create_customer_payment_id($customerId,$paymentBuilder);
        dd($paymentRes);
        //$checkout = new CheckoutAsiabill($this->payment);
        //$checkout



    }
}