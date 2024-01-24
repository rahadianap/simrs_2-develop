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

// Route::middleware('auth:api')->get('/manajemenuser', function (Request $request) {
//     return $request->user();
// });


Route::group(['middleware'=>['auth:api','api.verify']],function(){
    /**
     * workplace (client)
     */
    Route::GET('/workplaces/allmenus', 'ClientController@clientAllMenus');
    Route::GET('/workplaces/menus', 'ClientController@menus');
    Route::GET('/workplaces/members', 'ClientController@members');
    Route::GET('/workplaces/{client_id}', 'ClientController@data');
    Route::GET('/workplaces', 'ClientController@list');
    Route::POST('/workplaces/logo', 'ClientController@uploadLogo');
    Route::POST('/workplaces', 'ClientController@create');
    Route::PUT('/workplaces', 'ClientController@update');
    Route::DELETE('/workplaces/{client_id}', 'ClientController@delete');
    /**
     * workplace (client) join invitation
     */
    Route::GET('/invitations/lists', 'InvitationController@myInvitation');
    Route::GET('/invitations/users', 'InvitationController@searchUser');

    Route::GET('/invitations/{invitation_id}', 'InvitationController@data');
    Route::GET('/invitations', 'InvitationController@list');
    Route::PUT('/invitations/accept/{invitation_id}', 'InvitationController@accept');
    Route::PUT('/invitations/reject/{invitation_id}', 'InvitationController@reject');
    Route::POST('/invitations', 'InvitationController@create');
    Route::DELETE('/invitations/{invitation_id}', 'InvitationController@delete');

    /**
     * Role user
     */
    
    Route::PUT('/roles','RoleController@updateRole');
    Route::GET('/roles/{id}','RoleController@dataRole');
    Route::GET('/roles','RoleController@lists');
    Route::POST('/roles','RoleController@createRole');
    Route::DELETE('/roles/{id}','RoleController@deleteRole');

    /**
     * user data
     */
    Route::GET('/user/lists','ManajemenUserController@allUserLists');    
    
    Route::GET('/users/datas','ManajemenUserController@userData');
    Route::GET('/users/units','ManajemenUserController@userUnits');
    Route::GET('/users/depos/{depoId}','ManajemenUserController@isUserDepoMember');
    Route::GET('/users/depos','ManajemenUserController@userDepos');
    Route::GET('/users/permissions','ManajemenUserController@userPermissions');
    Route::GET('/users','ManajemenUserController@lists');
    
    Route::POST('/users/units','ManajemenUserController@addUserUnits');
    Route::POST('/users/depos','ManajemenUserController@addUserDepos');
    Route::POST('/users','ManajemenUserController@create');
    Route::PUT('/users','ManajemenUserController@update');
    

    // Route::POST('/roles','ManajemenUserController@createRole');
    // Route::DELETE('/roles/{id}','ManajemenUserController@deleteRole');
});