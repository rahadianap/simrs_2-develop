<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Persediaan\Entities\KartuStock;

class CetakanKartuStockController extends Controller
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

    public function dataKartuStock(Request $request, $produk_id)
    {
        try{
            $data = KartuStock::where('client_id',$this->clientId)->where('produk_id', $produk_id)->orderBy('tgl_transaksi', 'asc')->get();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menampilkan data. Data tidak ditemukan.']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }
}
