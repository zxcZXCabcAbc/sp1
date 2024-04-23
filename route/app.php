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

Route::any('/','indexController/index');

Route::group('/api',function (){
    Route::any('/notify',"notifyController/index");//异步通知地址
    Route::any('/checkout/:id',"notifyController/checkout")->model(\app\model\Orders::class);//异步通知地址
    Route::any('/webhook','webhookController/index');//webhook地址
    //版本V1
    Route::group('/v1',function (){
        Route::get('/product-list','productController/index');//商品列表
        Route::get('/country-list','checkoutController/getCountrys');//国家下拉
        Route::get('/provice-list','checkoutController/getProviceByCountry');//州省下拉
        Route::post('/checkouId-create','checkoutController/createCheckout');//生成checkoutId
        Route::post('/checkout-update','checkoutController/updateCheckout');//更新checkoutId
        Route::get('/shipping-list','checkoutController/getShippingLines');//查询运费
        Route::post('/set-shipping','checkoutController/setShippingFee');//设置运费
        Route::post('/create-customer','checkoutController/createCustomer');//创建客户
        Route::post('/complete-payment','checkoutController/completePayment');//完成支付
        Route::post('/draft-create','orderController/createDraftOrder');//创建草稿订单
        Route::put('/draft-modify/:id','orderController/modifyDraftOrder')->model(\app\model\Orders::class);//修改订单
        Route::get('/payment-enable-get','orderController/getPaymentMethod');//获取支付方式
        Route::post('/place-order/:id','orderController/placeOrder')->model(\app\model\Orders::class);//下单
        Route::get('/session-token/:id','orderController/getSessionToken')->model(\app\model\ShopsPayment::class);//sessionToken
        Route::get('/shipping-zones/:id','orderController/getShippingZones')->model(\app\model\Orders::class);//获取运货出运费
        Route::post('/pre-paypal','orderController/prePayByPaypal');//paypal预下单
        Route::get('/paypal-config','orderController/getPaypalConfig');//获取paypal配置
        Route::get('/order-status/:id','orderController/getOrderStatus')->model(\app\model\Orders::class);//获取订单状态
    });
})
->middleware(\app\middleware\CheckShopifyRequest::class);//验证前端请求