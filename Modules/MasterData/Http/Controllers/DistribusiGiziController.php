<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\DistribusiGizi;
use Modules\Penunjang\Entities\DietPasien;
use Modules\Penunjang\Entities\DietPasienDetail;
use Modules\Penunjang\Entities\DietPasienLog;

use DB;

class DistribusiGiziController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request)
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
                    ->where('is_aktif','ILIKE',$active)
                    ->where('status', 'ORDERED')
                    ->where(function($q) use ($keyword) {
                        $q->where('pasien_diet_id','ILIKE',$keyword)
                        ->orWhere('unit_id','ILIKE',$keyword);
                    })->orderBy('pasien_diet_id', 'ASC')->paginate($rowNumber);

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
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $dist_gizi_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = DistribusiGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('dist_gizi_id', $dist_gizi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            $id = $this->getDistribusiGiziId();
            
            $data = new DistribusiGizi();
            $data->dist_gizi_id = $id;
            $data->tgl_permintaan = $request->tgl_permintaan;
            $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;
            $data->unit_id = $request->unit_id;
            $data->status = 'DRAFT';
            $data->catatan = $request->catatan;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data']);
            }
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try{
            $dist_gizi_id = $request->dist_gizi_id;
            $data = DistribusiGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('dist_gizi_id', $dist_gizi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;
            $data->unit_id = $request->unit_id;
            $data->catatan = $request->catatan;
            $data->is_aktif = 1;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success'=>true,'message'=>'Ada kesalahan saat mengubah data.']);    
            }
            return response()->json(['success'=>true,'message'=>'data berhasil disimpan','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ada kesalahan saat mengubah data','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $dist_gizi_id)
    {
        try{
            $data = DistribusiGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('dist_gizi_id', $dist_gizi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Distribusi Gizi.']);
            }
            return response()->json(['success' => true,'message' => 'data Distribusi Gizi berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonaktifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function distributeDiet(Request $request, $diet_id)
    {
        try {
            $data = DietPasien::where('client_id',$this->clientId)->where('is_aktif', 1)->where('pasien_diet_id', $diet_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan / sudah berubah status.']);
            }
            DB::connection('dbclient')->beginTransaction();
            $data->status = 'DISTRIBUTED';
            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data diet.']);
            }

            $logId = $this->getDietLogId();
            $log = new DietPasienLog();
            $log->pasien_diet_log_id = $logId;
            $log->pasien_diet_id = $diet_id;
            $log->trx_id = $request->trx_id;
            $log->status = 'DISTRIBUTED';
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
            return response()->json(['success' => true, 'message' => 'data diet berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data diet :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getDistribusiGiziId() 
    {
        try {
            $id = $this->clientId.'.DNT-0001';
            $maxId = DistribusiGizi::withTrashed()->where('client_id', $this->clientId)->where('dist_gizi_id','LIKE',$this->clientId.'.DNT-%')->max('dist_gizi_id');
            if (!$maxId) { $id = $this->clientId.'.DNT-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.DNT-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.DNT-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.DNT-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.DNT-0'.$count; } 
                else { $id = $this->clientId.'.DNT-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.DNT-' . Uuid::uuid4()->getHex();
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
