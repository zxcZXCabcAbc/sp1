<?php
declare (strict_types = 1);

namespace app\model;

class Notify extends BaseModel
{
    public $dateFormat = 'U';
    public $autoWriteTimestamp = false;
    protected $field = [
        'order_id','params','created_at'
    ];
    protected $json = ['params'];
    protected $jsonAssoc = true;
}
