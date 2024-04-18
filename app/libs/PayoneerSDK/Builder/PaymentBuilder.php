<?php

namespace app\libs\PayoneerSDK\Builder;

use app\libs\PayoneerSDK\PayoneerClient;
use app\model\Shops;
use app\trait\PaymentTrait;
use support\FormateHelper;
use app\model\Orders;

class PaymentBuilder extends BaseAbstract
{
    use PaymentTrait;
    protected $shopType;
    protected $isUpdate = false;
    protected $b_shop_id = 0;
  public function __construct(Orders $order)
  {
      parent::__construct($order);

  }
    public function toArray()
    {
        $shopType = optional($this->order->shop)->shop_type ?? Shops::SHOP_TYPE_A;
        $data =  [
            //'integration'=>in_array($shopType,[Shops::SHOP_TYPE_A,Shops::SHOP_TYPE_LP_A]) ? 'HOSTED' : 'DISPLAY_NATIVE',//hosted
            'integration'=>'HOSTED',//hosted
            'country'=>$this->order->country_code ?? 'US',
            'channel'=>'WEB_ORDER',
            'transactionId'=>$this->order->payer_id,
            'division'=>PayoneerClient::$division,
            'callback'=>$this->getCallback($this->getBShopId()),
            'customer'=>$this->getCustomer(),
            'payment'=>$this->getPayment(),
            'products'=>$this->getProducts(),
            'operationType'=>'CHARGE',
            'ttl' => 1800,
        ];

        if(!$this->getIsUpdate()) {
            $data['style'] = $this->getStyle();
        }
        if($this->getIsUpdate()) $data['transactionId'] = $this->order->payer_id;

        return $data;
    }



    //获取顾客信息
    public function getCustomer()
    {
        $address = $this->order->address;
        $shipping = $address['shipping'];
        $billing = $address['billing'];
        return [

                'email'=>$this->order->email,
                //'email'=>'james.blond@example.com',
                'name'=>[
                    'firstName'=>$shipping['first_name'],
                    'lastName'=>$shipping['last_name'],
                ],
                'addresses'=>[
                    'shipping'=>[
                        'street'=>$shipping['line1'],
                        'zip'=>$shipping['postal_code'],
                        'city'=>$shipping['city'],
                        'state'=>$shipping['state'],
                        'country'=>$shipping['country_code'],
                    ],
                    'billing'=>[
                        'street'=>$billing['line1'],
                        'zip'=>$billing['postal_code'],
                        'city'=>$billing['city'],
                        'state'=>$billing['state'],
                        'country'=>$billing['country_code'],
                    ],
                ],
                'phones'=>[
                    'mobile'=>['unstructuredNumber'=>$shipping['phone']],
                ],

        ];
    }

    public function setIsUpdate($isUpdate)
    {
        $this->isUpdate = $isUpdate;
        return $this;
    }

    protected function getIsUpdate()
    {
        return $this->isUpdate;
    }


    public function setBShopId($b_shop_id)
    {
        $this->b_shop_id = $b_shop_id;
        return $this;
    }

    public function getBShopId()
    {
        return $this->b_shop_id;
    }
}
