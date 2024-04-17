<?php

namespace app\exception;
 use think\Exception;
 use think\facade\Log;
 use Throwable;

 class BusinessException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


    
 }