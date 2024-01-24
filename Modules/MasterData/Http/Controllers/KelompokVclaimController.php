<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\KelompokVclaim;

class KelompokVclaimController extends Controller
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
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = KelompokVclaim::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KelompokVclaim::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('kelompok_vclaim','ILIKE',$keyword)
                        ->orWhere('vclaim_label','ILIKE',$keyword)
                        ->orWhere('kelompok_vclaim_id','ILIKE',$keyword);
                    })->orderBy('kelompok_vclaim','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok vklaim tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $list = KelompokVclaim::where('kelompok_vclaim_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Kelompok vklaim tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kelompok_vclaim' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }
            
            $data                           = new KelompokVclaim();
            $data->kelompok_vclaim_id       = $this->getKelompokVclaimId();
            $data->kelompok_vclaim          = $request->kelompok_vclaim;
            $data->vclaim_label             = strtoupper($request->vclaim_label);
            $data->deskripsi                = $request->deskripsi;
            $data->client_id                = $this->clientId;
            $data->is_aktif                 = 1;
            $data->created_by               = Auth::user()->username;
            $data->updated_by               = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data kelompok vklaim.']);
            }
            return response()->json(['success' => true,'message' => 'Data kelompok vklaim berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah kelompok vklaim tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kelompok_vclaim' => 'required|max:100',
                'vclaim_label' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $request->kelompok_vclaim_id;
            $data = KelompokVclaim::where('client_id',$this->clientId)->where('kelompok_vclaim_id', $id)->first();            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data kelompok vklaim tidak ditemukan']);
            }
            
            $success = KelompokVclaim::where('client_id',$this->clientId)
                ->where('kelompok_vclaim_id', $id)
                ->update([
                    'kelompok_vclaim'       => $request->kelompok_vclaim,
                    'vclaim_label'          => strtoupper($request->vclaim_label),
                    'deskripsi'             => $request->deskripsi,
                    'is_aktif'              => $request->is_aktif,
                    'updated_by'            =>Auth::user()->username
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data kelompok vklaim']);
            }
            return response()->json(['success'=>true,'message'=>'Data kelompok vklaim berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data kelompok vklaim tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = KelompokVclaim::where('kelompok_vclaim_id', $id)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus kelompok vklaim tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getKelompokVclaimId() 
    {
        try {
            $id = $this->clientId.'-VCL0001';
            $maxId = KelompokVclaim::withTrashed()->where('kelompok_vclaim_id','LIKE',$this->clientId.'-VCL%')->max('kelompok_vclaim_id');
            if (!$maxId) { $id = $this->clientId.'-VCL0001'; }
            else {
                $maxId = str_replace($this->clientId.'-VCL','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-VCL000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-VCL00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-VCL0'.$count; } 
                else { $id = $this->clientId.'-VCL'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'-VCL'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}