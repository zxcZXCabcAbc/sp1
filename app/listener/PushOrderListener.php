<?php
declare (strict_types = 1);

namespace app\listener;

use app\service\shopify\action\rest\OrderRest;

class PushOrderListener
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($order)
    {
        $rest = new OrderRest();
    }
}
