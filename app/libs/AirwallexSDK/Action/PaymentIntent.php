<?php

namespace app\libs\AirwallexSDK\Action;

use app\libs\AirwallexSDK\AirwallexClient;
use app\libs\AirwallexSDK\Build\PaymentIntentBuilder;

class PaymentIntent extends AirwallexClient
{
    /**
     * @see https://www.airwallex.com/docs/api#/Payment_Acceptance/Payment_Intents/_api_v1_pa_payment_intents_create/post
     * @desc 创建支付
     */
    public function create_payment_intent($params)
    {
        $params = $params instanceof PaymentIntentBuilder ? $params->toArray() : $params;
        return $this->setMethod('POST')
                    ->setPath('/api/v1/pa/payment_intents/create')
                    ->setOption(['json'=>$params])
                    ->send();

    }

    /**
     * @param $id
     * @see https://www.airwallex.com/docs/api#/Payment_Acceptance/Payment_Intents/_api_v1_pa_payment_intents__id_/get
     * @desc 获取支付详情
     */
    public function retrieve_a_paymentIntent($id)
    {
        return $this->setMethod('GET')
                    ->setPath("/api/v1/pa/payment_intents/{$id}")
                    ->send();
    }
    
}