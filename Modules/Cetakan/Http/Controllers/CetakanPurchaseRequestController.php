<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Pengadaan\Entities\PurchaseRequest;

class CetakanPurchaseRequestController extends Controller
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

    public function dataPR(Request $request, $prId)
    {
        try {
            $data = PurchaseRequest::select('tb_pr.*', 'tb_produk.*', 'tb_pr_detail.*')
            ->leftJoin('tb_pr_detail', 'tb_pr.pr_id', '=', 'tb_pr_detail.pr_id')
            ->leftJoin('tb_produk', 'tb_pr_detail.produk_id', '=', 'tb_produk.produk_id')
            ->where('tb_pr.client_id',$this->clientId)->where('tb_pr.is_aktif',1)->where('tb_pr.pr_id', $prId)->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
