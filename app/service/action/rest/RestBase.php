<?php

namespace app\service\action\rest;

use app\trait\PaymentTrait;

class RestBase
{
    use PaymentTrait;
    protected $session;
    public function __construct()
    {
        $this->setUp();
        $this->session = $this->getPaySession();
    }
}