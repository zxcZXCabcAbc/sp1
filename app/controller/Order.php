<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\logic\OrderLogic;
use app\validate\DraftOrderValidate;
use think\annotation\Inject;
use think\Request;


class Order extends BaseController
{
    #[Inject]
    protected OrderLogic $logic;

    public function createOrder(Request $request)
    {
        validate(DraftOrderValidate::class)->check($request->post());
        $data = $this->logic->createDraftOrder($request);
        return $this->success($data);
    }
}
