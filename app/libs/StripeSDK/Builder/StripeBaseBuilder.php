<?php

namespace app\libs\StripeSDK\Builder;

use app\model\Orders;

class StripeBaseBuilder
{
    public function __construct(public Orders $order)
    {
    }
}