<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\BedInap;

use Modules\MasterData\Entities\UnitOtorisasi;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterJadwal;
use Modules\MasterData\Entities\DepoUnit;
use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\Bridging;

use Modules\ManajemenUser\Entities\MemberUnit;
use DB;

class UnitPelayananController extends Controller
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
            $aktif = '%true%';
            $jnsRegistrasi = '%%';
            $perPage = 20;

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            if ($request->has('is_aktif')) {
                $aktif = '%' . $request->input('is_aktif') . '%';
            }

            if($request->has('jenis_reg')) {
                if ($request->input('jenis_reg') == "RADIOLOGI") { $jnsPenunjang = "RAD"; }
                else if ($request->input('jenis_reg') == "PATOLOGI ANATOMI" || $request->input('jenis_reg') == "PATOLOGI") { $jnsPenunjang = "PA"; }
                else { $jnsPenunjang = $request->input('jenis_reg'); }
                $jnsRegistrasi = '%'.$jnsPenunjang.'%';
            }
            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                if($perPage == 'ALL') { $perPage = UnitPelayanan::where('client_id',$this->clientId)->count(); }
            }   
            // $jadwalOps = '%%';         
            // if($request->has('jadwal_operasi')){
            //     if($request->input('jadwal_operasi') == true) {
            //         $jnsRegistrasi = '%%';
            //     }
            //     $jadwalOps = '%'.$request->input('jadwal_operasi').'%';
            // }

            $list = UnitPelayanan::where('client_id', $this->clientId)            
                ->where('jenis_registrasi','ILIKE',$jnsRegistrasi)
                ->where('is_aktif', 'ILIKE', $aktif)
                ->where(function($q) use ($keyword) {
                    $q->where('unit_nama', 'ILIKE', $keyword)
                    ->orWhere('unit_id','ILIKE',$keyword);
                })
                ->orderBy('unit_id', 'ASC')->paginate($perPage);
                
            foreach($list->items() as $item) {
                $item['bridging'] = Bridging::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('local_resource_id',$item['unit_id'])
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

                /**ambil ruang unit */
                $rooms = RuangPelayanan::where('unit_id',$item['unit_id'])->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->get();
                foreach($rooms as $r) {
                    $r['beds'] = BedInap::where('ruang_id',$r['ruang_id'])->where('is_aktif',1)
                            ->where('client_id',$this->clientId)->get();
                }

                $dokter = DokterUnit::leftJoin('tb_dokter','tb_dokter_unit.dokter_id','=','tb_dokter.dokter_id')
                    ->where('tb_dokter_unit.unit_id',$item['unit_id']) 
                    ->where('tb_dokter_unit.is_aktif',1) 
                    ->where('tb_dokter_unit.client_id',$this->clientId)
                    ->select([
                        'tb_dokter_unit.dokter_unit_id',
                        'tb_dokter_unit.unit_id',
                        'tb_dokter_unit.dokter_id',
                        'tb_dokter_unit.prefix_no_antrian',
                        'tb_dokter.dokter_nama'])
                    ->get();
                foreach($dokter as $d){
                    $d['jadwal'] = DokterJadwal::where('dokter_id',$d['dokter_id'])->where('is_aktif',1)
                            ->where('client_id',$this->clientId)->get();
                }

                $item['ruang'] = $rooms;
                $item['dokter'] = $dokter;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data unit pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function UnitBpjsLists(Request $request)
    {
        try {
            $keyword = '%%';
            $aktif = '%%';
            $jnsRegistrasi = '%%';
            $perPage = 20;

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            if ($request->has('is_aktif')) {
                $aktif = '%' . $request->input('is_aktif') . '%';
            }

            if($request->has('jenis_reg')) {
                $jnsRegistrasi = '%'.$request->input('jenis_reg').'%';
            }
            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                if($perPage == 'ALL') { $perPage = UnitPelayanan::where('client_id',$this->clientId)->count(); }
            }            

            $list = UnitPelayanan::where('client_id', $this->clientId)            
                ->where('jenis_registrasi','ILIKE',$jnsRegistrasi)
                ->where('is_aktif', 'ILIKE', $aktif)
                ->where(function($q) use ($keyword) {
                    $q->where('unit_nama', 'ILIKE', $keyword)
                    ->orWhere('unit_id','ILIKE',$keyword);
                })
                ->orderBy('unit_id', 'ASC')->paginate($perPage);
            
            foreach($list->items() as $item) {
                $item['bridging'] = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('local_resource_id',$item['unit_id'])
                    //->where('bridging_group','BPJS')
                    ->first();

                // if($dtm) {
                //     $item['kode_bpjs'] = $dtm->bridging_resource_id;
                //     $item['nama_bpjs'] = $dtm->bridging_resource_name;
                // }
                // else {
                //     $item['kode_bpjs'] = null;
                //     $item['nama_bpjs'] = null;
                // }
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data unit pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = UnitPelayanan::where('client_id', $this->clientId)->where('unit_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data['dokter'] = $this->getUnitDokter($id);
            $data['depo'] = $this->getUnitDepo($id);

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data unit pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getId('UNIT');   
            $data = new UnitPelayanan();
            $data->unit_id = $id;
            $data->inisial = strtoupper($request->inisial);
            $data->unit_nama = strtoupper($request->unit_nama);
            $data->kepala_unit = $request->kepala_unit;
            $data->jenis_registrasi = $request->jenis_registrasi;            
            $data->group_unit = $request->group_unit;
            $data->icon_dir = $request->icon_dir;
            $data->icon_url = $request->icon_url;

            $data->is_registrasi = $request->is_registrasi;
            $data->is_rm_baru = $request->is_rm_baru;
            $data->is_unit_rujukan = $request->is_unit_rujukan;
            $data->is_bedah = $request->is_bedah;
            $data->is_unit_external = $request->is_unit_external;
            $data->is_pilih_dokter = $request->is_pilih_dokter;
            $data->is_unit_kiosk = $request->is_unit_kiosk;

            $data->is_aktif = $request->is_aktif;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data unit pelayanan']);
            }
            
            return response()->json(['success' => true, 'message' => 'data unit pelayanan berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menambah unit pelayanan. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {

            $data = UnitPelayanan::where('client_id', $this->clientId)->where('unit_id', $request->unit_id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            $data->inisial = strtoupper($request->inisial);
            $data->unit_nama = strtoupper($request->unit_nama);

            $data->kepala_unit = $request->kepala_unit;
            $data->jenis_registrasi = $request->jenis_registrasi;
            $data->group_unit = $request->group_unit;
            $data->is_registrasi = $request->is_registrasi;
            $data->is_rm_baru = $request->is_rm_baru;
            $data->is_unit_rujukan = $request->is_unit_rujukan;
            $data->is_bedah = $request->is_bedah;
            $data->is_unit_external = $request->is_unit_external;
            $data->is_pilih_dokter = $request->is_pilih_dokter;
            $data->is_unit_kiosk = $request->is_unit_kiosk;

            $data->icon_dir = $request->icon_dir;
            $data->icon_url = $request->icon_url;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data unit pelayanan']);
            }

            return response()->json(['success' => true, 'message' => 'data unit pelayanan berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan unit pelayanan. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = UnitPelayanan::where('unit_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus unit. Data unit tidak ditemukan.']);
            }

            $memberUnit = MemberUnit::where('client_id',$this->clientId)->where('unit_id',$id)->first();

            DB::connection('dbclient')->beginTransaction();

            $data->updated_by = Auth::user()->username;
            $data->is_aktif = 0;
            $success = $data->save();

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam memperbaharui data unit']);
            }

            if($memberUnit) {
                $success = MemberUnit::where('client_id',$this->clientId)->where('unit_id',$id)
                    ->update(['is_aktif'=>0, 'updated_by'=>Auth::user()->username, 'deleted_at' => date('Y-m-d H:i:s')]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam memperbaharui data unit']);
                }
            }

            DB::connection('dbclient')->commit();
            
            return response()->json(['success' => true, 'message' => 'unit pelayanan berhasil dinonaktifkan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data unit pelayanan gagal dinonaktifkan. Error :' . $e->getMessage()]);
        }
    }

    public function getId($prefix)
    {
        try {
            $id = $this->clientId . '-'.$prefix.'0001';
            $maxId = UnitPelayanan::withTrashed()->where('client_id', $this->clientId)->where('unit_id', 'LIKE', $this->clientId . '-'.$prefix.'%')->max('unit_id');
            if (!$maxId) {
                $id = $this->clientId . '-'.$prefix.'0001';
            } else {
                $maxId = str_replace($this->clientId . '-'.$prefix, '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'-'.$prefix.'000'.$count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId.'-'.$prefix.'00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId.'-'.$prefix.'0' . $count;
                } else {
                    $id = $this->clientId.'-'.$prefix.$count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId.'-'.date('Ymd').'-'.$prefix.Uuid::uuid4()->getHex();
        }
    }

    public function unitIcon(Request $request) {
        try {            
            $id = '';
            if($request->has('unit_id')) { $id = $request->unit_id; }
            
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            } 
            
            $path = Storage::disk('avatars')->putFile('avatars/units', $request->file('image'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url;

            if ($id == '') {
                return response()->json(['success'=>true, 'message'=>'avatar berhasil diupload', 'data'=>$data]);
            } 
            else {
                $unit = UnitPelayanan::where('unit_id',$id)->first();
                $unit->icon_url = $path_url;
                $success = $unit->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'Icon gagal diupload']);
                }                
                return response()->json(['success'=>true, 'message'=>'Icon berhasil diupload' ,'data'=>$data]);
            }
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menyimpan icon unit.','error'=> $e->getMessage()]);
        }
    }

    public function getUnitDokter($unitId){
        try {
            $unitdok = DokterUnit::leftJoin('tb_dokter','tb_dokter_unit.dokter_id','=','tb_dokter.dokter_id')
                ->where('tb_dokter_unit.client_id',$this->clientId)
                ->where('tb_dokter_unit.unit_id',$unitId)
                ->where('tb_dokter_unit.is_aktif',true)
                ->select('tb_dokter_unit.dokter_unit_id','tb_dokter_unit.dokter_id','tb_dokter_unit.prefix_no_antrian','tb_dokter.dokter_nama')->get();
            
            if(!$unitdok) { return []; }
            return $unitdok;

            // $dokterlist = [];
            // $i = 0;
            // foreach($unitdok as $dt){
            //     $dok = Dokter::where('dokter_id',$dt['dokter_id'])
            //         ->where('client_id',$this->clientId)->where('is_aktif',true)
            //         ->select('dokter_nama')->first();
            //     if($dok) {
            //         $dt['dokter_nama'] = $dok->dokter_nama;
            //         $dokterlist[$i] = $dt;
            //         $i++;
            //     }
            // }
            //return $dokterlist;
        } catch (\Exception $e) {
            //return null;
            return $e->getMessage();
        }
    }

    public function getUnitDepo($unitId){
        try {
            $unitdepo = DepoUnit::leftJoin('tb_depo','tb_depo_unit.depo_id','=','tb_depo.depo_id')
                ->where('tb_depo_unit.client_id',$this->clientId)
                ->where('tb_depo_unit.unit_id',$unitId)
                ->where('tb_depo_unit.is_aktif',1)
                ->where('tb_depo.is_aktif',1)
                ->select('tb_depo_unit.depo_unit_id','tb_depo_unit.depo_id','tb_depo_unit.unit_id','tb_depo_unit.is_depo_utama','tb_depo.depo_nama')
                ->get();
            
            if(!$unitdepo) { return []; }

            return $unitdepo;

            // $depolist = [];
            // $i = 0;
            // foreach($unitdepo as $dt){
            //     $dok = Depo::where('depo_id',$dt['depo_id'])
            //         ->where('client_id',$this->clientId)->where('is_aktif',1)
            //         ->select('depo_nama')->first();
            //     if($dok) {
            //         $dt['depo_nama'] = $dok->depo_nama;
            //     }
            //     $depolist[$i] = $dt;
            //     $i++; 
            // }
            // return $depolist;
        } catch (\Exception $e) {
            //return null;
            return $e->getMessage();
        }
    }
}
