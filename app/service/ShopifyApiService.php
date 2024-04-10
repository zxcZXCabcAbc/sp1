<?php

namespace app\service;

use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Rest;
use Shopify\Clients\Storefront;
use Shopify\Context;
use Shopify\Auth\Session;
use think\Request;

class ShopifyApiService
{
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
        $path = runtime_path('/tmp/php_sessions');
        $scopes = [
            'unauthenticated_read_product_listings',
            'unauthenticated_read_product_tags',
            'unauthenticated_read_checkouts',
            'unauthenticated_write_checkouts'
        ];
        Context::initialize(
            apiKey: env('SHOPIFY_API_KEY'),
            apiSecretKey: env('SHOPIFY_API_SECRET'),
            scopes: $scopes,
            hostName: env('SHOPIFY_APP_HOST_NAME'),
            sessionStorage: new FileSessionStorage($path),
            apiVersion: self::$api_version,
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
        $domain = env('SHOPIFY_APP_HOST_NAME');
        if($apiType == self::STORE_API){
            $access_token = env('SHOPIFY_API_STOREFRONT_TOKEN');
            $this->api = new Storefront($domain, $access_token);
        }else{
            $access_token = env('SHOPIFY_API_ADMIN_TOKEN');
            $this->api = new Rest($domain,$access_token);
            $this->session = new Session("session_id", $domain, true, "1234");
            $this->session->setAccessToken($access_token);
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