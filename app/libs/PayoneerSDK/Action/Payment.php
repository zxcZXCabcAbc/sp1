<?php

namespace app\libs\PayoneerSDK\Action;

use app\libs\PayoneerSDK\Builder\PaymentBuilder;
use app\libs\PayoneerSDK\PayoneerClient;
use app\model\Orders;

class Payment extends PayoneerClient
{
    public function createPayment($builder)
    {
        $params = $builder instanceof PaymentBuilder ? $builder->toArray() : $builder;
       return $this->setPath('/api/lists')
                   ->setMethod('post')
                   ->setOption(['json'=>$params])
                   ->send();
   }

   //信用卡支付订单
    public function payWithNetWork(string $listId,string $network, array $account)
    {
        return $this->setPath('/api/lists/' . $listId . '/' . $network . '/charge')
            ->setMethod('post')
            ->setOption(['json'=>['account'=>$account]])
            //->setDebug()
            ->send();
    }

    public function payWithPresetNetWork(string $listId,array $account)
    {
        return $this->setPath('/api/lists/' . $listId . '/charge')
            ->setMethod('post')
            ->setOption(['json'=>['account'=>$account]])
            //->setDebug()
            ->send();
    }

    public function getListDetail(string $listId)
    {
        return $this->setPath('/api/lists/' . $listId )
            ->setMethod('get')
            ->setOption([])
            //->setDebug()
            ->send();
    }

    //更新list
    public function updateList(string $listId,array $params)
    {
        return $this->setPath('/api/lists/' . $listId )
            ->setMethod('put')
            ->setParams(['json'=>$params])
            //->setDebug()
            ->send();
    }


}
