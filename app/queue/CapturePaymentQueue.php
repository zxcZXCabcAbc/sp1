<?php

namespace app\queue;

use app\libs\PaypalSDK\action\PurchasePaypal;
use app\model\Orders;
use app\model\ShopsPayment;
use think\facade\Event;
use think\helper\Arr;
use think\queue\Job;

class CapturePaymentQueue
{
    protected Orders $order;
    protected array $request;
    public function fire(Job $job, $data)
    {
        if(empty($data) || !isset($data['order_id'])){
            $job->delete();
            return false;
        }
        // 执行任务
        $isJobDone = $this->run($data);
        if ($isJobDone) {
            // 任务执行成功 删除任务
            $job->delete();
        } else {
            if ($job->attempts() > 1) {
                // 任务重试3次后 删除任务
                $job->delete();
            }
        }

    }

    public function run($data)
    {
        try {
            dump('***************正在执行订单ID: ' . $data['order_id'] . ' *********支付');
            $order_id = $data['order_id'];
            $this->request = $data['request'] ?? [];
            $this->order = Orders::findOrEmpty($order_id);
            if ($this->order->isEmpty()) return true;
            if(is_null($this->order)) return true;//订单是NULL 问题
            if ($this->order->order_status == Orders::ORDER_STATUS_COMPLETED) return true;
            $payment = $this->order->payment;
            switch ($payment->pay_method) {
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
                    $result = true;
            }

            if ($result) {
                //修改订单状态
                $this->order->order_status = Orders::ORDER_STATUS_COMPLETED;
                $this->order->error_msg = '';
                $this->order->save();
                //队列处理订单
                event('PushOrder', $this->order);
            }

            return $result;
        }catch (\Exception $e){
            dump($e);
            tplog('capture order',$e->getMessage());
            $this->order->order_status = Orders::ORDER_STATUS_FAIL;
            $this->order->error_msg = $e->getMessage();
            $this->order->save();
            return false;
        }
    }

    //paypal
    protected function handleWithPaypal()
    {
        dump('*****************paypal支付: order_id: ' . $this->order->id . ', transaction_id: '. $this->order->transaction_id);
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
            //交易ID
            $purchase_units = $payRes['purchase_units'] ?? [];
            $tradeId = '';
            if($purchase_units){
                $purchaseFirst = Arr::first($purchase_units);
                $payments = $purchaseFirst['payments'];
                $capturesFirst = Arr::first($payments['captures']);
                $tradeId = $capturesFirst['id'];
            }
            $this->order->order_no = $tradeId;
            $this->order->save();

            tplog("paypal_order_". $this->order->id,$payRes,'paypal');
        }
        return $payRes['status'] == 'COMPLETED';
    }

    //asiabill
    protected function handleWithAsiabill()
    {
        $data = $this->request['data'] ?? [];
        if(empty($data)) return false;
        $orderStatus = $data['orderStatus'];
        return $orderStatus == 'success';
    }

    //payoneer
    protected function handleWithPayoneer()
    {
        return true;
    }

}