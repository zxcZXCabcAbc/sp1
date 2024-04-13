<?php
declare (strict_types = 1);

namespace app\middleware;

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
