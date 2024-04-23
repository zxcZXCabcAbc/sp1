<?php

namespace app\queue;

use app\libs\PaypalSDK\action\PurchasePaypal;
use app\model\Orders;
use app\model\ShopsPayment;
use think\facade\Event;
use think\queue\Job;

class CapturePaymentQueue
{
    protected Orders $order;
    protected array $request;
    public function fire(Job $job, $data)
    {
        dump('***************正在执行订单ID: ' . $data['order_id'] . ' *********支付');
        // 执行任务
        $isJobDone = $this->run($data);
        if ($isJobDone) {
            // 任务执行成功 删除任务
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                // 任务重试3次后 删除任务
                $job->delete();
            }
        }
        $job->delete();
    }

    public function run($data)
    {
        $order_id = $data['order_id'];
        $this->request = $data['request'] ?? [];
       $this->order = Orders::findOrEmpty($order_id);
       if($this->order->isEmpty()) return true;
       if($this->order->order_status == Orders::ORDER_STATUS_COMPLETED) return true;
       $payment = $this->order->payment;
       switch ($payment->pay_method){
           case ShopsPayment::PAY_METHOD_PAYPAL:
               $result = $this->handleWithPaypal();
               break;
           case ShopsPayment::PAY_METHOD_ASIABILL:
               $result = $this->handleWithAsiabill();
               break;
           case ShopsPayment::PAY_METHOD_PAYONEER:
               $result = $this->handleWithPayoneer();
               break;
           default:
               $result =  true;
       }

       if($result){
           $this->order->order_status = Orders::ORDER_STATUS_COMPLETED;
           $this->order->save();
           //创建shopify订单
          event('PushOrder',$this->order);
       }

       return $result;
    }

    //paypal
    protected function handleWithPaypal()
    {
        $client = new PurchasePaypal($this->order->payment);
        //获取详情
        $payRes = $client->fetchPurchase($this->order->transaction_id);
        $payRes = $payRes['result'];
        if(empty($payRes)) throw new \Exception('订单异常');
        if(isset($payRes['error']) && !empty($payRes['error'])) throw new \Exception('paypal error: ' . $payRes['error']['message']);
        if(isset($payRes['status']) && $payRes['status'] != 'COMPLETED') {
            #2.执行支付
            $paymentSource = [
                'payment_source'=>[
                    'token'=>[
                        'id'=>$this->order->transaction_id,
                        'type'=>'BILLING_AGREEMENT'
                    ],
                ],
            ];
            $payRes = $client->completePurchase($this->order->transaction_id,$paymentSource);
            $payRes = $payRes['result'];
        }
        return $payRes['status'] == 'COMPLETED';
    }

    //asiabill
    protected function handleWithAsiabill()
    {
        return true;
    }

    //payoneer
    protected function handleWithPayoneer()
    {
        return true;
    }

}