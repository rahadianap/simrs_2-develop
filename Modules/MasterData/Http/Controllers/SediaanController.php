<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\Sediaan;
use DB;

class SediaanController extends Controller
{
    public function list(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $rowNumber = 10;
            $keyword = '%%';

            if($request->has('per_page')){
                $rowNumber = $request->get('per_page');
                if($rowNumber == 'ALL') { $rowNumber = Sediaan::where('client_id',$clientId)->count(); }
            }
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->get('keyword').'%';
            }           

            $list = Sediaan::where('client_id',$clientId)->where('is_aktif',1)
                    ->where(function($q) use ($keyword) {
                        $q->where('sediaan','ILIKE',$keyword)
                        ->orWhere('sediaan_id','ILIKE',$keyword)
                        ->orWhere('deskripsi','ILIKE',$keyword);
                    })->orderBy('sediaan','ASC')->paginate($rowNumber);
                    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List sediaan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
            
            $list = Sediaan::where('sediaan_id',$id)->where('client_id',$clientId)->where('is_aktif',1)->first(); 
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian sediaan tidak ditemukan.','error'=>$e->getMessage()]);
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
                'sediaan' => 'required|max:100',
            ]);
            $data                   = new Sediaan();
            $data->sediaan_id       = $this->getSediaanId($clientId);
            $data->sediaan          = $request->sediaan;
            $data->deskripsi        = $request->deskripsi;
            $data->is_aktif         = 1;
            $data->client_id        = $clientId;
            $data->created_by       = Auth::user()->username;
            $data->updated_by       = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data Sediaan']);
            }
            return response()->json(['success' => true,'message' => 'Data Sediaan berhasil disimpan','data'=>$data]);


        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Sediaan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'sediaan' => 'required|max:100',
            ]);
            
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
            $id = $request->sediaan_id;

            $data = Sediaan::where('client_id',$clientId)->where('sediaan_id',$id)->first();
            $data->sediaan          = $request->sediaan;
            $data->deskripsi        = $request->deskripsi;
            $data->is_aktif         = $request->is_aktif;
            $data->updated_by       = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data sediaan']);
            }
            return response()->json(['success' => true,'message' => 'Data sediaan berhasil disimpan','data'=>$data]);


        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Sediaan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $sediaan_id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
    
            $sediaan = Sediaan::where('sediaan_id', $sediaan_id)->where('client_id',$clientId)->update([
                // 'updated_by' => Auth::user()->username,
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'Sediaan berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Sediaan tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getSediaanId($clientId) 
    {
        try {
            $id = $clientId.'-SD'.'-0001';
            $maxId = Sediaan::withTrashed()->where('sediaan_id','LIKE',$clientId.'-SD'.'%')->max('sediaan_id');
            if (!$maxId) { $id = $clientId.'-SD'.'-0001'; }
            else {
                $maxId = str_replace($clientId.'-SD'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-SD'.'-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-SD'.'-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-SD'.'-0'.$count; } 
                else { $id = $clientId.'-SD'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'SD'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}
