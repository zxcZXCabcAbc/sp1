<?php
declare (strict_types = 1);

namespace app\event;

use app\listener\PushOrderListener;
use app\model\Orders;
use think\facade\Event;

class PushOrder
{
    public Orders $orders;
    public function __construct(Orders $orders)
    {
        $this->orders = $orders;
        //Event::listen(PushOrder::class,PushOrderListener::class);
    }
}
