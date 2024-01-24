<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\HumanResource\Entities\Karyawan;
use Modules\HumanResource\Entities\HRJabatan;
use Modules\HumanResource\Entities\HRPendidikan;
use Modules\HumanResource\Entities\HRKeluarga;
use Modules\HumanResource\Entities\HRDokumen;

use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\UnitPelayanan;

use Modules\HumanResource\Entities\Jabatan;


class MasterKaryawanController extends Controller
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
            $perPage = Karyawan::where('client_id',$this->clientId)->count();
                        
            $lists = Karyawan::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where(function($q) use ($keyword) {
                    $q->where('nama','ILIKE',$keyword)
                    ->orWhere('nip','ILIKE',$keyword)
                    ->orWhere('nik','ILIKE',$keyword);
                })->orderBy('nama', 'ASC')
                ->paginate($perPage);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan daftar. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request,$karyawanId)
    {
        try {
            $data = Karyawan::where('karyawan_id',$karyawanId)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data['keluarga'] = HRKeluarga::where('karyawan_id',$karyawanId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            $data['pendidikan'] = HRPendidikan::where('karyawan_id',$karyawanId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            $data['jabatan'] = HRJabatan::where('karyawan_id',$karyawanId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            $data['dokumen'] = HRDokumen::where('karyawan_id',$karyawanId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
                        
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
            $id = $this->getKaryawanId();
            
            $data = new Karyawan();
            $data->karyawan_id = $id;
            $data->nama = $request->nama;
            $data->nip = $request->nip;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->nik = $request->nik;
            $data->no_telepon = $request->no_telepon;
            $data->email = $request->email;
            $data->tgl_masuk = $request->tgl_masuk;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->jabatan_id = $request->jabatan_id;
            $data->jabatan_nama = $jabatan->jabatan_nama;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $unit->unit_nama;
            $data->url_foto = $request->url_foto;
            $data->keahlian = $request->keahlian;
            $data->status = $request->status;
            $data->rekening_gaji = $request->rekening_gaji;
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
            $data = Karyawan::where('karyawan_id',$request->karyawan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data karyawan tidak ditemukan.']);
            }

            $jabatan = Jabatan::where('jabatan_id',$request->jabatan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$jabatan) {
                return response()->json(['success' => false, 'message' => 'master data jabatan tidak ditemukan.']);
            }

            $unit = UnitPelayanan::where('unit_id',$request->unit_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'master data unit tidak ditemukan.']);
            }
            $id = $this->getKaryawanId();
            
            $data->nama = $request->nama;
            $data->nip = $request->nip;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tanggal_lahir = $request->tanggal_lahir;
            $data->nik = $request->nik;
            $data->no_telepon = $request->no_telepon;
            $data->email = $request->email;
            $data->tgl_masuk = $request->tgl_masuk;
            $data->status_perkawinan = $request->status_perkawinan;
            $data->jabatan_id = $request->jabatan_id;
            $data->jabatan_nama = $jabatan->jabatan_nama;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $unit->unit_nama;
            $data->url_foto = $request->url_foto;
            $data->keahlian = $request->keahlian;
            $data->status = $request->status;
            $data->rekening_gaji = $request->rekening_gaji;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data gagal disimpan .']);
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
            $data = Karyawan::where('karyawan_id',$request->karyawan_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data karyawan tidak ditemukan.']);
            }

            $success = Karyawan::where('karyawan_id',$request->karyawan_id)->where('is_aktif',1)->where('client_id',$this->clientId)
                ->update(['status'=>'NON AKTIF', 'is_aktif'=>false, 'updated_by'=>Auth::user()->username]);
            
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'data karyawan gagal dihapus.']);               
            }
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getKaryawanId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'.KRY-0001';
            $maxId = Karyawan::withTrashed()->where('client_id', $this->clientId)->where('karyawan_id','LIKE', $this->clientId.'-'.date('Ym').'.KRY-%')->max('karyawan_id');
            if (!$maxId) { $id =  $this->clientId.'-'.date('Ym').'.KRY-0001'; }
            else {
                $maxId = str_replace( $this->clientId.'-'.date('Ym').'.KRY-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id =  $this->clientId.'-'.date('Ym').'.KRY-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id =  $this->clientId.'-'.date('Ym').'.KRY-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id =  $this->clientId.'-'.date('Ym').'.KRY-0'.$count; } 
                else { $id =  $this->clientId.'-'.date('Ym').'.KRY-'.$count; } 
            }
            return $id;
        }
        catch (\Exception $e) {
            return null;
        }
    }

    public function uploadFotoKaryawan(Request $request, $karyawanId){
        try {            
            $id = $karyawanId;
            //if($request->has('karyawan_id')) { $id = $request->karyawan_id; }
            
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            } 
            
            $path = Storage::disk('avatars')->putFile('avatars/karyawan', $request->file('image'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url;

            if ($id == '') {
                return response()->json(['success'=>true, 'message'=>'foto karyawan berhasil diupload', 'data'=>$data]);
            } 
            else {
                $karyawan = Karyawan::where('karyawan_id',$id)->first();
                $karyawan->url_foto = $path_url;
                $success = $karyawan->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'Foto gagal diupload']);
                }                
                return response()->json(['success'=>true, 'message'=>'foto berhasil diupload' ,'data'=>$data]);
            }
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menyimpan foto.','error'=> $e->getMessage()]);
        }
    }
}
