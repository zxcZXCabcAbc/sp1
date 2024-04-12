<?php

namespace app\service\action\rest;

use Shopify\Rest\Admin2023_04\DraftOrder;
use think\Request;

class DraftOrderRest extends RestBase
{
    public function create_draft_order(Request $request)
    {
        $draft_order = new DraftOrder($this->session);
        $draft_order->line_items = $request->post('line_items');
        $draft_order->use_customer_default_address = true;
//        $draft_order->customer = [
//            "id" => 207119551
//        ];
        $draft_order->save(
            true, // Update Object
        );

        return $draft_order->toArray();
    }
}