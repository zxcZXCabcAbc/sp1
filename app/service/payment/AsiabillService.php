<?php

namespace app\service\payment;

use app\exception\BusinessException;
use app\libs\AsiabillSDK\action\CheckoutAsiabill;
use app\libs\AsiabillSDK\action\CustomerAsiabill;
use app\libs\AsiabillSDK\builder\CheckoutBuilder;
use app\libs\AsiabillSDK\builder\CustomerBuilder;
use app\libs\AsiabillSDK\builder\PaymentMethodsBuilder;
use app\Request;

class AsiabillService extends PaymentBase implements PaymentInterface
{
    public function placeOrder()
    {
        try {
            $customerId = $this->request->param('customerId', '');
            $customerPaymentMethodId = $this->request->param('customerPaymentMethodId', 'pm_1782323329894969344');
            if (empty($customerPaymentMethodId)) throw new BusinessException('asibill payment require customerPaymentMethodId');
            $checkout = new CheckoutAsiabill($this->payment);
            $builder = new CheckoutBuilder($this->order);
            $result = $checkout->confirm_charge($customerId, $customerPaymentMethodId, $builder);
            dd($result->getBody());
        }catch (\Exception $e){
            dd($e);
        }
    }
}