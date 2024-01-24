<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/laboratorium', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    /**
     * pendaftaran pasien lab
     */
    Route::get('/labs/registrations', 'LabRegistrasiController@lists');
    Route::get('/labs/registrations/{subTrxId}', 'LabRegistrasiController@data');
    Route::post('/labs/registrations', 'LabRegistrasiController@create');
    Route::put('/labs/registrations/unconfirm', 'LabRegistrasiController@deleteConfirm');
    Route::put('/labs/registrations/confirm', 'LabRegistrasiController@confirm');
    Route::put('/labs/registrations', 'LabRegistrasiController@update');
    Route::delete('/labs/registrations/{subTrxId}', 'LabRegistrasiController@delete');

    /** MASTER ITEM HASIL LAB */
    Route::get('/labs/items/results', 'ItemLabController@lists');
    Route::get('/labs/items/results/{resultId}', 'ItemLabController@data');
    Route::post('/labs/items/results', 'ItemLabController@create');
    Route::put('/labs/items/results', 'ItemLabController@update');
    Route::delete('/labs/items/results/{id}', 'ItemLabController@delete');

    /** MASTER TINDAKAN/PEMERIKSAAN LAB */
    Route::get('/labs/inspections', 'PemeriksaanLabController@lists');
    Route::get('/labs/inspections/{resultId}', 'PemeriksaanLabController@data');
    Route::post('/labs/inspections', 'PemeriksaanLabController@create');
    Route::put('/labs/inspections', 'PemeriksaanLabController@update');
    Route::delete('/labs/inspections/{id}', 'PemeriksaanLabController@delete');

    /** HASIL PEMERIKSAAN LAB */
    Route::get('/labs/results', 'HasilLabController@lists');
    Route::get('/labs/results/{subTrxId}', 'HasilLabController@data');
    Route::put('/labs/results', 'HasilLabController@saveResult');
    //Route::put('/labs/results', 'HasilLabController@update');
    Route::delete('/labs/results/{id}', 'HasilLabController@delete');

    // /** TRANSAKSI PEMERIKSAAN LAB */
    // Route::get('/labs/transactions', 'TransaksiLabController@lists');
    // Route::get('/labs/transactions/{regId}', 'TransaksiLabController@data');
    // Route::post('/labs/transactions', 'TransaksiLabController@create');
    // Route::put('/labs/transactions', 'TransaksiLabController@update');
    // Route::delete('/labs/transactions/{id}', 'TransaksiLabController@delete');

    // /**
    //  * LAB TEMPLATE HASIL PEMERIKSAAN 
    //  */
    // Route::get('/labs/templates/{templateId}', 'TemplateLabController@data');
    // Route::post('/labs/templates', 'TemplateLabController@create');
    // Route::delete('/labs/templates/{id}', 'TemplateLabController@delete');

});