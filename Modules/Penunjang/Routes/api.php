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
    /**
     * LINEN (LAUNDRY)
     */
    //penerimaan linen dan proses di laundry 
    Route::get('/linens/product', 'LinenProsesController@linenLists');
    Route::get('/linens/receive/{receive_id}', 'LinenProsesController@data');
    Route::get('/linens/receive', 'LinenProsesController@lists');    
    Route::post('/linens/receive', 'LinenProsesController@create');
    Route::put('/linens/receive', 'LinenProsesController@update');
    Route::delete('/linens/receive/{receive_id}', 'LinenProsesController@delete');

    //distribusi linen dan proses di laundry 
    Route::get('/linens/distributions/product', 'LinenDistribusiController@linenLists');
    Route::get('/linens/distributions/{dist_id}', 'LinenDistribusiController@data');
    Route::get('/linens/distributions', 'LinenDistribusiController@lists');    
    Route::post('/linens/distributions', 'LinenDistribusiController@create');
    Route::put('/linens/distributions', 'LinenDistribusiController@update');
    Route::delete('/linens/distributions/{dist_id}', 'LinenDistribusiController@delete');

    //pemusnahan linen di laundry 
    Route::get('/linens/exterminations/{exter_id}', 'LinenPemusnahanController@data');
    Route::get('/linens/exterminations', 'LinenPemusnahanController@lists');    
    Route::post('/linens/exterminations', 'LinenPemusnahanController@create');
    Route::put('/linens/exterminations/approve', 'LinenPemusnahanController@approve');
    Route::put('/linens/exterminations', 'LinenPemusnahanController@update');


    /**
     * CSSD
     */
    Route::get('/sterilizations/product', 'CssdTerimaController@itemLists');
    Route::get('/sterilizations/receive/{receive_id}', 'CssdTerimaController@data');
    Route::get('/sterilizations/receive', 'CssdTerimaController@lists');    
    Route::post('/sterilizations/receive', 'CssdTerimaController@create');
    Route::put('/sterilizations/receive', 'CssdTerimaController@update');
    Route::delete('/sterilizations/receive/{receive_id}', 'CssdTerimaController@delete');

    Route::get('/sterilizations/distributions/product', 'CssdDistribusiController@itemLists');
    Route::get('/sterilizations/distributions/{dist_id}', 'CssdDistribusiController@data');
    Route::get('/sterilizations/distributions', 'CssdDistribusiController@lists');    
    Route::post('/sterilizations/distributions', 'CssdDistribusiController@create');
    Route::put('/sterilizations/distributions', 'CssdDistribusiController@update');
    Route::delete('/sterilizations/distributions/{dist_id}', 'CssdDistribusiController@delete');


    /**
     * BANK DARAH
     */
    Route::get('/bloods/stocks', 'BankDarahMusnahController@stockLists');
    
     //penerimaan
    Route::get('/bloods/references', 'BankDarahTerimaController@referensiLists');
    Route::get('/bloods/receive/{receive_id}', 'BankDarahTerimaController@data');
    Route::get('/bloods/receive', 'BankDarahTerimaController@lists');    
    Route::post('/bloods/receive', 'BankDarahTerimaController@create');
    Route::put('/bloods/receive', 'BankDarahTerimaController@update');
    Route::delete('/bloods/receive/{receive_id}', 'BankDarahTerimaController@delete');

    //permintaan
    Route::get('/bloods/requests/{dist_id}', 'BankDarahRequestController@data');
    Route::get('/bloods/requests', 'BankDarahRequestController@lists');    
    Route::post('/bloods/requests', 'BankDarahRequestController@create');
    Route::put('/bloods/requests', 'BankDarahRequestController@update');
    Route::delete('/bloods/requests/{dist_id}', 'BankDarahRequestController@delete');
    
    Route::put('/bloods/distributions', 'BankDarahRequestController@updateRealisasi');
    Route::get('/bloods/distributions/product', 'BankDarahDistribusiController@itemLists');
    
    //distribusi
    // Route::get('/bloods/distributions/{dist_id}', 'BankDarahDistribusiController@data');
    // Route::get('/bloods/distributions', 'BankDarahDistribusiController@lists');
    // Route::post('/bloods/distributions', 'BankDarahDistribusiController@create');
    // Route::delete('/bloods/distributions/{dist_id}', 'BankDarahDistribusiController@delete');

    //pemusnahan
    Route::get('/bloods/exterminations/{ext_id}', 'BankDarahMusnahController@data');
    Route::get('/bloods/exterminations', 'BankDarahMusnahController@lists');    
    Route::post('/bloods/exterminations', 'BankDarahMusnahController@create');
    Route::delete('/bloods/exterminations/{ext_id}', 'BankDarahMusnahController@delete');

    //referensi bank darah
    Route::get('/bloods/groups/{group_id}', 'JenisProdukDarahController@data');
    Route::get('/bloods/groups', 'JenisProdukDarahController@lists');    
    Route::post('/bloods/groups', 'JenisProdukDarahController@create');
    Route::put('/bloods/groups', 'JenisProdukDarahController@update');
    Route::delete('/bloods/groups/{group_id}', 'JenisProdukDarahController@delete');

    Route::get('/bloods/sources/{src_id}', 'AsalDarahController@data');
    Route::get('/bloods/sources', 'AsalDarahController@lists');    
    Route::post('/bloods/sources', 'AsalDarahController@create');
    Route::put('/bloods/sources', 'AsalDarahController@update');
    Route::delete('/bloods/sources/{src_id}', 'AsalDarahController@delete');

    //Booking Jadwal Operasi 
    Route::get('/surgery/lists', 'OperasiController@lists');    
    Route::get('/surgery/{trx_id}', 'OperasiController@data');
    Route::post('/surgery', 'OperasiController@create');
    Route::put('/surgery/confirm', 'OperasiController@confirm');
    Route::put('/surgery/{trx_id}/status', 'OperasiController@updateStatus');
    Route::put('/surgery', 'OperasiController@update');
    Route::delete('/surgery/{trx_id}', 'OperasiController@delete');

    Route::get('/patient/diet', 'DietPasienController@lists');
    Route::get('/patient/diet/monitoring', 'DietPasienController@listMonitoring');
    Route::post('/patient/diet', 'DietPasienController@createDiet');
    Route::put('/patient/diet', 'DietPasienController@updateDiet');
    Route::post('/patient/diet/monitoring', 'DietPasienController@createDietMonitoring');
    Route::GET('/patient/diet/{pasien_diet_id}', 'DietPasienController@dataDietPasien');

    Route::get('/mealorder', 'MealOrderController@lists');
    Route::put('/mealorder/{diet_id}/approve', 'MealOrderController@approveDiet');
    Route::put('/mealorder/{diet_id}/order', 'MealOrderController@orderDiet');

});