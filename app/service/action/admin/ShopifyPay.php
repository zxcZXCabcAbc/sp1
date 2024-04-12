<?php

namespace app\service\action\admin;

use app\service\ShopifyApiService;
use Shopify\Rest\Admin2023_04\Payment;
use think\Request;

class ShopifyPay extends ShopifyApiService
{
    public function createPaymentId(Request $request)
    {
        $payment = new Payment($this->session);
        $payment->checkout_id = $request->post('checkoutId');
        //$payment->checkout_id = '7972465ae1127aedc3f9d4f19f6b47ff';
        $payment->request_details = [
            "ip_address" => $request->ip(),
            "accept_language" => $request->header('Accept-Language'),
            "user_agent" => $request->header('User-Agent')
        ];
        $payment->amount = $request->post('amount');
        $payment->session_id = $this->session->getId();
        $payment->unique_token = uniqid();
        $payment->save(
            true, // Update Object
        );

       return $payment->id;
    }
}