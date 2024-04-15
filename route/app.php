<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');

Route::group('/api',function (){
    //版本V1
    Route::group('/v1',function (){
        Route::get('/product-list','product/index');//商品列表
        Route::get('/country-list','checkout/getCountrys');//国家下拉
        Route::get('/provice-list','checkout/getProviceByCountry');//州省下拉
        Route::post('/checkouId-create','checkout/createCheckout');//生成checkoutId
        Route::post('/checkout-update','checkout/updateCheckout');//更新checkoutId
        Route::get('/shipping-list','checkout/getShippingLines');//查询运费
        Route::post('/set-shipping','checkout/setShippingFee');//设置运费
        Route::post('/create-customer','checkout/createCustomer');//创建客户
        Route::post('/complete-payment','checkout/completePayment');//完成支付
        Route::post('/draft-create','order/createDraftOrder');//创建草稿订单
        Route::put('/draft-modify/:id','Order/modifyDraftOrder')->model(\app\model\Orders::class);//修改订单
        Route::get('/payment-enable-get','Order/getPaymentMethod');//获取支付方式
        Route::post('/place-order/:id','Order/placeOrder')->model(\app\model\Orders::class);//下单
        Route::get('/session-token/:id','Order/getSessionToken')->model(\app\model\ShopsPayment::class);//sessionToken
    });
})
->middleware(\app\middleware\CheckShopifyRequest::class);//验证前端请求