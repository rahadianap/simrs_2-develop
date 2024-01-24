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
    /** Medical record */
    Route::get('/medical/records/data/{regId}', 'MedicalRecordController@data');
    Route::get('/medical/records/{pasienId}', 'MedicalRecordController@lists');
    // Route::post('/farmasi', 'FarmasiController@create');
    // Route::put('/farmasi', 'FarmasiController@update');
    // Route::delete('/farmasi/{id}', 'FarmasiController@delete');
    
    Route::get('/medical/records/inform/lists', 'MedrecInformRecordController@lists');
    Route::get('/medical/records/inform/{informId}', 'MedrecInformController@data');
    Route::post('/medical/records/inform', 'MedrecInformController@create');
    Route::put('/medical/records/inform', 'MedrecInformController@update');
    Route::delete('/medical/records/inform/{informId}', 'MedrecInformController@remove');
    
    
    Route::get('/medical/letters/lists', 'MedrecLetterController@lists');
    Route::get('/medical/letters/{letterId}', 'MedrecLetterController@data');
    Route::post('/medical/letters', 'MedrecLetterController@create');
    Route::put('/medical/letters', 'MedrecLetterController@update');
    Route::delete('/medical/letters/{letterId}', 'MedrecLetterController@remove');
    
});

