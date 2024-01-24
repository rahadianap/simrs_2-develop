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

Route::GET('/v1/kiosk/poli/{kodePoli}/tanggal/{tglPeriksa?}', 'DummyKioskController@GetListPoliByIdAndDate');
Route::GET('/v1/kiosk/poli/{tglPeriksa?}', 'DummyKioskController@GetListPoli');

Route::GET('/v1/kiosk/dokter/{dokterId}/unit/{unitId}', 'DummyKioskController@dataDokter');
Route::GET('/v1/kiosk/pasien/{typeId}/{noRM}', 'DummyKioskController@dataPasien');

Route::POST('/v1/kiosk/booking/verification', 'DummyKioskController@verifikasiBooking');
Route::POST('/v1/kiosk/booking', 'DummyKioskController@saveBooking');
Route::GET('/v1/kiosk/booking/{bookingId}', 'DummyKioskController@dataBooking');

Route::GET('/v1/kiosk/rujukan/nik/{nik}', 'DummyKioskController@dataRujukanByNik');
Route::GET('/v1/kiosk/rujukan/peserta/{noPeserta}', 'DummyKioskController@dataRujukanByNoPeserta');
Route::GET('/v1/kiosk/rujukan/nomor/{nomorRujukan}', 'DummyKioskController@dataRujukanByNoRujukan');
Route::GET('/v1/kiosk/rujukan/kontrol/{nomorKontrol}', 'DummyKioskController@dataRujukanByNoKontrol');

Route::GET('/jkn/token', 'AuthJknController@login');
Route::group(['middleware' => ['jkn.verify']], function () {
    Route::POST('/jkn/rs/antrean/status', 'AntrianRSController@StatusAntrean');
    Route::POST('/jkn/rs/antrean/ambil', 'AntrianRSController@AmbilAntrean');
    Route::POST('/jkn/rs/antrean/sisa', 'AntrianRSController@SisaAntrean');
    Route::POST('/jkn/rs/antrean/batal', 'AntrianRSController@BatalAntrean');
    Route::POST('/jkn/rs/antrean/checkin', 'AntrianRSController@CheckInAntrean');
    Route::POST('/jkn/rs/pasien/baru', 'AntrianRSController@InfoPasienBaru');
    Route::POST('/jkn/rs/jadwal/operasi/rs', 'AntrianRSController@JadwalOperasiRS');
    Route::POST('/jkn/rs/jadwal/operasi/pasien', 'AntrianRSController@JadwalOperasiPasien');
    Route::POST('/jkn/rs/antrean/farmasi/ambil', 'AntrianRSController@AmbilAntreanFarmasi');
    Route::POST('/jkn/rs/antrean/farmasi/status', 'AntrianRSController@StatusAntreanFarmasi');
});

Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    Route::GET('/kiosk/jkn/antrean/data/{kodebooking}', 'AntrianKioskController@GetAntreanData');
    Route::GET('/kiosk/pasien/data/{jenisId}/no/{id}', 'AntrianKioskController@GetPasienData');
    Route::GET('/kiosk/list/poli/{tglPeriksa?}', 'AntrianKioskController@GetListPoli');

    Route::GET('/kiosk/rujukan/bpjs/nomor/{noRujukan}', 'AntrianKioskController@GetRujukanBpjsByNomor');
    Route::GET('/kiosk/rujukan/bpjs/nokartu/{noKartu}', 'AntrianKioskController@GetRujukanBpjsByNoka');
    Route::GET('/kiosk/jadwal/{jadwalId}/tanggal/{tglPeriksa}', 'AntrianKioskController@infoJadwal');

    Route::GET('/kiosk/booking/{bookingId}', 'AntrianKioskController@dataBooking');
    Route::POST('/kiosk/booking', 'AntrianKioskController@saveBooking');
    Route::POST('/kiosk/booking/verifikasi', 'AntrianKioskController@verifikasiBooking');

    Route::POST('/kiosk/antrian/kasir', 'AntrianKioskController@createAntrianKasir');
    Route::POST('/kiosk/pasien/baru', 'AntrianKioskController@createPasienBaru');


    Route::GET('/jkn/referensi/poli', 'AntrianJknController@RefPoli');
    Route::GET('/jkn/referensi/dokter', 'AntrianJknController@RefDokter');
    Route::POST('/jkn/tambah/antrean', 'AntrianJknController@TambahAntrean');


    // Route::POST('/jkn/referensi/jadwal/dokter','AntrianRSController@SisaAntrean');
    // Route::GET('/jkn/referensi/poli/fingerprint','AntrianRSController@BatalAntrean');

    // Route::GET('/jkn/referensi/pasien/fingerprint','AntrianRSController@CheckInAntrean');
    // Route::POST('/jkn/update/jadwal/dokter','AntrianRSController@InfoPasienBaru');
    // Route::POST('/jkn/tambah/antrean/farmasi','AntrianRSController@JadwalOperasiPasien');    
    // Route::POST('/jkn/update/waktu/antrean','AntrianRSController@AmbilAntreanFarmasi');    
    // Route::POST('/jkn/batal/antrean','AntrianRSController@StatusAntreanFarmasi');      
    // Route::POST('/jkn/list/task','AntrianRSController@StatusAntreanFarmasi');  
    // Route::POST('/jkn/dashboard/pertanggal','AntrianRSController@StatusAntreanFarmasi');      
    // Route::POST('/jkn/dashboard/perbulan','AntrianRSController@StatusAntreanFarmasi');      
    // Route::POST('/jkn/antrean/pertanggal','AntrianRSController@StatusAntreanFarmasi');      
    // Route::POST('/jkn/antrean/perkode','AntrianRSController@StatusAntreanFarmasi');      

    // Route::POST('/jkn/antrean/belum/dilayani/poli/{kodepoli}/dokter/{kodedokter}/hari/{hari}/jam/{jam}','AntrianRSController@StatusAntreanFarmasi');      
    // Route::POST('/jkn/antrean/aktif','AntrianRSController@StatusAntreanFarmasi');      


});
