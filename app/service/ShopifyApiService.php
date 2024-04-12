<?php

namespace app\service;

use app\trait\PaymentTrait;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Rest;
use Shopify\Clients\Storefront;
use Shopify\Context;
use Shopify\Auth\Session;
use Shopify\Utils;
use think\Request;

class ShopifyApiService
{
    use PaymentTrait;
    protected $api;
    const STORE_API = 1;
    const ADMIN_API = 2;
    protected $apiType;
    public $session;
    public static $api_version;

    public function __construct($apiType = self::STORE_API,Request $request = null)
    {
        $this->apiType = $apiType;
        self::$api_version = env('SHOPIFY_API_VERSION');
        //初始化
        $this->setUp();
        $domain = env('SHOPIFY_APP_HOST_NAME');
        if($apiType == self::STORE_API){
            $access_token = env('SHOPIFY_API_STOREFRONT_TOKEN');
            $this->api = new Storefront($domain, $access_token);
        }else{
            $access_token = env('SHOPIFY_API_ADMIN_TOKEN');
            $this->api = new Rest($domain,$access_token);
            if(is_null($request)){
                $this->session = new Session("session_id", $domain, true, "1234");
                $this->session->setAccessToken($access_token);
            }else{
                $token = $request->header('X-Opc-Checkout-Token');
                $header = [
                    'Content-Type'=> 'application/json',
                    'authorization'=>'Bearer '.$token
                ];
                $cookie = [];
                $this->session = Utils::loadCurrentSession($header, $cookie, true);
            }

        }

    }

    protected function send($query,$variables = [])
    {
        $params = ['query'=>$query];
        if(!empty($variables)) $params['variables'] = json_encode($variables);
        $response = $this->api->query($params);
        return $response->getDecodedBody();

    }
}