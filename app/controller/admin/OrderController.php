<?php

namespace app\controller\admin;

use app\BaseController;
use app\constant\CommonConstant;
use app\constant\ModelConstant;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use Carbon\Carbon;
use think\facade\View;
use think\helper\Str;
use think\Request;

class OrderController extends BaseController
{
    public function index()
    {
        $shopList = Shops::query()->where('status',ModelConstant::STATUS_ON)->field(['id','name'])->select()->toArray();
        $paymentList = ShopsPayment::query()->field(['id','account'])->select()->toArray();
        $orderStatus = Orders::$orderStatus;
        View::assign(['title'=>'订单列表',
            'shopList'=>$shopList,
            'paymentList'=>$paymentList,
            'orderStatus'=>$orderStatus
        ]);

        return View::fetch('admin/order_list');
        //return View::fetch('admin/test');
    }

    //
    public function getList(Request $request)
    {
        $list = Orders::query()
               //订单编号
                ->when($request->has('last_order_name') && !empty($request->param('last_order_name')),function ($q) use ($request){
                    return $q->where('last_order_name','like',trim($request->param('last_order_name')) . '%');
                })
            //草稿订单
            ->when($request->has('name') && !empty($request->param('name')),function ($q) use ($request){
                return $q->where('name','like',trim($request->param('name')) . '%');
            })

            //店铺
            ->when($request->has('payment_id') && !empty($request->param('payment_id')),function ($q) use ($request){
                return $q->where('payment_id',$request->param('payment_id'));
            })
            //支付方式
            ->when($request->has('shop_id') && !empty($request->param('shop_id')),function ($q) use ($request){
                return $q->where('shop_id',$request->param('shop_id'));
            })
            //状态
            ->when($request->has('order_status') && !empty($request->param('order_status')),function ($q) use ($request){
                return $q->where('order_status',$request->param('order_status'));
            })
            //时间
            ->when($request->has('created_at') && !empty($request->param('created_at')),function ($q) use ($request){
                $created_at = $request->param('created_at');
                $created_at = explode('-',$created_at);
                $dateTime = [Carbon::parse($created_at[0])->startOfDay()->timestamp,Carbon::parse($created_at[1])->endOfDay()->timestamp];
                //dd($dateTime);
                return $q->whereBetween('created_at',$dateTime);
            })
            ->order('created_at','DESC')
            ->paginate(intval($request->param('limit',10)))
            ->toArray();
            $list['count'] = $list['total'];
            $list['code'] = 0;
            $this->formatOrderData($list['data']);
            return json($list);
    }

    protected function formatOrderData(&$data)
    {
        if(empty($data)) return [];
        $shopList = Shops::query()->field(['id','host'])->select()->toArray();
        $shopList = array_column($shopList,'host','id');
        $paymentList = ShopsPayment::query()->field(['id','pay_method'])->select()->toArray();
        $paymentList = array_column($paymentList,'pay_method','id');
        foreach ($data as &$item){
            $item['shop_name'] = $shopList[$item['shop_id']] ?? '-';
            $pay_method = $paymentList[$item['payment_id']] ?? 0;
            $item['pay_method'] = ShopsPayment::$payMethodNames[$pay_method] ?? '-';
            $item['created_at'] = Carbon::parse($item['created_at'])->toDateTimeString();
        }
    }

    public function show(Request $request,Orders $order)
    {
        $data = $order->toArray();
        $data['goodsList'] = $order->items()->field(['name','sku','quantity','price','image','variant_title'])->select()->toArray();
        $data['shippingLines'] = $order->shippings()->field(['title','price'])->select()->toArray();
        $data['shippingAddress'] = $order->shippingAddress->toArray();
        $data['billingAddress'] = $order->billingAddress->toArray();
        $data['customer'] = $order->customer->toArray();
        $data['title'] = "订单详情";
        $data['order_name'] = Str::contains($data['last_order_name'],'#') ? $data['last_order_name'] : $data['name'];
        $data['orderId'] = $data['order_id'] > 0 ? $data['order_id'] : pathinfo($data['admin_graphql_api_id'],PATHINFO_BASENAME);
        $data['created_at'] = Carbon::parse($data['created_at'])->format('Y年m月d日 H:i');
        //dd($data);
        return view('admin/order_detail',$data);
    }
}