<?php

namespace app\service\payment;

use app\libs\StripeSDK\Builder\CustomerBuilder;
use app\libs\StripeSDK\Builder\PaymentLinksBuilder;
use app\libs\StripeSDK\StripeAPI;
use app\Request;
use app\service\payment\PaymentInterface;

class StripeService extends PaymentBase implements PaymentInterface
{

    public function placeOrder()
    {
        try {
            $stripe = new StripeAPI($this->payment);
            # 1.创建用户
            $customerBuilder = new CustomerBuilder($this->order);
            $customerRes = $stripe->getStripe()->customers->create($customerBuilder->toArray());
            $customer = $customerRes->toArray();
            $this->saveSendRequest(['params'=>$customerBuilder->toArray(),'result'=>$customer,'api'=>"customer"]);
            if (empty($customer)) throw new \Exception('create stripe customer error');
            $builder = new PaymentLinksBuilder($this->order);
            $session = $builder->toArray();
            $session['customer'] = $customer['id'];
            //$session['customer_email'] = $customer['email'];
            $response = $stripe->getStripe()->checkout->sessions->create($session);
            $result = $response->toArray();
            $this->saveSendRequest(['params'=>$builder->toArray(),'result'=>$result,'api'=>'sessions']);
            $transaction_id = $result['id'] ?? '';
            if (empty($transaction_id)) throw new \Exception('create stripe order error');
            $approval_url = $result['url'];
            return compact(
                'transaction_id',
                'approval_url'
            );
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}