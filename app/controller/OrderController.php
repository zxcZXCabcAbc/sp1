<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\constant\CommonConstant;
use app\libs\AsiabillSDK\action\SessionAsiabill;
use app\logic\OrderLogic;
use app\model\Orders;
use app\model\Shops;
use app\model\ShopsPayment;
use app\service\shopify\action\rest\ShippingZoneRest;
use app\validate\DraftOrderValidate;
use app\validate\OrderValidate;
use app\validate\PlaceOrderValidate;
use app\validate\PrePayValidate;
use think\annotation\Inject;
use think\Request;


class OrderController extends BaseController
{
    #[Inject]
    protected OrderLogic $logic;

    //创建订单
    public function createDraftOrder(Request $request)
    {

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
        if ($order->order_status == Orders::ORDER_STATUS_COMPLETED) throw new \Exception('order has payed',CommonConstant::ORDER_HAS_PAYED_ERROR_CODE);
        Validate(PlaceOrderValidate::class)->check($request->post());
        $data = $this->logic->placeOrder($request, $order);
        return $this->success($data);

    }

    //获取sessionToken
    public function getSessionToken(Request $request,ShopsPayment $payment)
    {
        $session = new SessionAsiabill($payment);
        $session_token = $session->get_session_token();
        $js_sdk = $session->getAsiabill()->getJsScript();
        return $this->success(compact('session_token','js_sdk'));
    }

    //获取所有国家运费
    public function getShippingZones(Request $request)
    {
        $this->validate($request->all(),['country_code'=>'require','sub_total'=>'require']);
        $data = $this->logic->getShippingZones($request);
        return $this->success(['shipping_line'=>$data]);
    }

    //预下单
    public function prePayByPaypal(Request $request)
    {
        Validate(PrePayValidate::class)->check($request->post());
        $data = $this->logic->prePayPaypal($request);
        return $this->success($data);
    }

    //获取贝宝配置
    public function getPaypalConfig(Request $request)
    {
        $data = $this->logic->getPaypalConfig($request);
        return $this->success($data);
    }

    //获取订单状态
    public function getOrderStatus(Request $request,Orders $order)
    {
        return $this->success(['order_status'=>$order->order_status]);
    }

    //获取订单详情
    public function getOrderDetail(Request $request,Orders $order)
    {
        $data = $this->logic->getOrderDetail($order);
        return $this->success($data);
    }

}
