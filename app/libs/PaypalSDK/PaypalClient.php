<?php

namespace app\libs\PaypalSDK;

use app\exception\BusinessException;
use app\model\ShopsPayment;
use Omnipay\PayPal\RestGateway;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalHttp\HttpRequest;

class PaypalClient
{
    protected  PayPalHttpClient $gateway;
    public function __construct(public ShopsPayment $payment)
    {
        $config = $this->payment->config;
        $environmentClass = $this->payment->mode == ShopsPayment::MODE_SANDBOX ? SandboxEnvironment::class : ProductionEnvironment::class;
        $clientClass = new \ReflectionClass($environmentClass);
        $client = $clientClass->newInstance($config['app_key'],$config['app_secret']);
        $this->gateway = new PayPalHttpClient($client);
    }

    public function send(HttpRequest $request)
    {
        try {
            $request->prefer('return=representation');
            $response = $this->gateway->execute($request);
            return json_decode(json_encode($response, JSON_UNESCAPED_UNICODE), true);
        }catch (\Exception $e){
            throw new BusinessException($e->getMessage());
        }
    }

}