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

    
Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    Route::GET('/outpatient/transactions/{regId}', 'PoliController@data');
    Route::GET('/outpatient/transactions', 'PoliController@lists');
    Route::POST('/outpatient/transactions', 'PoliController@saveData');
    
    Route::GET('/inpatient/transactions/{regId}', 'InapController@data');
    Route::GET('/inpatient/transactions', 'InapController@lists');
    Route::POST('/inpatient/transactions', 'InapController@saveData');
    Route::GET('/inpatient/cetak/gelang/{regId}', 'InapController@cetakanGelang');
    
    Route::GET('/prescriptions/depos', 'ResepController@listDepo');
    Route::GET('/prescriptions/{resepId}', 'ResepController@dataResep');
    Route::POST('/prescriptions', 'ResepController@saveResep');
    Route::PUT('/prescriptions', 'ResepController@updateResep');
    Route::DELETE('/prescriptions/{resepId}', 'ResepController@deleteResep');
    Route::GET('/prescriptions', 'ResepController@listResep');
    Route::PUT('/prescriptions/{resep_id}/realizations', 'ResepController@realisasiResep');

    Route::GET('/examinations/{bhpId}', 'PemeriksaanController@dataPemeriksaan');
    Route::POST('/examinations/confirm/{trxId}', 'PemeriksaanController@confirmPemeriksaan');
    Route::POST('/examinations/unconfirm/{trxId}', 'PemeriksaanController@unconfirmPemeriksaan');
    Route::POST('/examinations', 'PemeriksaanController@savePemeriksaan');
    Route::PUT('/examinations', 'PemeriksaanController@updatePemeriksaan');
    Route::DELETE('/examinations/{bhpId}', 'PemeriksaanController@deletePemeriksaan');

    Route::GET('/assesments/{asesmenId}', 'AsesmenController@dataAsesmen');
    Route::POST('/assesments', 'AsesmenController@createAsesmen');
    Route::PUT('/assesments', 'AsesmenController@updateAsesmen');
    Route::DELETE('/assesments/{asesmenId}', 'AsesmenController@deleteAsesmen');
    // CREATE DIAGNOSIS
    Route::POST('/assesments/diagnosis', 'AsesmenController@createDiagnosis');
 
    Route::GET('/billings/{regId}', 'BillingController@data');
    Route::GET('/billings', 'BillingController@lists');
    Route::POST('/billings', 'BillingController@create');
    Route::PUT('/billings', 'BillingController@update');
    Route::DELETE('/billings/{paymentId}', 'BillingController@delete');
    
});