<?php

namespace app\logic;

use app\model\Orders;
use app\service\shopify\action\rest\DraftOrderRest;
use app\trait\OrderTrait;
use app\trait\PaymentTrait;
use think\annotation\Inject;
use think\Request;

class OrderLogic
{
    use PaymentTrait,OrderTrait;
    #[Inject]
    protected DraftOrderRest $rest;

    /**
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @desc 创建草稿订单
     */
    public function createDraftOrder(Request $request)
    {
        $draft = $this->rest->create_draft_order($request);
        //存订单
        $order_id = $this->saveOrder($draft);
        return compact('draft','order_id');
    }

    /**
     * @param Request $request
     * @param Orders $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @desc 修改草稿订单
     */
    public function updateDraftOrder(Request $request,Orders $order)
    {
        $draft_id = $order->admin_graphql_api_id;
        $draft_id = pathinfo($draft_id,PATHINFO_BASENAME);
        $draft = $this->rest->update_draft_order($draft_id,$request->all());
        $this->saveOrder($draft,$order);
        return compact('draft');
    }

}