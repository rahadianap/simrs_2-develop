<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\KelompokTindakan;

class KelompokTindakanController extends Controller
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
            if($request->has('aktif')) {
                $aktif = '%'.$request->input('aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = KelompokTindakan::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }            
            $list = KelompokTindakan::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('kelompok_tindakan','ILIKE',$keyword);
                    })->orderBy('kelompok_tindakan','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tindakan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $list = KelompokTindakan::where('kelompok_tindakan_id',$id)->where('client_id',$clientId)->first(); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian kelompok tindakan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
           
            $request->validate([
                'kelompok_tindakan' => 'required',
            ]);
            
            $id = $this->getKelompokTindakanId($clientId);
            // return response()->json(['success' => false, 'message' => $id]);
            $req = KelompokTindakan::where('client_id', $this->clientId)->where('kelompok_tindakan', 'ILIKE', $request->kelompok_tindakan)->where('is_aktif', 1)->first();
            if($req){
                return response()->json(['success' => false,'message' => 'Kelompok tindakan sudah ada.']);
            }

            $data = new KelompokTindakan();
            $data->kelompok_tindakan_id     = $id;
            $data->kelompok_tindakan        = strtoupper($request->kelompok_tindakan);
            $data->deskripsi                = $request->deskripsi;
            $data->is_aktif                 = 1;
            $data->client_id                = $clientId;
            $data->created_by               = Auth::user()->username;
            $data->updated_by               = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data Kelompok Tindakan']);
            }
            return response()->json(['success' => true,'message' => 'Data Kelompok Tindakan berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Kelompok Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try{
            $id = $request->kelompok_tindakan_id;

            $data = KelompokTindakan::where('client_id',$this->clientId)->where('kelompok_tindakan_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data kelompok tindakan tidak ditemukan']);
            }
            
            $success = $data->update([
                'kelompok_tindakan'     => strtoupper($request->kelompok_tindakan),
                'deskripsi'             => $request->deskripsi,
                'is_aktif'              => $request->is_aktif,
                'updated_by'            =>Auth::user()->username
            ]);
            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data kelompok tindakan']);
            }
            return response()->json(['success'=>true,'message'=>'Data kelompok tindakan berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data kelompok tindakan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $kelompoktindakan_id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
    
            $data = KelompokTindakan::where('kelompok_tindakan_id', $kelompoktindakan_id)->where('client_id',$clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'Kelompok tindakan berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getKelompokTindakanId($clientId) 
    {
        try {
            $id = $clientId.'-KTD0001';
            $maxId = KelompokTindakan::withTrashed()->where('kelompok_tindakan_id','LIKE',$clientId.'-KTD%')->max('kelompok_tindakan_id');
            if (!$maxId) { $id = $clientId.'-KTD0001'; }
            else {
                $maxId = str_replace($clientId.'-KTD','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-KTD000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-KTD00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-KTD0'.$count; } 
                else { $id = $clientId.'-KTD'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'KTD'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}