<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use DB;

use Modules\Penunjang\Entities\DietPasien;
use Modules\Penunjang\Entities\DietPasienDetail;
use Modules\Penunjang\Entities\DietPasienLog;

class MealOrderController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request, $stat = null)
    {
        try{
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';
            $status = '%%'; 

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }

            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = PurchaseRequest::count();
                }
            }

            if($stat !== null) {
                $status = '%'.$stat.'%';
            }

            $data = DietPasien::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where('status', 'APPROVED')
                    ->where('status','ILIKE',$status)
                    ->where(function($q) use ($keyword) {
                        $q->where('pasien_diet_id','ILIKE',$keyword)
                        ->orWhere('trx_id','ILIKE',$keyword)
                        ->orWhere('nama_pasien','ILIKE',$keyword)
                        ->orWhere('nama_unit','ILIKE',$keyword);
                    })->orderBy('pasien_diet_id', 'ASC')->paginate($rowNumber);
            
            foreach($data->items() as $item) {
                $dt = DietPasienDetail::leftJoin('tb_menu_makanan', 'tb_pasien_diet_detail.menu_makanan_id', '=', 'tb_menu_makanan.menu_makanan_id')
                    ->leftJoin('tb_menu_makanan_detail', 'tb_menu_makanan.menu_makanan_id', '=', 'tb_menu_makanan_detail.menu_makanan_id')
                    ->where('tb_pasien_diet_detail.client_id',$this->clientId)
                    ->where('pasien_diet_id',$item['pasien_diet_id'])
                    ->where('tb_pasien_diet_detail.is_aktif',1)
                    ->get();
                $item['detail_diet'] = $dt;
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function approveDiet(Request $request, $diet_id)
    {
        try {
            $data = DietPasien::where('client_id',$this->clientId)->where('is_aktif', 1)->where('pasien_diet_id', $diet_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan / sudah berubah status.']);
            }
            DB::connection('dbclient')->beginTransaction();
            $data->status = 'APPROVED';
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
            $log->status = 'APPROVED';
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

    public function orderDiet(Request $request, $diet_id)
    {
        try {
            $data = DietPasien::where('client_id',$this->clientId)->where('is_aktif', 1)->where('pasien_diet_id', $diet_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan / sudah berubah status.']);
            }
            DB::connection('dbclient')->beginTransaction();
            $data->status = 'ORDERED';
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
            $log->status = 'ORDERED';
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
