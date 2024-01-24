<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Persediaan\Entities\Distribusi;

class CetakanDistribusiController extends Controller
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

    public function dataDist(Request $request, $distId)
    {
        try {
            $data = Distribusi::select('tb_distribusi.*', 'tb_produk.*', 'tb_distribusi_detail.*')
            ->leftJoin('tb_distribusi_detail', 'tb_distribusi.distribusi_id', '=', 'tb_distribusi_detail.distribusi_id')
            ->leftJoin('tb_produk', 'tb_distribusi_detail.produk_id', '=', 'tb_produk.produk_id')
            ->where('tb_distribusi.client_id',$this->clientId)->where('tb_distribusi.is_aktif',1)->where('tb_distribusi.distribusi_id', $distId)->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
