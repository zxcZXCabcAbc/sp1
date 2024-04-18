<?php

namespace app\libs\PaypalSDK\builder;

class PurchaseBuilder extends PaypalBuilder
{
    public function toArray()
    {
        return [
            'intent'=>'CAPTURE',
            'purchase_units'=>$this->getTransactions(),
            'application_context'=>[
                'return_url'=>$this->order->return_url,
                'cancel_url'=>$this->order->cancel_url,
                'shipping_preference'=>'SET_PROVIDED_ADDRESS'
            ],
        ];
    }

    public function getTransactions()
    {
        return [
            [
                'amount'=>[
                    'value'=>$this->order->total_price,
                    'currency_code'=>$this->order->currency,
                    'breakdown'=>[
                        'item_total'=>['value'=>$this->order->subtotal_price, 'currency_code'=>$this->order->currency,],
                        'shipping'=> ['value'=>$this->order->total_shipping_price,'currency_code'=>$this->order->currency],
                        'tax_total'=>['value'=>$this->order->total_tax,'currency_code'=>$this->order->currency]
                    ],
                ],
                'items'=>$this->getItemLines(),
                'shipping'=>$this->getShippingAddress(),
            ]
        ];
    }

    public function getItemLines()
    {
        $lines = [];
        foreach ($this->order->items as $item){
            $lines[] = [
                'name'=>$item->name,
                'description'=>$item->title,
                'quantity'=>$item->quantity,
                'unit_amount'=>['value'=>$item->price,'currency_code'=>$this->order->currency],
                'sku'=>$item->sku ?: pathinfo($item->admin_graphql_api_id,PATHINFO_FILENAME),
            ];
        }
        return $lines;
    }

    public function getShippingAddress()
    {

        return [
            'type'=>'SHIPPING',
            'name'=>[
                'full_name'=>$this->order->shippingAddress->name,
            ],
            'address'=>[
                'country_code'=>$this->order->shippingAddress->country_code,
                'postal_code'=>$this->order->shippingAddress->zip,
                'address_line_1'=>$this->order->shippingAddress->address1,
                'admin_area_1'=>$this->order->shippingAddress->province,
                'admin_area_2'=>$this->order->shippingAddress->city,
            ],
        ];

    }
}