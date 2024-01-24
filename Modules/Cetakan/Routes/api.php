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

    // Cetakan Master Data
    Route::GET('/cetakan/master/dokter/{id}', 'CetakanMasterController@dataDokter');
    Route::GET('/cetakan/master/pasien/history/{patient_id}', 'CetakanMasterController@dataHistoryPasien');

    // Cetakan Persediaan
    Route::GET('/cetakan/distribusi/{id}', 'CetakanPersediaanController@dataDistribusi');
    Route::GET('/cetakan/kartustock/{produk_id}', 'CetakanPersediaanController@dataKartuStock');
    Route::GET('/cetakan/prdata/{id}', 'CetakanPersediaanController@dataPurchaseRequest');
    Route::GET('/cetakan/podata/{id}', 'CetakanPersediaanController@dataPurchaseOrder');
    Route::GET('/cetakan/pordata/{id}', 'CetakanPersediaanController@dataPurchaseReceive');

    // Cetakan Laboratorium
    Route::GET('/cetakan/lab/{id}', 'CetakanLaboratoriumController@dataLab');
    Route::GET('/cetakan/lab/result/{id}', 'CetakanLaboratoriumController@dataHasilLab');

    // Cetakan Pendaftaran
    Route::GET('/cetakan/register/{reg_id}', 'CetakanPendaftaranController@dataPendaftaran');
    Route::GET('/cetakan/tracer/{reg_id}', 'CetakanPendaftaranController@dataTracer');
    Route::GET('/cetakan/gelangDewasa', 'CetakanPendaftaranController@dataGelangDewasa');
    Route::GET('/cetakan/form', 'CetakanPendaftaranController@dataFormInformasiPasien');
    Route::GET('/cetakan/registrasi', 'CetakanPendaftaranController@dataRegistrasiLabelPasien');

    // Cetakan Billing atau Transaksi
    Route::GET('/cetakan/billing/{jns_cetakan}/{ids}', 'CetakanBillingController@dataPembayaran');
    Route::GET('/cetakan/uangMuka', 'CetakanBillingController@dataUangMukaPasien');

    // Cetakan Resep
    Route::GET('/cetakan/etiket/{resep_id}', 'CetakanResepController@dataResep');
    
    // Cetakan Rawat Inap
    Route::GET('/cetakan/ri/penempatan/kelas/{no_rm}', 'CetakanRawatInapController@cetakanFormPenempatanKelasRi');
    
});