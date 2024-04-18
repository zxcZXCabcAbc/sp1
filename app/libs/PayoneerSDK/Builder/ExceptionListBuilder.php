<?php

namespace app\libs\PayoneerSDK\Builder;

use app\controller\ExceptionOrderController;
use app\libs\PayoneerSDK\PayoneerClient;
use app\model\GoodsBShop;
use app\model\Orders;
use app\model\Shops;
use support\CodeGeneratorHelper;
use support\FormateHelper;
use support\Request;

class ExceptionListBuilder extends BaseAbstract
{

    protected $order;
    public function __construct(Orders $order)
    {
        parent::__construct($order);

    }

    public function toArray()
    {
        $data =  [
            'integration'=>'HOSTED',//hosted
            'country'=>$this->order->country_code ?? 'US',
            'channel'=>'WEB_ORDER',
            'transactionId'=>$this->order->payer_id,
            'division'=>PayoneerClient::$division,
            'callback'=>$this->getCallback(ExceptionOrderController::$payoneerAccountId),
            'payment'=>$this->getPayment(),
            'products'=>$this->getProducts(ExceptionOrderController::$payoneerAccountId),
            'operationType'=>'CHARGE',
            'ttl' => 1800,
            'customer'=>$this->getCustomer(),
            'style'=>$this->getStyle()
        ];
        return $data;
    }

    public function getCustomer()
    {
        try {
            $address = $this->order->address;
            $shipping = $address['shipping'];
            $billing = $address['billing'];
            $shippingAddress = [];
            if ($shipping['line1']) $shippingAddress['street'] = $shipping['line1'];
            if ($shipping['postal_code']) $shippingAddress['zip'] = $shipping['postal_code'];
            if ($shipping['city']) $shippingAddress['city'] = $shipping['city'];
            if ($shipping['state']) $shippingAddress['state'] = $shipping['state'];
            if ($shipping['country_code']) $shippingAddress['country'] = $shipping['country_code'];
            $billingAddress = [];
            if ($billing['line1']) $billingAddress['street'] = $billing['line1'];
            if ($billing['postal_code']) $billingAddress['zip'] = $billing['postal_code'];
            if ($billing['city']) $billingAddress['city'] = $billing['city'];
            if ($billing['state']) $billingAddress['state'] = $billing['state'];
            if ($billing['country_code']) $billingAddress['country'] = $billing['country_code'];
            $countryCode = $billing['country_code'];
            $defaultAddress = [
                'US' => [
                    'line1' => '1201 S Shawn Ra Nae dr',
                    'city' => 'fowler',
                    'postal_code' => '47944',
                    'state' => 'Indiana',
                    'phone' => '7064439563',
                ],
                'FR' => [
                    'line1' => '22 le rossignol',
                    'city' => 'Saint loubès',
                    'postal_code' => '33450',
                    'state' => '',
                    'phone' => '0609367142',
                ],
            ];
            $default = $defaultAddress[$countryCode] ?? $defaultAddress['US'];
            if (!array_key_exists('street', $shippingAddress)) $shippingAddress['street'] = $default['line1'];
            if (!array_key_exists('zip', $shippingAddress)) $shippingAddress['zip'] = $default['postal_code'];
            if (!array_key_exists('city', $shippingAddress)) $shippingAddress['city'] = $default['city'];
            if (!array_key_exists('country', $shippingAddress)) $shippingAddress['country'] = $countryCode;

            if (!array_key_exists('street', $billingAddress)) $billingAddress['street'] = $default['line1'];
            if (!array_key_exists('zip', $billingAddress)) $billingAddress['zip'] = $default['postal_code'];
            if (!array_key_exists('city', $billingAddress)) $billingAddress['city'] = $default['city'];
            if (!array_key_exists('country', $billingAddress)) $billingAddress['country'] = $countryCode;

            return [

                'email' => $this->order->email,
                'name' => [
                    'firstName' => $shipping['first_name'],
                    'lastName' => $shipping['last_name'],
                ],
                'addresses' => [
                    'shipping' => $shippingAddress,
                    'billing' => $billingAddress
                ],
                'phones' => [
                    'mobile' => ['unstructuredNumber' => $shipping['phone'] ?: $default['phone']],
                ],

            ];
        }catch (\Exception $e){
            dump($e);
        }
    }

    public function getPayment()
    {
        return [
            'reference'=>md5(time()),
            'amount'=>0 + $this->order->recall_money,
            'currency'=>$this->order->currency,
        ];
    }


    public function getProducts($b_shop_id = 0)
    {
        dump('*************组装B站数据: ' . time());
        $bGoods = GoodsBShop::generateGoodsB($this->order->recall_money * 100,$b_shop_id);
        $products =  [
            [
                'code'=> $bGoods->sku,
                'name'=>substr($bGoods->first_head,0,120),
                'amount'    => $this->order->recall_money,
                'currency'=>$this->order->currency,
                'quantity'    => 1,
            ]
        ];
        dump('*************组装B站数据: ' . time());
        return $products;

    }


    protected function getCallback($b_shop_id = 0)
    {
        list($returnUrl,$cancelUrl) = $this->getReturnUrl($this->order,$b_shop_id,['is_abnormal_orders'=>1]);
        $domainUrl = ExceptionOrderController::$payoneerDomainUrl;
        $notifyUrl = $domainUrl . '/notify/payoneer';
        return [
            'returnUrl'=>$returnUrl,
            'notificationUrl'=>$notifyUrl,
            'backToShopUrl'=>$cancelUrl,
            'cancelUrl'=>$cancelUrl,
        ];
    }


}