<?php

namespace app\libs\PayoneerSDK;

use app\constant\CommonConstant;
use app\constant\ModelConstant;
use app\libs\HttpSDK\HttpService;
use app\model\ShopsPayment;

class PayoneerClient extends HttpService
{
    public static $base_url;

    protected $config;

    protected $path;

    protected $params;

    protected $debug = false;

    protected $method;

    protected $headers = [
        'Content-Type'=>[
            'application/json',
            'application/vnd.optile.payment.enterprise-v1-extensible+json'
        ],
        'Accept'=>[
            'application/json',
            'application/vnd.optile.payment.enterprise-v1-extensible+json'
        ],
    ];

    public static $division;

    protected $authorization;
    protected $username;
    protected $cnf;

    public function __construct(ShopsPayment $payment)
    {
        $mode = $payment->mode == ModelConstant::STATUS_OFF_NAME ? CommonConstant::MODE_SANDBOX_WORD : CommonConstant::MODE_LIVE_WORD;
        self::$base_url = "https://api.{$mode}.oscato.com";
        $paymentCnf = $payment->config;
        $this->authorization = $paymentCnf['app_secret'];
        $this->username = $paymentCnf['merchant_no'];
        self::$division = $paymentCnf['app_key'];
        $this->setHeader($this->header)
            ->setBaseUrl(self::$base_url)
            ->setAuth([$this->username,$this->authorization]);

    }
}
