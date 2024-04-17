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
    public function fire(Job $job, $data)
    {
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
        $request = $data['request'] ?? [];
       $this->order = Orders::findOrEmpty($data);
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
        $response = $client->completePurchase($this->order->transaction_id,'7W8EXFWVYHUPA');
        $result = $response->getData();
        $state = $result['state'] ?? '';
        return $state == 'approved';
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