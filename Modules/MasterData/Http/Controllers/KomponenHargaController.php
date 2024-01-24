<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\KomponenHarga;

class KomponenHargaController extends Controller
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
                if($per_page == "ALL"){ $per_page = KomponenHarga::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KomponenHarga::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('komp_harga_id','ILIKE',$keyword)
                        ->orWhere('komp_harga','ILIKE',$keyword);
                    })->orderBy('komp_harga','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List komponen harga tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $list = KomponenHarga::where('komp_harga_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Komponen harga tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'komp_harga' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $this->getKomponenHargaId();
            
            $data                   = new KomponenHarga();
            $data->komp_harga_id    = $id;
            $data->komp_harga       = strtoupper($request->komp_harga);
            $data->deskripsi        = $request->deskripsi;
            $data->is_jasa_dokter   = $request->is_jasa_dokter;
            $data->is_hitung_pajak  = $request->is_hitung_pajak;
            $data->is_auto_hitung   = $request->is_auto_hitung;
            $data->jenis_pajak      = $request->jenis_pajak;            
            $data->client_id        = $this->clientId;
            $data->is_aktif         = 1;
            $data->created_by       = Auth::user()->username;
            $data->updated_by       = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data komponen harga.']);
            }
            return response()->json(['success' => true,'message' => 'Data komponen harga berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah komponen harga tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'komp_harga' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $request->komp_harga_id;
            $data = KomponenHarga::where('client_id',$this->clientId)->where('komp_harga_id', $id)->first();            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data komponen harga tidak ditemukan']);
            }
            
            $success = KomponenHarga::where('client_id',$this->clientId)
                ->where('komp_harga_id', $id)
                ->update([
                    'komp_harga'            => strtoupper($request->komp_harga),
                    'deskripsi'             => $request->deskripsi,
                    'is_jasa_dokter'        => $request->is_jasa_dokter,
                    'is_hitung_pajak'       => $request->is_hitung_pajak,
                    'is_auto_hitung'        => $request->is_auto_hitung,
                    'jenis_pajak'           => $request->jenis_pajak,
                    'is_aktif'              => $request->is_aktif,
                    'updated_by'            =>Auth::user()->username
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data komponen harga']);
            }
            return response()->json(['success'=>true,'message'=>'Data komponen harga berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data komponen harga tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = KomponenHarga::where('komp_harga_id', $id)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus komponen harga tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getKomponenHargaId() 
    {
        try {
            $id = $this->clientId.'-KOM0001';
            $maxId = KomponenHarga::withTrashed()->where('komp_harga_id','LIKE',$this->clientId.'-KOM%')->max('komp_harga_id');
            if (!$maxId) { $id = $this->clientId.'-KOM0001'; }
            else {
                $maxId = str_replace($this->clientId.'-KOM','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-KOM000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-KOM00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-KOM0'.$count; } 
                else { $id = $this->clientId.'-KOM'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'-KOM'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}