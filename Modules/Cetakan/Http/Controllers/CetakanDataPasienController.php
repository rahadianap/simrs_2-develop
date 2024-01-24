<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\MasterData\Entities\Pasien;

class CetakanDataPasienController extends Controller
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

    public function dataHistoryPasien(Request $request, $patient_id)
    {
        try {
            $data = Pasien::leftJoin('tb_pasien_alamat', 'tb_pasien.pasien_id', '=', 'tb_pasien_alamat.pasien_id')
            ->leftJoin('tb_pasien_detail', 'tb_pasien.pasien_id', '=', 'tb_pasien_detail.pasien_id')
            ->leftJoin('tb_transaksi', 'tb_pasien.pasien_id', '=', 'tb_transaksi.pasien_id')
            ->where('tb_pasien.client_id', $this->clientId)->where('tb_pasien.pasien_id', $patient_id)->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
