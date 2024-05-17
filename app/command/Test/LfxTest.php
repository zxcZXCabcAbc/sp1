<?php
declare (strict_types = 1);

namespace app\command\Test;

use app\event\PushOrderToShopify;
use app\helpers\RedisHelper;
use app\helpers\RedisLock;
use app\libs\AirwallexSDK\Action\PaymentIntent;
use app\libs\AirwallexSDK\Build\PaymentIntentBuilder;
use app\libs\AsiabillSDK\action\CheckoutAsiabill;
use app\libs\AsiabillSDK\action\TransactionsAsiabill;
use app\libs\AsiabillSDK\builder\CheckoutBuilder;
use app\libs\PaypalSDK\action\PurchasePaypal;
use app\libs\PaypalSDK\builder\PurchaseBuilder;
use app\libs\StripeSDK\Action\Cards;
use app\libs\StripeSDK\Builder\CardsBuilder;
use app\model\Customer;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use app\queue\CapturePaymentQueue;
use app\queue\TestQueue;
use app\service\payment\PaymentBase;
use app\service\shopify\action\rest\DraftOrderRest;
use app\service\shopify\action\rest\OrderRest;
use app\trait\OrderTrait;
use app\trait\PaymentTrait;
use Asiabill\Classes\AsiabillIntegration;
use Omnipay\PayPal\RestGateway;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Event;
use Omnipay\Omnipay;
use think\facade\Log;
use think\facade\Redis;
use think\facade\Session;
use think\helper\Arr;
use think\Request;

class LfxTest extends Command
{
    use PaymentTrait,OrderTrait;
    protected $clientId = 'AStgX20Bx4ZGVF7OovRksHtjOvCXxOz4F0KsE35TRu_v-JbMSO61cTfpAnFfQ9G5KhOA4CwiOgRzoYaW';
    protected $secret = 'EPivtk3r7bBXQIJlkFEctTb_poZXZnNvkQ_Me87I185b9xHtdADMlPgy30sWicsD2kZXmNTlXc5MoWHM';
    protected function configure()
    {
        // 指令配置
        $this->setName('lfx:test')
            ->setDescription('the lfxtest command');
    }

    protected function execute(Input $input, Output $output)
    {
        try {

            $arr = ['test'=>'name'];

           Session::set('name','lfx');
           dd(Session::get('name'));

            $payment = ShopsPayment::query()->find(5);
            dump($payment->mode);
            dd($payment->config);
            $account = [
                "holderName"=> "Jhone",
                "number"=> "4242424242424242",
                "expiryMonth"=> "02",
                "expiryYear"=> "2026",
                "verificationCode"=> "421"
            ];
            $card = new Cards($payment);
            //$builder = new CardsBuilder($order,$account);
            //$res = $card->create_payment_method($builder);
            $params = [
                'card'=>[
                    'exp_month'=>$account['expiryMonth'],
                    'exp_year'=>$account['expiryYear'],
                    'number'=>$account['number'],
                    'cvc'=>$account['verificationCode'],
                    'name'=>$account['holderName'],
                    'address_city'=>$order->billingAddress->city,
                    'address_country'=>$order->billingAddress->country_code,
                    'address_line1'=>$order->billingAddress->address1,
                    'address_state'=>$order->billingAddress->province,
                    'address_zip'=>$order->billingAddress->zip,
                    'currency'=>$order->currency,
                ],
            ];

            $res = $card->create_card_token($params);
            dd($res);


//            $builder = new CheckoutBuilder($order);
//            $builder->setCustomerPaymentMethodId("pm_12451111");
//            dd($builder->toArray());

            //$order = Orders::query()->find(83);
            $payment = ShopsPayment::query()->find(6);
            //$asiabill = new CheckoutAsiabill($payment);
            $asiabill = new TransactionsAsiabill($payment);
            //$builder = new CheckoutBuilder($order);
            //dd($builder->toArray());
            //$res = $asiabill->confirm_charge('','pm_1784518746061651968',$builder);
            $res = $asiabill->query_a_transaction([
                //'tradeNo'=>'2024042817455202638284',
                'startTime'=>'2024-04-28T00:00:00',
                'endTime'=>'2024-04-28T23:59:59',
                'pageSize'=>10,
                'pageIndex'=>1
            ]);
            dd($res);




            $payment = ShopsPayment::query()->find(3);
            $airwallex = new PaymentIntent($payment);
            $builder = new PaymentIntentBuilder($order);
            $res = $airwallex->create_payment_intent($builder);
            dd($res);


            //\event('PushOrder',$order);




            dd(11);


            $environment = new SandboxEnvironment($this->clientId,$this->secret);
            $client = new PayPalHttpClient($environment);
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "reference_id" => "test_ref_id1",
                    "amount" => [
                        "value" => "100.00",
                        "currency_code" => "USD"
                    ]
                ]],
                "application_context" => [
                    "cancel_url" => "https://example.com/cancel",
                    "return_url" => "https://example.com/return"
                ]
            ];
            //$request = new OrdersGetRequest('2W737695PF680443M');
            $request = new OrdersCaptureRequest('2W737695PF680443M');
            $response = $client->execute($request);
            dd((array)$response);

        }catch (\Exception $e){
            dump($e);
        }
    }

    protected function addShopifyOrderNote(Orders $order)
    {
        try {
            $pay_method = $order->payment->pay_method;
            if ($pay_method == ShopsPayment::PAY_METHOD_PAYPAL) return true;
            $rest = new OrderRest($order->shop_id);
            $note = "交易号: " . $order->transaction_id . ',订单号: ' . $order->order_no;
            $tags = $order->transaction_id .','.$order->order_no;
            $note_attributes = [
                ['name' => 'tradeNo', 'value' => $order->transaction_id],
                ['name' => 'orderNo', 'value' => $order->order_no],

            ];
            $res = $rest->update_order($order->order_id, compact('note', 'note_attributes','tags'));
            dd($res);
            tplog('update_shopify_order_'. $order->id,$res,'shopify');
        }catch (\Exception $e){
            tplog('update_shopify_order_err_'. $order->id,$e->getMessage(),'shopify');
        }
    }


    public function paypalOmnipay()
    {
        $paypal = Omnipay::create('PayPal_Rest');
        $paypal->initialize(
            [
                'clientId'=>'AStgX20Bx4ZGVF7OovRksHtjOvCXxOz4F0KsE35TRu_v-JbMSO61cTfpAnFfQ9G5KhOA4CwiOgRzoYaW',
                'secret'=>'EPivtk3r7bBXQIJlkFEctTb_poZXZnNvkQ_Me87I185b9xHtdADMlPgy30sWicsD2kZXmNTlXc5MoWHM',
                'testMode'=>true
            ]
        );
        $url = 'https://www.baidu.com';
        $amount = '1.00';
        $currency = 'USD';
        $params = [
            'intent'=>'sale',
            'payer'=>['payment_method'=>'paypal'],
            'transactions'=>[
                [
                    'amount'=>[
                        'total'=>$amount,
                        'currency'=>$currency,
                    ],
                    'item_list'=>[
                        'items'=>[
                            [
                                "name"=> "hat",
                                "description"=> "Brown hat.",
                                "quantity"=> 1,
                                "price"=> $amount,
                                "sku"=> 'SKU0055',
                                "currency"=> $currency
                            ]
                        ],
                    ],
                    'notify_url'=>$url
                ]
            ],
            'redirect_urls'=>[
                'return_url'=>$url,
                'cancel_url'=>$url
            ],
        ];
        $response = $paypal->purchase()->sendData($params);
        dd($response->getData());
    }
}
