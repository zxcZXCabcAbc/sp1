<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\logic\OrderLogic;
use app\model\Orders;
use app\validate\DraftOrderValidate;
use app\validate\OrderValidate;
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
        try {
            Validate(OrderValidate::class)->check($request->put());
            $data = $this->logic->updateDraftOrder($request, $order);
            return $this->success($data);
        }catch (\Exception $e){
            dump($e);
        }
   }


}
