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
    public function setUp($shop_id = 0)
    {
        $this->setShop($shop_id);//设置店铺
        $path = runtime_path('/tmp/php_sessions');
        $scopes = [
            'unauthenticated_read_product_listings',
            'unauthenticated_read_product_tags',
            'unauthenticated_read_checkouts',
            'unauthenticated_write_checkouts'
        ];
        //dd($this->shop->api_key,$this->shop->api_secret,$this->shop->host,$this->shop->version);
        Context::initialize(
            apiKey: $this->shop->api_key,
            apiSecretKey: $this->shop->api_secret,
            scopes: $scopes,
            hostName: $this->shop->host,
            sessionStorage: new FileSessionStorage($path),
            apiVersion: $this->shop->version,
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
    }

    public function getPaySession() : Session
    {
        $session = new Session(uniqid(time()), $this->shop->host, true, md5(time()));
        $session->setAccessToken($this->shop->admin_token);
        return $session;
    }

    protected function setShop($shop_id = 0)
    {
        $shop_id = $shop_id ?: request()->middleware('x_shop_id');
        $this->shop = Shops::query()->find($shop_id);
    }

    public function getShop()
    {
        return $this->shop;
    }




}