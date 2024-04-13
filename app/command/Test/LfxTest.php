<?php
declare (strict_types = 1);

namespace app\command\Test;

use app\event\PushOrder;
use app\listener\PushOrderListener;
use app\model\BaseModel;
use app\model\Customer;
use app\model\LineItems;
use app\model\Orders;
use app\service\action\rest\CustomerRest;
use app\service\action\rest\OrderRest;
use Carbon\Carbon;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Event;
use think\Model;

class LfxTest extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('lfx:test')
            ->setDescription('the lfxtest command');
    }

    protected function execute(Input $input, Output $output)
    {
        try {

            //$rest = new CustomerRest();
            //dd($rest->create_customer());
            $order = Orders::query()->find(2);
            Event::trigger(PushOrder::class,new PushOrder($order));

            dd(11);



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
