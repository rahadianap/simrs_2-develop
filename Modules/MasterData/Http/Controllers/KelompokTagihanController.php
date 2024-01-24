<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\KelompokTagihan;

class KelompokTagihanController extends Controller
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
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = KelompokTagihan::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KelompokTagihan::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('kelompok_tagihan','ILIKE',$keyword)
                        ->orWhere('kelompok_tagihan_id','ILIKE',$keyword);
                    })->orderBy('kelompok_tagihan','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $list = KelompokTagihan::where('kelompok_tagihan_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Kelompok tagihan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kelompok_tagihan' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }
            
            $data                           = new KelompokTagihan();
            $data->kelompok_tagihan_id      = $this->getKelompokTagihanId();
            $data->kelompok_tagihan         = strtoupper($request->kelompok_tagihan);
            $data->deskripsi                = $request->deskripsi;
            $data->client_id                = $this->clientId;
            $data->is_aktif                 = 1;
            $data->created_by               = Auth::user()->username;
            $data->updated_by               = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data kelompok tagihan.']);
            }
            return response()->json(['success' => true,'message' => 'Data kelompok tagihan berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah kelompok tagihan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kelompok_tagihan' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $request->kelompok_tagihan_id;
            $data = KelompokTagihan::where('client_id',$this->clientId)->where('kelompok_tagihan_id', $id)->first();            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data kelompok tagihan tidak ditemukan']);
            }
            
            $success = KelompokTagihan::where('client_id',$this->clientId)
                ->where('kelompok_tagihan_id', $id)
                ->update([
                    'kelompok_tagihan'      => strtoupper($request->kelompok_tagihan),
                    'deskripsi'             => $request->deskripsi,
                    'is_aktif'              => $request->is_aktif,
                    'updated_by'            =>Auth::user()->username
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data kelompok tagihan']);
            }
            return response()->json(['success'=>true,'message'=>'Data kelompok tagihan berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data kelompok tagihan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = KelompokTagihan::where('kelompok_tagihan_id', $id)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus kelompok tagihan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getKelompokTagihanId() 
    {
        try {
            $id = $this->clientId.'-BGR0001';
            $maxId = KelompokTagihan::withTrashed()->where('kelompok_tagihan_id','LIKE',$this->clientId.'-BGR%')->max('kelompok_tagihan_id');
            if (!$maxId) { $id = $this->clientId.'-BGR0001'; }
            else {
                $maxId = str_replace($this->clientId.'-BGR','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-BGR000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-BGR00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-BGR0'.$count; } 
                else { $id = $this->clientId.'-BGR'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'-BGR'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}