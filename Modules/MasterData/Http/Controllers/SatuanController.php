<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\Satuan;

class SatuanController extends Controller
{
    public function list(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }   
            $per_page = Satuan::where('client_id',$clientId)->count();

            $list = Satuan::where('client_id',$clientId)->where('is_aktif',1)
                    ->where(function($q) use ($keyword) {
                        $q->where('satuan_nama','ILIKE',$keyword)
                        ->orWhere('satuan_id','ILIKE',$keyword);
                    })->orderBy('satuan_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List satuan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
            
            $list = Satuan::where('satuan_id',$id)->where('client_id',$clientId)->where('is_aktif',1)->first(); 
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian satuan tidak ditemukan.','error'=>$e->getMessage()]);
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
                'satuan_id' => 'required|max:100',
                'satuan_nama' => 'required|max:100',
            ]);
            $data                 = new Satuan();
            $data->satuan_id      = $request->satuan_id;
            $data->satuan_nama    = $request->satuan_nama;
            $data->is_aktif       = 1;
            $data->client_id      = $clientId;
            $data->created_by     = Auth::user()->username;
            $data->updated_by     = Auth::user()->username;
            
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data Satuan']);
            }
            return response()->json(['success' => true,'message' => 'Data Satuan berhasil disimpan','data'=>$data]);


        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Satuan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        // Tidak ada update karena data sedikit
        // Jika ada perubahaan maka perlu dihapus lalu dibuat lagi
    }

    public function delete(Request $request, $satuan_id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
    
            $satuan = Satuan::where('satuan_id', $satuan_id)->where('client_id',$clientId)->update([
                'satuan_id' => $satuan_id.'-DEL'.date('YmdHis'),
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'data satuan berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Satuan tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    // public function getSatuanId($clientId) 
    // {
    //     try {
    //         $id = $clientId.'-ST'.'-0001';
    //         $maxId = Satuan::withTrashed()->where('satuan_id','LIKE',$clientId.'-ST'.'%')->max('satuan_id');
    //         if (!$maxId) { $id = $clientId.'-ST'.'-0001'; }
    //         else {
    //             $maxId = str_replace($clientId.'-ST'.'-','',$maxId);
    //             $count = $maxId + 1;
    //             if ($count < 10) { $id = $clientId.'-ST'.'-000'.$count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $clientId.'-ST'.'-00'.$count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-ST'.'-0'.$count; } 
    //             else { $id = $clientId.'-ST'.'-'.$count; } 
    //         }
    //         return $id;
    //     } catch(\Exception $e){
    //         return 'ST'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
    //     }
    // }
}
