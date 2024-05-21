<?php
declare (strict_types = 1);

namespace app\model;


use app\constant\ModelConstant;
use think\model\relation\HasMany;
use think\Request;

/**
 * @property string $host
 * @property string $name
 * @property string $api_key
 * @property string $api_secret
 * @property string $admin_token
 * @property string $store_token
 * @property string $version
 * @property integer $status
 * @property HasMany $payments
 * @property integer $pay_step
 * @property string $website
 */
class Shops extends BaseModel
{
    public $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'U';
    protected $field = [
        'host','name','api_key',
        'api_secret','admin_token','store_token',
        'version','created_at','updated_at','status',
        'pay_step','website'
    ];
    protected $updateTime = 'updated_at';
    protected $createTime = 'created_at';
    public function payments()
    {
        return $this->hasMany(ShopsPayment::class,'shop_id');
    }

    public function scopeHost($query,$host)
    {
        return $query->where('host',$host);
    }

    public static function getEnablePayment(Request $request)
    {
        $shop = self::query()->host($request->header('X-Opc-Shop-Id'))->find();

        return $shop->payments()->status(ModelConstant::STATUS_ON)->field(['id as payment_id','pay_method','account'])->order('pay_method','DESC')->select();
    }

    public function setStatusAttr($status)
    {
        return $status == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
    }

    public function getStatusAttr($status)
    {
        return $status == ModelConstant::STATUS_ON ? ModelConstant::STATUS_ON_NAME : ModelConstant::STATUS_OFF_NAME;
    }
}
