<?php

namespace app\logic;

use app\model\Address;
use app\model\Customer;
use app\model\LineItems;
use app\model\Orders;
use app\model\ShippingLines;
use app\service\action\rest\DraftOrderRest;
use app\trait\PaymentTrait;
use think\annotation\Inject;
use think\Request;

class OrderLogic
{
    use PaymentTrait;
    #[Inject]
    protected DraftOrderRest $rest;

    /**
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @desc 创建草稿订单
     */
    public function createDraftOrder(Request $request)
    {
        $draft_order = $this->rest->create_draft_order($request);
        //存订单
        $order_id = $this->saveOrder($draft_order);
        return compact('draft_order','order_id');
    }

    /**
     * @param array $order
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @dec 保存订单
     */
    protected function saveOrder(array $order)
    {
        try {
            if (empty($order)) throw new \Exception('create draft order error');
            $lineItems = $order['line_items'];
            $customer = $order['customer'] ?? [];
            $shippingAddress = $order['shipping_address'] ?: [];
            if ($shippingAddress) $shippingAddress['type'] = Address::SHIPPING_ADDRESS;
            $shippingLines = $order['shipping_lines'] ?? [];
            $orderModel = new Orders();
            //dd($orderModel->setIsConvert(true)->fill($order)->getDatas());
            $orderId = $orderModel->setIsConvert(true)->fill($order)->saveData();
            $orders = Orders::query()->find($orderId);
            $this->saveLineItems($orders, $lineItems);//保存商品
            $this->saveAddress($orders, $shippingAddress);//保存地址
            $this->saveShippingLines($orders, $shippingLines);//保存物流
            $this->saveCustomer($orders, $customer);//保存顾客
            return $orderId;
        }catch (\Exception $e){
            dd($e);
        }
    }

    /**
     * @param Orders $orders
     * @param $lineItems
     * @return array|false
     * @desc 保存商品
     */
    protected function saveLineItems(Orders $orders,$lineItems)
    {
        $lineItemsData = (new LineItems())->fill($lineItems)->getDatas();
        return $orders->items()->saveAll($lineItemsData);
    }

    /**
     * @param Orders $orders
     * @param $shippingAddress
     * @return array|false
     * @desc 保存地址
     */
    protected function saveAddress(Orders $orders,$shippingAddress)
    {
        if(empty($shippingAddress)) return false;
        $addressData = (new Address())->fill($shippingAddress)->getDatas();
        return $orders->addresses()->saveAll($addressData);
    }

    /**
     * @param Orders $orders
     * @param $shippingLines
     * @return array|false
     * @dsec 保存物流
     */
    protected function saveShippingLines(Orders $orders,$shippingLines)
    {
        if(empty($shippingLines)) return false;
        $shippingLinesData = (new ShippingLines())->fill($shippingLines)->getDatas();
        return $orders->shippings()->saveAll($shippingLinesData);
    }

    /**
     * @param Orders $orders
     * @param $customer
     * @return false|\think\Model
     * @desc 保存客户
     */
    protected function saveCustomer(Orders $orders,$customer)
    {
        if(empty($customer)) return false;
        $customerData = (new Customer())->fill($customer)->getDatas();
        return $orders->customer()->save($customerData);
    }


}