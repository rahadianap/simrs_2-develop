<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:api', 'api.verify']], function () {
    //master pasien    
    Route::GET('/praktek/dokter/pasien/{pasienId}', 'MasterPasienController@data');
    Route::GET('/praktek/dokter/pasien', 'MasterPasienController@lists');
    Route::POST('/praktek/dokter/pasien', 'MasterPasienController@create');
    Route::PUT('/praktek/dokter/pasien', 'MasterPasienController@update');
    Route::DELETE('/praktek/dokter/pasien/{pasienId}', 'MasterPasienController@remove');

    //master dokter    
    Route::GET('/praktek/dokter/masterdokter/{dokterId}', 'MasterDokterController@data');
    Route::GET('/praktek/dokter/masterdokter', 'MasterDokterController@lists');
    Route::POST('/praktek/dokter/masterdokter', 'MasterDokterController@create');
    Route::PUT('/praktek/dokter/masterdokter', 'MasterDokterController@update');
    Route::DELETE('/praktek/dokter/masterdokter/{dokterId}', 'MasterDokterController@remove');

    //master tindakan    
    Route::GET('/praktek/dokter/mastertindakan/{tindakanId}', 'MasterTindakanController@data');
    Route::GET('/praktek/dokter/mastertindakan', 'MasterTindakanController@lists');
    Route::POST('/praktek/dokter/mastertindakan', 'MasterTindakanController@create');
    Route::PUT('/praktek/dokter/mastertindakan', 'MasterTindakanController@update');
    Route::DELETE('/praktek/dokter/mastertindakan/{tindakanId}', 'MasterTindakanController@remove');

    //pembayaran    
    Route::GET('/praktek/dokter/pembayaran/{pembayaranId}', 'PaymentController@data');
    Route::GET('/praktek/dokter/pembayaran', 'PaymentController@lists');
    Route::POST('/praktek/dokter/pembayaran', 'PaymentController@create');
    Route::PUT('/praktek/dokter/pembayaran', 'PaymentController@update');
    Route::PUT('/praktek/dokter/pembayaran/delete', 'PaymentController@remove');
    Route::GET('/praktek/dokter/pembayaran/cetak/kwitansi/{regId}', 'PaymentController@cetakKwitansi');

    //pencatatan kas
    Route::PUT('/praktek/dokter/kas/delete', 'KasController@remove');
    Route::GET('/praktek/dokter/kas/{kasId}', 'KasController@data');
    Route::GET('/praktek/dokter/kas', 'KasController@lists');
    Route::POST('/praktek/dokter/kas', 'KasController@create');
    Route::PUT('/praktek/dokter/kas', 'KasController@update');
    
    //bukti pencatatan kas
    Route::DELETE('/praktek/dokter/kas/bukti/{idLampiran}', 'KasController@removeBuktiKas');
    Route::POST('/praktek/dokter/kas/bukti', 'KasController@buktiKas');
    
    //registrasi 
    Route::GET('/praktek/dokter/queue/{queueId}', 'RegistrasiController@data');
    Route::GET('/praktek/dokter/queue', 'RegistrasiController@lists');
    Route::POST('/praktek/dokter/queue', 'RegistrasiController@create');
    Route::PUT('/praktek/dokter/queue/delete', 'RegistrasiController@remove');
    Route::PUT('/praktek/dokter/queue/status', 'RegistrasiController@updateStatus');
    Route::PUT('/praktek/dokter/queue', 'RegistrasiController@update');
    
    //pelayanan
    // Route::GET('/praktek/dokter/pelayanan/{pelayananId}', 'PelayananController@data');
    // Route::GET('/praktek/dokter/pelayanan', 'PelayananController@lists');
    // Route::POST('/praktek/dokter/pelayanan', 'PelayananController@create');
    // Route::PUT('/praktek/dokter/pelayanan', 'PelayananController@update');
    // Route::DELETE('/praktek/dokter/pelayanan/{pelayananId}', 'PelayananController@remove');
    
    /**PEMERIKSAAN */
    Route::GET('/praktek/dokter/pemeriksaan/{regId}', 'PemeriksaanController@data');
    Route::POST('/praktek/dokter/pemeriksaan', 'PemeriksaanController@savePemeriksaan');
    Route::PUT('/praktek/dokter/pemeriksaan', 'PemeriksaanController@savePemeriksaan');
    Route::GET('/praktek/dokter/pemeriksaan/surat/pasien/cetak/{jnsSurat}/{regId}', 'PemeriksaanController@cetakSuratPasien');
    Route::GET('/praktek/dokter/pemeriksaan/surat/pasien/{jnsSurat}/{regId}', 'PemeriksaanController@dataSuratPasien');
    Route::POST('/praktek/dokter/pemeriksaan/surat/pasien/{jnsSurat}', 'PemeriksaanController@createSuratPasien');
    Route::PUT('/praktek/dokter/pemeriksaan/surat/pasien/{jnsSurat}', 'PemeriksaanController@updateSuratPasien');
    // Route::DELETE('/praktek/dokter/pemeriksaan/{soap_id}', 'PemeriksaanController@remove');
    
    /**Resep */
    Route::GET('/praktek/dokter/resep/{id}', 'ResepController@data');
    Route::GET('/praktek/dokter/resep/cetak/{id}', 'ResepController@cetakResep');
    Route::POST('/praktek/dokter/resep', 'ResepController@update');
    Route::PUT('/praktek/dokter/resep', 'ResepController@update');
    

    /**
     * data medical record
     */
    Route::GET('/praktek/dokter/medrec/{pasienId}/lists', 'PraktekDokterController@lists');
    Route::GET('/praktek/dokter/medrec/{regId}', 'PraktekDokterController@medrecDataByReg');
    Route::PUT('/praktek/dokter/medrec/{regId}', 'PraktekDokterController@update');
    Route::GET('/praktek/dokter/medrec/pendaftaran/cetak/{regId}', 'PraktekDokterController@cetakanPendaftaran');
    Route::GET('/praktek/dokter/medrec/history/cetak/{regId}', 'PraktekDokterController@cetakanHistory');

    
    // Route::POST('/praktek/dokter/medrec/{regId}/diagnosa', 'PelayananController@createDiagnosa');
    // Route::PUT('/praktek/dokter/medrec/{regId}/diagnosa', 'PelayananController@updateDiagnosa');
    // Route::DELETE('/praktek/dokter/medrec/{regId}/diagnosa/{diagnosa_id}', 'PelayananController@removeDiagnosa');
    
    //laporan
    Route::GET('/praktek/dokter/laporan/pendapatan', 'ReportController@lapPendapatan');
    Route::GET('/praktek/dokter/laporan/kunjungan', 'ReportController@lapKunjungan');
    Route::GET('/praktek/dokter/laporan/kas', 'ReportController@lapKas');
    // cetakan laporan
    Route::GET('/praktek/dokter/laporan/cetakan/pendapatan', 'ReportController@cetakanPendapatan');
    Route::GET('/praktek/dokter/laporan/cetakan/kunjungan', 'ReportController@cetakanKunjungan');
    Route::GET('/praktek/dokter/laporan/cetakan/kas', 'ReportController@cetakanKas');

    // Route : Dashboard
    /** 
     * Dashboard Summary (Ringkas) akan mewakili beberapa data sekaligus seperti : 
     * */ 
    Route::GET('/praktek/dokter/dashboard/summary/kunjungan', 'DashboardController@summaryTabKunjungan');
    Route::GET('/praktek/dokter/dashboard/summary/all', 'DashboardController@summaryTab');

    /** 
     * Dashboard Monitoring (Monitor) akan mewakili beberapa data sekaligus seperti : 
     * */ 
    Route::GET('/praktek/dokter/dashboard/monitoring/status', 'DashboardController@monitoringStatus');
    Route::GET('/praktek/dokter/dashboard/monitoring/antrian', 'DashboardController@monitoringAntrian');
    Route::GET('/praktek/dokter/dashboard/monitoring/transaksi', 'DashboardController@monitoringTransaksi');
    
    // // Router : Master Metode Pembayaran
    // Route::GET('/cara/bayar', 'MasterCaraBayarController@lists');
    // Route::GET('/cara/bayar/{cara_bayar_id}', 'MasterCaraBayarController@data');
    // Route::POST('/cara/bayar', 'MasterCaraBayarController@create');
    // /** METODE PEMBAYARAN TIDAK BOLEH DIHAPUS PERMANEN */
    // Route::PUT('/cara/bayar', 'MasterCaraBayarController@update');
    // Route::DELETE('/cara/bayar/{cara_bayar_id}', 'MasterCaraBayarController@delete');

    // // Master : Pasien
    // Route::GET('/patients/history/{patientsId}', 'MasterPasienController@history');

    // // Daftar Antrian
    // Route::POST('/registrations/emr', 'PendaftaranController@createEMR');
    // Route::POST('/registrations/emr/{emr_id}', 'PendaftaranController@updateEMR');

    // Riwayat RPembayaran
    Route::GET('/pembayaran', 'PaymentController@lists');
    Route::GET('/pembayaran/{reg_id}', 'PaymentController@data');
    Route::POST('/pembayaran', 'PaymentController@create');
    Route::PUT('/pembayaran', 'PaymentController@update');
    Route::DELETE('/pembayaran/{payment_id}', 'PaymentController@delete');

    // // Pencatatan Kas
    // Route::GET('/pencatatan/kas', 'PencatatanKasController@lists');
    // Route::GET('/pencatatan/kas/{pencatatan_kas_id}', 'PencatatanKasController@data');
    // Route::POST('/pencatatan/kas', 'PencatatanKasController@create');
    // Route::PUT('/pencatatan/kas', 'PencatatanKasController@update');
    // Route::DELETE('/pencatatan/kas/{pencatatan_kas_id}', 'PencatatanKasController@delete');

    // // Lap.Pendapatan
    // Route::GET('/report/pendpatan', 'ReportPendpatanController@data');

    // // Lap.Kunjungan
    // Route::GET('/report/kunjungan', 'ReportKunjunganController@data');

    // // Lap.Pencatatan Kas
    // Route::GET('/report/pencatatan/kas', 'ReportPencatatanKasController@data');
});