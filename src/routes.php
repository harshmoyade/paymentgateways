<?php
use Sunil\Payments\Facades\Ggpay;
use Illuminate\Http\Request;

Route::get('paynow/{payment_mode}', function($request){

    $parameters = [


        'tid' => '1233221223322',

        'order_id' => '1232212',
        'payment_mode' => $request,

        'amount' => 1.00,
        'firstname' => 'Sunil',
        'email' => 'sunilahir880@gmail.com',
        'phone' => '8128273971',
        'productinfo' => '1232212', // For the Payumoney Gateway Optional Paramater

    ];

    // gateway = CCAvenue / PayUMoney / EBS / Citrus / InstaMojo / ZapakPay / Mocker / CitrusPopup
//    if(empty($request)){
        $order = Ggpay::gateway($request)->prepare($parameters);
//    }else{
//        $order = Ggpay::gateway($request)->prepare($parameters);
//    }

    return Ggpay::process($order);

});

//Route::post('/indipay/response',function(Request $request){
//
////    $response = Ggpay::gateway('CitrusPopup')->response($request);
//
////    dd($response);
//    dd($request->all());
//});