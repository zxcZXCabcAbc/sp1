<?php

namespace app\libs\StripeSDK\Builder;

use app\model\Orders;
use app\trait\PaymentTrait;

class PaymentLinksBuilder extends StripeBaseBuilder
{
    use PaymentTrait;
    public function toArray()
    {

        return [
            'line_items'=>$this->getLineItems(),
            'mode'=>'payment',
            //'return_url'=>$cancel_url,
            'success_url'=>$this->order->return_url,
            'currency'=>$this->order->currency,
            //'ui_mode'=>'embedded',
            //'billing_address_collection'=>'required',
            'custom_text'=>[
                'shipping_address'=>['message'=>$this->order->shippingAddress->address_message]
            ],
            'shipping_address_collection'=>[
                'allowed_countries'=>['US','DE','FR','HK','SG','AU']
            ],
        ];
    }

    public function getLineItems()
    {
        $line_items = [];
        foreach ($this->order->items as $product){
            $line_items[] = [
                'quantity'=>$product->quantity,
                'price_data'=>[
                    'currency'=>$this->order->currency,
                    'product_data'=>['name'=>$product->title],
                    'unit_amount'=>$product->price * 100,
//                    'lookup_key'=>$product->sku,
                ],
            ];
        }

        if($this->order->shippingLine){
            $shippingFee = [
                'quantity'=>1,
                'price_data'=>[
                    'currency'=>$this->order->currency,
                    'product_data'=>['name'=>$this->order->shippingLine->title],
                    'unit_amount'=>$this->order->shippingLine->price * 100,
                ],
            ];
            array_push($line_items,$shippingFee);
        }

        if($this->order->total_tax > 0){
            $taxFee = [
                'quantity'=>1,
                'price_data'=>[
                    'currency'=>$this->order->currency,
                    'product_data'=>['name'=>'tax'],
                    'unit_amount'=>$this->order->total_tax * 100,
                ],
            ];
            array_push($line_items,$taxFee);
        }

         return $line_items;
    }
}