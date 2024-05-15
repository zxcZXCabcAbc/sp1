<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Notify;
use app\model\Orders;
use app\model\ShopsPayment;
use app\queue\CapturePaymentQueue;
use Carbon\Carbon;
use think\facade\Queue;
use think\Request;

class NotifyController extends BaseController
{

    //异步通知
    public function index(Request $request)
    {
        $params = $request->all();
        tplog('notify',$params,'notify');
        $type = $request->param('type','');
        if($type && $type == 'transaction.success'){//asiabill异步通知
            $data = $request->param('data');
            $tradeNo = $data['tradeNo'] ?? '';
            $order = Orders::query()->where('transaction_id',$tradeNo)->findOrEmpty();
            if(!$order->isEmpty()){
                Notify::saveParams($order->id,$params,Notify::TYPE_NOTIFY,ShopsPayment::PAY_METHOD_ASIABILL);//存数据库
                Queue::push(CapturePaymentQueue::class,['order_id'=>$order->id,'request'=>$params],'checkout');
            }
        }

        echo 'success';
    }

    //跳转加载页面
    public function checkout(Request $request,Orders $order)
    {
           $host = $order->shop->host;
        try {
            $params = $request->all();
            if (empty($params)) throw new \Exception("no params");
            tplog('checkout', $params,'paypal');
            Notify::saveParams($order->id,$params,Notify::TYPE_CHECKOUT,$order->payment->pay_method);//存数据库
            $success = $request->param('success','');
            $access_token = $request->param('access_token','');
            //if($access_token != $order->token) throw new \Exception('token invalid');
            if($success != 'true') throw new \Exception('checkout exception');
            $order->token = $order->token . '_' . time();//修改token
            $order->save();
            //队列处理
            Queue::push(CapturePaymentQueue::class, ['order_id' => $order->id, 'request' => $request->all()], 'checkout');
            $query = ['cid'=>$order->app_id];
            $path = sprintf('%s/%s/%s','a/s/checkout',$order->checkout_id,'credit-card-3ds-redirect-loading');
            $url = sprintf('%s/%s?%s',$host,$path,http_build_query($query));
            return redirect(domain($url));
        }catch (\Exception $e){
            $query = ['error'=>$e->getMessage()];
            $url = sprintf('%s?%s',$host,http_build_query($query));
            return redirect(domain($url));
        }
    }


}
