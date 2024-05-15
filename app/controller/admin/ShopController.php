<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use app\model\Shops;
use think\Request;

class ShopController extends BaseController
{

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
                'versions'=>[
                    '2023-04',
                    '2023-07',
                    '2023-10',
                    '2024-01',
                ],
            ];
            return view('admin/add_shop',$vars);
            //return view('admin/layout');
        }else{

        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
