<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;
use Modules\ManajemenUser\Entities\Client;

class CetakanResepController extends Controller
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

    public function dataResep(Request $request, $trx_id)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            $data = TrxResepDetail::where('client_id',$this->clientId)
            ->where('is_aktif',1)
            ->where('trx_id', $trx_id)
            ->get();

            $merged = $central->merge($data);
            $result = $merged->all();

            if (!$result) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataTracer(Request $request, $reg_id)
    {
        try {
            
            $data = Pendaftaran::select('tb_reg_pasien.nama_pasien', 'tb_reg_pasien.no_rm', 'tb_reg_pasien.reg_id', 'tb_unit.unit_nama', 'tb_dokter.dokter_nama', 'tb_registrasi.no_antrian', 'tb_penjamin.penjamin_nama', 'tb_registrasi.tgl_registrasi')
            ->leftJoin('tb_reg_pasien', 'tb_registrasi.reg_id', '=', 'tb_reg_pasien.reg_id')
            ->leftJoin('tb_unit', 'tb_registrasi.unit_id', '=', 'tb_unit.unit_id')
            ->leftJoin('tb_dokter', 'tb_registrasi.dokter_id', '=', 'tb_dokter.dokter_id')
            ->leftJoin('tb_penjamin', 'tb_registrasi.penjamin_id', '=', 'tb_penjamin.penjamin_id')
            ->where('tb_registrasi.client_id',$this->clientId)
            ->where('tb_registrasi.is_aktif',1)
            ->where('tb_registrasi.reg_id', $reg_id)
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