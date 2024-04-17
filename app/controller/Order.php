<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\libs\AsiabillSDK\action\SessionAsiabill;
use app\logic\OrderLogic;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use app\validate\DraftOrderValidate;
use app\validate\OrderValidate;
use app\validate\PlaceOrderValidate;
use think\annotation\Inject;
use think\Request;


class Order extends BaseController
{
    #[Inject]
    protected OrderLogic $logic;

    //创建订单
    public function createDraftOrder(Request $request)
    {
        validate(DraftOrderValidate::class)->check($request->post());
        $data = $this->logic->createDraftOrder($request);
        return $this->success($data);
    }

    //更新订单
    public function modifyDraftOrder(Request $request,Orders $order)
    {
        if($order->order_status == Orders::ORDER_STATUS_COMPLETED) throw new \Exception('order has payed');
        Validate(OrderValidate::class)->check($request->put());
        $data = $this->logic->updateDraftOrder($request, $order);
        return $this->success($data);
   }

    public function getPaymentMethod(Request $request)
    {
        return $this->success(Shops::getEnablePayment($request));
    }

    //下单
    public function placeOrder(Request $request,Orders $order)
    {
        if($order->order_status == Orders::ORDER_STATUS_COMPLETED) throw new \Exception('order has payed');
        Validate(PlaceOrderValidate::class)->check($request->post());
        $data = $this->logic->placeOrder($request,$order);
        return $this->success($data);
    }

    //获取sessionToken
    public function getSessionToken(Request $request,ShopsPayment $payment)
    {
        $session = new SessionAsiabill($payment);
        return $this->success($session->get_session_token());
    }


}
