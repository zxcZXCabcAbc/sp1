<?php
// 应用公共文件
use think\facade\Log;

function domain(string $url, string $protocol = 'https')
{
    if(\think\helper\Str::contains($url,['http','https'])) return $url;
    return sprintf('%s://%s',$protocol,$url);
}


 function tplog(string $msg,array $data,$channel = 'request')
{
    $json = json_encode($data,JSON_UNESCAPED_UNICODE);
    Log::channel($channel)->record(sprintf('%s: %s',$msg,$json));
}