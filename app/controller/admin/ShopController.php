<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use app\constant\ModelConstant;
use app\model\Shops;
use app\model\ShopsPayment;
use Carbon\Carbon;
use think\helper\Arr;
use think\Request;

class ShopController extends BaseController
{
    protected $versions = [
        '2023-04',
        '2023-07',
        '2023-10',
        '2024-01',
    ];
    public function index()
    {
       return view('admin/shop_list');

    }

    public function getList(Request $request)
    {
        $limit = $request->get('limit',10);
        $limit = intval($limit);
        $list = Shops::query()
            ->when($request->has('shop_id') && $request->get('shop_id'),function($q) use ($request){
                return $q->where('id',$request->get('shop_id'));
            })
            ->when($request->has('name') && $request->get('name'),function($q) use ($request){
                return $q->where('name','like',$request->get('name') . '%');
            })
            ->paginate($limit)
            ->toArray();
        $list['code'] = 0;
        $list['count'] = $list['total'];
        unset($list['total']);
        return json($list);
    }


    public function create(Request $request)
    {
        if($request->method() == 'GET'){
            $vars = [
                'title'=>'添加店铺',
                'versions'=>$this->versions,
            ];
            return view('admin/add_shop',$vars);
        }else{
            $shopId = 0;
            try {
                $params = $request->post();
                $payments = $params['payments'];
                unset($params['payments']);
                $rows = Shops::query()->where('host', $params['host'])->select();
                if (!$rows->isEmpty()) throw new \Exception('店铺已存在');
                $params['status'] = $params['status'] == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
                $params['created_at'] = Carbon::now()->toDateTimeString();
                $shopId = Shops::query()->insertGetId($params);
                $payments = $this->formatPayments($payments,$shopId);
                ShopsPayment::query()->insertAll($payments);
                return $this->success(['shopId' => $shopId]);
            }catch (\Exception $e){
                Shops::query()->where('id',$shopId)->delete();
                throw new \Exception($e->getMessage());
            }

        }
    }

    public function formatPayments($payments,$shopId)
    {
        $arr = [];
        foreach ($payments as $index => $payment) {
            $payment['shop_id'] = $shopId;
            $payment['status'] = Arr::get($payment,'status',ModelConstant::STATUS_OFF_NAME) == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
            $payment['apply_status'] = Arr::get($payment,'apply_status',ModelConstant::STATUS_OFF_NAME) == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
            $payment['mode'] = Arr::get($payment,'mode',ModelConstant::STATUS_OFF_NAME) == ModelConstant::STATUS_ON_NAME ? ModelConstant::LIVE_MODE : ModelConstant::TEST_MODE;
            $payment['created_at'] = Carbon::now()->toDateTimeString();
            $mode = Arr::get($payment,'mode',ModelConstant::STATUS_OFF_NAME);
            dd($mode);
            if(Arr::get($payment,'mode',ModelConstant::STATUS_OFF_NAME) == ModelConstant::STATUS_OFF_NAME){
                $payment['client_id_sandbox'] = $payment['client_id'];
                $payment['secrect_sandbox'] = $payment['secrect'];
                $payment['client_id'] = $payment['secrect'] = '';
            }
            $arr[] = $payment;
        }
        dd($arr);
        return $arr;
    }


    public function edit(Request $request,Shops $shop)
    {
        $params = $request->post();
        $payments = $params['payments'];
        unset($params['payments']);
        $rows = Shops::query()->where('host', $params['host'])->where('id','<>',$shop->id)->select();
        if (!$rows->isEmpty()) throw new \Exception('店铺已存在');
        $status = $params['status'] ?? ModelConstant::STATUS_OFF_NAME;
        $params['status'] = $status == ModelConstant::STATUS_ON_NAME ? ModelConstant::STATUS_ON : ModelConstant::STATUS_OFF;
        $params['updated_at'] = Carbon::now()->toDateTimeString();
        Shops::query()->where('id',$shop->id)->update($params);
        $shop->payments()->delete();
        $payments = $this->formatPayments($payments,$shop->id);
        ShopsPayment::query()->insertAll($payments);
        return $this->success();
    }


    public function update(Request $request, Shops $shop)
    {
        $shopData = $shop->toArray();
        $payments = $shop->payments()->select()->toArray();
        foreach ($payments as &$payment){
            $payment['client_id'] = $payment['mode'] == ModelConstant::STATUS_OFF_NAME ? $payment['client_id_sandbox'] : $payment['client_id'];
            $payment['secrect'] = $payment['mode'] == ModelConstant::STATUS_OFF_NAME ? $payment['secrect_sandbox'] : $payment['secrect'];
        }
        $shopData['payments'] = $payments;
        $shopData['versions'] = $this->versions;
        dump($shopData);
        return view('admin/shop_edit',$shopData);
    }

    public function delete(Request $request,Shops $shop)
    {
        $shop->payments()->delete();
        $shop->delete();
        return $this->success($shop->id);
    }
}
