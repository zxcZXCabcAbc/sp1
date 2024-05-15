<?php

namespace app\service\shopify\action\rest;
use think\Request;

class DraftOrderRest extends RestBase
{
    /**
     * @param Request $request
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/draftorder#post-draft-orders
     * @desc 创建
     */
    public function create_draft_order(array $data) :array
    {
            $this->rest->line_items = $data['line_items'];
            $this->rest->shipping_address = $data['shipping_address'];
            $this->rest->email = $data['email'];
            $this->rest->shipping_line = $data['shipping_line'];
            $this->rest->use_customer_default_address = true;
            if (isset($data['billing_address']) && !empty($data['billing_address'])) {
                $this->rest->billing_address = $data['billing_address'];
            }

//        $this->rest->customer = [
//            "id" => 207119551
//        ];
            $this->rest->save(
                true, // Update Object
            );

            return $this->rest->toArray();
    }

    /**
     * @param $draft_id
     * @param array $params
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/draftorder#put-draft-orders-draft-order-id
     * @desc 修改订单
     */
    public function update_draft_order($draft_id,array $params = []):array
    {
        $this->rest->id = $draft_id;
        if(isset($params['line_items']) && !empty($params['line_items'])) $this->rest->line_items = $params['line_items'];
        if(isset($params['shipping_address']) && !empty($params['shipping_address'])) $this->rest->shipping_address = $params['shipping_address'];
        if(isset($params['billing_address']) && !empty($params['billing_address'])) $this->rest->billing_address = $params['billing_address'];
        if(isset($params['email']) && !empty($params['email'])) $this->rest->email = $params['email'];
        if(isset($params['shipping_line']) && !empty($params['shipping_line'])) $this->rest->shipping_line = $params['shipping_line'];
        /*
         $draft_order->applied_discount = [
            "description" => "Custom discount",
            "value_type" => "percentage",
            "value" => "10.0",
            "amount" => "19.90",
            "title" => "Custom"
];
         */
        if(isset($params['applied_discount']) && !empty($params['applied_discount'])) $this->rest->applied_discount = $params['applied_discount'];
        if(isset($params['tags']) && !empty($params['tags'])) $this->rest->tags = $params['tags'];
        if(isset($params['note']) && !empty($params['note'])) $this->rest->note = $params['note'];
        $this->rest->save(true);
        return $this->rest->toArray();
    }

    /**
     * @param $draft_id
     * @param array $params
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/draftorder#put-draft-orders-draft-order-id-complete
     * @desc 完成订单
     */
    public function complete_draft_order($draft_id,array $params) :array
    {
        $this->rest->id = $draft_id;
        $this->rest->complete($params);
        return $this->rest->toArray();
    }

    /**
     * @param int $draft_id
     * @param array $draft_order_invoice
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/draftorder#post-draft-orders-draft-order-id-send-invoice
     * @desc 发送发票信息
     */
    public function send_draft_invoice(int $draft_id,array $draft_order_invoice = []):array
    {
        $this->rest->id = $draft_id;
        $this->rest->send_invoice([],['draft_order_invoice'=>$draft_order_invoice]);
        return $this->rest->toArray();
    }

    public function receive_a_draft_order(int $draft_id)
    {
        return $this->rest->find($this->session,$draft_id);
    }
}