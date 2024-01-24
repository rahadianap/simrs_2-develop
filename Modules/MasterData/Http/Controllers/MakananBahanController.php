<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\MakananBahan;

use DB;

class MakananBahanController extends Controller
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
        try{
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = MakananBahan::count();
                }
            }

            $data = MakananBahan::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('produk_nama','ILIKE',$keyword);
                    })->orderBy('produk_nama', 'ASC')->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data bahan makanan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $makanan_bahan_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = MakananBahan::where('client_id',$this->clientId)->where('is_aktif',1)->where('makanan_bahan_id', $makanan_bahan_id)->first();
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
            $id = $this->getMakananBahanId();
            
            $data = new MakananBahan();
            $data->makanan_bahan_id = $id;
            $data->makanan_id = $request->makanan_id;
            $data->bahan_id = $request->bahan_id;
            $data->produk_id = $request->produk_id;
            $data->produk_nama = $request->produk_nama;
            $data->jumlah = $request->jumlah;
            $data->satuan = $request->satuan;
            $data->kategori = $request->kategori;
            $data->catatan = $request->catatan;
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
            $makanan_bahan_id = $request->makanan_bahan_id;
            $data = MakananBahan::where('client_id',$this->clientId)->where('is_aktif',1)->where('makanan_bahan_id', $makanan_bahan_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            // $data->makanan_id = $request->makanan_id;
            $data->bahan_id = $request->bahan_id;
            $data->produk_id = $request->produk_id;
            $data->produk_nama = $request->produk_nama;
            $data->jumlah = $request->jumlah;
            $data->satuan = $request->satuan;
            $data->kategori = $request->kategori;
            $data->catatan = $request->catatan;
            $data->is_aktif = 1;
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

    public function delete(Request $request, $makanan_bahan_id)
    {
        try{
            $data = MakananBahan::where('client_id',$this->clientId)->where('is_aktif',1)->where('makanan_bahan_id', $makanan_bahan_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Makanan Bahan.']);
            }
            return response()->json(['success' => true,'message' => 'data Makanan Bahan berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getMakananBahanId() 
    {
        try {
            $id = $this->clientId.'.MIG-0001';
            $maxId = MakananBahan::withTrashed()->where('client_id', $this->clientId)->where('makanan_bahan_id','LIKE',$this->clientId.'.MIG-%')->max('makanan_bahan_id');
            if (!$maxId) { $id = $this->clientId.'.MIG-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.MIG-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.MIG-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.MIG-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.MIG-0'.$count; } 
                else { $id = $this->clientId.'.MIG-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.MIG-' . Uuid::uuid4()->getHex();
        }
    }
}
