<?php

namespace app\libs\AsiabillSDK;

use app\model\ShopsPayment;
use Asiabill\Classes\AsiabillIntegration;

class AsiabillClient
{
    public AsiabillIntegration $asiabill;
    protected string $requestType;
    protected array $body = [];

    public function getRequestType(): string
    {
        return $this->requestType;
    }

    public function setRequestType(string $requestType): AsiabillClient
    {
        $this->requestType = $requestType;
        return $this;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(array $body): AsiabillClient
    {
        $this->body = $body;
        return $this;
    }
    public function __construct(ShopsPayment $payment)
    {
        $cnf = $payment->config;
        $mode = $payment->mode == ShopsPayment::MODE_SANDBOX ? 'test' : 'live';
        $this->asiabill = new AsiabillIntegration($mode,$cnf['app_key'],$cnf['app_secret']);
    }

    //请求
    public function send()
    {
        return $this->asiabill->request($this->getRequestType(),$this->getBody());
    }

    public function getAsiabill()
    {
        return $this->asiabill;
    }

}