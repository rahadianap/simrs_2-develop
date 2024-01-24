<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;

use Modules\Penunjang\Entities\DietPasien;
use Modules\Penunjang\Entities\DietPasienDetail;
use Modules\Penunjang\Entities\DietPasienMonitoring;
use Modules\Penunjang\Entities\DietPasienLog;
use Modules\Transaksi\Entities\RawatJalan;
use Modules\Pendaftaran\Entities\Pendaftaran;

use DB;
use Carbon\Carbon;

class DietPasienController extends Controller
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
            
            $query = RawatJalan::query();
            $query = $query->where('tb_rawat_jalan.client_id',$this->clientId);
            
            if ($request->has('tgl_periksa_start') && $request->has('tgl_periksa_end')) {
                $dtStart = $request->input('tgl_periksa_start').' 00:00:00';
                $dtEnd = $request->input('tgl_periksa_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_periksa', [$dtStart, $dtEnd]);
            }else{
                $dtStart = Carbon::now();
                $dtEnd = Carbon::now();                
                $query = $query->whereBetween('tgl_periksa', [$dtStart, $dtEnd]);
            }

            if ($request->has('is_aktif')) {
                $query = $query->where('tb_rawat_jalan.is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('tb_rawat_jalan.is_aktif', 'ILIKE', '%true%');
            }
            
            if ($request->has('unit')) {
                $unit = $request->input('unit');
                $query = $query->where(function($q) use ($unit) {
                        $q->where('unit_id','ILIKE',$unit)
                        ->orWhere('unit_nama','ILIKE',$unit);
                    });
            }

            if ($request->has('dokter')) {
                $dokter = $request->input('dokter');
                $query = $query->where(function($q) use ($dokter) {
                        $q->where('dokter_id','ILIKE',$dokter)
                        ->orWhere('dokter_nama','ILIKE',$dokter);
                    });
            }

            if ($request->has('status')) {
                $query = $query->where('status', 'ILIKE', '%' .$request->input('status'). '%');
            }

            if ($request->has('jns_penjamin')) {
                $query = $query->where('jns_penjamin', 'ILIKE', '%' .$request->input('jns_penjamin'). '%');
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                $q->where('tb_rawat_jalan.no_rm','ILIKE',$keyword)
                ->orWhere('tb_rawat_jalan.nama_pasien','ILIKE',$keyword);
                })->select('tb_rawat_jalan.trx_id', 'tb_rawat_jalan.no_rm', 'tb_rawat_jalan.nama_pasien', 'tb_rawat_jalan.is_aktif', 'tb_pasien_diet.pasien_diet_id',
                        'tb_pasien_diet.start_date','tb_pasien_diet.start_time','tb_pasien_diet.meal_set','tb_pasien_diet.bentuk_makanan','tb_pasien_diet.diagnosa',
                        'tb_pasien_diet.catatan','tb_pasien_diet.tinggi_badan','tb_pasien_diet.berat_badan','tb_pasien_diet.bmi','tb_pasien_diet.is_special')
                ->leftJoin('tb_pasien_diet', 'tb_rawat_jalan.trx_id', '=', 'tb_pasien_diet.trx_id')->orderBy('tb_rawat_jalan.tgl_periksa','ASC')
                // ->where('tb_pasien_diet.status', 'DRAFT')
                // ->whereDate('tgl_periksa', Carbon::today())
                ->orderBy('tb_rawat_jalan.trx_id','ASC')
                // ->groupBy('tb_rawat_jalan.trx_id')
                ->paginate($per_page);
            // dd($list);
            
            foreach($list->items() as $item) {
                $dt = DietPasien::leftJoin('tb_pasien_diet_detail', 'tb_pasien_diet.pasien_diet_id', '=', 'tb_pasien_diet_detail.pasien_diet_id')
                    ->leftJoin('tb_diet','tb_pasien_diet_detail.diet_id','=','tb_diet.diet_id')
                    ->where('tb_pasien_diet.client_id',$this->clientId)
                    ->where('tb_pasien_diet.pasien_diet_id',$item['pasien_diet_id'])
                    ->where('tb_pasien_diet.is_aktif',1)
                    ->get();
                $item['detail_diet'] = $dt;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    public function listMonitoring(Request $request)
    {
        try{
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = DietPasien::count();
                }
            }

            $data = DietPasien::where('client_id',$this->clientId)
                    ->where('status', 'DISTRIBUTED')
                    ->orWhere('status', 'CHECKED')
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('trx_id','ILIKE',$keyword)
                        ->orWhere('pasien_diet_id','ILIKE',$keyword);
                    })->orderBy('trx_id', 'ASC')->paginate($rowNumber);
            
            foreach($data->items() as $item) {
                $dt = DietPasien::leftJoin('tb_pasien_diet_detail', 'tb_pasien_diet.pasien_diet_id', '=', 'tb_pasien_diet_detail.pasien_diet_id')
                    ->where('tb_pasien_diet.client_id',$this->clientId)
                    ->where('tb_pasien_diet.pasien_diet_id',$item['pasien_diet_id'])
                    ->where('tb_pasien_diet.is_aktif',1)
                    ->get();
                $item['detail_monitoring'] = $dt;
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data makanan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function dataDietPasien(Request $request, $pasien_diet_id)
    {
        try {
            $data = DietPasien::leftJoin('tb_rawat_jalan', 'tb_pasien_diet.trx_id', '=', 'tb_rawat_jalan.trx_id')
            ->where('tb_pasien_diet.client_id', $this->clientId)->where('pasien_diet_id', $pasien_diet_id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data dokter : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function createDiet(Request $request)
    {
        try {
            $dietId = $this->getDietId();
            $diet = new DietPasien();
            $diet->pasien_diet_id = $dietId;
            $diet->trx_id = $request->trx_id;
            $diet->pasien_id = $request->pasien_id;
            $diet->nama_pasien = $request->nama_pasien;
            $diet->unit_id = $request->unit_id;
            $diet->nama_unit = $request->unit_nama;
            $diet->dokter_id = $request->dokter_id;
            $diet->nama_dokter = $request->dokter_nama;
            $diet->start_date = $request->tgl_berlaku;
            $diet->start_time = $request->jam_berlaku;
            $diet->catatan = $request->catatan;
            $diet->is_special = $request->is_special;
            $diet->diagnosa = $request->diagnosa;
            $diet->meal_set = $request->meal_set;
            $diet->status = 'DRAFT';
            $diet->bentuk_makanan = $request->bentuk_makanan;
            $diet->tinggi_badan = $request->height;
            $diet->berat_badan = $request->weight;
            $diet->bmi = $request->bmi;
            $diet->is_aktif = 1;
            $diet->created_by = Auth::user()->username;
            $diet->updated_by = Auth::user()->username;
            $diet->client_id = $this->clientId;
            DB::connection('dbclient')->beginTransaction();
            $success = $diet->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet.']);
            }

            foreach ($request->diets as $detail) {
                if ($detail !== null) {
                    $menu = DietPasienDetail::select('tb_menu_makanan_detail.menu_makanan_id')
                    ->leftJoin('tb_menu_makanan_detail', 'tb_pasien_diet_detail.menu_makanan_id', '=', 'tb_menu_makanan_detail.menu_makanan_id')
                    ->get();
                    $data = new DietPasienDetail();
                    $data->pasien_diet_detail_id = $this->getDietDetailId();
                    $data->pasien_diet_id = $diet->pasien_diet_id;
                    $data->trx_id = $request->trx_id;
                    $data->diet_id = $detail['diet_id'];
                    $data->nama_diet = $detail['nama_diet'];
                    $data->nama_menu = $detail['nama_menu'];
                    $data->qty = $detail['lf_qty'];
                    $data->schedule = json_encode($detail['schedule']);
                    $data->created_by = Auth::user()->username;
                    $data->client_id = $this->clientId;
                    $data->is_aktif = 1;
                    $data->updated_by = Auth::user()->username;
                    foreach ($menu as $menud) {
                        // dd($menu['nama_makanan']);
                        $data->menu_makanan_id = $menud['menu_makanan_id'];
                        
                        $success = $data->save();
                    }
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet baru']);
                    }
                }
            }

            $logId = $this->getDietLogId();
            $log = new DietPasienLog();
            $log->pasien_diet_log_id = $logId;
            $log->pasien_diet_id = $diet->pasien_diet_id;
            $log->trx_id = $request->trx_id;
            $log->status = 'DRAFT';
            $log->is_aktif = 1;
            $log->created_by = Auth::user()->username;
            $log->updated_by = Auth::user()->username;
            $log->client_id = $this->clientId;
            $success = $log->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet.']);
            }

            $diet['detail'] = DietPasienDetail::where('pasien_diet_id', $diet->pasien_diet_id)->get();

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'data diet berhasil di update.', 'data' => $diet]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data diet :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function updateDiet(Request $request)
    {
        try {
            // return $request;
            $diet_id = $request->pasien_diet_id;
            $trx_id = $request->trx_id;
            $diet = DietPasien::where('pasien_diet_id',$diet_id)
                    ->where('trx_id',$trx_id)
                    ->where('client_id',$this->clientId)
                    ->where('status','DRAFT')
                    ->first();

            $diet->start_date = $request->tgl_berlaku;
            $diet->start_time = $request->jam_berlaku;
            $diet->catatan = $request->catatan;
            $diet->is_special = $request->is_special;
            $diet->diagnosa = $request->diagnosa;
            $diet->meal_set = $request->meal_set;
            $diet->bentuk_makanan = $request->bentuk_makanan;
            $diet->tinggi_badan = $request->height;
            $diet->berat_badan = $request->weight;
            $diet->bmi = $request->bmi;

            $diet->created_by = Auth::user()->username;
            $diet->updated_by = Auth::user()->username;

            DB::connection('dbclient')->beginTransaction();
            $success = $diet->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data diet.']);
            }

            foreach ($request->diets as $detail) {
                if (!isset($detail['diet_detail_id'])) {
                    $data = new DietPasienDetail();
                    $data->pasien_diet_detail_id = $this->getDietDetailId();
                    $data->pasien_diet_id = $diet->pasien_diet_id;
                    $data->trx_id = $request->trx_id;
                    $data->diet_id = $detail['diet_id'];
                    $data->nama_diet = $detail['nama_diet'];
                    $data->nama_menu = $detail['nama_menu'];
                    $data->qty = $detail['lf_qty'];
                    $data->schedule = json_encode($detail['schedule']);
                    $data->created_by = Auth::user()->username;
                    $data->client_id = $this->clientId;
                    $data->is_aktif = 1;
                    $data->updated_by = Auth::user()->username;
                    $success = $data->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet baru']);
                    }
                } else {
                    $dietDetail = DietPasienDetail::where('pasien_diet_id',$diet_id)
                                ->where('pasien_diet_detail_id',$detail['diet_detail_id'])
                                ->where('trx_id',$trx_id)
                                ->where('client_id',$this->clientId)
                                ->first();
                                
                    $dietDetail->diet_id    = $detail['diet_id'];
                    $dietDetail->nama_diet  = $detail['nama_diet'];
                    $dietDetail->nama_menu  = $detail['nama_menu'];
                    $dietDetail->qty        = $detail['lf_qty'];
                    $dietDetail->schedule   = json_encode($detail['schedule']);
                    $dietDetail->created_by = Auth::user()->username;
                    $dietDetail->client_id  = $this->clientId;
                    $dietDetail->is_aktif   = 1;
                    $dietDetail->updated_by = Auth::user()->username;
                    $success = $dietDetail->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengubah data diet']);
                    }
                }
            }
            $logId = $this->getDietLogId();
            $log = new DietPasienLog();
            $log->pasien_diet_log_id = $logId;
            $log->pasien_diet_id = $diet->pasien_diet_id;
            $log->trx_id = $request->trx_id;
            $log->status = 'DRAFT';
            $log->is_aktif = 1;
            $log->created_by = Auth::user()->username;
            $log->updated_by = Auth::user()->username;
            $log->client_id = $this->clientId;
            $success = $log->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data log diet.']);
            }

            $diet['detail'] = DietPasienDetail::where('pasien_diet_id', $diet->pasien_diet_id)->get();

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'data diet berhasil di update.', 'data' => $diet]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data diet :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function createDietMonitoring(Request $request)
    {
        try {
            $dietId = $this->getDietMonitoringId();
            $diet = new DietPasienMonitoring();
            $diet->pasien_diet_monitoring_id = $dietId;
            $diet->pasien_diet_id = $request->diet_id;
            $diet->trx_id = $request->trx_id;
            $diet->meal_set = $request->meal_set;
            $diet->catatan = $request->catatan;
            $diet->skala_karbo = $request->karbo;
            $diet->skala_sayuran = $request->sayur;
            $diet->skala_lauk = $request->lauk;
            $diet->skala_buah = $request->buah;
            $diet->skala_minuman = $request->minuman;
            $diet->tgl_kontrol = $request->tgl_kontrol;
            $diet->alasan = $request->alasan;
            $diet->status = 'CHECKED';
            $diet->is_aktif = 1;
            $diet->created_by = Auth::user()->username;
            $diet->updated_by = Auth::user()->username;
            $diet->client_id = $this->clientId;
            DB::connection('dbclient')->beginTransaction();
            $success = $diet->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet.']);
            }

            $data = DietPasien::where('client_id',$this->clientId)->where('is_aktif', 1)->where('pasien_diet_id', $request->diet_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan / sudah berubah status.']);
            }
            $data->status = 'CHECKED';
            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet.']);
            }

            $logId = $this->getDietLogId();
            $log = new DietPasienLog();
            $log->pasien_diet_log_id = $logId;
            $log->pasien_diet_id = $request->diet_id;
            $log->trx_id = $request->trx_id;
            $log->status = 'CHECKED';
            $log->is_aktif = 1;
            $log->created_by = Auth::user()->username;
            $log->updated_by = Auth::user()->username;
            $log->client_id = $this->clientId;
            $success = $log->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet.']);
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'data diet berhasil di update.', 'data' => $diet]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data diet :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getDietId()
    {
        try {
            $id = $this->clientId . '.DIET-0001';
            $maxId = DietPasien::withTrashed()->where('pasien_diet_id', 'LIKE', $this->clientId . '.DIET-%')->max('pasien_diet_id');
            if (!$maxId) {
                $id = $this->clientId . '.DIET-0001';
            } else {
                $maxId = str_replace($this->clientId . '.DIET-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.DIET-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.DIET-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.DIET-0' . $count;
                } else {
                    $id = $this->clientId . '.DIET-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.DIET-' . Uuid::uuid4()->getHex();
        }
    }

    public function getDietDetailId()
    {
        try {
            $id = $this->clientId . '.DDI-0001';
            $maxId = DietPasienDetail::withTrashed()->where('pasien_diet_detail_id', 'LIKE', $this->clientId . '.DDI-%')->max('pasien_diet_detail_id');
            if (!$maxId) {
                $id = $this->clientId . '.DDI-0001';
            } else {
                $maxId = str_replace($this->clientId . '.DDI-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.DDI-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.DDI-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.DDI-0' . $count;
                } else {
                    $id = $this->clientId . '.DDI-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.DDI-' . Uuid::uuid4()->getHex();
        }
    }

    public function getDietMonitoringId()
    {
        try {
            $id = $this->clientId . '.DDM-0001';
            $maxId = DietPasienMonitoring::withTrashed()->where('pasien_diet_monitoring_id', 'LIKE', $this->clientId . '.DDM-%')->max('pasien_diet_monitoring_id');
            if (!$maxId) {
                $id = $this->clientId . '.DDM-0001';
            } else {
                $maxId = str_replace($this->clientId . '.DDM-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.DDM-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.DDM-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.DDM-0' . $count;
                } else {
                    $id = $this->clientId . '.DDM-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.DDM-' . Uuid::uuid4()->getHex();
        }
    }

    public function getDietLogId()
    {
        try {
            $id = $this->clientId . '.DDL-0001';
            $maxId = DietPasienLog::withTrashed()->where('pasien_diet_log_id', 'LIKE', $this->clientId . '.DDL-%')->max('pasien_diet_log_id');
            if (!$maxId) {
                $id = $this->clientId . '.DDL-0001';
            } else {
                $maxId = str_replace($this->clientId . '.DDL-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.DDL-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.DDL-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.DDL-0' . $count;
                } else {
                    $id = $this->clientId . '.DDL-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.DDL-' . Uuid::uuid4()->getHex();
        }
    }
}
 