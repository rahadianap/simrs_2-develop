<?php

namespace Modules\AntrianBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AntrianBpjs\Entities\Client;
use Ramsey\Uuid\Uuid;

class AuthJknController extends Controller
{
    public function login(Request $request) {
        try {
            if(!$request->hasHeader('X-username')) {
                $meta['code'] = 201;
                $meta['message'] = 'username / passkey tidak diketahui(1)';
                return response()->json(['metadata' => $meta],201);
            }

            if(!$request->hasHeader('X-password')) {
                $meta['code'] = 201;
                $meta['message'] = 'username / passkey tidak diketahui(2)';
                return response()->json(['metadata' => $meta],201);
            }

            $username = $request->header('X-username');
            $password = $request->header('X-password');
            $data = Client::where('jkn_username',$username)->where('jkn_password',$password)->where('is_aktif',1)->first();
            if(!$data) {
                $meta['code'] = 201;
                $meta['message'] = 'username / passkey tidak diketahui(3)';
                return response()->json(['metadata' => $meta],201);
            }

            $token = date('ymd').Uuid::uuid4()->getHex();
            $success = Client::where('jkn_username',$username)->where('jkn_password',$password)->where('is_aktif',1)
                ->update([
                    'jkn_token' => $token, 
                    'updated_by'=>'USER_JKN',
                    'updated_at'=>date('Y-m-d H:i:s') 
                ]);
            
            if(!$success) {
                $meta['code'] = 201;
                $meta['message'] = 'gagal membuat token akses';
                return response()->json(['metadata' => $meta],201);
            }
            
            $meta['code'] = 200;
            $meta['message'] = 'OK';
            return response()->json(['response' => ['token'=>$token], 'metadata'=>$meta],201);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam login';
            return response()->json(['metadata' => $meta]);
        }
    }
}
