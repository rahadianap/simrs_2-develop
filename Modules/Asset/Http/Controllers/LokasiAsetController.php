<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Asset\Entities\LokasiAset;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class LokasiAsetController extends Controller
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
                if($per_page == "ALL"){ $per_page = LokasiAset::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = LokasiAset::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('lokasi_nama','ILIKE',$keyword)
                        ->orWhere('lokasi_aset_id','ILIKE',$keyword);
                    })->orderBy('lokasi_nama','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List lokasi aset tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $lokasi_id)
    {
        try{
            $data = LokasiAset::where('client_id',$this->clientId)->where('is_aktif',1)->where('lokasi_aset_id', $lokasi_id)->first();
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
            $id = $this->getLokasiId();
            
            $data = new LokasiAset();
            $data->lokasi_aset_id = $id;
            $data->lokasi_nama = $request->lokasi_nama;
            $data->deskripsi = $request->deskripsi;
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

    public function update(Request $request)
    {
        try{
            $id = $request->lokasi_aset_id;
            $data = LokasiAset::where('client_id',$this->clientId)->where('is_aktif',1)->where('lokasi_aset_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data. Data tidak ditemukan.']);
            }

            $data->deskripsi = $request->deskripsi;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success'=>true,'message'=>'Ada kesalahan saat mengubah data.']);    
            }
            return response()->json(['success'=>true,'message'=>'data berhasil diubah','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ada kesalahan saat mengubah data','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $lokasi_id)
    {
        try{
            $data = LokasiAset::where('client_id',$this->clientId)->where('is_aktif',1)->where('lokasi_aset_id', $lokasi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
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

    public function getLokasiId() 
    {
        try {
            $id = $this->clientId.'.LKA0001';
            $maxId = LokasiAset::withTrashed()->where('client_id', $this->clientId)
                ->where('lokasi_aset_id','LIKE',$this->clientId.'.LKA%')->max('lokasi_aset_id');
            if (!$maxId) { $id = $this->clientId.'.LKA0001'; }
            else {
                $maxId = str_replace($this->clientId.'.LKA','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.LKA000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.LKA00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.LKA0'.$count; } 
                else { $id = $this->clientId.'.LKA'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return null;
        }
    }

}
