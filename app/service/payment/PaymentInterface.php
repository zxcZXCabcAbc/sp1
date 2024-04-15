<?php

namespace app\service\payment;

use app\Request;

interface PaymentInterface
{
    public function placeOrder();
}