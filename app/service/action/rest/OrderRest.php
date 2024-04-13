<?php

namespace app\service\action\rest;

use Shopify\Rest\Admin2023_04\Order;

class OrderRest extends RestBase
{

    /**
     * @param array $data
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2023-07/resources/order#post-orders
     * @desc 创建订单
     */
    public function create_order(array $data = []):array
    {
        /*
        $this->rest->line_items = [
            [
                "title" => "Big Brown Bear Boots",
                "price" => 74.99,
                "grams" => "1300",
                "quantity" => 1,
                "tax_lines" => [
                    [
                        "price" => 13.5,
                        "rate" => 0.06,
                        "title" => "State tax"
                    ]
                ]
            ]
        ];
        $this->rest->transactions = [
            [
                "kind" => "sale",
                "status" => "fail",
                "amount" => 74.99
            ]
        ];
        $this->rest->total_tax = 13.5;
        $this->rest->currency = "EUR";
        */
        $this->rest->line_items = $data['line_items'];
        $this->rest->transactions = $data['transactions'];
        $this->rest->total_tax = $data['total_tax'];
        $this->rest->currency = $data['currency'];
        $this->rest->financial_status = "pending";
        $this->rest->save(
            true, // Update Object
        );
        return $this->rest->toArray();
    }

    /**
     * @param $data
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/order#post-orders
     * @desc 创建顾客关联的订单
     */
    public function create_customer_order($data = []):array
    {
        $this->rest->line_items = $data['line_items'];

//        $this->rest->customer = [
//            "id" => $data['customer_id']
//        ];
        $this->rest->customer = $data['customer'];
        $this->rest->financial_status = "pending";
        $this->rest->save(
            true, // Update Object
        );
      return $this->rest->toArray();

    }

    /**
     * @param int $order_id
     * @param array $data
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/order#put-orders-order-id
     * @desc 修改订单
     */
    public function update_order(int $order_id,array $data):array
    {
        $this->rest->id = $order_id;
        if(isset($data['financial_status'])) $this->rest->financial_status = $data['financial_status'];
        if(isset($data['email']))  $this->rest->email = $data['email'];
        if(isset($data['phone']))  $this->rest->phone = $data['phone'];
        if(isset($data['shipping_address']))  $this->rest->shipping_address = $data['shipping_address'];
        if(isset($data['order_status'])) {
            $this->rest->transactions = [
                [
                    "kind" => "sale",
                    "status" => $data['order_status'],//pending,failure,success,error
                    "order_id" => $order_id
                ]
            ];
        }
        $this->rest->save(true);
        return $this->rest->toArray();
    }
}