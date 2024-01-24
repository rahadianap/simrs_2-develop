<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {

    /** MASTER PURCHASE REQUEST */
    Route::get('/purchases/requests/details/{status?}', 'PurchaseRequestController@detailLists');
    Route::get('/purchases/requests/data/{pr_id}', 'PurchaseRequestController@data');    
    Route::get('/purchases/requests/{status?}', 'PurchaseRequestController@list');
    Route::post('/purchases/requests', 'PurchaseRequestController@create');
    Route::put('/purchases/requests', 'PurchaseRequestController@update');
    Route::put('/purchases/requests/{id}/approve', 'PurchaseRequestController@approvePR');
    Route::put('/purchases/requests/{id}/cancel', 'PurchaseRequestController@cancelPR');
    Route::delete('/purchases/requests/{id}', 'PurchaseRequestController@deleteDetail');

    /** MASTER PURCHASE ORDER */
    Route::GET('/purchases/orders/data/{trxid}', 'PurchaseOrderController@data');
    Route::GET('/purchases/orders/{status?}', 'PurchaseOrderController@list');
    //Route::POST('/purchases/orders', 'PurchaseOrderController@createPOFromPR');
    Route::POST('/purchases/orders', 'PurchaseOrderController@createPO');
    Route::PUT('/purchases/orders', 'PurchaseOrderController@updatePO');
    Route::DELETE('/purchases/orders/{trxid}', 'PurchaseOrderController@deletePO');

    Route::PUT('/purchases/orders/{id}/approve', 'PurchaseOrderController@approvePO');
    //Route::PUT('/purchases/orders/{id}/cancel', 'PurchaseOrderController@cancelPO');
    Route::PUT('/purchases/orders/{id}/proses', 'PurchaseOrderController@prosesPO');
    
    /** MASTER PURCHASE RECEIVE */
    Route::GET('/purchases/receives/{trxid}', 'PurchaseReceiveController@data');
    Route::GET('/purchases/receives', 'PurchaseReceiveController@list');
    Route::POST('/purchases/receives', 'PurchaseReceiveController@createPOR');    
    Route::PUT('/purchases/receives/{trxId}/approve', 'PurchaseReceiveController@approvePOR');    
    Route::PUT('/purchases/receives', 'PurchaseReceiveController@updatePOR');
    Route::DELETE('/purchases/receives/{trxId}', 'PurchaseReceiveController@deletePOR');
    
    /** MASTER PURCHASE RETURN */
    Route::GET('/purchases/returns/{trxid}', 'PurchaseReturnController@data');
    Route::GET('/purchases/returns', 'PurchaseReturnController@list');
    Route::POST('/purchases/returns', 'PurchaseReturnController@createPORR');    
    Route::PUT('/purchases/returns/{trxId}/approve', 'PurchaseReturnController@approvePORR');    
    Route::PUT('/purchases/returns', 'PurchaseReturnController@updatePORR');
    Route::DELETE('/purchases/returns/{trxId}', 'PurchaseReturnController@deletePORR');

    // Route::get('/purchases/returns', 'PurchaseReturnController@list');
    // Route::post('/purchases/returns', 'PurchaseReturnController@updatePORR');
    // Route::get('/purchases/returns/{pengadaan_id}', 'PurchaseReturnController@data');

    /** PURCHASE MONITORING */
    Route::GET('/purchases/managements/{trxid}', 'PurchaseManagementsController@data');
    Route::GET('/purchases/managements', 'PurchaseManagementsController@list');

});