<?php
declare (strict_types = 1);

namespace app\middleware;

use app\exception\UnLoginException;
use think\facade\Cache;
use think\facade\Session;
use think\Request;

class CheckUserLogin
{
    protected $whiteUrl = [
        '/admin/login',
        '/admin/login/check',
    ];
    public function handle(Request $request, \Closure $next)
    {
        $path = $request->url();
        if(in_array($path,$this->whiteUrl)) return $next($request);
        $uid = Cache::get('uid');
        if(!$uid) throw new UnLoginException('no login');
         return $next($request);
    }
}
