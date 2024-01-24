<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    /** MASTER TINDAKAN/PEMERIKSAAN RADIOLOGI */
    Route::get('/radiology/inspections', 'PemeriksaanRadiologiController@lists');
    Route::get('/radiology/inspections/{resultId}', 'PemeriksaanRadiologiController@data');
    Route::post('/radiology/inspections', 'PemeriksaanRadiologiController@create');
    Route::put('/radiology/inspections', 'PemeriksaanRadiologiController@update');
    Route::delete('/radiology/inspections/{id}', 'PemeriksaanRadiologiController@delete');

    /**
     * PENDAFTARAN RADIOLOGY
     */
    Route::get('/radiology/registrations/{reg_id}', 'RadiologiController@data');
    Route::get('/radiology/registrations', 'RadiologiController@lists');    
    Route::post('/radiology/registrations', 'RadiologiController@create');    
    Route::put('/radiology/registrations/unconfirm', 'RadiologiController@deleteConfirm');
    Route::put('/radiology/registrations/confirm', 'RadiologiController@confirm');
    Route::put('/radiology/registrations', 'RadiologiController@update');
    Route::delete('/radiology/registrations/{reg_id}', 'RadiologiController@delete');

    /**
     * HASIL RADIOLOGY
     */
    Route::get('/radiology/results/{subTrxId}', 'RadiologiController@resultData');    
    Route::get('/radiology/results', 'RadiologiController@lists');    
    
    Route::put('/radiology/results', 'RadiologiController@saveHasilRad');    
    Route::delete('/radiology/results/{resultId}', 'RadiologiController@deleteResultData');    
    

    /**
     * TEMPLATE RADIOLOGY
     */
    Route::get('/radiology/templates/{templateId}', 'RadTemplateController@data');    
    Route::get('/radiology/templates', 'RadTemplateController@lists');    
    
    Route::post('/radiology/templates', 'RadTemplateController@create');
    Route::put('/radiology/templates', 'RadTemplateController@update');
    Route::delete('/radiology/templates/{templateId}', 'RadTemplateController@delete');    
    
    //Route::get('/radiology/detail/{trx_id}', 'RadiologiController@detailData');
    //Route::get('/radiology/result/{trx_id}', 'RadiologiController@resultData');

    // Route::get('/radiology/tindakan/{reg_id}', 'RadiologiController@listTindakanRad');
    // Route::put('/radiology/hasil/{reg_id}/{jns_hasil}', 'RadiologiController@hasilRad');

    
});