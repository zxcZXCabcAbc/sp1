<?php
declare (strict_types = 1);

namespace app\controller\api;

use app\BaseController;
use app\service\shopify\action\admin\ShopifyPay;
use app\service\shopify\action\store\Payment;
use app\service\shopify\ShopifyApiService;
use think\exception\ValidateException;
use think\helper\Arr;
use think\Request;

class CheckoutController extends BaseController
{

    //获取国家
    /**
     * @param Request $request
     * @return \think\response\Json
     * @deprecated
     */
    public function getCountrys(Request $request)
    {
        $data = [];
        return $this->success($data);
    }


    //根据国家获取州省

    /**
     * @param Request $request
     * @return \think\response\Json
     * @deprecated 
     */
    public function getProviceByCountry(Request $request)
    {
        $list = [];
        return $this->success($list);

    }

    //创建结账ID
    public function createCheckout(Request $request)
    {
        $checkout = $request->post('checkout',[]);
        $defaultResponse = [
            'checkout'=>[
                'action'=>'live',
                'id'=>'',
                'funnel_type'=>'opc',
                'btn_uid'=>'prod_page_checkout',
                'approval_url'=>''
            ]
        ];
        if(empty($checkout)) return json($defaultResponse);
        $lineItems = $checkout['cart']['items'];
        $lines = [];
        foreach ($lineItems as $item){
            if(!isset($item['variant_id']) || empty($item['variant_id'])) throw new ValidateException('variantId require');
            if(!isset($item['quantity']) || empty($item['quantity'])) throw new ValidateException('quantity require');
            //gid://shopify/ProductVariant/
            $lines[] = [
                'variantId'=>sprintf('gid://shopify/ProductVariant/%s',$item['variant_id']),
                'quantity'=>$item['quantity'],
            ];
        }
        $api = new Payment();
        $data = $api->createCheckOut($lines);
        $checkoutId = $data['data']['checkoutCreate']['checkout']['id'] ?? '';
        $error = $data['data']['checkoutCreate']['checkoutUserErrors'];
        if($error) {
            $message = implode(',',array_column($error,'message'));
            throw new \Exception($message);
        }
        //$checkoutId = 'gid://shopify/Checkout/9132132cc47b9127ec1605a875f1edac?key=d7070e740cfa599411c11c3f441ad3f1';
        $checkoutId = pathinfo($checkoutId,PATHINFO_BASENAME);
        $checkoutId = Arr::first(explode('?',$checkoutId));
        $defaultResponse['checkout']['id'] = $checkoutId;
        return json($defaultResponse);

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
    //完成支付
    public function completePayment(Request $request)
    {
        $this->validate(
            $request->all(),
            [
                'checkoutId'=>'require',
                'amount'=>'require|number',
            ]);
        $payApi = new ShopifyPay(ShopifyApiService::ADMIN_API,$request);
        $paymentId = $payApi->createPaymentId($request);



    }


}
