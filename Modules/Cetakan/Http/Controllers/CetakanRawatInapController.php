<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Modules\ManajemenUser\Entities\Client;
use Modules\Transaksi\Entities\RawatInap;
use App\Models\UserProfile;

class CetakanRawatInapController extends Controller
{
    public $clientId;
    
    public function __construct(Request $request)
    {
        /*** check apakah header menyertakan client ID atau tidak */
        if (!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'Tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
        
    }

    public function cetakanFormPenempatanKelasRi($trx_id)
    {
        try {
            $user = UserProfile::where('user_id',Auth::user()->user_id)->first();
            if($user == null){
                $user = "Petugas";
            }
            $data['user']    = $user;
            $data['central'] = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['inap']    = RawatInap::where('client_id', $this->clientId)->where('is_aktif',1)->where('trx_id', $trx_id)->first();
            
            return view('cetakan/cetakanFormPenempatanKelasRI',  compact('data'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data dokter : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
