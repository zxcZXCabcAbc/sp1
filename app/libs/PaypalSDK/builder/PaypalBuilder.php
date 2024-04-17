<?php

namespace app\libs\PaypalSDK\builder;

use app\model\Orders;

class PaypalBuilder
{
    public function __construct(protected Orders $order)
    {
    }
}