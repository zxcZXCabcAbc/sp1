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
        //$approval_url = $this->get_payment_url($result);
        $transaction_id = $result['id'];
        $client_secret = $result['client_secret'];
        $pay_result = $result;
        return compact('client_secret','transaction_id','pay_result');
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
    

    //确认支付
    public function confirmPayment()
    {
        $client = new PaymentIntent($this->payment);
        $account = $this->request->post('account');
        $params = $this->getPaymentMethod($account);
        $result = $client->confirm_a_paymentIntent($this->order->transaction_id,$params);
        dd($result);
    }

    protected function getPaymentMethod($account)
    {
        return [
            'request_id'=>uniqid(),
            'payment_method'=>[
                'card'=>[
                    'billing'=>[
                        'address'=>[
                            'city'=>$this->order->billingAddress->city,
                            'country_code'=>$this->order->billingAddress->country_code,
                            'postcode'=>$this->order->billingAddress->zip,
                            'state'=>$this->order->billingAddress->province,
                            'street'=>$this->order->billingAddress->address1,
                        ],
                        'email'=>$this->order->contact_email,
                        'first_name'=>$this->order->billingAddress->first_name,
                        'last_name'=>$this->order->billingAddress->last_name,
                        'phone_number'=>$this->order->billingAddress->phone,
                    ],
                    'cvc'=>$account['verificationCode'],
                    'expiry_month'=>$account['expiryMonth'],
                    'expiry_year'=>$account['expiryYear'],
                    'name'=>$account['holderName'],
                    'number'=>$account['number'],
                ],
            ],
            'type'=>'card',
            'payment_method_options'=>[
                'card'=>[
                   'authorization_type'=>'final_auth',
                    'auto_capture'=>true,
                ],
            ],
        ];
    }
}