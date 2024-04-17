<?php

namespace app\service\payment;

use app\exception\BusinessException;
use app\model\Orders;
use app\model\ShopsPayment;
use think\Request;

class PaymentBase
{
    protected $service;
    protected ShopsPayment $payment;
    protected Orders $order;
    protected Request $request;
    public function __construct( Orders $order, Request $request)
    {
        $this->order = $order;
        $this->request = $request;
        $this->payment = $this->order->payment;
    }

    public function createThirdPayment()
    {
        try {
            $result = $this->newServiceInstance()->placeOrder();
            $this->order->transaction_id = $result['transaction_id'];
            $this->order->save();
            return $result;
        }catch (\Exception $e){
            $this->order->error_msg = $e->getMessage();
            $this->order->save();
            throw new BusinessException($e->getMessage());
        }
    }

    public function newServiceInstance()
    {
        switch ($this->payment->pay_method){
            case ShopsPayment::PAY_METHOD_ASIABILL://asiabill
                $service =  AsiabillService::class;
                break;
            case ShopsPayment::PAY_METHOD_PAYONEER://payoneer
                $service =  PayoneerService::class;
                break;
            case ShopsPayment::PAY_METHOD_AIRWALLEX://airwallex
                $service =  AirwallexService::class;
                break;
            case ShopsPayment::PAY_METHOD_STRIPE://stripe
                $service =  StripeService::class;
                break;
            default:
                $service =  PaypalService::class;//paypal
        }
        $class = new \ReflectionClass($service);

        return $class->newInstance($this->order,$this->request);
    }



}