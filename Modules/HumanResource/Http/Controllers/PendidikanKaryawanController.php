<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\HumanResource\Entities\HRPendidikan;

class PendidikanKaryawanController extends Controller
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
            $lists = HRPendidikan::where('client_id',$this->clientId)
                ->where('is_aktif',true)->where('karyawan_id',$karyawanId)->get();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan daftar. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $pendidikanId)
    {
        try {
            $data = HRPendidikan::where('hr_pendidikan_id',$pendidikanId)->where('client_id',$this->clientId)->first();

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
            $id = $this->getPendidikanId();

            $data = new HRPendidikan();
            $data->hr_pendidikan_id = $id;
            $data->karyawan_id = $request->karyawan_id;
            $data->jns_pendidikan = $request->jns_pendidikan;
            $data->jenjang = $request->jenjang;
            $data->nama_pendidikan = $request->nama_pendidikan;
            $data->institusi = $request->institusi;
            $data->tahun_lulus = $request->tahun_lulus;
            $data->ipk = $request->ipk;
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
            $data = HRPendidikan::where('hr_pendidikan_id',$request->hr_pendidikan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data->jns_pendidikan = $request->jns_pendidikan;
            $data->jenjang = $request->jenjang;
            $data->nama_pendidikan = $request->nama_pendidikan;
            $data->institusi = $request->institusi;
            $data->tahun_lulus = $request->tahun_lulus;
            $data->ipk = $request->ipk;
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

    public function delete(Request $request, $pendidikanId)
    {
        try {
            $data = HRPendidikan::where('hr_pendidikan_id',$pendidikanId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
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

    public function getPendidikanId()
    {
        try {
            $id = $this->clientId.'-'.date('Ymd').'.EDU0001';
            $maxId = HRPendidikan::withTrashed()->where('client_id', $this->clientId)->where('hr_pendidikan_id','LIKE',$this->clientId.'-'.date('Ymd').'.EDU%')->max('hr_pendidikan_id');
            if (!$maxId) { $id = $this->clientId.'-'.date('Ymd').'.EDU0001'; }
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'.EDU','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'.EDU000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'.EDU00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'.EDU0'.$count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'.EDU'.$count; } 
            }
            return $id;
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
