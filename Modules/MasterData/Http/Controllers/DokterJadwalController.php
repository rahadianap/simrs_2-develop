<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\DokterJadwal;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;

use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class DokterJadwalController extends Controller
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
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $keyword = '%%';
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            $unitID = '%%';
            if ($request->has('unit_nama')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            $list = DokterJadwal::leftJoin('tb_dokter_unit AS tdu', 'tdu.dokter_unit_id', '=', 'tb_dokter_jadwal.dokter_unit_id')
                ->leftjoin('tb_dokter AS td', 'td.dokter_id', '=', 'tdu.dokter_id')
                ->leftjoin('tb_unit AS tu', 'tu.unit_id', '=', 'tdu.unit_id')
                ->where('tb_dokter_jadwal.client_id', $clientId)
                ->where('tb_dokter_jadwal.is_aktif', 1)
                ->where('tb_dokter_jadwal.hari', 'ILIKE', $keyword)
                ->where('td.dokter_nama', 'ILIKE', $keyword)
                ->where('tu.unit_nama', 'ILIKE', $unitID)
                ->orderBy('tb_dokter_jadwal.jadwal_id', 'ASC')
                ->get();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data jadwal dokter', 'error' => $e->getMessage()]);
        }
    }

    public function listByPoli(Request $request){
        try {
            $per_page = 10;
            $keyword = '%%';
            
            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            $list = UnitPelayanan::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where(function($q) use($keyword) {
                    $q->where('unit_id','ILIKE',$keyword)
                    ->orWhere('unit_nama','ILIKE',$keyword);
                })->orderBy('unit_nama','ASC')->paginate($per_page);
            
            
            $datas = $list->items();
            if($datas) {
                foreach($datas as $dt) {
                    $jadwal = DokterJadwal::leftJoin('tb_dokter','tb_dokter.dokter_id','=','tb_dokter_jadwal.dokter_id')
                        ->where('tb_dokter_jadwal.client_id',$this->clientId)->where('tb_dokter_jadwal.is_aktif',1)
                        ->where('tb_dokter_jadwal.unit_id',$dt['unit_id'])
                        ->select('tb_dokter_jadwal.*','tb_dokter.dokter_nama')
                        ->orderBy('tb_dokter.dokter_nama','ASC')
                        ->orderBy('tb_dokter_jadwal.hari','ASC')
                        ->get();

                    $dt['jadwal'] = $jadwal;                        
                }
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data jadwal dokter', 'error' => $e->getMessage()]);
        }

    }

    public function listByDokter(Request $request){
        try{
            $per_page = 10;
            $keyword = '%%';
            $active = 'true';
            $unit = '%%';

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL") {
                    $per_page = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->count();
                }
            }
            
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            if($request->has('unit')) {
                $unit = '%'.$request->input('unit').'%';
            }

            $day = '%%';
            if($request->has('tanggal')) {
                $tanggal = $request->input('tanggal');
                $dateService = date_create($tanggal);
                $day = date_format($dateService,'N');
            }
            
            $lists = Dokter::where('client_id',$this->clientId)
                    ->select('dokter_id','dokter_nama','smf_id','no_sip','tgl_akhir_sip','is_aktif')
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('dokter_nama','ILIKE',$keyword)
                        ->orWhere('dokter_id','ILIKE',$keyword);
                    })->with('smf')
                    ->orderBy('dokter_nama', 'ASC')
                    ->paginate($per_page);
                    
            $datas = $lists->items();
            if($datas) {
                foreach($datas as $dt) {
                    $jadwal = DokterJadwal::leftJoin('tb_unit','tb_unit.unit_id','=','tb_dokter_jadwal.unit_id')
                        ->where('tb_dokter_jadwal.client_id',$this->clientId)
                        ->where('tb_dokter_jadwal.is_aktif',1)
                        ->where('tb_dokter_jadwal.dokter_id',$dt['dokter_id'])
                        ->where('tb_dokter_jadwal.unit_id','ILIKE',$unit)   
                        ->where('tb_dokter_jadwal.hari','ILIKE',$day)
                        ->select('tb_dokter_jadwal.*','tb_unit.unit_nama',DB::raw("CONCAT(tb_dokter_jadwal.mulai,' - ',tb_dokter_jadwal.selesai) as full_jadwal"))
                        ->orderBy('tb_unit.unit_nama','ASC')
                        ->orderBy('tb_dokter_jadwal.hari','ASC')
                        ->get();

                    $dt['jadwal'] = $jadwal;
                }
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists, 'hari'=> $day]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data dokter tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = DokterJadwal::where('client_id', $this->clientId)->where('jadwal_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $data['dokter_nama'] = '';
            $data['unit_nama'] = '';
            $dokter = Dokter::where('client_id',$this->clientId)->where('dokter_id',$data->dokter_id)->select('dokter_id','dokter_nama')->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('unit_id',$data->unit_id)->select('unit_id','unit_nama')->first();
            
            if($dokter) { $data['dokter_nama'] = $dokter->dokter_nama; }
            if($unit) { $data['unit_nama'] = $unit->unit_nama; }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data jadwal dokter', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $prefix = 'JD';
            $id = $this->getDokterJadwalId();

            $dkJadwal = DokterJadwal::where('client_id',$this->clientId)
                        ->where('dokter_id',$request->dokter_id)
                        ->where('dokter_unit_id',$request->dokter_unit_id)
                        ->where('unit_id',$request->unit_id)
                        ->where('hari',$request->hari)
                        ->where('nama_hari',$request->nama_hari)
                        ->where('is_aktif','true')
                        ->first();

            if($dkJadwal) {
                $jamMulai       = Carbon::createFromFormat("H:i", $dkJadwal->mulai);
                $jamSelesai     = Carbon::createFromFormat("H:i", $dkJadwal->selesai);
                $reqJamMulai    = Carbon::createFromFormat("H:i", $request->mulai);
                $reqJamSelesai  = Carbon::createFromFormat("H:i", $request->selesai);
    
                if($jamMulai <= $reqJamMulai && $jamSelesai >= $reqJamMulai){
                    return response()->json(['success' => false, 'message' => 'Dokter dengan jam (mulai) tersebut sudah ada']);
                }
                if($jamMulai <= $reqJamSelesai && $jamSelesai >= $reqJamSelesai){
                    return response()->json(['success' => false, 'message' => 'Dokter dengan jam (selesai) tersebut sudah ada']);
                }
                if($reqJamSelesai < $reqJamMulai){
                    return response()->json(['success' => false, 'message' => 'Jam selesai tidak boleh kurang dari jam mulai']);
                }
            }
            
            $data = new DokterJadwal();

            $data->jadwal_id        = $id;
            $data->dokter_unit_id   = $request->dokter_unit_id;
            $data->dokter_id        = $request->dokter_id;
            $data->unit_id          = $request->unit_id;            
            $data->hari             = $request->hari;
            $data->nama_hari        = $request->nama_hari;
            $data->mulai            = $request->mulai;
            $data->selesai          = $request->selesai;
            $data->is_ext_app       = $request->is_ext_app;
            $data->kuota_online     = $request->kuota_online;
            $data->kuota            = $request->kuota;
            $data->interval_periksa = $request->interval_periksa;
            $data->is_aktif         = 1;
            $data->client_id        = $this->clientId;
            $data->created_by       = Auth::user()->username;
            $success                = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data jadwal dokter']);
            }

            return response()->json(['success' => true, 'message' => 'data jadwal dokter berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menambah jadwal dokter. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $data = DokterJadwal::where('client_id', $clientId)->where('jadwal_id', $request->jadwal_id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data jadwal dokter tidak ditemukan.']);
            }

            $data->hari = $request->hari;
            $data->nama_hari = $request->nama_hari;
            $data->mulai = $request->mulai;
            $data->selesai = $request->selesai;
            $data->is_ext_app = $request->is_ext_app;
            $data->kuota_online = $request->kuota_online;
            $data->kuota = $request->kuota;
            $data->interval_periksa = $request->interval_periksa;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data jadwal dokter']);
            }
            return response()->json(['success' => true, 'message' => 'data jadwal dokter berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan jadwal dokter. Error : ' . $e->getMessage()]);
        }
    }

    // cek tabel registrasi, ada pasien daftar/booking atau tidak
    public function delete(Request $request, $id)
    {
        try {
            $data = DokterJadwal::where('jadwal_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus jadwal. Data jadwal dokter tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data jadwal dokter']);
            }
            return response()->json(['success' => true, 'message' => 'jadwal dokter berhasil dinonaktifkan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data jadwal dokter gagal dinonaktifkan. Error :' . $e->getMessage()]);
        }
    }

    public function getDokterJadwalId()
    {
        try {
            $id = $this->clientId . '-JD0001';
            $maxId = DokterJadwal::withTrashed()->where('client_id', $this->clientId)->where('jadwal_id', 'LIKE', $this->clientId . '-JD%')->max('jadwal_id');
            if (!$maxId) { $id = $this->clientId . '-JD0001'; } 
            else {
                $maxId = str_replace($this->clientId.'-JD','', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'-JD000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '-JD00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '-JD0' . $count;
                } else {
                    $id = $this->clientId . '-JD'. $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return 'JD' . date('Y.m.d') . '-' . Uuid::uuid4()->getHex();
        }
    }

    
}
