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
    // Master Lokasi Asset
    Route::get('/assets/locations/{lokasi_aset_id}', 'LokasiAsetController@data');
    Route::get('/assets/locations', 'LokasiAsetController@lists');
    Route::post('/assets/locations', 'LokasiAsetController@create');
    Route::put('/assets/locations', 'LokasiAsetController@update');
    Route::delete('/assets/locations/{lokasi_aset_id}', 'LokasiAsetController@delete');
    
    Route::get('/assets/references/status', 'ReferensiAsetController@refStatusLists');
    Route::get('/assets/references/group', 'ReferensiAsetController@refGroupLists');
    Route::put('/assets/references/{refAsetId}', 'ReferensiAsetController@refAsetUpdate');
    
    Route::get('/assets/overview', 'AssetController@overviewData');
    // Master Data Asset
    Route::get('/assets', 'AssetController@lists');
    Route::get('/assets/{asset_id}', 'AssetController@data');
    Route::post('/assets', 'AssetController@create');
    Route::put('/assets', 'AssetController@update');
    Route::delete('/assets/{asset_id}', 'AssetController@delete');

    //Maintenance Ticket
    Route::get('/mtickets', 'MaintenanceTicketController@lists');
    Route::get('/mtickets/{ticket_id}', 'MaintenanceTicketController@data');
    Route::post('/mtickets', 'MaintenanceTicketController@create');
    Route::put('/mtickets', 'MaintenanceTicketController@update');
    Route::delete('/mtickets/{ticket_id}', 'MaintenanceTicketController@delete');

    // Maintenance Schedule
    Route::get('/mschedule', 'MaintenanceScheduleController@listSchedule');
    Route::get('/mschedule/{schedule_id}', 'MaintenanceScheduleController@data');
    Route::post('/mschedule', 'MaintenanceScheduleController@createSchedule');
    Route::put('/mschedule', 'MaintenanceScheduleController@updateSchedule');
    Route::delete('/mschedule/{schedule_id}', 'MaintenanceTicketController@delete');


    //Sparepart
    Route::get('/spareparts', 'SparepartController@lists');
    Route::get('/spareparts/{sparepart_id}', 'SparepartController@data');
    Route::post('/spareparts', 'SparepartController@create');
    Route::put('/spareparts', 'SparepartController@update');
    Route::delete('/spareparts/{sparepart_id}', 'SparepartController@delete');

    // Teknisi
    Route::get('/technicians', 'AssetController@listTeknisi');

});