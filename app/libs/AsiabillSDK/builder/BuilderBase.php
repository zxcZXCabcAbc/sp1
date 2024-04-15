<?php

namespace app\libs\AsiabillSDK\builder;

use app\model\Orders;

class BuilderBase
{
    protected string $customerId;
    protected string $customerPaymentMethodId;
    public function __construct(protected Orders $order)
    {
    }

    public function setCustomerId(string $customerId): BuilderBase
    {
        $this->customerId = $customerId;
        return $this;
    }
    public function getCustomerId():string
    {
        return $this->customerId;
    }

    public function getCustomerPaymentMethodId(): string
    {
        return $this->customerPaymentMethodId;
    }

    public function setCustomerPaymentMethodId(string $customerPaymentMethodId): BuilderBase
    {
        $this->customerPaymentMethodId = $customerPaymentMethodId;
        return $this;
    }

}