<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Pengadaan\Entities\PurchaseOrder;

class CetakanPurchaseOrderController extends Controller
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

    public function dataCetakanPO(Request $request, $trx_id)
    {
        try {
            $data = PurchaseOrder::select('tb_pengadaan.*', 'tb_pengadaan_detail.*')
            ->leftJoin('tb_pengadaan_detail', 'tb_pengadaan.trx_id', '=', 'tb_pengadaan_detail.trx_id')
            ->where('tb_pengadaan.client_id',$this->clientId)
            ->where('tb_pengadaan.is_aktif',1)
            ->where('tb_pengadaan.pengadaan_id', $trx_id)
            ->where('tb_pengadaan.trx_id', $trx_id)
            ->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
