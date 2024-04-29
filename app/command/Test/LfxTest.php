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
use app\model\Customer;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use app\queue\CapturePaymentQueue;
use app\queue\TestQueue;
use app\service\payment\PaymentBase;
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

class LfxTest extends Command
{
    use PaymentTrait;
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
            //$order = Orders::query()->find(83);
            $payment = ShopsPayment::query()->find(6);
            //$asiabill = new CheckoutAsiabill($payment);
            $asiabill = new TransactionsAsiabill($payment);
            //$builder = new CheckoutBuilder($order);
            //dd($builder->toArray());
            //$res = $asiabill->confirm_charge('','pm_1784518746061651968',$builder);
            $res = $asiabill->query_a_transaction([
                'tradeNo'=>'2024042817455202638284',
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
