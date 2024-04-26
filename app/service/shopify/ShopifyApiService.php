<?php

namespace app\service\shopify;

use app\model\ShopsPayment;
use app\trait\PaymentTrait;
use Shopify\Auth\Session;
use Shopify\Clients\Rest;
use Shopify\Clients\Storefront;
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

    public function __construct($shopId = 0)
    {
        //初始化
        $this->setUp($shopId);
        $shop = $this->getShop();
        $domain = $shop->host;
        $access_token = $this->shop->store_token;
        $this->api = new Storefront($domain, $access_token);
    }

    protected function send($query,$variables = [])
    {
        $params = ['query'=>$query];
        if(!empty($variables)) $params['variables'] = json_encode($variables);
        $response = $this->api->query($params);
        return $response->getDecodedBody();

    }
}