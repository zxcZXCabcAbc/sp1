<?php

namespace app\libs\StripeSDK\Builder;

use app\model\Orders;
use think\Request;

class StripeBaseBuilder
{
    public function __construct(public Orders $order,public Request|array $request)
    {
    }
}