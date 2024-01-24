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
    /**
     * Data credential
     */
    Route::GET('/bpjs/credential', 'BridgingBpjsController@dataCredential');
    Route::PUT('/bpjs/credential', 'BridgingBpjsController@updateCredential');

    Route::GET('/jkn/credential', 'BridgingBpjsController@dataJknCredential');
    Route::PUT('/jkn/credential', 'BridgingBpjsController@updateJknCredential');

    /**
     * Data Peserta BPJS
     */
    Route::get('/bpjs/peserta/noka/{noKepesertaaan}', 'PesertaController@dataByNoka');
    Route::get('/bpjs/peserta/nik/{nik}', 'PesertaController@dataByNik');
    
    /**
     * data referensi
     */
    Route::GET('/bpjs/ref/spesialistik', 'ReferensiController@refSpesialistik');
    Route::GET('/bpjs/ref/propinsi', 'ReferensiController@refPropinsi');
    Route::GET('/bpjs/ref/kabupaten/{propId}', 'ReferensiController@refKabupaten');
    Route::GET('/bpjs/ref/kecamatan/{kabId}', 'ReferensiController@refKecamatan');
    Route::GET('/bpjs/ref/kelas', 'ReferensiController@refKelasRawat');
    Route::GET('/bpjs/ref/ruang', 'ReferensiController@refRuangRawat');
    Route::GET('/bpjs/ref/cara/keluar', 'ReferensiController@refCaraKeluar');
    Route::GET('/bpjs/ref/pasca/pulang', 'ReferensiController@refPascaPulang');
    Route::GET('/bpjs/ref/poli/{param}', 'ReferensiController@refPoli');
    Route::GET('/bpjs/ref/dokter/{param}', 'ReferensiController@refDokter');
    Route::GET('/bpjs/ref/dpjp/{jnsPelayanan}/tanggal/{tglPelayanan}/Spesialis/{kodeSpesialis}', 'ReferensiController@refDpjp');
    Route::GET('/bpjs/ref/diagnosa/{param}', 'ReferensiController@refDiagnosa');
    Route::GET('/bpjs/ref/procedure/{param}', 'ReferensiController@refProcedure');
    Route::GET('/bpjs/ref/prb/obat/{param}', 'ReferensiController@refPrbObat');
    Route::GET('/bpjs/ref/prb/diagnosa', 'ReferensiController@refPrbDiagnosa');    
    Route::GET('/bpjs/ref/faskes/{param}/jenis/{param2}', 'ReferensiController@refFaskes');
    
    Route::POST('/bpjs/ref/mapping','BridgingBpjsController@updateBridging');
    Route::PUT('/bpjs/ref/mapping','BridgingBpjsController@removeBridging');
    /**
     * update tgl pulang
     */
    Route::get('/bpjs/sep/tglpulang/list/{tahun}/{bulan}/{?param}', 'SepController@sepTglPulangList');
    Route::put('/bpjs/sep/tglpulang/update', 'SepController@sepTglPulangUpdate');
    /**
     * pengajuan dan approval SEP
     */
    Route::post('/bpjs/sep/pengajuan', 'SepController@sepPengajuan');
    Route::post('/bpjs/sep/approval', 'SepController@sepApprovalPengajuan');
    
    /**
     * SEP Internal
     */
    Route::get('/bpjs/sep/internal', 'SepController@sepInternalData');
    Route::delete('/bpjs/sep/internal/{noSep}', 'SepController@sepInternalDelete');
    
    /**
     * data SEP
     */
    Route::get('/bpjs/sep/{noSep}', 'SepController@sepData');
    Route::post('/bpjs/sep', 'SepController@sepInsert');
    Route::put('/bpjs/sep', 'SepController@sepUpdate');
    Route::delete('/bpjs/sep/{noSep}', 'SepController@sepDelete');
    
    /**
     * Monitoring
     */
    Route::get('/bpjs/monitoring/kunjungan/{tglKunjungan}/jenis/{jnsPelayanan}', 'MonitoringBpjsController@dataKunjungan');
    Route::get('/bpjs/monitoring/klaim/{tglKunjungan}/jenis/{jnsPelayanan}/status/{status}', 'MonitoringBpjsController@dataKlaim');
    Route::get('/bpjs/monitoring/pelayanan/{noka}/awal/{tglMulai}/akhir/{tglAkhir}', 'MonitoringBpjsController@dataPelayanan');
    Route::get('/bpjs/monitoring/jasaraharja/{jenisPelayanan}/awal/{tglMulai}/akhir/{tglAkhir}', 'MonitoringBpjsController@dataJasaRaharja');
    
    /**
     * Data Rujukan
     */

    Route::get('/bpjs/rujukan/lists/{noPeserta}', 'RujukanController@listRujukanByNokaPcare');
    Route::get('/bpjs/rujukan/peserta/{noPeserta}', 'RujukanController@dataRujukanByNokaPcare');
    Route::get('/bpjs/rujukan/{noRujukan}', 'RujukanController@dataRujukanByNomorPcare');

    Route::get('/bpjs/rujukan/rs/lists/{noPeserta}', 'RujukanController@listRujukanByNokaRS');
    Route::get('/bpjs/rujukan/rs/peserta/{noPeserta}', 'RujukanController@dataRujukanByNokaRS');
    Route::get('/bpjs/rujukan/rs/{noRujukan}', 'RujukanController@dataRujukanByNomorRS');
    
    Route::post('/bpjs/rujukan', 'RujukanController@createRujukan');
    Route::put('/bpjs/rujukan', 'RujukanController@updateRujukan');
    Route::delete('/bpjs/rujukan/{noRujukan}', 'RujukanController@deleteRujukan');
    
    /**
     * Data Rencana Kontrol
     */
    
    
});