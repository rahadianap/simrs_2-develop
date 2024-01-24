<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Laboratorium\Entities\TrxLab;
use Modules\ManajemenUser\Entities\Client;

class CetakanLaboratoriumController extends Controller
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

    public function dataLab(Request $request, $trx_id)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $trxLab = TrxLab::leftJoin('tb_trx_detail', 'tb_trx_lab.trx_id', '=', 'tb_trx_detail.trx_id')
            ->where('tb_trx_lab.client_id',$this->clientId)
            ->where('tb_trx_lab.is_aktif',1)
            ->where('tb_trx_lab.trx_id', $trx_id)
            ->get();

            if (!$trxLab) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            $data['central']    = $central;
            $data['lab']        = $trxLab;

            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
            return view('report/lab/cetakanRegisLaboratorium',  compact('data'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataHasilLab(Request $request, $trx_id)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $trxLab = TrxLab::leftJoin('tb_trx_detail', 'tb_trx_lab.trx_id', '=', 'tb_trx_detail.trx_id')
                        ->leftJoin('tb_trx_lab_hasil', 'tb_trx_detail.detail_id', '=', 'tb_trx_lab_hasil.detail_id')
                        ->where('tb_trx_lab.client_id',$this->clientId)
                        ->where('tb_trx_lab.is_aktif',1)
                        //->where('tb_trx_lab.is_hasil',1)
                        ->where('tb_trx_lab.trx_id', $trx_id)
                        ->select('tb_trx_detail.*','tb_trx_lab.*')
                        ->get();
            
            if (!$trxLab) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            $data['central']    = $central;
            $data['lab']        = $trxLab;

            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
            return view('report/lab/cetakanHasilLaboratorium',  compact('data'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
