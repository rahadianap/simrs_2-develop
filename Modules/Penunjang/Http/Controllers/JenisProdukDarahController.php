<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\Penunjang\Entities\JenisProdukDarah;

class JenisProdukDarahController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function lists(Request $request) {
        try {
            $keyword = '%%';
            $aktif = 'true';

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $page = JenisProdukDarah::where('client_id',$this->clientId)->count();
            
            $lists = JenisProdukDarah::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where(function($q) use ($keyword) {
                    $q->where('jenis_produk_darah_id','ILIKE',$keyword)
                    ->orWhere('jenis_produk_darah','ILIKE',$keyword);
                })->orderBy('jenis_produk_darah', 'ASC')
                ->paginate($page);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request) {
        try {
            $exist = JenisProdukDarah::where('jenis_produk_darah',$request->jenis_produk_darah)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if($exist) {
                return response()->json(['success' => false, 'message' => 'jenis produk darah sudah ada.']);
            }
            
            $id = $this->createId();
            $data = new JenisProdukDarah();
            $data->jenis_produk_darah_id = $id;
            $data->jenis_produk_darah = strtoupper($request->jenis_produk_darah);
            $data->inisial = $request->inisial;
            $data->deskripsi = $request->deskripsi;
            $data->tindakan_id = $request->tindakan_id;
            $data->tindakan_nama = $request->tindakan_nama;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data tidak berhasil disimpan.']);
            }
            
            return response()->json(['success' => true, 'message' => 'data berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createId() {
        try {
            $id = $this->clientId.'.PD0001';
            $maxId = JenisProdukDarah::withTrashed()->where('client_id', $this->clientId)
                ->where('jenis_produk_darah_id','LIKE',$this->clientId.'.PD%')->max('jenis_produk_darah_id');
            if (!$maxId) { $id = $this->clientId.'.PD0001'; }
            else {
                $maxId = str_replace($this->clientId.'.PD','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.PD000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.PD00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.PD0'.$count; } 
                else { $id = $this->clientId.'.PD'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.PD' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = JenisProdukDarah::where('jenis_produk_darah_id',$id)
                ->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            
            return response()->json(['success' => true, 'message' => 'Data ditemukan.', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request) {
        try {
            $id = $request->jenis_produk_darah_id;
            $data = JenisProdukDarah::where('client_id',$this->clientId)->where('jenis_produk_darah_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }
            
            $data->inisial = $request->inisial;
            $data->deskripsi = $request->deskripsi;
            $data->tindakan_id = $request->tindakan_id;
            $data->tindakan_nama = $request->tindakan_nama;
            $data->is_aktif = $request->is_aktif;
            $data->client_id = $this->clientId;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data tidak berhasil disimpan.']);
            }
            return response()->json(['success' => true, 'message' => 'data berhasil diubah.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id) {
        try {
            $data = JenisProdukDarah::where('client_id',$this->clientId)
                ->where('jenis_produk_darah_id',$id)
                ->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }
            
            $success = JenisProdukDarah::where('client_id',$this->clientId)
                ->where('jenis_produk_darah_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
            
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data tidak berhasil dihapus.']);
            }
            
            return response()->json(['success' => true, 'message' => 'data berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }
}
