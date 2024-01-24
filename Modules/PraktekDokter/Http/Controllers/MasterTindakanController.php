<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon;

use Modules\PraktekDokter\Entities\MasterTindakan;

class MasterTindakanController extends Controller
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
        try{
            $keyword = '%%';
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }

            $perpage = 20;
            if($request->has('per_page')) {
                $perpage = $request->input('per_page');
                if($perpage == 'ALL') {
                    $perpage = MasterTindakan::where('client_id',$this->clientId)->where('is_aktif',1)->count();
                } 
            }
            $data = MasterTindakan::where('client_id',$this->clientId)
                    ->select('tindakan_id','tindakan_nama','harga','is_aktif')
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('tindakan_nama','ILIKE',$keyword)
                        ->orWhere('tindakan_id','ILIKE',$keyword);
                    })->orderBy('tindakan_nama', 'ASC')->paginate($perpage);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $tindakanId)
    {
        try{
            $data = MasterTindakan::where('client_id',$this->clientId)->where('tindakan_id', $tindakanId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            }            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam pengambilan data.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            $id = $this->getTindakanId();
            
            $data                       = new MasterTindakan();
            $data->tindakan_id          = $id;
            $data->tindakan_nama        = $request->tindakan_nama;
            $data->klasifikasi          = 'TINDAKAN';
            $data->kelompok_tindakan_id = '';
            $data->meta_data            = '';
            $data->satuan               = 'X';
            $data->is_paket             = false;
            $data->is_hitung_admin      = false;
            $data->is_diskon            = false;
            $data->is_cito              = false;            
            $data->is_tampilkan_dokter  = true;
            $data->is_lab_hasil         = false;
            $data->is_kamar             = false;
            // $data->rl_kode              = $request->rl_kode;
            // $data->kelompok_tagihan_id  = $request->kelompok_tagihan_id;
            // $data->kelompok_vklaim_id   = $request->kelompok_vklaim_id;
            // $data->deskripsi            = $request->deskripsi;
            $data->is_aktif             = true;
            $data->harga                = $request->harga;
            
            $data->client_id            = $this->clientId;
            $data->created_by           = Auth::user()->username;
            $data->updated_by           = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data tindakan.']);
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
            $id = $request->tindakan_id;
            $data = MasterTindakan::where('client_id',$this->clientId)->where('is_aktif',1)->where('tindakan_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }
            
            $data->tindakan_nama        = $request->tindakan_nama;
            $data->is_aktif             = $request->is_aktif;
            $data->harga                = $request->harga;
            $data->updated_by           = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data tindakan.']);
            }
            
            return response()->json(['success'=>true,'message'=>'data berhasil disimpan','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ada kesalahan saat mengubah data','error'=>$e->getMessage()]);
        }
    }

    public function remove(Request $request, $id)
    {
        try{
            $data = MasterTindakan::where('client_id',$this->clientId)->where('is_aktif',1)->where('tindakan_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = date('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data.']);
            }
            return response()->json(['success' => true,'message' => 'data berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getTindakanId() 
    {
        try {
            $id = $this->clientId.'-TDK0001';
            $maxId = MasterTindakan::withTrashed()->where('tindakan_id','LIKE',$this->clientId.'-TDK%')->max('tindakan_id');
            if (!$maxId) { $id = $this->clientId.'-TDK0001'; }
            else {
                $maxId = str_replace($this->clientId.'-TDK','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-TDK000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-TDK00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-TDK0'.$count; } 
                else { $id = $this->clientId.'-TDK'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'TDK'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }
}
