<?php
declare (strict_types = 1);

namespace app\command\Test;

use app\event\PushOrder;
use app\libs\AsiabillSDK\builder\CheckoutBuilder;
use app\model\Customer;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use app\queue\CapturePaymentQueue;
use app\service\payment\PaymentBase;
use app\trait\PaymentTrait;
use Asiabill\Classes\AsiabillIntegration;
use Omnipay\PayPal\RestGateway;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Event;
use Omnipay\Omnipay;
use think\facade\Log;
use think\facade\Queue;

class LfxTest extends Command
{
    use PaymentTrait;
    protected function configure()
    {
        // 指令配置
        $this->setName('lfx:test')
            ->setDescription('the lfxtest command');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            $data = [
                'order_id'=>3,
                'request'=>[],
            ];

            $order = Orders::find(3);
           event('PushOrder',$order);
            dd(11);



            Queue::push(CapturePaymentQueue::class,$data,'t1');
            dd(11);
            $paypal = new RestGateway();
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
                'amount'=>$amount,
                'return_url'=>$url,
                'cancel_url'=>$url,
                'currency'=>$currency,
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
            $response = $paypal->purchase($params)->send();
            dd($response);



            // 指令输出
            $path = public_path() . '\json\draft_order.json';
            $str = file_get_contents($path);
            $orderData = json_decode($str, true);
            $order = $orderData['order'];
            $lineItems = $order['line_items'] ?? [];
            $customer = $order['customer'] ?? [];
            $billingAddress = $order['billing_address'] ?? [];
            $shippingAddress = $order['shipping_address'] ?? [];
            $shippingLines = $order['shipping_lines'] ?? [];
            //$orderModel = Orders::query()->find(1);
            $orderModel = new Orders();
            $res  = $orderModel->setIsConvert(true)->fill($order)->getDatas();
            dd($res);
            //$lineItemsData = (new LineItems())->fill($lineItems)->getDatas();
            //$res = $orderModel->items()->saveAll($lineItemsData);
            $customerData = (new Customer())->fill($customer)->getDatas();
            $res = $orderModel->customer()->save($customerData);
            dd($res);




            //$this->formateData($orderModel,$order);
            //$orderId = Orders::query()->save($order);
            //$ord = Ord

        }catch (\Exception $e){
            dump($e);
        }
    }

//    protected function formateData(BaseModel $model,&$data)
//    {
//
//        $fields = $model->getField();
//        foreach ($data as $key => $item){
//            if(in_array($key,$model->getDateField())){
//                $data[$key] = Carbon::parse($item)->timestamp;
//            }
//            if(!in_array($key,$fields)) unset($data[$key]);
//        }
//    }
}
