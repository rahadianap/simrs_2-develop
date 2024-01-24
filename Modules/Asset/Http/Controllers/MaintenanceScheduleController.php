<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

use Modules\Asset\Entities\MaintenanceTicket;
use Modules\Asset\Entities\MaintenanceTicketLog;
use Modules\Asset\Entities\MaintenanceSchedule;
use Modules\Asset\Entities\Asset;

class MaintenanceScheduleController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function listSchedule(Request $request)
    {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = MaintenanceSchedule::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = MaintenanceSchedule::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('maint_schedule_id','ILIKE',$keyword)
                        ->orWhere('nama_vendor','ILIKE',$keyword);
                    })->orderBy('maint_schedule_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $schedule_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = MaintenanceSchedule::where('client_id',$this->clientId)->where('is_aktif',1)->where('maint_schedule_id', $schedule_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function createSchedule(Request $request)
    {
        try{
            $id = $this->getScheduleId();
            
            $data = new MaintenanceSchedule();
            $data->maint_ticket_id = $request->id;
            $data->maint_schedule_id = $id;
            $data->teknisi = $request->teknisi;
            $data->tgl_rencana = $request->tgl_rencana;
            // $data->tgl_realisasi = $request->tgl_realisasi;
            // $data->catatan = $request->catatan;
            // $data->tindak_lanjut = $request->tindak_lanjut;
            $data->is_vendor = $request->is_vendor;
            $data->nama_vendor = $request->nama_vendor;
            $data->status = 'Preventive';
            $data->is_aktif = true;
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

    public function updateSchedule(Request $request)
    {
        try{
            $schedule_id = $request->maint_schedule_id;
            $data = MaintenanceSchedule::where('client_id',$this->clientId)->where('is_aktif',1)->where('maint_schedule_id', $schedule_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }
            $data->maint_ticket_id = $request->id;
            $data->tgl_realisasi = $request->tgl_realisasi;
            $data->catatan = $request->catatan;
            $data->tindak_lanjut = $request->tindak_lanjut;
            $data->status = $request->status;
            $data->is_aktif = true;
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

    public function delete(Request $request, $schedule_id)
    {
        try{
            $data = MaintenanceSchedule::where('client_id',$this->clientId)->where('is_aktif',1)->where('maint_schedule_id', $schedule_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Menu.']);
            }
            return response()->json(['success' => true,'message' => 'data maintenance tiket berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getScheduleId() 
    {
        try {
            $id = $this->clientId.'.SCH-0001';
            $maxId = MaintenanceSchedule::withTrashed()->where('client_id', $this->clientId)->where('maint_schedule_id','LIKE',$this->clientId.'.SCH-%')->max('maint_schedule_id');
            if (!$maxId) { $id = $this->clientId.'.SCH-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.SCH-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.SCH-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.SCH-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.SCH-0'.$count; } 
                else { $id = $this->clientId.'.SCH-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.SCH-' . Uuid::uuid4()->getHex();
        }
    }
}