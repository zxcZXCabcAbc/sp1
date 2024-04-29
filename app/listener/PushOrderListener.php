<?php
declare (strict_types = 1);

namespace app\listener;

use app\model\Orders;
use app\model\ShopsPayment;
use app\service\shopify\action\rest\DraftOrderRest;
use app\service\shopify\action\rest\OrderRest;

class PushOrderListener
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle(Orders|null $order)
    {
        dump('*********************完成订单ID: ' . $order->id .'************');
       $rest = new DraftOrderRest($order->shop_id);
       $draft_id = pathinfo($order->admin_graphql_api_id,PATHINFO_BASENAME);
       $draft_id = 0 + $draft_id;
       $rest->complete_draft_order($draft_id,['payment_pending'=>false]);//完成订单
       $info  = $rest->receive_a_draft_order($draft_id);
       $data = $info->toArray();
       //dump($data);
       tplog('shopify_order_'. $order->id,$data,'shopify');
       $order->order_id = $data['customer']['last_order_id'] ?? 0;
       $order->last_order_name = $data['customer']['last_order_name'] ?? '';
       $order->order_status = Orders::ORDER_STATUS_COMPLETED;
       $order->error_msg = '';
       $order->save();
       # 更新订单增加note
        $this->addShopifyOrderNote($order);
    }


    //增加note日志
    protected function addShopifyOrderNote(Orders $order)
    {
        try {
            $rest = new OrderRest($order->shop_id);
            $note = "交易号: " . $order->transaction_id . ',订单号: ' . $order->order_no;
            $tags = $order->transaction_id .','.$order->order_no.',' .$order->payment->pay_method_name;
            $tags = substr($tags,0,40);
            $note_attributes = [
                ['name' => 'tradeNo', 'value' => $order->transaction_id],
                ['name' => 'orderNo', 'value' => $order->order_no],

            ];
            $res = $rest->update_order($order->order_id, compact('note', 'note_attributes','tags'));
            tplog('update_shopify_order_'. $order->id,$res,'shopify');
        }catch (\Exception $e){
            tplog('update_shopify_order_err_'. $order->id,$e->getMessage(),'shopify');
        }
    }
}
