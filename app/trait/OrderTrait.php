<?php

namespace app\trait;

use app\model\Address;
use app\model\Customer;
use app\model\LineItems;
use app\model\Orders;
use app\model\ShippingLines;

trait OrderTrait
{
    /**
     * @param array $order
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @dec 保存订单
     */
    protected function saveOrder(array $order,Orders $orders = null)
    {
        try {
            if (empty($order)) throw new \Exception('create draft order error');
            $order['order_type'] = Orders::ORDER_DRAFT;
            if(array_key_exists('email',$order)) $order['contact_email'] = $order['email'];
            $lineItems = $order['line_items'];
            $customer = $order['customer'] ?? [];
            $shippingAddress = $order['shipping_address'] ?: [];
            if ($shippingAddress) $shippingAddress['type'] = Address::SHIPPING_ADDRESS;
            $shippingLines = $order['shipping_lines'] ?? [];
            $orderModel = new Orders();
            if(is_null($orders)) {
                $orderId = $orderModel->setIsConvert(true)->fill($order)->saveData();
                $orders = Orders::query()->find($orderId);
            }else{
                $orderData = $orderModel->setIsConvert(true)->fill($order)->getDatas();
                $orders->update($orderData);
            }
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
        $orders->items()->delete();
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
        $billingAddress = $shippingAddress;
        $billingAddress['type'] = Address::BILLING_ADDRESS;
        $addressList = [$shippingAddress,$billingAddress];
        $addressData = (new Address())->fill($addressList)->getDatas();
        $orders->addresses()->delete();
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
        $orders->shippings()->delete();
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
        $orders->customer()->delete();
        return $orders->customer()->save($customerData);
    }

}