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

Route::any('/test','app\controller\api\indexController@index');
Route::any('/session/set/:name','app\controller\api\indexController@setSession');
Route::any('/session/get/:name','app\controller\api\indexController@getSession');

Route::any('/api/notify',"app\controller\api\\notifyController@index");//异步通知地址
Route::any('/api/checkout/:id',"app\controller\api\\notifyController@checkout")->model(\app\model\Orders::class);//异步通知地址
Route::any('/api/webhook','app\controller\api\webhookController@index');//webhook地址

Route::group('/api',function (){
    //版本V1
    Route::group('/v1',function (){
        Route::get('/product-list','app\controller\api\productController@index');//商品列表
        Route::get('/country-list','app\controller\api\checkoutController@getCountrys');//国家下拉
        Route::get('/provice-list','app\controller\api\checkoutController@getProviceByCountry');//州省下拉
        Route::post('/checkout','app\controller\api\checkoutController@createCheckout');//生成checkoutId
        Route::post('/checkout-update','app\controller\api\checkoutController@updateCheckout');//更新checkoutId
        Route::get('/shipping-list','app\controller\api\checkoutController@getShippingLines');//查询运费
        Route::post('/set-shipping','app\controller\api\checkoutController@setShippingFee');//设置运费
        Route::post('/create-customer','app\controller\api\checkoutController@createCustomer');//创建客户
        Route::post('/complete-payment','app\controller\api\checkoutController@completePayment');//完成支付
        Route::post('/draft-create','app\controller\api\orderController@createDraftOrder');//创建草稿订单
        Route::put('/draft-modify/:id','app\controller\api\orderController@modifyDraftOrder')->model(\app\model\Orders::class);//修改订单
        Route::get('/payment-enable-get','app\controller\api\orderController@getPaymentMethod');//获取支付方式
        Route::post('/place-order/:id','app\controller\api\orderController@placeOrder')->model(\app\model\Orders::class);//下单
        Route::get('/session-token/:id','app\controller\api\orderController@getSessionToken')->model(\app\model\ShopsPayment::class);//sessionToken
        Route::get('/shipping-lines','app\controller\api\orderController@getShippingZones');//根据国家获取运费
        Route::post('/pre-paypal','app\controller\api\orderController@prePayByPaypal');//paypal预下单
        Route::get('/paypal-config','app\controller\api\orderController@getPaypalConfig');//获取paypal配置
        Route::get('/order-status/:id','app\controller\api\orderController@getOrderStatus')->model(\app\model\Orders::class);//获取订单状态
        Route::get('/order-detail/:id','app\controller\api\orderController@getOrderDetail')->model(\app\model\Orders::class);//获取订单详情
        Route::get('/order-status-url/:id','app\controller\api\orderController@getOrderStatusUrl')->model(\app\model\Orders::class);//获取成功页面
        Route::post('/order-confirm/:id','app\controller\api\orderController@confirmCheckout')->model(\app\model\Orders::class);//完成支付
        Route::post('/order-logs','app\controller\api\orderController@saveOrderLogs');//保存订单日志
    });
})
->middleware(\app\middleware\CheckShopifyRequest::class);//验证前端请求

//后台管理
Route::group('/admin',function (){
    Route::rule('/shop/add','app\controller\admin\ShopController@create','GET|POST');//添加店铺
    Route::get('/shop/list','app\controller\admin\ShopController@index');//店铺列表
    Route::any('/shop/update/:id','app\controller\admin\ShopController@update')->model(\app\model\Shops::class);//更新店铺
    Route::delete('/shop/del/:id','app\controller\admin\ShopController@delete')->model(\app\model\Shops::class);//删除店铺
    Route::post('/shop/edit/:id','app\controller\admin\ShopController@edit')->model(\app\model\Shops::class);//编辑店铺
    Route::post('/shop/status/:id','app\controller\admin\ShopController@editShopStatus')->model(\app\model\Shops::class);//编辑店铺
    Route::get('/shop/json','app\controller\admin\ShopController@getList');//店铺数据
    Route::get('/shop/payment/:id','app\controller\admin\ShopController@getShopPayment')->model(\app\model\Shops::class);//编辑店铺
    Route::get('/login','app\controller\admin\AdminController@login');//登录
    Route::get('/home','app\controller\admin\AdminController@index');//主页
    Route::post('/login/check','app\controller\admin\AdminController@checkLogin');//检查登录
    Route::get('/logout','app\controller\admin\AdminController@logout');//退出
    Route::get('/order/list','app\controller\admin\OrderController@index');//订单列表
    Route::get('/order/data','app\controller\admin\OrderController@getList');//订单数据
    Route::get('/order/:id','app\controller\admin\OrderController@show')->model(\app\model\Orders::class);//订单数据
})->middleware(\app\middleware\CheckUserLogin::class);