<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Notify;
use app\model\Orders;
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
            if($order){
                Notify::saveParams($order->id,$params,Notify::TYPE_NOTIFY);//存数据库
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
            tplog('checkout', $params);
            $notifyData = ['order_id' => $order->id, 'params' => $params, 'created_at' => Carbon::now()->toDateTimeString(), 'type' => Notify::TYPE_CHECKOUT];
            $order->notifies()->save($notifyData);
            $success = $request->param('success','');
            $token = $request->param('token','');
            if($token != $order->token) throw new \Exception('token invalid');
            if($success != 'true') throw new \Exception('checkout exception');
            $order->token = $order->token . '_' . time();//修改token
            $order->save();
            //队列处理
            Queue::push(CapturePaymentQueue::class, ['order_id' => $order->id, 'request' => $request->all()], 'checkout');
            $path = 'checkout';
            $query = ['order_id'=>$order->id];
            $query = array_merge($request->all(),$query);
            $url = sprintf('%s/%s?%s',$host,$path,http_build_query($query));
            return redirect(domain($url));
        }catch (\Exception $e){
            $query = ['error'=>$e->getMessage()];
            $url = sprintf('%s?%s',$host,http_build_query($query));
            return redirect(domain($url));
        }
    }


}
