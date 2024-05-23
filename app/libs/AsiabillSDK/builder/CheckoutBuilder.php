<?php

namespace app\libs\AsiabillSDK\builder;

use app\model\Orders;

class CheckoutBuilder extends BuilderBase
{
    private $rate = 0.15;
    const CUSTOMER_CLIENT_IP = '52.36.193.167';
    public function toArray()
    {
        $requestData = [
            'callbackUrl'=>$this->order->notify_url,
            //'customerId'=>$this->getCustomerId(),
            'customerPaymentMethodId'=>$this->getCustomerPaymentMethodId(),
            'isMobile'=>'0',
            'customerIp'=>self::CUSTOMER_CLIENT_IP,
            'orderAmount'=> $this->getTotalMoney(),
            'orderCurrency'=>$this->order->currency,
            'platform'=>'php_SDK',
            'remark'=>'remark',
            'returnUrl'=>$this->order->return_url,
            'webSite'=>$this->order->shop->website ?: $this->order->shop->host,
            'shipping'=>$this->getShipping(),
            'goodsDetails'=>$this->getGoodsDetails(),
            'orderNo'=>$this->order->order_no,//订单号
        ];
        if($this->getCustomerId()) $requestData['customerId'] = $this->getCustomerId();
        return $requestData;
    }

    public function getShipping()
    {
        return [
            'address' => array(
                'line1' => $this->order->shippingAddress->address1,
                'line2' => $this->order->shippingAddress->address2,
                'city' => $this->order->shippingAddress->city,
                'country' => $this->order->shippingAddress->country,
                'state' => $this->order->shippingAddress->province,
                'postalCode' => $this->order->shippingAddress->zip
            ),
            'email' => $this->order->contact_email,
            'firstName' => $this->order->shippingAddress->first_name,
            'lastName' => $this->order->shippingAddress->last_name,
            'phone' => $this->order->shippingAddress->phone,
        ];
    }

    public function getGoodsDetails()
    {
        $products = [];
        foreach ($this->order->items as $items){
            $products[] = [
                'goodsCount' => $items->quantity,
                'goodsPrice' => $items->price,
                'goodsTitle' => $items->title
            ];
        }

        if($this->order->shippingLine && $this->order->shippingLine->price > 0){
            $products[] = [
                'goodsCount' => 1,
                'goodsPrice' => $this->order->shippingLine->price,
                'goodsTitle' => $this->order->shippingLine->title,
            ];
        }
        //税费
//        $products[] = [
//            'goodsCount'=>1,
//            'goodsPrice'=>bcmul($this->order->total_price,$this->rate,2),
//            'goodsTitle'=>"Tax",
//        ];

        return $products;
    }

    protected function getTotalMoney()
    {
        if($this->order->shipping_protection > 0){
            return bcadd($this->order->total_price,$this->order->shipping_protection,2);
        }
        return bcmul($this->order->total_price,1 + $this->rate,2);
    }

}