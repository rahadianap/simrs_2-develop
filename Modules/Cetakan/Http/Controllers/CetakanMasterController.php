<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

use Modules\ManajemenUser\Entities\Client;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\Dokter;

class CetakanMasterController extends Controller
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

    public function dataDokter(Request $request, $dokterId)
    {
        try {
            $data = Dokter::where('client_id', $this->clientId)->where('dokter_id', $dokterId)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            // return response()->json(['success' => true, 'data' => $data]);
            return view('cetakan/cetakanDataDokter',  compact('data'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data dokter : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataHistoryPasien(Request $request, $patient_id)
    {
        try {
            $data['central'] = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['pasien']  = Pasien::leftJoin('tb_pasien_alamat','tb_pasien.pasien_id','=','tb_pasien_alamat.pasien_id')
                                ->leftJoin('tb_pasien_detail','tb_pasien.pasien_id','=','tb_pasien_detail.pasien_id')
                                ->where('tb_pasien.pasien_id',$patient_id)
                                ->where('tb_pasien.client_id',$this->clientId)
                                ->where('tb_pasien.is_aktif',1)
                                ->select('tb_pasien.no_rm','tb_pasien.nama_pasien','tb_pasien.nik','tb_pasien.jns_kelamin','tb_pasien.tempat_lahir', 'tb_pasien.tgl_lahir',
                                    'tb_pasien.penjamin_nama','tb_pasien.jns_penjamin','tb_pasien_detail.no_telepon','tb_pasien_alamat.alamat',
                                    'tb_pasien_alamat.rt','tb_pasien_alamat.rw','tb_pasien_alamat.propinsi','tb_pasien_alamat.kota','tb_pasien_alamat.kecamatan',
                                    'tb_pasien_alamat.kelurahan','tb_pasien_alamat.kodepos',)
                                ->first();
            $data['pasien']['usia'] = Carbon::parse($data['pasien']->tgl_lahir)->age;
            $data['pasien']['alamat_lengkap'] = $data['pasien']->alamat . ' ' . $data['pasien']->rt . '/' . $data['pasien']->rw . ' ' . $data['pasien']->kota . ' ' . $data['pasien']->kecamatan . ' ' . $data['pasien']->kelurahan . ' ' . $data['pasien']->propinsi . ' ' . $data['pasien']->kodepos;
            $data['riwayat'] = Pasien::leftJoin('tb_pasien_alamat', 'tb_pasien.pasien_id', '=', 'tb_pasien_alamat.pasien_id')
                                ->leftJoin('tb_pasien_detail', 'tb_pasien.pasien_id', '=', 'tb_pasien_detail.pasien_id')
                                ->leftJoin('tb_transaksi', 'tb_pasien.pasien_id', '=', 'tb_transaksi.pasien_id')
                                ->where('tb_pasien.client_id', $this->clientId)->where('tb_pasien.pasien_id', $patient_id)
                                ->select('tb_transaksi.*')
                                ->get();
            $data['tgl_cetak'] = Carbon::now()->format('Y-m-d');
            if (!$data['pasien']) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
            return view('cetakan/cetakanRiwayatPasien',  compact('data'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
