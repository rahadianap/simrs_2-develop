<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

use Modules\HumanResource\Entities\Jabatan;

class MasterJabatanController extends Controller
{

    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }
    

    public function lists(Request $request)
    {
        try {
            $keyword = '%%';
            $aktif = 'true';

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }
            $perPage = Jabatan::where('client_id',$this->clientId)->count();
                        
            $lists = Jabatan::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where(function($q) use ($keyword) {
                    $q->where('jabatan_nama','ILIKE',$keyword)
                    ->orWhere('jabatan_id','ILIKE',$keyword);
                })->orderBy('jabatan_nama', 'ASC')
                ->paginate($perPage);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan daftar. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request,$jabatanId)
    {
        try {
            $data = Jabatan::where('jabatan_id',$jabatanId)->where('client_id',$this->clientId)->first();

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
            $id = $this->getJabatanId();
            
            $data = new Jabatan();
            $data->jabatan_id = $id;
            $data->jabatan_nama = $request->jabatan_nama;
            $data->deskripsi = $request->deskripsi;
            $data->tanggung_jawab = $request->tanggung_jawab;
            $data->tgl_berlaku = $request->tgl_berlaku;
            $data->status = 'AKTIF';
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
            $data = Jabatan::where('jabatan_id',$request->jabatan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data->jabatan_nama = $request->jabatan_nama;
            $data->deskripsi = $request->deskripsi;
            $data->tanggung_jawab = $request->tanggung_jawab;
            $data->tgl_berlaku = $request->tgl_berlaku;
            $data->client_id = $this->clientId;
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
            $data = Jabatan::where('jabatan_id',$jabatanId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
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
            $id = $this->clientId.'.POS-0001';
            $maxId = Jabatan::withTrashed()->where('client_id', $this->clientId)->where('jabatan_id','LIKE',$this->clientId.'.POS-%')->max('jabatan_id');
            if (!$maxId) { $id = $this->clientId.'.POS-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.POS-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.POS-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.POS-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.POS-0'.$count; } 
                else { $id = $this->clientId.'.POS-'.$count; } 
            }
            return $id;
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
