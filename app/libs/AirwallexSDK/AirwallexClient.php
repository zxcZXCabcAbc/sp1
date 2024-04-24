<?php

namespace app\libs\AirwallexSDK;

use app\constant\CommonConstant;
use app\helpers\RedisHelper;
use app\libs\HttpSDK\HttpService;
use app\model\ShopsPayment;

class AirwallexClient extends HttpService
{
    public static  $appKey;
    public static  $appSecrect;
    public static  $appUser;
    const BASE_URL = 'https://api.airwallex.com';
    const SANDBOX_URL = 'https://api-demo.airwallex.com';
    public static $cacheToken;
    protected $expire = 20 * 60;
    public static $access_token;
    protected static $apiUrl;
    protected static $shopId;
    protected static $version = 'v1';
    const CHANNEL = 'airwallex';
    protected $cnf;
    public function __construct(ShopsPayment $payment)
    {
        self::$apiUrl = $payment->mode == ShopsPayment::MODE_LIVE ? self::BASE_URL : self::SANDBOX_URL;
        $paymentCnf = $payment->config;
        self::$appKey = $paymentCnf['app_key'];
        self::$appSecrect = $paymentCnf['app_secret'];
        self::$appUser = $paymentCnf['merchant_no'];
        self::$shopId = $paymentCnf['shop_id'];
        self::$cacheToken = 'AIRWALLEX_KEY_ACCOUNT_' . self::$shopId . '_MODE_' . $payment->mode;
        $this->setBaseUrl(self::$apiUrl)
             ->setChannel(self::CHANNEL);
        $this->getAccessToken();
        if(self::$access_token){
            $this->setHeader(['Authorization'=>self::$access_token]);
        }
    }


    public function getAccessToken()
    {
        if(RedisHelper::Exists(self::$cacheToken)){
            self::$access_token = RedisHelper::Get(self::$cacheToken);
        }else{
            $result = $this
                ->setPath("/api/v1/authentication/login")
                ->setMethod('POST')
                ->setHeader(['x-client-id'=>self::$appKey,'x-api-key'=>self::$appSecrect])
                ->send();
            self::$access_token = 'Bearer ' . $result['token'];
            RedisHelper::Set(self::$cacheToken,self::$access_token,$this->expire);
        }
    }

}