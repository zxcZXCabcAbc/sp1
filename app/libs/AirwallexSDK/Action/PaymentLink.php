<?php

namespace app\libs\AirwallexSDK\Action;
use app\libs\AirwallexSDK\AirwallexClient;

class PaymentLink extends AirwallexClient
{
    /**
     * @see https://www.airwallex.com/docs/api#/Payment_Acceptance/Payment_Links/_api_v1_pa_payment_links_create/post
     * @desc 创建支付链接
     */
    public function create_a_paymentLink($params)
    {
        return $this->setPath("/api/v1/pa/payment_links/create")
                    ->setMethod("POST")
                    ->setOption(['json'=>$params])
                    ->send();
    }
}