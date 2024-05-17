<?php
declare (strict_types = 1);

namespace app\model;

use app\constant\CommonConstant;
use app\constant\ModelConstant;
use think\Model;
use think\model\relation\HasOne;

/**
 * @property string $account
 * @property string $merchant_no
 * @property string $client_id
 * @property string $secrect
 * @property string $client_id_sandbox
 * @property string $secrect_sandbox
 * @property integer $shop_id
 * @property integer $status
 * @property integer $pay_method
 * @property integer $is_risk
 * @property HasOne $shop
 * @property string $shopify_key
 * @property integer $mode
 * @property mixed $config
 * @property mixed $redirect_urls
 * @property string $pay_method_name
 */
class ShopsPayment extends BaseModel
{

    protected $field = [
        'shop_id','account','merchant_no',
        'client_id','secrect','created_at',
        'updated_at','status', 'shopify_key',
        'apply_status','mode','client_id_sandbox',
        'secrect_sandbox','pay_method','is_risk',
    ];

    protected $dateFormat = 'U';
    const PAY_METHOD_PAYPAL = 1;
    const PAY_METHOD_ASIABILL = 2;
    const PAY_METHOD_PAYONEER = 3;
    const PAY_METHOD_AIRWALLEX = 4;
    const PAY_METHOD_STRIPE = 5;
    const MODE_SANDBOX = 1;
    const MODE_LIVE = 2;

    public static $payMethodNames = [
        self::PAY_METHOD_PAYPAL =>'paypal',
        self::PAY_METHOD_ASIABILL =>'asiabill',
        self::PAY_METHOD_PAYONEER =>'payoneer',
        self::PAY_METHOD_AIRWALLEX =>'airwallex',
        self::PAY_METHOD_STRIPE =>'stripe',
    ];


    public function shop()
    {
        return $this->belongsTo(Shops::class,'shop_id');
    }

    public  function getConfigAttr()
    {
        return [
            'merchant_no' => $this->merchant_no,
            'app_key' => $this->mode == ModelConstant::STATUS_OFF_NAME ? $this->client_id_sandbox : $this->client_id,
            'app_secret' => $this->mode == ModelConstant::STATUS_OFF_NAME ? $this->secrect_sandbox : $this->secrect,
            'shopify_key' => $this->shopify_key,
            'shop_id'=>$this->shop_id
        ];
    }

    public function scopeStatus($query,$status)
    {
        return $query->where('status',$status);
    }

    public function getRedirectUrlsAttr()
    {
        return 1;
    }

    public static function payment(int $shopid,$pay_method)
    {
        return self::query()
                   ->where('shop_id',$shopid)
                   ->where('pay_method',$pay_method)
                   ->where('status',ModelConstant::STATUS_ON)
                   ->findOrEmpty();
    }

    public function getPayMethodNameAttr()
    {
        return $this->pay_method > ShopsPayment::PAY_METHOD_PAYPAL ? CommonConstant::LOG_CREDIT_CARD : CommonConstant::LOG_PAYPAL;
    }

    public function setStatusAttr($status)
    {
        return $status == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
    }

    public function getStatusAttr($status)
    {
        return $status == ModelConstant::STATUS_ON ? ModelConstant::STATUS_ON_NAME : ModelConstant::STATUS_OFF_NAME;
    }


    public function setApplyStatusAttr($status)
    {
        return $status == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
    }

    public function getApplyStatusAttr($status)
    {
        return $status == ModelConstant::STATUS_ON ? ModelConstant::STATUS_ON_NAME : ModelConstant::STATUS_OFF_NAME;
    }

    public function setModeAttr($status)
    {
        return $status == ModelConstant::STATUS_ON_NAME ? ModelConstant::LIVE_MODE : ModelConstant::TEST_MODE;
    }

    public function getModeAttr($status)
    {
        return $status == ModelConstant::STATUS_ON ? ModelConstant::STATUS_OFF_NAME : ModelConstant::STATUS_ON_NAME;
    }
}
