<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\Shops;
use think\Response;

class CheckShopifyRequest
{
    const SHARED_SECRET = 'hush';
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        try {
            $shop = $request->header('X-Opc-Shop-Id');
            if(empty($shop)) throw new \Exception('miss shop host');
            $shop = Shops::query()->host($shop)->find();
           if(is_null($shop)) throw new \Exception('shop host not exists');
           $request->x_shop_id = $shop->id;
            return $next($request);
            $path_prefix = $request->request('path_prefix', '');
            $timestamp = $request->request('timestamp');
            $logged_in_customer_id = $request->request('logged_in_customer_id', '');
            $signature = $request->request('signature', '');
            $extra = $request->request('extra');
            $params = compact('shop', 'path_prefix', 'timestamp', 'logged_in_customer_id', 'extra');
            $calculated_signature = $this->calculated_signature($params);
            if ($calculated_signature != $signature) throw new \Exception('Authorization failed',401);
            return $next($request);
        }catch (\Exception $e){
            return Response::create(['code'=>$e->getCode(),'msg'=>$e->getMessage()],'json',$e->getCode());
        }
    }

    protected function calculated_signature ($params)
    {
        ksort($params);
        $queryStr = http_build_query($params);
        return hash_hmac('sha256',$queryStr,self::SHARED_SECRET,false);

    }
}
