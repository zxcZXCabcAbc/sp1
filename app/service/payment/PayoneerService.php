<?php

namespace app\service\payment;

use app\libs\PayoneerSDK\Action\Payment;
use app\libs\PayoneerSDK\Builder\PaymentBuilder;
use app\Request;
use app\service\payment\PaymentInterface;
use think\helper\Arr;

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
        $pay_result = $result;
        return compact('transaction_id', 'paypal_approve_url','pay_result');
    }

    protected function checkResult($result){
        $returnCode = $result['returnCode'] ?? [];
        if (empty($returnCode)) throw new \Exception($result['resultInfo']);
        $isOk = $returnCode['name'] ?? 'err';
        if ($isOk != 'OK') throw new \Exception($result['resultInfo']);
    }

    public function confirmPayment()
    {
        $client = new Payment($this->payment);
        $result = $client->getListDetail($this->order->transaction_id);
        $this->saveSendRequest(['type'=>'detail','listId'=>$this->order->transaction_id,'result'=>$result]);
        $this->checkResult($result);
        $account = $this->request->post('account');
        $networks = $result['networks']['applicable'] ?? [];
        $networkList = [];
        if($networks) {$networkList = array_column($networks,'code');}
        $network = detectCardType($account['number']);
        if(!in_array($network,$networkList)) throw new \Exception("not applicable networks");
        $pay_result = $client->payWithNetWork($this->order->transaction_id,$network,$account);
        $this->saveSendRequest(['listId'=>$this->order->transaction_id,'network'=>$network,'account'=>$account,'pay_result'=>$pay_result]);
        return $pay_result;
    }
}