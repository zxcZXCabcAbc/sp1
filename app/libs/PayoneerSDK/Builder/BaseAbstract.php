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
        dump('*************组装B站数据: ' . time());
        $products =  $this->transferProductsToThirdParty($this->order,Orders::PAY_METHOD_PAYONEER,$b_shop_id);
        dump('*************组装B站数据: ' . time());
        //增加运费
        $shippingFee = [
            'amount'=>$this->order->shipping,
            'name'=>$this->order->logistics_id ?: 'Shipping fees',
            'currency'=>$this->order->currency,
            'quantity'=>1,
        ];
        array_push($products,$shippingFee);
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
            'reference'=>$this->order->order_sn,
            'amount'=>0 + $this->order->total_money,
            'currency'=>$this->order->currency,
            'taxAmount'=>0 + $this->order->tax_total,
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
    protected function getCallback($b_shop_id = 0){
        list($returnUrl,$cancelUrl) = $this->getReturnUrl($this->order,$b_shop_id);
        $domainUrl = optional($this->order->shopB)->domain_url;
        $domainUrl = FormateHelper::handleDomainUrl($domainUrl);
        $notifyUrl = $domainUrl . '/notify/payoneer';
        return [
            'returnUrl'=>$returnUrl,
            'notificationUrl'=>$notifyUrl,
            'backToShopUrl'=>$cancelUrl,
            'cancelUrl'=>$cancelUrl,
        ];
    }
}