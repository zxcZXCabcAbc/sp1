<?php
namespace app\middleware;


use think\Request;
use think\Response;

class CrossDomain
{
    public function handle(Request $request, callable $next) : Response
    {
        $response = strtoupper($request->method()) === 'OPTIONS' ? response('', 204) : $next($request);
        // 给响应添加跨域相关的http头
        $response->header([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
            'Access-Control-Expose-Headers' => $request->header('Authorization, authenticated'),
        ]);
        //dump('middleware');
        return $response;
    }
    
}
