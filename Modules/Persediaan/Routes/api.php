<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    
Route::group(['middleware' => ['auth:api', 'api.verify']], function () {

    // Start Route : Persediaan STOCK OPNAME    
    // Pencarian Tanggal : tgl_dibuat, tgl_so, tgl_selesai
    //last update tgl 03-11-2022 - Agung Stefanus

    Route::get('/stock/opname/depos', 'StockOpnameController@listDepo');
    Route::get('/stock/opname/{depo_id}/detail', 'StockOpnameController@dataProdukDepo');
    
    Route::get('/stock/opname', 'StockOpnameController@list');
    Route::get('/stock/opname/{so_id}', 'StockOpnameController@data');
    Route::post('/stock/opname', 'StockOpnameController@create'); // draft
    Route::put('/stock/opname/data', 'StockOpnameController@updateData'); //edit so data (yang masih berstatus draft)
    
    Route::put('/stock/opname', 'StockOpnameController@update'); // proceed
    Route::delete('/stock/opname/{so_id}', 'StockOpnameController@delete'); // cancel
    Route::put('/stock/opname/{so_id}/draft', 'StockOpnameController@draft'); // draft
    Route::put('/stock/opname/{so_id}/proceed', 'StockOpnameController@proceed'); // proceed
    Route::put('/stock/opname/{so_id}/approve', 'StockOpnameController@approve'); // done
    // EMD Route : Persediaan STOCK OPNAME

    // Start Route : Persediaan STOCK ADJUSTMENTS   
    Route::get('/stock/adjustments', 'StockAdjustmentsController@list');
    Route::get('/stock/adjustments/{adjustment_id}', 'StockAdjustmentsController@data');
    Route::post('/stock/adjustments', 'StockAdjustmentsController@create'); // submit
    Route::put('/stock/adjustments', 'StockAdjustmentsController@update'); // submit
    Route::delete('/stock/adjustments/{adjustment_id}', 'StockAdjustmentsController@delete'); // cancel
    Route::put('/stock/adjustments/{adjustment_id}/draft', 'StockAdjustmentsController@edit'); // draft
    Route::put('/stock/adjustments/{adjustment_id}/submit', 'StockAdjustmentsController@edit'); // draft
    Route::put('/stock/adjustments/{adjustment_id}/approve', 'StockAdjustmentsController@approve'); // approve
    // EMD Route : Persediaan STOCK ADJUSTMENTS

    // Start Router : Inventory Issue
    Route::get('/inventory/issue/{adjustment_id}', 'InventoryIssueController@data');
    Route::get('/inventory/issue', 'InventoryIssueController@list');
    Route::post('/inventory/issue', 'InventoryIssueController@create'); // submit
    Route::put('/inventory/issue', 'InventoryIssueController@update'); // submit
    Route::delete('/inventory/issue/{adjustment_id}', 'InventoryIssueController@delete'); // cancel
    Route::put('/inventory/issue/{adjustment_id}/draft', 'InventoryIssueController@edit'); // draft
    Route::put('/inventory/issue/{adjustment_id}/submit', 'InventoryIssueController@edit'); // draft
    Route::put('/inventory/issue/{adjustment_id}/approve', 'InventoryIssueController@approve'); // approve
    // EMD Route : Inventory Issue

    // Route : Persediaan DISTRIBUSI
    // Pencarian Tanggal : tgl_dibuat, tgl_dibutuhkan, tgl_realisasi
    Route::put('/distributions/products/receive/{distributions_id}', 'DistribusiController@receive'); // reveived
    Route::put('/distributions/products/approve', 'DistribusiController@approve'); // approve
    
    Route::get('/distributions/products', 'DistribusiController@list');
    Route::get('/distributions/products/{distributions_id}', 'DistribusiController@data');
    Route::post('/distributions/products', 'DistribusiController@create'); // draft
    Route::put('/distributions/products', 'DistribusiController@update'); // draft

    Route::delete('/distributions/products/{distributions_id}', 'DistribusiController@delete');
    Route::put('/distributions/products/{distributions_id}/draft', 'DistribusiController@draft'); // draft
    Route::put('/distributions/products/{distributions_id}/submit', 'DistribusiController@submit'); // submitted
    Route::put('/distributions/products/{distributions_id}/proceed', 'DistribusiController@proceed'); // proceed
    //Route::put('/distributions/products/{distributions_id}/approve', 'DistribusiController@approved'); // approve
    //Route::put('/distributions/products/{distributions_id}/received', 'DistribusiController@received'); // reveived
    // Start Router : Produksi
    Route::get('/items/productions/results/{depoId}', 'ProduksiController@listItemResults');
    Route::get('/items/productions', 'ProduksiController@list');
    Route::get('/items/productions/{productions_id}', 'ProduksiController@data');
    Route::post('/items/productions', 'ProduksiController@create'); // draft
    Route::put('/items/productions', 'ProduksiController@update'); // draft
    Route::delete('/items/productions/{productions_id}', 'ProduksiController@delete'); // canceled
    Route::put('/items/productions/{productions_id}/approve', 'ProduksiController@approve'); // approve    
    // EMD Route : Produksi

    // Start Route : Persediaan DISTRIBUSI
    // Pencarian Tanggal : tgl_dibuat, tgl_dibutuhkan, tgl_realisasi    
    // Route::get('/distributions/datas/{distributions_id}', 'DistribusiController@data');
    // Route::get('/distributions/{status?}', 'DistribusiController@list');
    // Route::post('/distributions', 'DistribusiController@create'); // draft
    // Route::put('/distributions/approve', 'DistribusiController@approve'); // approve
    // Route::put('/distributions/receive/{distribution_id}', 'DistribusiController@receive'); // received
    // Route::put('/distributions', 'DistribusiController@update'); // draft
    // Route::delete('/distributions/{distributions_id}', 'DistribusiController@delete');
    // Route Distribusi untuk pengadaan belum digali lebih lanjut
    // EMD Route : Persediaan DISTRIBUSI
});
