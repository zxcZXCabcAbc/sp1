<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $note
 * @property string $username
 */
class Customer extends BaseModel
{
    protected $dateFormat = 'U';
    protected $field = [
        'order_id','email','created_at','updated_at',
        'first_name','last_name','note','verified_email',
        'multipass_identifier','tax_exempt','phone','email_marketing_consent',
        'sms_marketing_consent','tags','currency','tax_exemptions','admin_graphql_api_id',
        'state'
    ];
    protected $json = ['email_marketing_consent','sms_marketing_consent','tax_exemptions'];
    protected $jsonAssoc = true;

    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }

    public function getUsernameAttr()
    {
        return sprintf('%s %s',$this->last_name,$this->first_name);
    }
}
