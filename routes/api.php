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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware'=>'api.verify'],function(){
    /*** user registration */
    Route::POST('/signup', 'ApiAuth\AuthUserController@signup');
    Route::POST('/signup/verification', 'ApiAuth\AuthUserController@signupVerification');
    /*** user reset password */
    Route::POST('/reset/password', 'ApiAuth\AuthUserController@resetPassword');
    Route::GET('/reset/password', 'ApiAuth\AuthUserController@dataResetPassword');
    Route::POST('/reset/verification', 'ApiAuth\AuthUserController@resetVerification');
    /*** user login */
    Route::POST('/login', 'ApiAuth\AuthUserController@login');
    
    /**
     * proses setelah login 
     * dibatasi hanya untuk auth user
     */
    Route::group(['middleware'=>'auth:api'],function() {
        /*** logout */
        Route::POST('/logout', 'ApiAuth\AuthUserController@logout');
        /** update profile */
        Route::GET('/profile', 'ApiAuth\AuthUserController@userProfile');
        Route::POST('/profile', 'ApiAuth\AuthUserController@updateProfile');
        /** update password */
        Route::POST('/change/password', 'ApiAuth\AuthUserController@changePassword');

        /**
         * user profile data
         */
        Route::GET('/profile', 'ApiAuth\UserProfileController@data');
        Route::POST('/profile', 'ApiAuth\UserProfileController@update');
        Route::POST('/avatar', 'ApiAuth\UserProfileController@avatar');
    });
});
