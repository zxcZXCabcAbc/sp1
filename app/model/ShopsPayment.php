<?php
declare (strict_types = 1);

namespace app\model;

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
 */
class ShopsPayment extends BaseModel
{
    protected $table = 'shops_payment';

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

    public function shop()
    {
        return $this->belongsTo(Shops::class,'shop_id');
    }

    public  function getConfigAttr()
    {
        return [
            'merchant_no' => $this->merchant_no,
            'app_key' => $this->mode == self::MODE_SANDBOX ? $this->client_id_sandbox : $this->client_id,
            'app_secret' => $this->mode == self::MODE_SANDBOX ? $this->secrect_sandbox : $this->secrect,
            'shopify_key' => $this->shopify_key,
        ];
    }

    public function scopeStatus($query,$status)
    {
        return $query->where('status',$status);
    }
}
