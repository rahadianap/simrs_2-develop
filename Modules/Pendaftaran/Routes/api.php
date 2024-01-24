<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
    
Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    // PENDAFTARAN RAWAT JALAN
    Route::GET('registrations/unit/praktek', 'PendaftaranController@DokterJadwalByDay');
    Route::GET('registrations', 'PendaftaranController@lists');
    Route::GET('registrations/{id}', 'PendaftaranController@data');
    //Route::GET('registrations/{jns}/patients', 'PendaftaranController@listsRegJns');
    Route::POST('registrations', 'PendaftaranController@create');
    Route::PUT('registrations/confirm', 'PendaftaranController@confirm');
    Route::PUT('registrations', 'PendaftaranController@update');
    Route::DELETE('registrations/{id}', 'PendaftaranController@delete');

    Route::GET('/outpatient/booking/{trx_id}', 'BookingController@data');
    Route::GET('/outpatient/booking', 'BookingController@lists');
    Route::PUT('/outpatient/booking/confirm', 'BookingController@confirm');
    Route::POST('/outpatient/booking', 'BookingController@create');
    Route::PUT('/outpatient/booking', 'BookingController@update');
    Route::DELETE('/outpatient/booking/{regId}', 'BookingController@delete');

    // PENDAFTARAN RAWAT INAP
    Route::GET('/inpatients/admissions/{trx_id}', 'ManajemenInapController@data');
    Route::GET('/inpatients/admissions', 'ManajemenInapController@lists');
    Route::POST('/inpatients/admissions', 'ManajemenInapController@create');
    Route::PUT('/inpatients/admissions/confirm', 'ManajemenInapController@confirm');
    Route::PUT('/inpatients/admissions', 'ManajemenInapController@update');
    Route::DELETE('/inpatients/admissions/{trxId}', 'ManajemenInapController@delete');

    /**transfer ruang pasien*/
    Route::PUT('/inpatients/doctor/change', 'ManajemenInapController@gantiDokterDPJP');
    Route::PUT('/inpatients/room/change', 'ManajemenInapController@gantiRuangInap');
    Route::PUT('/inpatients/discharges', 'ManajemenInapController@pasienPulang');
    Route::PUT('/inpatients/guarantor/change', 'ManajemenInapController@gantiPenjamin');

    /**JADWAL OPERASI */
    Route::get('/operation/registrations/{trx_id}', 'JadwalOperasiController@data');
    Route::get('/operation/registrations', 'JadwalOperasiController@lists');  
    
    Route::post('/operation/registrations', 'JadwalOperasiController@create');    
    Route::put('/operation/registrations/unconfirm', 'JadwalOperasiController@deleteConfirm');
    Route::put('/operation/registrations/confirm', 'JadwalOperasiController@confirm');
    Route::put('/operation/registrations', 'JadwalOperasiController@update');
    Route::delete('/operation/registrations/{trx_id}', 'JadwalOperasiController@delete');

});