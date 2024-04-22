<?php
declare (strict_types = 1);

namespace app\model;

class Notify extends BaseModel
{
    public $autoWriteTimestamp = false;
    protected $field = [
        'order_id','params','created_at','type'
    ];
    protected $json = ['params'];
    protected $jsonAssoc = true;
    const TYPE_SEND = 1;
    const TYPE_NOTIFY = 2;
    const TYPE_CHECKOUT = 3;
}
