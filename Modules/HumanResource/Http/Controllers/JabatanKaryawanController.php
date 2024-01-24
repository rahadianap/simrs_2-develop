<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\HumanResource\Entities\HRJabatan;
use Modules\HumanResource\Entities\Jabatan;
use Modules\MasterData\Entities\UnitPelayanan;

class JabatanKaryawanController extends Controller
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
                $lists = HRJabatan::where('client_id',$this->clientId)
                    ->where('is_aktif',true)->where('karyawan_id',$karyawanId)->get();

                return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    
        
            }
            catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Gagal menampilkan daftar. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
            }
    }

    public function data(Request $request,$jabatanId)
    {
        try {
            $data = HRJabatan::where('hr_jabatan_id',$jabatanId)->where('client_id',$this->clientId)->first();

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
            $jabatan = Jabatan::where('jabatan_id',$request->jabatan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$jabatan) {
                return response()->json(['success' => false, 'message' => 'master data jabatan tidak ditemukan.']);
            }
            $unit = UnitPelayanan::where('unit_id',$request->unit_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'master data unit tidak ditemukan.']);
            }
            $id = $this->getJabatanId();
            
            $data = new HRJabatan();
            $data->hr_jabatan_id = $id;
            $data->karyawan_id = $request->karyawan_id;
            $data->jabatan_id = $request->jabatan_id;
            $data->jabatan_nama = $jabatan->jabatan_nama;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $unit->unit_nama;
            $data->tgl_mulai = $request->tgl_mulai;
            $data->tgl_selesai = $request->tgl_selesai;
            $data->is_selesai = $request->is_selesai;            
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
            $data = HRJabatan::where('hr_jabatan_id',$request->hr_jabatan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $jabatan = Jabatan::where('jabatan_id',$request->jabatan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$jabatan) {
                return response()->json(['success' => false, 'message' => 'master data jabatan tidak ditemukan.']);
            }
            $unit = UnitPelayanan::where('unit_id',$request->unit_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'master data unit tidak ditemukan.']);
            }
            $id = $this->getJabatanId();
            
            $data->jabatan_id = $request->jabatan_id;
            $data->jabatan_nama = $jabatan->jabatan_nama;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $unit->unit_nama;
            $data->tgl_mulai = $request->tgl_mulai;
            $data->tgl_selesai = $request->tgl_selesai;
            $data->is_selesai = $request->is_selesai;            
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

    public function delete(Request $request, $jabatanId)
    {
        try {
            $data = HRJabatan::where('hr_jabatan_id',$jabatanId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
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

    public function getJabatanId()
    {
        try {
            $id = $this->clientId.'-'.date('Ymd').'.JBT0001';
            $maxId = HRJabatan::withTrashed()->where('client_id', $this->clientId)->where('hr_jabatan_id','LIKE',$this->clientId.'-'.date('Ymd').'.JBT%')->max('hr_jabatan_id');
            if (!$maxId) { $id = $this->clientId.'-'.date('Ymd').'.JBT0001'; }
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'.JBT','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'.JBT000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'.JBT00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'.JBT0'.$count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'.JBT'.$count; } 
            }
            return $id;
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
