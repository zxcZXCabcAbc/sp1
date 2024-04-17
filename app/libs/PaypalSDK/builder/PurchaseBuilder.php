<?php

namespace app\libs\PaypalSDK\builder;

class PurchaseBuilder extends PaypalBuilder
{
    public function toArray()
    {
        return [
            'amount'=>$this->order->total_price,
            'return_url'=>$this->order->return_url,
            'cancel_url'=>$this->order->cancel_url,
            'currency'=>$this->order->currency,
            'payer'=>['payment_method'=>'paypal'],
            'transactions'=>$this->getTransactions(),
            'redirect_urls'=>[
                'return_url'=>$this->order->return_url,
                'cancel_url'=>$this->order->cancel_url,
            ],
        ];
    }

    public function getTransactions()
    {
        return [
            [
                'amount'=>[
                    'total'=>$this->order->subtotal_price,
                    'currency'=>$this->order->currency,
                ],
                'item_list'=>[
                    'items'=>$this->getItemLines(),
                ],
                'notify_url'=>$this->order->notify_url
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
                'price'=>$item->price,
                'sku'=>$item->sku ?: pathinfo($item->admin_graphql_api_id,PATHINFO_FILENAME),
                'currency'=>$this->order->currency
            ];
        }
        return $lines;
    }
}