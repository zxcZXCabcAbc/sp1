<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class ShopConfigValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'host'=>'require|string|url',
        'name'=>'require|string',
        'api_key'=>'require|string',
        'api_secret'=>'require|string',
        'admin_token'=>'require|string',
        'store_token'=>'require|string',
        'version'=>'require|string',
        'status'=>'integer|in:0,1',
        'payments'=>"array",
        'payments.*.account'=>"string",
        'payments.*.merchant_no'=>"string",
        'payments.*.client_id'=>"string",
        'payments.*.secrect'=>"string",
        'payments.*.status'=>"integer|in:0,1",
        'payments.*.apply_status'=>"integer|in:0,1",
        'payments.*.mode'=>"integer|in:1,2",
        'payments.*.client_id_sandbox'=>"require|string",
        'payments.*.secrect_sandbox'=>"require|string",
        'payments.*.pay_method'=>"integer|in:1,2,3,4,5",

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'host.require'=>'域名不能为空',
        'name.require'=>'店铺名不能为空',
        'api_key.require'=>'apiKey不能为空',
        'api_secret.require'=>'api秘钥不能为空',
        'admin_token.require'=>'店铺后台token不能为空',
        'store_token.require'=>'店面token不能为空',
        'version.require'=>'版本号不能为空',
    ];
}
