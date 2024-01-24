<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {

    Route::GET('/report', 'ReportController@list');
    Route::POST('/report', 'ReportController@create');

    Route::get('/report/lab/pemeriksaan', 'ReportLaboratoriumController@dataPemeriksaan');
    Route::get('/report/lab/kunjungan', 'ReportLaboratoriumController@dataKunjungan');
    Route::get('/report/lab/rujukan', 'ReportLaboratoriumController@dataRujukan');
    Route::get('/report/lab/rujukanDetail', 'ReportLaboratoriumController@dataRujukanDetail');
    Route::get('/report/lab/skrining', 'ReportLaboratoriumController@dataSkriningPenyakit');

    Route::get('/report/penerimaan/medis', 'ReportPenerimaanBarangController@dataMedis');
    Route::get('/report/penerimaan/umum', 'ReportPenerimaanBarangController@dataUmum');
    
    // Route::get('/report/cetakan/gelangDewasa', 'ReportCetakanController@cetakanGelangDewasa');
    Route::get('/report/cetakan/registrasiLabel', 'ReportCetakanController@cetakanRegistrasiLabel');
    Route::get('/report/cetakan/ringkasanMasukKeluar', 'ReportCetakanController@cetakanRingkasanMasukKeluar');
    Route::get('/report/cetakan/formPenempatanKelasRi', 'ReportCetakanController@cetakanFormPenempatanKelasRi');
    // Route::get('/report/cetakan/formInformasiPasien', 'ReportCetakanController@cetakanFormInformasiPasien');
    Route::get('/report/cetakan/kwitansi', 'ReportCetakanController@cetakanKwitansi');
    // Route::get('/report/cetakan/uangMukaPasien', 'ReportCetakanController@cetakanUangMukaPasien');
    Route::get('/report/cetakan/pendaftaranRajal', 'ReportCetakanController@cetakanPendaftaranRajal');
    Route::get('/report/cetakan/tracer', 'ReportCetakanController@cetakanTracer');

    Route::get('/report/akunting/pendapatanRanap', 'ReportAkuntingController@dataPendapatanRanap');
    Route::get('/report/akunting/pendapatanRajal', 'ReportAkuntingController@dataPendapatanRajal');
    Route::get('/report/akunting/pendapatanFarmasi', 'ReportAkuntingController@dataPendapatanFarmasi');
    Route::get('/report/akunting/harianKasirRanap', 'ReportAkuntingController@dataHarianKasirRanap');
    Route::get('/report/akunting/harianKasirRajal', 'ReportAkuntingController@dataHarianKasirRajal');
    Route::get('/report/akunting/pendapatanHarianKasir', 'ReportAkuntingController@dataPendapatanHarianKasir');
    Route::get('/report/akunting/marektingPerPayer', 'ReportAkuntingController@dataMarketingPerPayer');

    Route::get('/report/farmasi/pemakaianObat', 'ReportFarmasiController@dataPemakaianObat');
    Route::get('/report/farmasi/jumlahResep', 'ReportFarmasiController@dataJumlahResep');
    Route::get('/report/farmasi/waktuTunggu', 'ReportFarmasiController@dataWaktuTunggu');

});