<?php

namespace app\service\payment;

use app\constant\CommonConstant;
use app\libs\AirwallexSDK\Action\PaymentIntent;
use app\libs\AirwallexSDK\Build\PaymentIntentBuilder;
use app\model\Notify;
use app\model\ShopsPayment;
use app\Request;
use app\service\payment\PaymentInterface;

class AirwallexService extends PaymentBase implements PaymentInterface
{

    const CHECKOUT_URL_LIVE = 'https://checkout.airwallex.com';
    const CHECK_URL_SANDBOX = 'https://checkout-demo.airwallex.com';
    public function placeOrder()
    {
        $client = new PaymentIntent($this->payment);
        $builder = new PaymentIntentBuilder($this->order);
        $result = $client->create_payment_intent($builder);
        $this->saveSendRequest(['params'=>$builder->toArray(),'result'=>$result]);
        if(empty($result)) throw new \Exception('create airwallex order error');
        $approval_url = $this->get_payment_url($result);
        $transaction_id = $result['id'];
        return compact('approval_url','transaction_id');
    }


    //获取支付链接
    public function get_payment_url($result)
    {
        $path = '/#/standalone/checkout';
        $theme = [
            'fonts'=>[
                [
                    'src'=>'https://checkout.airwallex.com/fonts/CircularXXWeb/CircularXXWeb-Regular.woff2',
                    'family'=>'AxLLCircular',
                    'weight'=>400
                ],
            ],
        ];

        //withBilling:true,requiredBillingContactFields: ['name''email ,'address']
        $query = [
            'client_secret'=>$result['client_secret'],
            'intent_id'=>$result['id'],
            'currency'=>$this->order->currency,
            'mode'=>'payment',
            'theme'=>urlencode(json_encode($theme,JSON_UNESCAPED_UNICODE)),
            'locale'=>'en',
            //'methods'=>urlencode(json_encode(['card'])),
            'sessionId'=>session_id(),
            'withBilling'=>true,
            //'requiredBillingContactFields[]'=>'name',
            //'requiredBillingContactFields[]'=>'email',
            //'requiredBillingContactFields[]'=>'address',
            'requiredBillingContactFields'=> json_encode(['name','email','address'],JSON_UNESCAPED_UNICODE),
        ];
        $queryStr = http_build_query($query);
        $baseUrl = $this->payment->mode == ShopsPayment::MODE_LIVE ? self::CHECKOUT_URL_LIVE : self::CHECK_URL_SANDBOX;
        return sprintf('%s%s?%s',$baseUrl,$path,$queryStr);

    }
}