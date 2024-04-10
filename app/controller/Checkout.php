<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\service\action\admin\Location;
use app\service\action\store\Payment;
use app\service\ShopifyApiService;
use think\exception\ValidateException;
use think\Request;

class Checkout extends BaseController
{

    //获取国家
    public function getCountrys(Request $request)
    {
        $api = new Location(ShopifyApiService::ADMIN_API);
        $data = $api->getCountry($request->get('since_id',0));
        return $this->success($data);
    }


    //根据国家获取州省
    public function getProviceByCountry(Request $request)
    {
        $this->validate($request->all(),['country_id'=>'require']);
        $api = new Location(ShopifyApiService::ADMIN_API);
        $list = $api->getProvices($request->get('country_id'));
        return $this->success($list);

    }

    //创建结账ID
    public function createCheckout(Request $request)
    {
        $lineItems = $request->post('lineItems');
        foreach ($lineItems as $item){
            if(!isset($item['variantId']) || empty($item['variantId'])) throw new ValidateException('variantId require');
            if(!isset($item['quantity']) || empty($item['quantity'])) throw new ValidateException('quantity require');
        }
        $api = new Payment();
        $data = $api->createCheckOut($lineItems);
        $checkoutId = $data['data']['checkoutCreate']['checkout']['id'] ?? '';
        if(empty($checkoutId)) throw new \Exception('create checkout fail');
        return $this->success(compact('checkoutId'));

    }
    /**
     * 'shippingAddress'=>[
     * 'lastName'=>'Doe',
     * 'firstName'=>'John',
     * 'address1'=>'123 Test Street',
     * 'province'=>'QC',
     * 'country'=>'Canada',
     * 'zip'=>'H3K0X2',
     * 'city'=>'Montreal',
     * ],
     */

    //绑定用户
    public function updateCheckout(Request $request)
    {
        $this->validate(
            $request->all(),
            [
                'checkoutId'=>'require',
                'shippingAddress'=>'require|array',
                'shippingAddress.lastName'=>'require',
                'shippingAddress.firstName'=>'require',
                'shippingAddress.address1'=>'require',
                'shippingAddress.province'=>'require',
                'shippingAddress.country'=>'require',
                'shippingAddress.zip'=>'require',
                'shippingAddress.city'=>'require',
            ]
        );
        $api = new Payment();
        $data = $api->updateCheckout($request->post('checkoutId'),$request->post('shippingAddress'));
        return $this->success($data);
    }
    
    //查询运费
    public function getShippingLines(Request $request)
    {
        $this->validate($request->all(),['checkoutId'=>'require',]);
        $api = new Payment();
        $data = $api->queryShippingFee($request->get('checkoutId'));
        return $this->success($data);
    }
    //设置运费
    public function setShippingFee(Request $request)
    {
        $this->validate($request->all(),['checkoutId'=>'require','handle'=>'require']);
        $api = new Payment();
        $data = $api->setCheckoutShoppingFee($request->post('checkoutId'),$request->post('handle'));
        return $this->success($data);
    }

    public function createCustomer(Request $request)
    {
        $this->validate(
            $request->all(),
            [
                'checkoutId'=>'require',
                'customer'=>'require|array',
                'customer.email'=>'require|email',
                'customer.firstName'=>'require',
                'customer.lastName'=>'require',
                'customer.phone'=>'require',
            ]);
        # 1.创建用户
        $customer = $request->post('customer');
        $customer['password'] = 'ddhd@2024';
        $api = new Payment();
        $customerRes = $api->createCustomer($customer);
        #2.获取用户token
        $access_token_res = $api->getCustomerAccessToken($customer);
        $access_token = $access_token_res['data']['customerAccessTokenCreate']['customerAccessToken']['accessToken'] ?? '';
        if(empty($access_token)) throw new \Exception('create customer fail');
        #3.关联订单
        $data = $api->associateCustomerWithCheckout($request->post('checkoutId'),$access_token);
        return $this->success($data);
    }


}
