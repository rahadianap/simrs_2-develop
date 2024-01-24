<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\Signa;

class SignaController extends Controller
{
    public function list(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { $rowNumber = Signa::where('client_id',$clientId)->count(); }
            } 
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            } 

            $list = Signa::where('client_id',$clientId)->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('signa','ILIKE',$keyword)
                        ->orWhere('signa_id','ILIKE',$keyword)
                        ->orWhere('deskripsi','ILIKE',$keyword);
                    })->orderBy('signa_id','ASC')->paginate($rowNumber); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List signa tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
            
            $list = Signa::where('signa_id',$id)->where('client_id',$clientId)->first(); 
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian signa tidak ditemukan.','error'=>$e->getMessage()]);
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
                'signa' => 'required|max:100',
            ]);

            $data                   = new Signa();
            $data->signa_id         = $this->getSignaId($clientId);
            $data->signa            = $request->signa;
            $data->deskripsi        = $request->deskripsi;
            $data->is_aktif         = 1;
            $data->client_id        = $clientId;
            $data->created_by       = Auth::user()->username;
            $data->updated_by       = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data Signa']);
            }
            return response()->json(['success' => true,'message' => 'Data Signa berhasil disimpan','data'=>$data]);


        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Signa tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            
            $request->validate([
                'signa' => 'required|max:100',
            ]);
            
            $clientId = $request->header('X-cid');
            $id = $request->signa_id;

            $data = Signa::where('client_id',$clientId)->where('signa_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data tidak ditemukan.']);
            }

            $data->signa            = $request->signa;
            $data->deskripsi        = $request->deskripsi;
            $data->is_aktif         = $request->is_aktif;
            $data->updated_by       = Auth::user()->username;

            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan perubahan data Signa']);
            }
            return response()->json(['success' => true,'message' => 'Data Signa berhasil diubah','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Signa tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $signa_id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
    
            $signa = Signa::where('signa_id', $signa_id)->where('client_id',$clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'Signa berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Signa tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getSignaId($clientId) 
    {
        try {
            $id = $clientId.'-SG'.'-0001';
            $maxId = Signa::withTrashed()->where('signa_id','LIKE',$clientId.'-SG'.'%')->max('signa_id');
            if (!$maxId) { $id = $clientId.'-SG'.'-0001'; }
            else {
                $maxId = str_replace($clientId.'-SG'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-SG'.'-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-SG'.'-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-SG'.'-0'.$count; } 
                else { $id = $clientId.'-SG'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'SG'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}
