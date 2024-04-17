<?php

namespace app\trait;

use app\model\Orders;
use app\model\Shops;
use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\Session;
use Shopify\Context;

trait PaymentTrait
{
    protected ?Shops $shop = null;
    public function setUp()
    {
        $this->setShop();//设置店铺
        $path = runtime_path('/tmp/php_sessions');
        $scopes = [
            'unauthenticated_read_product_listings',
            'unauthenticated_read_product_tags',
            'unauthenticated_read_checkouts',
            'unauthenticated_write_checkouts'
        ];
        Context::initialize(
            apiKey: $this->shop ? $this->shop->api_key : env('SHOPIFY_API_KEY'),
            apiSecretKey: $this->shop ? $this->shop->api_secret : env('SHOPIFY_API_SECRET'),
            scopes: $scopes,
            hostName: $this->shop ? $this->shop->host : env('SHOPIFY_APP_HOST_NAME'),
            sessionStorage: new FileSessionStorage($path),
            apiVersion: $this->shop ? $this->shop->version : env('SHOPIFY_API_VERSION'),
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
    }

    public function getPaySession() : Session
    {
        $session = new Session(uniqid(time()), env('SHOPIFY_APP_HOST_NAME'), true, md5(time()));
        $session->setAccessToken($this->shop ? $this->shop->admin_token :env('SHOPIFY_API_ADMIN_TOKEN'));
        return $session;
    }

    protected function setShop()
    {
        $host = request()->header('X-Opc-Shop-Id','');
        $this->shop = Shops::query()->host($host)->find();
    }

    public function getShop()
    {
        return $this->shop;
    }




}