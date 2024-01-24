<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\GolonganProduk;


class GolonganProdukController extends Controller
{
    public function list(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $aktif = 1;
            if($request->has('aktif')) {
                $aktif = $aktif = $request->input('aktif');
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }            
            
            $list = GolonganProduk::where('client_id',$clientId)->where('is_aktif','LIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('golongan_produk_id','LIKE',$keyword)
                        ->orWhere('nama','LIKE',$keyword)
                        ->orWhere('inisial','LIKE',$keyword)
                        ->orWhere('produk_id','LIKE',$keyword);
                    })->orderBy('golongan_produk_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List golongan produk tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    
    public function data(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $list = GolonganProduk::where('golongan_produk_id',$id)->where('client_id',$clientId)->first(); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian golongan produk tidak ditemukan.','error'=>$e->getMessage()]);
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
                'produk_id' => 'required'
            ]);
           
            $data = new GolonganProduk();
            $data->golongan_produk_id   = $this->getGolonganProdukId($clientId);
            $data->produk_id            = $request->produk_id;
            $data->nama                 = $request->nama;
            $data->inisial              = $request->inisial;
            $data->deskripsi            = $request->deskripsi;
            $data->is_aktif             = 1;
            $data->client_id            = $clientId;
            // $data->created_by        = Auth::user()->username;
            $data->created_by           = "hafiizh_create";
            // $data->updated_by        = Auth::user()->username;
            $data->updated_by           = "hafiizh_create";
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produk']);
            }
            return response()->json(['success' => true,'message' => 'Data Golongan Produk berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Golongan Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $id = $request->golongan_produk_id;
            $data = GolonganProduk::where('golongan_produk_id', $id)->where('client_id', $clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data golongan produk tidak ditemukan.']);
            }
            // return $data;
            $data->update([
                $data->produk_id        = $request->produk_id,
                $data->nama             = $request->nama,
                $data->inisial          = $request->inisial,
                $data->deskripsi        = $request->deskripsi,
                $data->is_aktif         = $request->is_aktif,
                // $data->updated_by    = Auth::user()->username,
                $data->updated_by       = "hafiizh_update"
            ]);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Golongan Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');
            
            $data = GolonganProduk::where('golongan_produk_id', $id)->where('client_id',$clientId)->update([
                // 'updated_by' => Auth::user()->username,
                'updated_by' => "hafiizh_delete",
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Golongan Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getGolonganProdukId($clientId) 
    {
        try {
            $id = $clientId.'-GPK'.'-00001';
            $maxId = GolonganProduk::withTrashed()->where('golongan_produk_id','LIKE',$clientId.'-GPK'.'%')->max('golongan_produk_id');

            if (!$maxId) { $id = $clientId.'-GPK'.'-0001'; }
            else {
                $maxId = str_replace($clientId.'-GPK'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-GPK'.'-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-GPK'.'-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-GPK'.'-0'.$count; } 
                else { $id = $clientId.'-GPK'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'GPK'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}
