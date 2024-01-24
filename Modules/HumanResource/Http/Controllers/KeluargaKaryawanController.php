<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\HumanResource\Entities\HRKeluarga;

class KeluargaKaryawanController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }
    

    public function lists(Request $request, $karyawanId)
    {
        try {
            $lists = HRKeluarga::where('client_id',$this->clientId)
                ->where('is_aktif',true)->where('karyawan_id',$karyawanId)->get();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan daftar. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request,$keluargaId)
    {
        try {
            $data = HRKeluarga::where('hr_keluarga_id',$keluargaId)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getKeluargaId();
            
            $data->hr_keluarga_id = $id;
            $data->karyawan_id = $request->karyawan_id;
            $data->nama_keluarga = $request->nama_keluarga;
            $data->hubungan = $request->hubungan;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->pendidikan = $request->pendidikan;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->catatan = $request->catatan;            
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;            
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data gagal disimpan.']);
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = HRKeluarga::where('hr_keluarga_id',$request->hr_keluarga_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data->nama_keluarga = $request->nama_keluarga;
            $data->hubungan = $request->hubungan;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->pendidikan = $request->pendidikan;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->catatan = $request->catatan;            
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data gagal disimpan.']);
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]); 

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengubah data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $keluargaId)
    {
        try {
            $data = HRKeluarga::where('hr_keluarga_id',$keluargaId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->status = 'NON AKTIF';
            $data->client_id = $this->clientId;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data gagal dihapus.']);
            }
            
            return response()->json(['success' => true, 'message' => 'OK']); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getKeluargaId()
    {
        try {
            $id = $this->clientId.'-'.date('Ymd').'.KLG0001';
            $maxId = HRKeluarga::withTrashed()->where('client_id', $this->clientId)->where('hr_keluarga_id','LIKE',$this->clientId.'-'.date('Ymd').'.KLG%')->max('hr_keluarga_id');
            if (!$maxId) { $id = $this->clientId.'-'.date('Ymd').'.KLG0001'; }
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'.KLG','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'.KLG000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'.KLG00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'.KLG0'.$count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'.KLG'.$count; } 
            }
            return $id;
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
