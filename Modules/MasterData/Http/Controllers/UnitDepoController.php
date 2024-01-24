<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\DepoUnit;


class UnitDepoController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request,$unitId)
    {
        try {
            $list = DepoUnit::leftjoin('tb_depo', 'tb_depo_unit.depo_id', '=', 'tb_depo.depo_id')
                ->where('tb_depo_unit.is_aktif',1)
                ->where('tb_depo_unit.client_id',$this->clientId)
                ->select('tb_depo_unit.depo_unit_id','tb_depo.depo_id','tb_depo.depo_nama','tb_depo_unit.is_depo_utama')
                ->get();
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List depo unit tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = DepoUnit::where('depo_unit_id',$id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->select('depo_unit_id','depo_id','unit_id','is_depo_utama','depo_nama')
                ->first(); 

            if(!$data) {
                return response()->json(['success'=>false,'message'=>'data unit depo tidak ditemukan.']);
            }
            $data['depo_nama'] = Depo::where('client_id',$this->clientId)
                ->where('depo_id',$data->depo_id)
                ->select('depo_nama')
                ->first()->depo_nama; 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian tindakan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    // public function unitDepoAllProduk(Request $request, $unitId)
    // {
    //     try {
    //         $data = DepoUnit::where('unit_id',$unitId)
    //             ->where('is_aktif',1)->where('client_id',$this->clientId)
    //             ->select('depo_id')->get(); 

    //         if(!$data) {
    //             return response()->json(['success'=>false,'message'=>'data unit depo produk tidak ditemukan.']);
    //         }
    //         $data['depo_nama'] = Depo::where('client_id',$this->clientId)
    //             ->where('depo_id',$data->depo_id)
    //             ->select('depo_nama')
    //             ->first()->depo_nama; 

    //         return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
    //     } 
    //     catch(\Exception $e) {
    //         return response()->json(['success' => false,'message' =>'Pencarian tindakan tidak ditemukan.','error'=>$e->getMessage()]);
    //     }
    // }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'depo_id' => 'required',
                'unit_id' => 'required',
            ]);
            
            $isExist = DepoUnit::where('unit_id',$request->unit_id)->where('depo_id',$request->depo_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if($isExist) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data Depo unit. Depo unit sudah termapping.']);
            }

            $data                 = new DepoUnit();
            $data->depo_unit_id   = $this->getDepoUnitId();
            $data->depo_id        = $request->depo_id;
            $data->unit_id        = $request->unit_id;
            $data->is_depo_utama  = $request->is_depo_utama;
            $data->is_aktif       = 1;
            $data->client_id      = $this->clientId;
            $data->created_by     = Auth::user()->username;
            $data->updated_by     = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data Depo unit']);
            }
            return response()->json(['success' => true,'message' => 'Data Depo unit berhasil disimpan','data'=>$data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Depo unit tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'unit_id' => 'required',
                'depo_id' => 'required',
            ]);

            $id = $request->depo_unit_id;
            $data = DepoUnit::where('depo_unit_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data depo unit tidak ditemukan.']);
            }

            $success = DepoUnit::where('depo_unit_id', $id)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->update(['is_depo_utama'=>$request->is_depo_utama,'updated_by'=>Auth::user()->username]);

            return response()->json(['success'=>true,'message'=>'Data depo unit berhasil diubah.','data'=> $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Depo unit tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $success = DepoUnit::where('depo_unit_id', $id)->where('client_id',$this->clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);
            if(!$success) {
                return response()->json(['success'=>false,'message'=>'data tidak berhasil dihapus']);
            }

            return response()->json(['success'=>true,'message'=>'data berhasil dihapus']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus depo unit tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getDepoUnitId() 
    {
        try {
            $id = $this->clientId.'-DPU0001';
            $maxId = DepoUnit::withTrashed()->where('depo_unit_id','LIKE',$this->clientId.'-DPU%')->max('depo_unit_id');
            if (!$maxId) { $id = $this->clientId.'-DPU0001'; }
            else {
                $maxId = str_replace($this->clientId.'-DPU','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-DPU000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-DPU00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-DPU0'.$count; } 
                else { $id = $this->clientId.'-DPU'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'-DPU'.Uuid::uuid4()->getHex();
        }
    }

}
