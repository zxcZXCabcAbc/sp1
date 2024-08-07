<?php

namespace app\trait;

use app\constant\CommonConstant;
use app\model\Address;
use app\model\Customer;
use app\model\LineItems;
use app\model\Orders;
use app\model\ShippingLines;
use think\Request;

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
    protected function saveOrder(array $order,Request $request,Orders $orders = null)
    {
        try {
            if (empty($order)) throw new \Exception('create draft order error');
            $order['order_type'] = Orders::ORDER_DRAFT;
            $order['shop_id'] = request()->middleware('x_shop_id');
            if(array_key_exists('email',$order)) $order['contact_email'] = $order['email'];
            $checkout = $request->param('checkout',[]);
            //$lineItems = $order['line_items'];
            $lineItems = $checkout['cart']['items'] ?? [];
            list($goodsList,$shipping_protection) = $this->formatLineItems($lineItems,$request);
            $customer = $order['customer'] ?? [];
            $addresses = [];
            $billingAddress = $shippingAddress = $request->param('shipping_address') ?: [];
            if($request->has('billing_address')) $billingAddress = $request->param('billing_address');
            if ($shippingAddress) {
                $shippingAddress['type'] = Address::SHIPPING_ADDRESS;
                $addresses[] = $shippingAddress;
            }
            if ($billingAddress) {
                $billingAddress['type'] = Address::BILLING_ADDRESS;
                $addresses[] = $billingAddress;
            }
            $shippingLines = array_key_exists('shipping_line',$order) ? $order['shipping_line'] : $request->param('shipping_line',[]);
            $orderModel = new Orders();
            $order['shipping_protection'] = $shipping_protection;
            $order['total_shipping_price'] = $shippingLines['price'] ?? '0.00';
            $order['browser_ip'] = $request->ip();
            $order['app_id'] = $request->header('X-Opc-Client-Id','');
            $orderNo = array_key_exists('last_order_name',$customer) && !empty($customer['last_order_name']) ? $customer['last_order_name'] : $order['name'];
            $order['order_no'] = $this->formatOrderNo($order['shop_id'],$orderNo);
            if(is_null($orders)) {
                //存token
                $checkout_id = $request->param('checkout_id','');
                $token = $checkout['cart']['token'] ?? '';
                if($token) $order['token'] = $token;
                if($checkout_id) $order['checkout_id'] = $checkout_id;
                $orderId = $orderModel->setIsConvert(true)->fill($order)->saveData();
                $orders = Orders::query()->find($orderId);
            }else{
                $orderData = $orderModel->setIsConvert(true)->fill($order)->getDatas();
                $orders->save($orderData);
                $orderId = $orders->id;
            }
            $this->saveLineItems($orders, $goodsList);//保存商品
            $this->saveAddress($orders, $addresses);//保存地址
            $this->saveShippingLines($orders, $shippingLines);//保存物流
            $this->saveCustomer($orders, $customer);//保存顾客
            return $orderId;
        }catch (\Exception $e){
            dump($e);
            if($orders){
                $orders->addresses()->delete();
                $orders->customer()->delete();
                $orders->items()->delete();
                $orders->shippings()->delete();
                $orders->delete();
            }
            throw new \Exception($e->getMessage());
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
        $addressData = (new Address())->fill($shippingAddress)->getDatas();
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
        $shippingLines['title'] = json_encode(['title'=>$shippingLines['title']]);
        $shippingLinesData = (new ShippingLines())->fill($shippingLines)->getDatas();
        //dump(compact('shippingLinesData'));
        $shippingLinesData['custom'] = $shippingLinesData['custom'] ?? false;
        $shippingLinesData['custom'] = $shippingLinesData['custom'] ? 1 : 0;
        $orders->shippings()->delete();
        return $orders->shippings()->save($shippingLinesData);
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

    protected function formatLineItems(array $lineItems,Request $request)
    {
        $checkout = $request->param('checkout');
        $items = $checkout['cart']['items'];
        //$images = array_column($items,'image','variant_id');
        $shipping_protection = 0;
        $goodsList = [];
        foreach ($lineItems as $index => $item){
            $title = $item['title'];
            $item['price'] = bcdiv($item['price'],100,2);
            $item['variant_title'] = json_encode(['title'=>$item['variant_title'] ?: $item['title']]);
            $item['name'] = json_encode(['title'=>$item['product_title']]);
            $item['title'] = json_encode(['title'=>$item['title']]);
            if(in_array($title,CommonConstant::SHIPPING_PROTECTION_FEE)) {
                $shipping_protection = $item['price'];
            }else{
                $goodsList[] = $item;
            }

        }

        return [$goodsList,$shipping_protection];

    }

    protected function formatOrderNo($shopId,$orderNo)
    {
        $shopId = $shopId < 10 ? '0' . $shopId : $shopId ;
        $time = date("His");
        $orderNo = str_replace('#','',$orderNo);
        return sprintf('#%s%s%s',$shopId,$orderNo,$time);
    }

}