<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\DokterJadwal;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\Bridging;

use DB;

class DokterController extends Controller
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
        try{
            $keyword = '%%';
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            $perpage = 20;
            if($request->has('per_page')) {
                $perpage = $request->input('per_page');
                if($perpage == 'ALL') {
                    $perpage = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->count();
                } 
            }
            $data = Dokter::where('client_id',$this->clientId)
                    ->select('dokter_id','dokter_nama','smf_id','no_sip','tgl_akhir_sip','is_aktif')
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('dokter_nama','ILIKE',$keyword)
                        ->orWhere('dokter_id','ILIKE',$keyword);
                    })->with('smf')
                    ->orderBy('dokter_nama', 'ASC')->paginate($perpage);
            
            foreach($data->items() as $item) {
                $item['bridging'] = Bridging::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('local_resource_id',$item['dokter_id'])
                    ->select(
                        'bridging_id',
                        'bridging_group',
                        'local_resource_id',
                        'local_resource_name',
                        'bridging_resource_id',
                        'bridging_resource_name',
                        'bridging_sub_resource_id',
                        'bridging_sub_resource_name'
                    )->get();
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data dokter tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function listsDokUnit(Request $request, $unit_id)
    {
        try{
            $keyword = '%%';
            $active = 'true';

            $perpage = 20;
            if($request->has('per_page')) {
                $perpage = $request->input('per_page');
                if($perpage == 'ALL') {
                    $perpage = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->count();
                } 
            }
            $data = DB::connection('dbclient')->table('tb_dokter as dt')
                    ->join('tb_dokter_unit as dtu','dt.dokter_id','=','dtu.dokter_id')
                    ->join('tb_unit as u','dtu.unit_id','=','dtu.unit_id')
                    ->where('dt.client_id',$this->clientId)
                    ->where('dt.is_aktif','1')
                    ->where('dtu.unit_id','=',$unit_id)
                    ->select('dt.dokter_id','dt.dokter_nama','dtu.dokter_unit_id','u.unit_id','u.unit_nama')
                    ->orderBy('dt.dokter_nama', 'ASC')->paginate($perpage);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data dokter tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $dokterId)
    {
        try{
            $data = Dokter::where('client_id',$this->clientId)->where('dokter_id', $dokterId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            }

            $units = DokterUnit::where('dokter_id',$dokterId)->where('is_aktif',1)->where('client_id',$this->clientId)
            ->select('dokter_unit_id','dokter_id','unit_id')->get();
            $unitDokter = [];
            $i = 0;
            foreach($units as $unit) {
                $unitpelayanan = UnitPelayanan::where('client_id',$this->clientId)
                    ->where('unit_id', $unit['unit_id'])
                    ->where('is_aktif',1)
                    ->select('unit_nama','unit_id')
                    ->first();

                $jadwal = DokterJadwal::select("*", DB::raw("CONCAT(mulai,' : ',selesai) as jam_praktik"))
                    ->where('client_id',$this->clientId)
                    ->where('dokter_id',$dokterId)
                    ->where('unit_id',$unit['unit_id'])
                    ->where('is_aktif',1)->get();

                if($unitpelayanan) {
                    $unit['unit_nama'] = $unitpelayanan->unit_nama;
                    $unit['jadwal'] = $jadwal;
                    $unitDokter[$i] = $unit;
                    $i++;
                }
            }

            $data['units'] = $unitDokter;
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam pengambilan data.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            $id = $this->getDokterId();
            
            $data = new Dokter();
            $data->dokter_id = $id;
            $data->dokter_nama = $request->dokter_nama;
            $data->spesialis_id = $request->spesialis_id;
            $data->pend_akhir = $request->pend_akhir;
            $data->smf_id = $request->smf_id;
            $data->biografi = $request->biografi;
            $data->url_avatar = $request->url_avatar; 
            $data->no_registrasi = $request->no_registrasi;
            $data->no_sip = $request->no_sip;
            $data->tgl_akhir_sip = $request->tgl_akhir_sip;
            $data->status_kerjasama = $request->status_kerjasama;
            $data->npwp = $request->npwp;
            $data->rekening = $request->rekening;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tgl_lahir = $request->tgl_lahir;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->no_telepon = $request->no_telepon;
            $data->email = $request->email;
            $data->alamat = $request->alamat;
            $data->user_id = $request->user_id; // berhubungan dengan member
            $data->status = $request->status;
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
            $dokterId = $request->dokter_id;
            $data = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id', $dokterId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }
            $data->dokter_nama = $request->dokter_nama;
            $data->spesialis_id = $request->spesialis_id;
            $data->pend_akhir = $request->pend_akhir;
            $data->smf_id = $request->smf_id;
            $data->biografi = $request->biografi;
            $data->url_avatar = $request->url_avatar;
            $data->no_registrasi = $request->no_registrasi;
            $data->no_sip = $request->no_sip;
            $data->tgl_akhir_sip = $request->tgl_akhir_sip;
            $data->status_kerjasama = $request->status_kerjasama;
            $data->npwp = $request->npwp;
            $data->rekening = $request->rekening;
            $data->tempat_lahir = $request->tempat_lahir;
            $data->tgl_lahir = $request->tgl_lahir;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->no_telepon = $request->no_telepon;
            $data->user_id = $request->user_id; 
            $data->email = $request->email;
            $data->alamat = $request->alamat;
            $data->status = $request->status;
            $data->is_aktif = $request->is_aktif;
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

    public function delete(Request $request, $dokterId)
    {
        try{
            $data = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id', $dokterId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Dokter.']);
            }
            return response()->json(['success' => true,'message' => 'data Dokter berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function avatar(Request $request){
        try {            
            $id = '';
            if($request->has('dokter_id')) { $id = $request->dokter_id; }
            
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            } 
            
            $path = Storage::disk('avatars')->putFile('avatars/doctors', $request->file('image'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url;

            if ($id == '') {
                return response()->json(['success'=>true, 'message'=>'avatar berhasil diupload', 'data'=>$data]);
            } 
            else {
                $dokter = Dokter::where('dokter_id',$id)->first();
                $dokter->url_avatar = $path_url;
                $success = $dokter->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'Avatar gagal diupload']);
                }                
                return response()->json(['success'=>true, 'message'=>'avatar berhasil diupload' ,'data'=>$data]);
            }
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menyimpan avatar.','error'=> $e->getMessage()]);
        }
    }

    public function getDokterId() 
    {
        try {
            $id = $this->clientId.'.DOK-0001';
            $maxId = Dokter::withTrashed()->where('client_id', $this->clientId)->where('dokter_id','LIKE',$this->clientId.'.DOK-%')->max('dokter_id');
            if (!$maxId) { $id = $this->clientId.'.DOK-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.DOK-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.DOK-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.DOK-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.DOK-0'.$count; } 
                else { $id = $this->clientId.'.DOK-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.DOK-' . Uuid::uuid4()->getHex();
        }
    }
}
