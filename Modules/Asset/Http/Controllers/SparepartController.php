<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\Asset\Entities\Sparepart;

class SparepartController extends Controller
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
                if($per_page == "ALL"){ $per_page = Sparepart::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = Sparepart::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('sparepart_nama','ILIKE',$keyword)
                        ->orWhere('sparepart_id','ILIKE',$keyword);
                    })->orderBy('sparepart_nama','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $sparepart_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = Sparepart::where('client_id',$this->clientId)->where('is_aktif',1)->where('sparepart_id', $sparepart_id)->first();
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
            $id = $this->getSparepartId();
            
            $data = new Sparepart();
            $data->sparepart_id = $id;
            $data->sparepart_nama = $request->sparepart_nama;
            $data->serial_no = $request->serial_no;
            $data->brand_nama = $request->brand_nama;
            $data->deskripsi = $request->deskripsi;
            $data->tgl_kadaluarsa = $request->tgl_kadaluarsa;
            $data->status = $request->status;
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
            $sparepart_id = $request->sparepart_id;
            $data = Sparepart::where('client_id',$this->clientId)->where('is_aktif',1)->where('sparepart_id', $sparepart_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->sparepart_nama = $request->sparepart_nama;
            $data->serial_no = $request->serial_no;
            $data->brand_nama = $request->brand_nama;
            $data->deskripsi = $request->deskripsi;
            $data->tgl_kadaluarsa = $request->tgl_kadaluarsa;
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

    public function delete(Request $request, $sparepart_id)
    {
        try{
            $data = Sparepart::where('client_id',$this->clientId)->where('is_aktif',1)->where('sparepart_id', $sparepart_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data sparepart.']);
            }
            return response()->json(['success' => true,'message' => 'data Menu berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getSparepartId() 
    {
        try {
            $id = $this->clientId.'.SPT-0001';
            $maxId = Sparepart::withTrashed()->where('client_id', $this->clientId)->where('sparepart_id','LIKE',$this->clientId.'.SPT-%')->max('sparepart_id');
            if (!$maxId) { $id = $this->clientId.'.SPT-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.SPT-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.SPT-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.SPT-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.SPT-0'.$count; } 
                else { $id = $this->clientId.'.SPT-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.SPT-' . Uuid::uuid4()->getHex();
        }
    }
}
