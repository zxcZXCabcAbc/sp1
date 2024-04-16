<?php

namespace app\service\payment;

use app\model\Orders;
use app\model\ShopsPayment;
use think\Request;

class PaymentBase
{
    protected $service;
    protected ShopsPayment $payment;
    public function __construct(protected Orders $order,protected Request $request)
    {
        $this->payment = $this->order->payment;
        switch ($this->payment->pay_method){
            case ShopsPayment::PAY_METHOD_ASIABILL://asiabill
                $service = AsiabillService::class;
                break;
            case ShopsPayment::PAY_METHOD_PAYONEER://payoneer
                $service = PayoneerService::class;
                break;
            case ShopsPayment::PAY_METHOD_AIRWALLEX://airwallex
                $service = AirwallexService::class;
                break;
            case ShopsPayment::PAY_METHOD_STRIPE://stripe
                $service = StripeService::class;
                break;
            default:
                $service = PaypalService::class;//paypal
        }
        $class = new \ReflectionClass($service);
        $this->service = $class->newInstance($this->order,$this->request);
    }

    public function createThirdPayment()
    {
       return $this->service->placeOrder();
    }



}