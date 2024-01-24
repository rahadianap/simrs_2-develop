<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    /** MASTER TINDAKAN/PEMERIKSAAN RADIOLOGI */
    Route::get('/patologi/inspections', 'PemeriksaanPatologiAnatomiController@lists');
    Route::get('/patologi/inspections/{resultId}', 'PemeriksaanPatologiAnatomiController@data');
    Route::post('/patologi/inspections', 'PemeriksaanPatologiAnatomiController@create');
    Route::put('/patologi/inspections', 'PemeriksaanPatologiAnatomiController@update');
    Route::delete('/patologi/inspections/{id}', 'PemeriksaanPatologiAnatomiController@delete');

    /**
     * PENDAFTARAN patologi
    //  */
    Route::get('/patologi/registrations/{reg_id}', 'PatologiAnatomiController@data');
    Route::get('/patologi/registrations', 'PatologiAnatomiController@lists');    
    Route::post('/patologi/registrations', 'PatologiAnatomiController@create');    
    Route::put('/patologi/registrations/unconfirm', 'PatologiAnatomiController@deleteConfirm');
    Route::put('/patologi/registrations/confirm', 'PatologiAnatomiController@confirm');
    Route::put('/patologi/registrations', 'PatologiAnatomiController@update');
    Route::delete('/patologi/registrations/{reg_id}', 'PatologiAnatomiController@delete');

    /**
     * HASIL PATOLOGI
     */
    Route::get('/patologi/results/{subTrxId}', 'PatologiAnatomiController@resultData');    
    Route::get('/patologi/results', 'PatologiAnatomiController@lists');    
    
    Route::put('/patologi/results', 'PatologiAnatomiController@saveHasilRad');    
    Route::delete('/patologi/results/{resultId}', 'PatologiAnatomiController@deleteResultData');    
    
    Route::get('/patologi/detail/{trx_id}', 'PatologiAnatomiController@detailData');
    Route::get('/patologi/result/{trx_id}', 'PatologiAnatomiController@resultData');

    Route::get('/patologi/tindakan/{reg_id}', 'PatologiAnatomiController@listTindakanRad');
    Route::put('/patologi/hasil/{reg_id}/{jns_hasil}', 'PatologiAnatomiController@hasilRad');

    /**
     * TEMPLATE PATOLOGI
     */
    Route::get('/patologi/templates/{templateId}', 'PatologiTemplateController@data');    
    Route::get('/patologi/templates', 'PatologiTemplateController@lists');    
    
    Route::post('/patologi/templates', 'PatologiTemplateController@create');
    Route::put('/patologi/templates', 'PatologiTemplateController@update');
    Route::delete('/patologi/templates/{templateId}', 'PatologiTemplateController@delete');    
});