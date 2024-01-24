<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    /** MASTER FARMASI */
    Route::get('/farmasi/depos', 'FarmasiController@listDepos');
    Route::get('/farmasi', 'FarmasiController@lists');
    Route::get('/farmasi/{resultId}', 'FarmasiController@data');
    Route::post('/farmasi', 'FarmasiController@create');
    Route::post('/farmasi/{trxId}/realisasi', 'FarmasiController@realisasiResep');
    Route::post('/farmasi/{trxId}/cancel', 'FarmasiController@derealisasiResep');
    
    Route::put('/farmasi', 'FarmasiController@update');
    Route::delete('/farmasi/{id}', 'FarmasiController@delete');
    Route::get('/farmasi/transaksi/{trxId}', 'FarmasiController@pasienReseplists');
    
    /** APOTEK */

    Route::get('/pharmacy/{depoId}/items', 'ApotekController@listItemDepo');
    Route::get('/pharmacy', 'ApotekController@listResep');
    Route::get('/pharmacy/{resultId}', 'ApotekController@dataResep');
    Route::post('/pharmacy/{trxId}/realisasi', 'ApotekController@realisasiResep');
    Route::post('/pharmacy/{trxId}/cancel', 'ApotekController@derealisasiResep');

    Route::post('/pharmacy', 'ApotekController@saveResep');
    Route::put('/pharmacy', 'ApotekController@updateResep');
    Route::delete('/pharmacy/{id}', 'ApotekController@deleteResep');
});