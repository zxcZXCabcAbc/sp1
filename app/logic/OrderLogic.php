<?php

namespace app\logic;

use app\constant\CommonConstant;
use app\exception\BusinessException;
use app\libs\PaypalSDK\action\PurchasePaypal;
use app\libs\PaypalSDK\builder\PrePurchaseBuilder;
use app\libs\PaypalSDK\PaypalClient;
use app\model\Notify;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use app\service\payment\PaymentBase;
use app\service\payment\PaypalService;
use app\service\shopify\action\rest\DraftOrderRest;
use app\service\shopify\action\rest\ShippingZoneRest;
use app\trait\OrderTrait;
use app\trait\PaymentTrait;
use think\annotation\Inject;
use think\helper\Arr;
use think\Request;
use function Clue\StreamFilter\fun;

class OrderLogic
{
    use PaymentTrait,OrderTrait;
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
        $draftData = $this->formatDraft($request);
        $draft = $this->rest->create_draft_order($draftData);
        //存订单
        $order_id = $this->saveOrder($draft,$request);
        return compact('draft','order_id');
    }

    /**
     * @param Request $request
     * @param Orders $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @desc 修改草稿订单
     */
    public function updateDraftOrder(Request $request,Orders $order)
    {
        $draft_id = $order->admin_graphql_api_id;
        $draft_id = pathinfo($draft_id,PATHINFO_BASENAME);
        $draftData = $this->formatDraft($request);
        $draft = $this->rest->update_draft_order($draft_id,$draftData);
        $this->saveOrder($draft,$request,$order);
        return compact('draft');
    }

    public function placeOrder(Request $request,Orders $order)
    {

            #1.更新订单
            $order->payment_id = $request->post('payment_id');
            $order->save();
            #2.更新账单地址
            $this->saveBillingAddress($order, $request->param('billingAddress', []));
            #2.下单
            $payment = new PaymentBase($order, $request);
            return $payment->createThirdPayment();
    }

    protected function saveBillingAddress(Orders $order,array $billingAddressData)
    {
        if(empty($billingAddressData)) return true;
        $billingAddress = $order->billingAddress;
        $billingAddress->save($billingAddressData);
        return true;
    }

    public function getShippingZones(Request $request)
    {
        $rest = new ShippingZoneRest();
        $data = $rest->get_shipping_zones();
        $countryCode = $request->param('country_code');
        $countryCode = strtoupper($countryCode);
        $sub_total = $request->param('sub_total');
        $shipping_fee_list = [];
        foreach ($data as $item){
            $temp = $item->toArray();
            $countryies = $temp['countries'];
            $countryCodes = array_column($countryies,'code');
            if(in_array($countryCode,$countryCodes)){
                $shipping_fee_list = $temp['price_based_shipping_rates'];
            }
        }

        if($shipping_fee_list){
            $shipping_lines = [];
            foreach ($shipping_fee_list as $key => $line){
                $min_price = $line['min_order_subtotal'] ?: 0;
                $max_price = $line['max_order_subtotal'] ?: 9999999;
                if($sub_total < $max_price && $sub_total > $min_price){
                    $shipping_lines = [
                        'title'=>$line['name'],
                        'price'=>$line['price'],
                        'handle'=>null,
                        'custom'=>true
                    ];
                }
            }
            $shipping_fee_list = $shipping_lines;
        }
        return $shipping_fee_list;
    }

    //预下单
    public function prePayPaypal(Request $request)
    {
        $result = $this->createDraftOrder($request);
        $order = Orders::query()->findOrEmpty($result['order_id']);
        $payment = ShopsPayment::payment($request->middleware('x_shop_id'),ShopsPayment::PAY_METHOD_PAYPAL);
        if(empty($payment)) throw new \Exception('paypal no setting');
        $builder = new PrePurchaseBuilder($order);
        Notify::saveParams($order->id,$builder->toArray());
        $paypal = new PurchasePaypal($payment);
        $response = $paypal->purchase($builder);
        $result = $response['result'] ?? [];
        if(empty($result)) throw new BusinessException("create pre paypal order error");
        $order->transaction_id = $result['id'];
        $order->payment_id = $payment->id;
        $order->save();
        $links = array_filter($result['links'],function ($item){
            return $item['rel'] == 'approve';
        });
        $approval_urls = Arr::first($links);
        $approval_url = $approval_urls['href'] ?? '';
        return [
            'order_id'=>$order->id,
            'approval_url'=>$approval_url
        ];
    }

    public function getPaypalConfig(Request $request)
    {
        $payment = ShopsPayment::payment($request->middleware('x_shop_id'),ShopsPayment::PAY_METHOD_PAYPAL);
        if(empty($payment)) throw new \Exception('paypal no setting');
        return [
            'mode'=>$payment->mode == ShopsPayment::MODE_SANDBOX ? CommonConstant::MODE_SANDBOX_WORD : CommonConstant::MODE_LIVE_WORD,
            'client_id'=>$payment->mode == ShopsPayment::MODE_SANDBOX ? $payment->client_id_sandbox : $payment->client_id,
        ];
    }

    protected function formatDraft(Request $request):array
    {
        $checkout = $request->param('checkout',[]);
        if(empty($checkout)) throw new \Exception("miss checkout");
        $line_items = [];
        $cart = $checkout['cart'] ?? [];
        if(empty($cart)) throw new \Exception('cart is empty');
        $items = $cart['items'];
        foreach ($items as $item){
            $line_items[] = [
               'variant_id'=>$item['variant_id'],
                'product_id'=>$item['product_id'],
                'variant_title'=>$item['variant_title'] ?: $item['title'],
                'title'=>$item['title'],
                'price'=>bcdiv($item['price'],100,2),
                'quantity'=>$item['quantity'],
                'sku'=>$item['sku'],
            ];
        }
        $shipping_address = $request->param('shipping_address');
        $shipping_line = $request->param('shipping_line');
        $email = $request->param('email');
        $billing_address = $request->param('billing_address',[]);
        return compact('line_items','shipping_address','shipping_line','email','billing_address');


    }

    //获取订单详情
    public function getOrderDetail(Orders $order)
    {

        return [
             'order_num'=>$order->last_order_name,
             'username'=>$order->customer->username,
            'order_details'=>['email'=>$order->contact_email,],
            'payment_method'=>[
                'pay_method_name'=>$order->payment->pay_method_name,
                'currency'=>$order->currency,
                'total_price'=>$order->total_price
            ],
            'shipping_address'=>[
                'username'=>$order->shippingAddress->username,
                'address1'=>$order->shippingAddress->address1,
                'province'=>$order->shippingAddress->province,
                'city'=>$order->shippingAddress->city,
                'province_code'=>$order->shippingAddress->province_code,
                'country'=>$order->shippingAddress->country,
                'phone'=>$order->shippingAddress->phone,
            ],
            'billing_address'=>[
                'username'=>$order->billingAddress->username,
                'address1'=>$order->billingAddress->address1,
                'province'=>$order->billingAddress->province,
                'city'=>$order->billingAddress->city,
                'province_code'=>$order->billingAddress->province_code,
                'country'=>$order->billingAddress->country,
                'phone'=>$order->billingAddress->phone,
            ],
            'shipping_method'=>$order->shippingLine ? $order->shippingLine->title : 'Free International Shipping',
        ];
    }


}