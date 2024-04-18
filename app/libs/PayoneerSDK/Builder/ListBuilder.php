<?php

namespace app\libs\PayoneerSDK\Builder;

use app\libs\PayoneerSDK\PayoneerClient;
use app\model\Orders;
use app\model\Shops;
use Illuminate\Support\Str;
use support\CodeGeneratorHelper;
use support\Request;

class ListBuilder extends BaseAbstract
{
    protected $request;
    protected $order;
    public function __construct(Orders $order,Request $request)
    {
        parent::__construct($order);
        $this->request = mergeRequest($request);
    }

    public function toArray()
    {

        if(!Str::contains($this->order->payer_id,'#')){
            $this->order->payer_id = CodeGeneratorHelper::generateNormalOrderSn();
            $this->order->save();
        }
        $data =  [
            //'integration'=>in_array($this->request->x_shop_type,[Shops::SHOP_TYPE_A,Shops::SHOP_TYPE_LP_A]) ? 'HOSTED' : 'DISPLAY_NATIVE',//hosted
            'integration'=> 'HOSTED',//hosted
            'country'=>$this->order->country_code ?? 'US',
            'channel'=>'WEB_ORDER',
            'transactionId'=>$this->order->payer_id,
            'division'=>PayoneerClient::$division,
            'callback'=>$this->getCallback($this->request->b_shop_id),
            'payment'=>$this->getPayment(),
            'products'=>$this->getProducts($this->request->b_shop_id),
            'operationType'=>'CHARGE',
            'ttl' => 1800,
            'customer'=>$this->getCustomer(),
            'style'=>$this->getStyle()
        ];
        //dump($data);
        return $data;
    }

    public function getCustomer()
    {
        $billing = [
            'line1'=>'',
            'postal_code'=>'',
            'city'=>'',
            'state'=>'',
            'country_code'=>'US',
        ];
        return [
            'email'=>$this->order->email,
            'addresses'=>[
                'shipping'=>[
                    'street'=>$billing['line1'],
                    'zip'=>$billing['postal_code'],
                    'city'=>$billing['city'],
                    'state'=>$billing['state'],
                    'country'=>$billing['country_code'],
                ],
                'billing'=>[
                    'street'=>$billing['line1'],
                    'zip'=>$billing['postal_code'],
                    'city'=>$billing['city'],
                    'state'=>$billing['state'],
                    'country'=>$billing['country_code'],
                ],
            ],
            ];
    }


}