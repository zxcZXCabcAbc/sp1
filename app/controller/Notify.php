<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Orders;
use think\Request;

class Notify extends BaseController
{

    //异步通知
    public function index(Request $request,Orders $order)
    {
        $params = $request->all();
        if(empty($params)) return $this->error('no params');
        $notifyData = [
            ['order_id'=>$order->id,'params'=>$params,'created_at'=>time()]
        ];
        $order->notifies()->saveAll($notifyData);
        echo 'success';
    }

    //跳转支付页面
    public function checkout(Request $request,Orders $order)
    {
        $host = $order->shop->host;
        $path = 'checkout';
        $query = ['order_id'=>$order->id];
        $query = array_merge($request->all(),$query);
        $url = sprintf('%s/%s?%s',$host,$path,http_build_query($query));
        return redirect(domain($url));
    }


}