<?php

namespace app\libs\PaypalSDK\builder;

use app\model\Orders;
use think\Request;

class PaypalBuilder
{
    public function __construct(protected Orders $order)
    {
    }
    public function getItemLines()
    {
        try {
            $lines = [];
            foreach ($this->order->items as $item) {
                $lines[] = [
                    'name' => substr($item->name, 0, 120),
                    'description' => substr($item->title, 0, 255),
                    'quantity' => $item->quantity,
                    'unit_amount' => ['value' => $item->price, 'currency_code' => $this->order->currency],
                    'sku' => $item->sku ?: pathinfo($item->admin_graphql_api_id, PATHINFO_FILENAME),
                ];
            }
            return $lines;
        }catch (\Exception $e){
            dd($e);
        }
    }

    public function getTransactions()
    {

        $transactions = [
            [
                'amount'=>[
                    'value'=>$this->calculateTotalMoney(),
                    'currency_code'=>$this->order->currency,
                    'breakdown'=>[
                        'item_total'=>['value'=>$this->order->subtotal_price, 'currency_code'=>$this->order->currency,],
                        'shipping'=> ['value'=>$this->order->total_shipping_price,'currency_code'=>$this->order->currency],
                        'tax_total'=>['value'=>Orders::EXTRA_MONEY,'currency_code'=>$this->order->currency],
                        //'handling'=>['value'=>Orders::EXTRA_MONEY,'currency_code'=>$this->order->currency]
                    ],
                ],
                'items'=>$this->getItemLines(),

            ]
        ];
        if($this->order->shippingAddress) $transactions[0]['shipping'] = $this->getShippingAddress();
        return $transactions;
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

    protected function calculateTotalMoney()
    {
        return Orders::EXTRA_MONEY + $this->order->subtotal_price + $this->order->total_shipping_price;
    }
}