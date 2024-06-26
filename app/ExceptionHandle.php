<?php
namespace app;

use app\constant\CommonConstant;
use app\exception\BusinessException;
use app\exception\UnLoginException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        switch (true){
            case $e instanceof ValidateException:
            return json(['code'=>412,'msg'=>$e->getMessage(),'data'=>[]]);
            case $e instanceof BusinessException:
                tplog($request->url(),$request->all());
                return json(['code'=>CommonConstant::API_REQUEST_ERROR,'msg'=>$e->getMessage(),'data'=>[]]);
            case $e instanceof UnLoginException:
                return redirect("/admin/login");
            default:
                return json(['code'=>$e->getCode() ?: 500,'msg'=>$e->getMessage(),'data'=>[]]);
        }
        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
