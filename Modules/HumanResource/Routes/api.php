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
     * master jabatan
     */
    Route::get('/jabatan/master/{jabatanId}', 'MasterJabatanController@data');
    Route::get('/jabatan/master', 'MasterJabatanController@lists');
    Route::post('/jabatan/master', 'MasterJabatanController@create');
    Route::put('/jabatan/master', 'MasterJabatanController@update');
    Route::delete('/jabatan/master/{jabatanId}', 'MasterJabatanController@delete');
    /**
     * master karyawan
     */
    Route::post('/karyawan/foto/{karyawanId}', 'MasterKaryawanController@uploadFotoKaryawan');
    Route::get('/karyawan/master/{karyawanId}', 'MasterKaryawanController@data');
    Route::get('/karyawan/master', 'MasterKaryawanController@lists');
    Route::post('/karyawan/master', 'MasterKaryawanController@create');
    Route::put('/karyawan/master', 'MasterKaryawanController@update');
    Route::delete('/karyawan/master/{karyawanId}', 'MasterKaryawanController@delete');

    /**
     * keluarga karyawan
     */
    Route::get('/karyawan/{karyawanId}/keluarga', 'KeluargaKaryawanController@lists');
    Route::get('/karyawan/keluarga/{keluargaId}', 'KeluargaKaryawanController@data');
    Route::post('/karyawan/keluarga', 'KeluargaKaryawanController@create');
    Route::put('/karyawan/keluarga', 'KeluargaKaryawanController@update');
    Route::delete('/karyawan/keluarga/{keluargaId}', 'KeluargaKaryawanController@delete');

    /**
     * pendidikan karyawan
     */
    Route::get('/karyawan/{karyawanId}/pendidikan', 'PendidikanKaryawanController@lists');
    Route::get('/karyawan/pendidikan/{pendidikanId}', 'PendidikanKaryawanController@data');
    Route::post('/karyawan/pendidikan', 'PendidikanKaryawanController@create');
    Route::put('/karyawan/pendidikan', 'PendidikanKaryawanController@update');
    Route::delete('/karyawan/pendidikan/{pendidikanId}', 'PendidikanKaryawanController@delete');

    /**
     * jabatan karyawan
     */
    Route::get('/karyawan/{karyawanId}/jabatan', 'JabatanKaryawanController@lists');
    Route::get('/karyawan/jabatan/{jabatanId}', 'JabatanKaryawanController@data');
    Route::post('/karyawan/jabatan', 'JabatanKaryawanController@create');
    Route::put('/karyawan/jabatan', 'JabatanKaryawanController@update');
    Route::delete('/karyawan/jabatan/{jabatanId}', 'JabatanKaryawanController@delete');

    /**
     * dokumen karyawan
     */
    Route::get('/karyawan/{karyawanId}/dokumen', 'DokumenKaryawanController@lists');
    Route::get('/karyawan/dokumen/{dokumenId}', 'DokumenKaryawanController@data');
    Route::post('/karyawan/dokumen', 'DokumenKaryawanController@create');
    Route::put('/karyawan/dokumen', 'DokumenKaryawanController@update');
    Route::delete('/karyawan/dokumen/{dokumenId}', 'DokumenKaryawanController@delete');
});