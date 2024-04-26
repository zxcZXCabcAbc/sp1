<?php

namespace app\libs\PayoneerSDK\Builder;

use app\model\Orders;
use app\trait\PaymentTrait;
use support\FormateHelper;

abstract class BaseAbstract
{
    use PaymentTrait;
    protected $order;
    public function __construct(Orders $orders)
    {
        $this->order = $orders;
    }

    //商品信息
    public function getProducts($b_shop_id = 0)
    {
        $products = [];
        foreach ($this->order->items as $product){
          $products[] = [
              'amount'=>$product->price,
              'name'=>$product->name,
              'currency'=>$this->order->currency,
              'quantity'=>1,
          ];
        }

        if($this->order->shippingLine){
            //增加运费
            $shippingFee = [
                'amount'=>$this->order->shippingLine->price,
                'name'=>$this->order->shippingLine->title ?: 'Shipping fees',
                'currency'=>$this->order->currency,
                'quantity'=>1,
            ];
            array_push($products,$shippingFee);
        }
        return $products;

    }

    public function getStyle()
    {
        return [
            //'cssOverride'=>"",
            //'resolution'=>'3x',
            //'challengeWindowSize'=>"600x400",
            'hostedVersion'=>'v4',
            //'primaryColor'=>'#21bb21',
            //'backgroundType'=>'BACKGROUND_COLOR',
            //'backgroundColor'=>'#2196f3',
            'language'=>'en'
        ];
    }

    //支付信息
    public function getPayment()
    {
        return [
            'reference'=>pathinfo($this->order->admin_graphql_api_id,PATHINFO_BASENAME),
            'amount'=>$this->order->total_price,
            'currency'=>$this->order->currency,
            'taxAmount'=>$this->order->total_tax,
        ];
    }

    //风险参数
    public function getRiskData()
    {
        return [
            'customer'=>[
                'paymentAttemptsLastDay'=>3,
                'paymentAttemptsLastYear'=>1,
                'cardRegistrationAttemptsLastDay' =>3,
                'purchasesLastSixMonths'=>1,
                'suspiciousActivity'=>true,

            ],
        ];
    }

    //回调地址
    protected function getCallback(){
        $returnUrl = $this->order->return_url;
        $cancelUrl = $this->order->cancel_url;
        $notifyUrl = $this->order->notify_url;
        return [
            'returnUrl'=>$returnUrl,
            'notificationUrl'=>$notifyUrl,
            'backToShopUrl'=>$cancelUrl,
            'cancelUrl'=>$cancelUrl,
        ];
    }
}