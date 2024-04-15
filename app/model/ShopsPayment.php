<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ShopsPayment extends BaseModel
{
    protected $table = 'shops_payment';

    protected $field = [
        'shop_id','account','paypal_account',
        'client_id','secrect','created_at',
        'updated_at','status','pay_type',
        'apply_status','mode','client_id_sandbox',
        'secrect_sandbox','pay_method','is_risk',
    ];

    protected $dateFormat = 'U';

    public function shop()
    {
        return $this->belongsTo(Shops::class,'shop_id');
    }
}
