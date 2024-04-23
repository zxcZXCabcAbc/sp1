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
       $rest = new DraftOrderRest($order->shop_id);
       $draft_id = pathinfo($order->admin_graphql_api_id,PATHINFO_BASENAME);
       $draft_id = 0 + $draft_id;
       $result = $rest->complete_draft_order($draft_id,['payment_pending'=>false]);
       $info  = $rest->receive_a_draft_order($draft_id);
       $data = $info->toArray();
       $order->order_id = $data['order_id'];
       $order->last_order_name = $data['last_order_name'];
       $order->save();
    }
}
