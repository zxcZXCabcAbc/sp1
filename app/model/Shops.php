<?php
declare (strict_types = 1);

namespace app\model;


/**
 * @mixin \think\Model
 */
class Shops extends BaseModel
{
    protected $table = 'shops';
    public $autoWriteTimestamp = true;
    protected $dateFormat = 'U';
    protected $field = [
        'host','name','api_key',
        'api_secret','admin_token','store_token',
        'version','created_at','updated_at','status'
    ];

    public function payments()
    {
        return $this->hasMany(ShopsPayment::class,'shop_id');
    }
}
