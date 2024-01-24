<?php

namespace Modules\EMR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Modules\EMR\Entities\MedrecInform;
use Modules\EMR\Entities\MedrecInformDetail;
use Modules\EMR\Entities\MedrecLetter;

use Modules\MasterData\Entities\InformedConsent;
use Modules\MasterData\Entities\InformedDetail;
use Modules\MasterData\Entities\InformedMapping;

use Modules\SatuSehat\Entities\BridgingLog;

use Ramsey\Uuid\Uuid;
use Carbon;
use DB;


class MedrecLetterController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request) 
    {
        try {
            $per_page = 20;
            $aktif = 'true';
            $keyword = '%%';
            
            $query = MedrecLetter::query();
            $query = $query->where('client_id',$this->clientId);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_start') && $request->has('tgl_end')) {
                $dtKeluar = $request->input('tgl_start').' 00:00:00';
                $dtEnd = $request->input('tgl_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_keluar', [$dtStart, $dtEnd]);
            }
            
            if ($request->has('dokter')) {
                $dokter = $request->input('dokter');
                $query = $query->where(function($q) use ($dokter) {
                        $q->where('dokter_id','ILIKE',$dokter)
                        ->orWhere('dokter_nama','ILIKE',$dokter);
                    });
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword)
                    ->orWhere('pasien_id','ILIKE',$keyword);
                })->orderBy('tgl_keluar','DESC')
                ->paginate($per_page);
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $letterId)
    {
        try {
            $data = MedrecLetter::where('letter_id',$letterId)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. Data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $regId = $request->reg_id;
            $trxId = $request->trx_id;   

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter) {
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }

            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
            
    }

    public function remove(Request $request, $letterId)
    {
        try {
            $data = MedrecLetter::where('letter_id',$letterId)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. Data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data', 'error' => $e->getMessage()]);
        }
            
    }
}
