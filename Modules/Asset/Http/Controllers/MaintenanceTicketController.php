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

class MaintenanceTicketController extends Controller
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
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = MaintenanceTicket::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $status = '%%';
            if($request->has('status')) {
                $status = $status = '%'.$request->input('status').'%';
                if($status == '') { $status = '%%'; }
            }

            $list = MaintenanceTicket::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)->where('status','ILIKE',$status)
                    ->where(function($q) use ($keyword) {
                        $q->where('maint_ticket_id','ILIKE',$keyword)
                        ->orWhere('asset_id','ILIKE',$keyword);
                    })->orderBy('maint_ticket_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $ticket_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = MaintenanceTicket::where('client_id',$this->clientId)->where('is_aktif',1)->where('maint_ticket_id', $ticket_id)->first();
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
            $id = $this->getMaintenanceId();
            
            $data = new MaintenanceTicket();
            $data->maint_ticket_id = $id;
            $data->asset_id = $request->asset_id;
            $data->jenis_maintenance = $request->jenis_maintenance;
            $data->tgl_tiket = Carbon::now()->format('Y-m-d H:i:s');
            $data->keterangan = $request->keterangan;
            $data->prioritas = $request->prioritas;
            $data->lokasi_asset = $request->lokasi_asset;
            $data->nama_teknisi = $request->nama_teknisi;
            $data->tindakan = $request->tindakan;
            $data->catatan_tindakan = $request->catatan_tindakan;
            $data->tgl_selesai = $request->tgl_selesai;
            $data->status = $request->status;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data']);
            }

            $logId = $this->getMaintenanceLogId();

            $log = new MaintenanceTicketLog();
            $log->maint_ticket_log_id = $logId;
            $log->maint_ticket_id = $data->maint_ticket_id;
            $log->tgl_log = Carbon::now()->format('Y-m-d H:i:s');
            $log->keterangan = $request->keterangan;
            $log->status = 'DRAFT';

            $success = $log->save();

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
            $ticket_id = $request->maint_ticket_id;
            $data = MaintenanceTicket::where('client_id',$this->clientId)->where('is_aktif',1)->where('maint_ticket_id', $ticket_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->jenis_maintenance = $request->jenis_maintenance;
            $data->keterangan = $request->keterangan;
            $data->prioritas = $request->prioritas;
            $data->lokasi_asset = $request->lokasi_asset;
            $data->nama_teknisi = $request->nama_teknisi;
            $data->tindakan = $request->tindakan;
            $data->catatan_tindakan = $request->catatan_tindakan;
            $data->tgl_selesai = $request->tgl_selesai;
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

    public function delete(Request $request, $ticket_id)
    {
        try{
            $data = MaintenanceTicket::where('client_id',$this->clientId)->where('is_aktif',1)->where('maint_ticket_id', $ticket_id)->first();
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

    public function getMaintenanceId() 
    {
        try {
            $id = $this->clientId.'.TIC-0001';
            $maxId = MaintenanceTicket::withTrashed()->where('client_id', $this->clientId)->where('maint_ticket_id','LIKE',$this->clientId.'.TIC-%')->max('maint_ticket_id');
            if (!$maxId) { $id = $this->clientId.'.TIC-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.TIC-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.TIC-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.TIC-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.TIC-0'.$count; } 
                else { $id = $this->clientId.'.TIC-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.TIC-' . Uuid::uuid4()->getHex();
        }
    }

    public function getMaintenanceLogId() 
    {
        try {
            $id = $this->clientId.'.LOG-0001';
            $maxId = MaintenanceTicketLog::withTrashed()->where('client_id', $this->clientId)->where('maint_ticket_log_id','LIKE',$this->clientId.'.LOG-%')->max('maint_ticket_log_id');
            if (!$maxId) { $id = $this->clientId.'.LOG-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.LOG-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.LOG-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.LOG-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.LOG-0'.$count; } 
                else { $id = $this->clientId.'.LOG-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.LOG-' . Uuid::uuid4()->getHex();
        }
    }
}
