<?php

namespace app\model;

use Carbon\Carbon;
use think\Model;

abstract class BaseModel extends Model
{
    protected $field;
    protected $isConvert = false;

    protected $data = [];
    protected $relationId = 0;

    public function getField()
    {
        $class = new \ReflectionClass(static::class);
        $model = $class->newInstance();
        return $model->getFields();
    }

    public function getDateField()
    {
       return [
           'created_at','updated_at','cancelled_at','closed_at',
           'processed_at',''
       ];
    }

    public function setIsConvert(bool $isConvert)
    {
         $this->isConvert = $isConvert;
         return $this;
    }

    public function fill(array $data)
    {
        $this->setDatas($data)->formatData();
        return $this;
    }

    public function saveData()
    {
        return $this->insertGetId($this->getDatas());
    }

    public function setDatas(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getDatas()
    {
        return $this->data;
    }

    public function setRelationId($relationId)
    {
        $this->relationId = $relationId;
        return $this;
    }

    public function getRelationId()
    {
        return $this->relationId;
    }

    protected function formatData()
    {
        //$fields = $this->getField();
        $fields = static ::getFields();
        foreach ($this->data as $key => &$item){
            if(is_numeric($key)){
                foreach ($item as $kk => &$vv){
                    if(in_array($kk,$this->getDateField())){
                        $item[$kk] = Carbon::parse($vv)->timestamp;
                    }
                    if(is_bool($vv)) $item[$kk] = $item[$kk] ? 1 : 0;
                    if(!in_array($kk,$fields)) unset($item[$kk]);

                }

            }else{
                if(in_array($key,$this->getDateField())){
                    $this->data[$key] = Carbon::parse($item)->timestamp;
                }
                if(is_bool($item)) $this->data[$key] = $item ? 1 : 0;
                if(!in_array($key,$fields)) unset($this->data[$key]);

            }

        }
    }

    protected function getFields()
    {
        return $this->field;
    }

}