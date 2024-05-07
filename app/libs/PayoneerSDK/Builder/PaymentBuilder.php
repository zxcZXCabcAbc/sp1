<?php

namespace app\libs\PayoneerSDK\Builder;

use app\libs\PayoneerSDK\PayoneerClient;
use app\model\Shops;
use app\trait\PaymentTrait;
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
        $data =  [
            //'integration'=>in_array($shopType,[Shops::SHOP_TYPE_A,Shops::SHOP_TYPE_LP_A]) ? 'HOSTED' : 'DISPLAY_NATIVE',//hosted
            'integration'=>'HOSTED',//hosted
            'country'=>$this->order->shippingAddress->country_code ?? 'US',
            'channel'=>'WEB_ORDER',
            'transactionId'=>$this->order->order_no,
            'division'=>PayoneerClient::$division,
            'callback'=>$this->getCallback(),
            'customer'=>$this->getCustomer(),
            'payment'=>$this->getPayment(),
            'products'=>$this->getProducts(),
            'operationType'=>'CHARGE',
            'ttl' => 1800,
        ];

        if(!$this->getIsUpdate()) {
            $data['style'] = $this->getStyle();
        }
        if($this->getIsUpdate()) $data['transactionId'] = $this->order->name;

        return $data;
    }



    //获取顾客信息
    public function getCustomer()
    {
        return [

                'email'=>$this->order->contact_email,
                //'email'=>'james.blond@example.com',
                'name'=>[
                    'firstName'=>$this->order->shippingAddress->first_name,
                    'lastName'=>$this->order->shippingAddress->last_name,
                ],
                'addresses'=>[
                    'shipping'=>[
                        'street'=>$this->order->shippingAddress->address1,
                        'zip'=>$this->order->shippingAddress->zip,
                        'city'=>$this->order->shippingAddress->city,
                        'state'=>$this->order->shippingAddress->province,
                        'country'=>$this->order->shippingAddress->country_code,
                    ],
                    'billing'=>[
                        'street'=>$this->order->billingAddress->address1,
                        'zip'=>$this->order->billingAddress->zip,
                        'city'=>$this->order->billingAddress->city,
                        'state'=>$this->order->billingAddress->province,
                        'country'=>$this->order->billingAddress->country_code,
                    ],
                ],
                'phones'=>[
                    'mobile'=>['unstructuredNumber'=>$this->order->shippingAddress->phone],
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
