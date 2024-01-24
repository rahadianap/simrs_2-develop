<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PraktekDokter\Entities\Dokter;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon;

class MasterDokterController extends Controller
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
                    $perpage = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->count();
                } 
            }
            $data = Dokter::where('client_id',$this->clientId)
                    ->select('dokter_id','dokter_nama','is_aktif')
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('dokter_nama','ILIKE',$keyword)
                        ->orWhere('dokter_id','ILIKE',$keyword);
                    })->orderBy('dokter_nama', 'ASC')->paginate($perpage);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data dokter tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function listsDokUnit(Request $request, $unit_id)
    {
        try{
            $keyword = '%%';
            $active = 'true';

            $perpage = 20;
            if($request->has('per_page')) {
                $perpage = $request->input('per_page');
                if($perpage == 'ALL') {
                    $perpage = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->count();
                } 
            }

            $data = Dokter::where('client_id',$this->clientId)
                    ->where('is_aktif',true)
                    ->select('dokter_id','dokter_nama')
                    ->orderBy('dokter_nama', 'ASC')->paginate($perpage);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data dokter tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $dokterId)
    {
        try{
            $data = Dokter::where('client_id',$this->clientId)->where('dokter_id', $dokterId)->first();
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
            $id = $this->getDokterId();
            
            $data = new Dokter();
            $data->dokter_id = $id;
            $data->dokter_nama = $request->dokter_nama;
            $data->spesialis_id = '';
            $data->pend_akhir = '';
            $data->smf_id = '';
            $data->biografi = '';
            $data->url_avatar = '';
            $data->no_registrasi = '';
            $data->no_sip = '';
            $data->tgl_akhir_sip = null;
            $data->status_kerjasama = '';
            $data->npwp = '';
            $data->rekening = '';
            $data->tempat_lahir = '';
            $data->tgl_lahir = null;
            $data->jns_kelamin = '';
            $data->no_telepon = '';
            $data->user_id = null; 
            $data->email = null;
            $data->alamat = null;
            $data->status = null;
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
            $dokterId = $request->dokter_id;
            $data = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id', $dokterId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }
            $data->dokter_nama = $request->dokter_nama;
            $data->spesialis_id = '';
            $data->pend_akhir = '';
            $data->smf_id = '';
            $data->biografi = '';
            $data->url_avatar = '';
            $data->no_registrasi = '';
            $data->no_sip = '';
            $data->tgl_akhir_sip = null;
            $data->status_kerjasama = '';
            $data->npwp = '';
            $data->rekening = '';
            $data->tempat_lahir = '';
            $data->tgl_lahir = null;
            $data->jns_kelamin = '';
            $data->no_telepon = '';
            $data->user_id = null; 
            $data->email = null;
            $data->alamat = null;
            $data->status = null;
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

    public function remove(Request $request, $dokterId)
    {
        try{
            $data = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id', $dokterId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = date('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Dokter.']);
            }
            return response()->json(['success' => true,'message' => 'data Dokter berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function avatar(Request $request){
        try {            
            $id = '';
            if($request->has('dokter_id')) { $id = $request->dokter_id; }
            
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            } 
            
            $path = Storage::disk('avatars')->putFile('avatars/doctors', $request->file('image'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url;

            if ($id == '') {
                return response()->json(['success'=>true, 'message'=>'avatar berhasil diupload', 'data'=>$data]);
            } 
            else {
                $dokter = Dokter::where('dokter_id',$id)->first();
                $dokter->url_avatar = $path_url;
                $success = $dokter->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'Avatar gagal diupload']);
                }                
                return response()->json(['success'=>true, 'message'=>'avatar berhasil diupload' ,'data'=>$data]);
            }
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menyimpan avatar.','error'=> $e->getMessage()]);
        }
    }

    public function getDokterId() 
    {
        try {
            $id = $this->clientId.'.DOK-0001';
            $maxId = Dokter::withTrashed()->where('client_id', $this->clientId)->where('dokter_id','LIKE',$this->clientId.'.DOK-%')->max('dokter_id');
            if (!$maxId) { $id = $this->clientId.'.DOK-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.DOK-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.DOK-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.DOK-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.DOK-0'.$count; } 
                else { $id = $this->clientId.'.DOK-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.DOK-' . Uuid::uuid4()->getHex();
        }
    }
}
