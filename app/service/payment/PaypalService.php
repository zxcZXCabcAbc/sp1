<?php

namespace app\service\payment;

use app\constant\CommonConstant;
use app\libs\PaypalSDK\action\PurchasePaypal;
use app\libs\PaypalSDK\builder\PaypalBuilder;
use app\libs\PaypalSDK\builder\PurchaseBuilder;
use app\Request;
use think\facade\Log;
use think\helper\Arr;
use function Clue\StreamFilter\fun;

class PaypalService extends PaymentBase implements PaymentInterface
{

    public function placeOrder()
    {
        $client = new PurchasePaypal($this->payment);
        $builder = new PurchaseBuilder($this->order);
        //dd($builder->toArray());
        $response = $client->purchase($builder);
        $result = $response['result'] ?? [];
        if(empty($result)) throw new \Exception('create order error');
        $this->saveSendRequest(['request'=>$builder->toArray(), 'result'=>$result,]);
        $transaction_id = $result['id'] ?? 0;
        if(empty($transaction_id)) throw new \Exception($response->getMessage());

        $approval_urls = array_filter($result['links'],function ($link){
            return $link['rel'] == 'approve';
        });
        $approval_urls = Arr::first($approval_urls);
        $approval_url = $approval_urls['href'] ?? '';
        return compact('transaction_id','approval_url');
    }



}