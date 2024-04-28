<?php
declare (strict_types = 1);

namespace app\listener;

use app\model\Orders;
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
    }
}
