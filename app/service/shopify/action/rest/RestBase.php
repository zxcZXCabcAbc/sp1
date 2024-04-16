<?php

namespace app\service\shopify\action\rest;

use app\model\Shops;
use app\trait\PaymentTrait;

abstract class RestBase
{
    use PaymentTrait;
    protected $session;
    protected $rest;
    public function __construct()
    {
        $this->setUp();//初始化
        $this->session = $this->getPaySession();//获取session
        $this->getInstance();//获取实例化rest
    }

    public function getInstance(){
        try {
            $shop = $this->getShop();
            $className = static::class;
            dump(compact('className'));
            $class = pathinfo($className, PATHINFO_BASENAME);
            $class = str_replace('Rest', '', $class);
            $version = $shop ? $shop->version : config('shopify.app_version');
            $version = str_replace('-', '_', $version);
            $shopifyClass = sprintf('Shopify\Rest\Admin%s\%s', $version, $class);
            dump(compact('shopifyClass'));
            $rest = new \ReflectionClass($shopifyClass);
            $this->rest = $rest->newInstance($this->session);
        }catch (\ReflectionException $e){
            dump($e);
        }
    }


}