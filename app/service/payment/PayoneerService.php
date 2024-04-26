<?php

namespace app\service\payment;

use app\libs\PayoneerSDK\Action\Payment;
use app\libs\PayoneerSDK\Builder\PaymentBuilder;
use app\Request;
use app\service\payment\PaymentInterface;

class PayoneerService extends PaymentBase implements PaymentInterface
{

    public function placeOrder()
    {
        $builder = new PaymentBuilder($this->order);
        $client = new Payment($this->payment);
        $result = $client->createPayment($builder);
        $this->saveSendRequest(['params'=>$builder->toArray(),'result'=>$result]);
        $this->checkResult($result);
        $identification = $result['identification'];
        $transaction_id = $identification['longId'];
        $links = $result['redirect'] ?? [];
        if(empty($links)) throw new \Exception('A create order fail');
        $paypal_approve_url = $links['url'] ?? '';
        return compact('transaction_id', 'paypal_approve_url');
    }

    protected function checkResult($result){
        $returnCode = $result['returnCode'] ?? [];
        if (empty($returnCode)) throw new \Exception($result['resultInfo']);
        $isOk = $returnCode['name'] ?? 'err';
        if ($isOk != 'OK') throw new \Exception($result['resultInfo']);
    }
}