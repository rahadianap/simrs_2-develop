<?php

namespace Modules\AntrianBpjs\Http\Middleware;
use Modules\AntrianBpjs\Entities\Client;
use Illuminate\Http\Request;
use Closure;

class CheckApiJknAccess
{
    public function handle( $request, Closure $next ) {

        if(!$request->hasHeader('X-username')) {
            $meta['code'] = 201;
            $meta['message'] = 'unauthorized';
            return response()->json(['metadata' => $meta],201);
        }

        if(!$request->hasHeader('X-token')) {
            $meta['code'] = 201;
            $meta['message'] = 'unauthorized';
            return response()->json(['metadata' => $meta],201);
        }

        if ( $this->checkToken( $request ) ) {
            return $next( $request );
        }        
        else {
            $res['code'] = 201; 
            $res['message'] = "Token / username tidak diketahui(l).";
            return response()->json( [ 'metadata' => $res ], 201 );
        }        
    }

    public function checkToken( $request ) {

        $username = $request->header( 'X-username' );
        $token  = $request->header( 'X-token' );

        $checkToken = Client::where( 'jkn_username', $username )
                              ->where( 'jkn_token', $token )->first();

        return $checkToken;
    }
}