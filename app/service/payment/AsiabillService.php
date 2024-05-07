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
            $customerId = $this->request->param('customerId', '');
            $customerPaymentMethodId = $this->request->param('customerPaymentMethodId');
            if (empty($customerPaymentMethodId)) throw new BusinessException('asibill payment require customerPaymentMethodId');
            $checkout = new CheckoutAsiabill($this->payment);
            $builder = new CheckoutBuilder($this->order);
            $result = $checkout->confirm_charge($customerId, $customerPaymentMethodId, $builder);
            $this->saveSendRequest(['params'=>$builder->toArray(),'result'=>$result]);
            $code = $result['code'] ?? '';
            if($code != '00000') throw new BusinessException($result['message']);
            $transaction_id = $result['data']['tradeNo'] ?? "";
            $redirect_url = $result['data']['redirectUrl'] ?? '';
            return ['transaction_id'=>$transaction_id,'approval_url'=>$redirect_url,'pay_result'=>$result];
    }

    public function confirmPayment()
    {
        // TODO: Implement confirmPayment() method.
    }
}