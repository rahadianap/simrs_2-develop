<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\SubTindakan;
use Modules\MasterData\Entities\UnitTindakan;
use Modules\MasterData\Entities\TindakanBhp;

use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\Laboratorium\Entities\LabTemplate;
use Modules\Laboratorium\Entities\LabNormal;

class TindakanController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request,$klasifikasi = null)
    {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = Tindakan::where('client_id',$this->clientId)->count(); }
            }
            
            if($klasifikasi == null) {
                $klasifikasi = '%%';
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            $list = Tindakan::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('klasifikasi','ILIKE',$klasifikasi)
                    ->where(function($q) use ($keyword) {
                        $q->where('tindakan_nama','ILIKE',$keyword)
                        ->orWhere('tindakan_id','ILIKE',$keyword);
                    })
                    ->with(['kelompok_tindakan','kelompok_tagihan','kelompok_vclaim'])
                    ->orderBy('klasifikasi','ASC')->orderBy('tindakan_nama','ASC')->paginate($per_page); 
            
            foreach($list->items() as $item) {
                if($item['klasifikasi'] == 'LAB') {
                    $item['labItems'] = LabTemplate::where('tindakan_id',$item['tindakan_id'])
                        ->where('client_id',$this->clientId)
                        ->where('is_aktif',1)
                        ->orderBy('klasifikasi','ASC')
                        ->orderBy('no_urut','ASC')
                        ->get();
                }
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List tindakan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = $this->dataTindakan($id);
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'Data tidak ditemukan']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian tindakan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function dataTindakan($id) {
        try {
            $list = Tindakan::where('tindakan_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$list) { return null; }            
            
            //sub tindakan
            $list['subTindakan'] = SubTindakan::where('tindakan_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            //unit tindakan
            $unit = UnitTindakan::where('tindakan_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            $unitLists = []; $k = 0;
            if($unit) {
                foreach($unit as $dt) {
                    $unitservice = UnitPelayanan::where('unit_id',$dt['unit_id'])
                        ->where('client_id',$this->clientId)
                        ->where('is_aktif',1)
                        ->select('unit_id','unit_nama','kepala_unit')
                        ->first();
                    
                    if($unitservice) {
                        $dt['unit_nama'] = $unitservice->unit_nama;
                        $dt['kepala_unit'] = $unitservice->kepala_unit;
                        $unitLists[$k] = $dt;
                        $k++;
                    }
                }
                $list['unitTindakan'] = $unitLists;
            }

            //bhp tindakan
            $bhp = TindakanBhp::where('tindakan_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            $bhpLists = []; $i = 0;
            if($bhp) {
                foreach($bhp as $dt) {
                    $produk = Produk::where('produk_id',$dt['produk_id'])
                        ->where('client_id',$this->clientId)
                        ->where('is_aktif',1)
                        ->select('produk_id','produk_nama','jenis_produk')
                        ->first();
                    if($produk) {
                        $dt['produk_nama'] = $produk->produk_nama;
                        $bhpLists[$i] = $dt;
                        $i++;
                    }
                }
                $list['bhpTindakan'] = $bhpLists;
            }
            //item hasil lab
            $labItems = LabTemplate::where('tindakan_id',$id)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->orderBy('no_urut','ASC')
                ->orderBy('klasifikasi','ASC')
                ->get();

            foreach($labItems as $item) {
                $item['normal'] = LabNormal::where('client_id',$this->clientId)
                    ->where('lab_hasil_id',$item['lab_hasil_id'])
                    ->where('is_aktif',1)
                    ->get();
            }

            $list['labItems'] = $labItems;

            return $list;
        }
        catch(\Exception $e) { return null; }
    }

    public function create(Request $request)
    {
        try {
            $request->validate(['tindakan_nama' => 'required',]);
            $id = $this->getTindakanId();
            
            DB::connection('dbclient')->beginTransaction();
            
            $data                       = new Tindakan();
            $data->tindakan_id          = $id;
            $data->tindakan_nama        = $request->tindakan_nama;
            $data->klasifikasi          = $request->klasifikasi;
            $data->kelompok_tindakan_id = $request->kelompok_tindakan_id;
            $data->meta_data            = $request->meta_data;
            $data->satuan               = $request->satuan;
            $data->is_paket             = $request->is_paket;
            $data->is_hitung_admin      = $request->is_hitung_admin;
            $data->is_diskon            = $request->is_diskon;
            $data->is_cito              = $request->is_cito;            
            $data->is_tampilkan_dokter  = $request->is_tampilkan_dokter;
            $data->is_lab_hasil         = $request->is_lab_hasil;
            $data->is_kamar             = $request->is_kamar;
            $data->rl_kode              = $request->rl_kode;
            $data->kelompok_tagihan_id  = $request->kelompok_tagihan_id;
            $data->kelompok_vklaim_id   = $request->kelompok_vklaim_id;
            $data->deskripsi            = $request->deskripsi;
            $data->is_aktif             = $request->is_aktif;
            $data->client_id            = $this->clientId;
            $data->created_by           = Auth::user()->username;
            $data->updated_by           = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data tindakan.']);
            }

            if($request->subTindakan) {                
                foreach($request->subTindakan as $dt) {
                    $sub = new SubTindakan();
                    $sub->detail_id = $id.'-'.Uuid::uuid4()->getHex();
                    $sub->tindakan_id = $id;
                    $sub->tindakan_nama = $request->tindakan_nama;
                    $sub->sub_tindakan_id = $dt['sub_tindakan_id'];
                    $sub->sub_tindakan_nama = $dt['sub_tindakan_nama'];
                    $sub->jumlah = $dt['jumlah'];
                    $sub->satuan = null;
                    $sub->is_aktif = true;
                    $sub->client_id = $this->clientId;
                    $sub->created_by = Auth::user()->username;
                    $sub->updated_by = Auth::user()->username;
                    
                    $success = $sub->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data sub tindakan.']);
                    }
                }
            }

            DB::connection('dbclient')->commit();
            
            $data = $this->dataTindakan($id);
            return response()->json(['success' => true,'message' => 'Data Tindakan berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'tindakan_nama' => 'required',
            ]);

            $tindakan_id = $request->tindakan_id;
            $data = Tindakan::where('tindakan_id', $tindakan_id)->where('client_id',$this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tindakan dengan nama '. $request->tindakan_nama .' tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $success = $data->update([
                $data->tindakan_nama        = $request->tindakan_nama,
                $data->klasifikasi          = $request->klasifikasi,
                $data->kelompok_tindakan_id = $request->kelompok_tindakan_id,
                $data->meta_data            = $request->meta_data,
                $data->satuan               = $request->satuan,
                $data->is_paket             = $request->is_paket,
                $data->is_diskon            = $request->is_diskon,
                $data->is_cito              = $request->is_cito,
                $data->is_hitung_admin      = $request->is_hitung_admin,
                $data->is_tampilkan_dokter  = $request->is_tampilkan_dokter,
                $data->is_lab_hasil         = $request->is_lab_hasil,
                $data->is_kamar             = $request->is_kamar,
                $data->rl_kode              = $request->rl_kode,
                $data->kelompok_tagihan_id  = $request->kelompok_tagihan_id,
                $data->kelompok_vklaim_id   = $request->kelompok_vklaim_id,
                $data->deskripsi            = $request->deskripsi,
                $data->is_aktif             = $request->is_aktif,
                $data->updated_by        = Auth::user()->username,
            ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data sub tindakan.']);
            }

            /**update sub tindakan*/
            if($request->subTindakan) {                
                foreach($request->subTindakan as $dt) {
                    $sub = SubTindakan::where('tindakan_id',$tindakan_id)
                        ->where('sub_tindakan_id',$dt['sub_tindakan_id'])
                        ->where('is_aktif',1)->where('client_id',$this->clientId)->first();

                    if(!$sub) {
                        $sub = new SubTindakan();
                        $sub->detail_id = $tindakan_id.'-'.Uuid::uuid4()->getHex();
                        $sub->tindakan_id = $tindakan_id;
                        $sub->tindakan_nama = $request->tindakan_nama;
                        $sub->sub_tindakan_id = $dt['sub_tindakan_id'];
                        $sub->sub_tindakan_nama = $dt['sub_tindakan_nama'];
                        $sub->client_id = $this->clientId;
                        $sub->created_by = Auth::user()->username;
                    }

                    $sub->jumlah = $dt['jumlah'];
                    $sub->satuan = null;
                    $sub->is_aktif = $dt['is_aktif'];
                    $sub->updated_by = Auth::user()->username;
                    
                    $success = $sub->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data sub tindakan.']);
                    }
                }
            }
            DB::connection('dbclient')->commit();

            $data = $this->dataTindakan($tindakan_id);
            return response()->json(['success'=>true,'message'=>'Data tindakan berhasil diubah.','data'=> $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Tindakan '. $request->tindakan_nama .' tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $tindakan_id)
    {
        try {
            $data = Tindakan::where('tindakan_id', $tindakan_id)
                ->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan penghapusan data. Data tidak ditemukan.']);
            }
            /**sub tindakan */
            $detail = SubTindakan::where('tindakan_id',$tindakan_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $success = Tindakan::where('tindakan_id', $tindakan_id)
                ->where('client_id',$this->clientId)
                ->update([ 'updated_by' => Auth::user()->username, 'updated_at' => now(), 'is_aktif' => 0 ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data tindakan.']);
            }

            if($detail) {
                $success = SubTindakan::where('tindakan_id',$tindakan_id)
                    ->where('client_id',$this->clientId)->where('is_aktif',1)
                    ->update([ 'updated_by' => Auth::user()->username, 'updated_at' => now(), 'is_aktif' => 0 ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data sub tindakan.']);
                }
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'data berhasil dinonaktifkan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Menonaktifkan Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getTindakanId() 
    {
        try {
            $id = $this->clientId.'-TDK0001';
            $maxId = Tindakan::withTrashed()->where('tindakan_id','LIKE',$this->clientId.'-TDK%')->max('tindakan_id');
            if (!$maxId) { $id = $this->clientId.'-TDK0001'; }
            else {
                $maxId = str_replace($this->clientId.'-TDK','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-TDK000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-TDK00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-TDK0'.$count; } 
                else { $id = $this->clientId.'-TDK'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'TDK'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }

    public function addUnit(Request $request)
    {
        try {           
            $request->validate([ 'tindakan_id' => 'required', ]);

            $units = $request->unitTindakan;
            DB::connection('dbclient')->beginTransaction();
            foreach($units as $dt) {
                $exist = UnitTindakan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$dt['unit_id'])->where('tindakan_id',$request->tindakan_id)->first();
                if(!$exist) {
                    $data = new UnitTindakan();
                    $data->unit_tindakan_id     = $this->getUnitTindakanId($this->clientId);
                    $data->unit_id              = $dt['unit_id'];
                    $data->tindakan_id          = $request->tindakan_id;
                    $data->is_aktif             = true;
                    $data->client_id            = $this->clientId;
                    $data->created_by           = Auth::user()->username;
                    $data->updated_by           = Auth::user()->username;
                    $success = $data->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Unit Tindakan']);
                    }
                }
            }
            DB::connection('dbclient')->commit();            
            return response()->json(['success' => true,'message' => 'Data Unit Tindakan berhasil disimpan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Unit Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getUnitTindakanId($clientId) 
    {
        try {
            $id = $clientId.'-UTK'.'-00001';
            $maxId = UnitTindakan::withTrashed()->where('unit_tindakan_id','LIKE',$clientId.'-UTK'.'%')->max('unit_tindakan_id');

            if (!$maxId) { $id = $clientId.'-UTK'.'-00001'; }
            else {
                $maxId = str_replace($clientId.'-UTK'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-UTK'.'-0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-UTK'.'-000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-UTK'.'-00'.$count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $clientId.'-UTK'.'-0'.$count; } 
                else { $id = $clientId.'-UTK'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'UTK'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

    /**
     * mapping BHP
     */
    public function addProduct(Request $request)
    {
        try {           
            $request->validate([ 'tindakan_id' => 'required', ]);

            $bhps = $request->bhpTindakan;
            DB::connection('dbclient')->beginTransaction();
            foreach($bhps as $dt) {
                $exist = TindakanBhp::where('client_id',$this->clientId)->where('is_aktif',1)->where('produk_id',$dt['produk_id'])->where('tindakan_id',$request->tindakan_id)->first();
                if(!$exist) {
                    $data = new TindakanBhp();
                    $data->tindakan_bhp_id  = $this->getBhpTindakanId($this->clientId);
                    $data->produk_id        = $dt['produk_id'];
                    $data->tindakan_id      = $request->tindakan_id;
                    $data->tindakan_nama    = $request->tindakan_nama;
                    $data->produk_nama      = $dt['produk_nama'];
                    $data->jenis_produk     = $dt['jenis_produk'];
                    $data->jumlah           = $dt['jumlah'];
                    $data->satuan           = $dt['satuan'];
                    $data->is_aktif         = true;
                    $data->client_id        = $this->clientId;
                    $data->created_by       = Auth::user()->username;
                    $data->updated_by       = Auth::user()->username;
                    $success = $data->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Bhp Tindakan']);
                    }
                }
            }
            DB::connection('dbclient')->commit();            
            return response()->json(['success' => true,'message' => 'Data Bhp Tindakan berhasil disimpan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Bhp Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getBhpTindakanId($clientId) 
    {
        try {
            $id = $clientId.'-BHP'.'-00001';
            $maxId = TindakanBhp::withTrashed()->where('tindakan_bhp_id','LIKE',$clientId.'-BHP'.'%')->max('tindakan_bhp_id');

            if (!$maxId) { $id = $clientId.'-BHP'.'-00001'; }
            else {
                $maxId = str_replace($clientId.'-BHP'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-BHP'.'-0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-BHP'.'-000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-BHP'.'-00'.$count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $clientId.'-BHP'.'-0'.$count; } 
                else { $id = $clientId.'-BHP'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'BHP'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

    public function deleteProduct(Request $request, $tindakan_bhp_id)
    {
        try {
            $data = TindakanBhp::where('tindakan_bhp_id', $tindakan_bhp_id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'data berhasil dinonaktifkan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Menonaktifkan Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    /**
     * mapping Hasil Lab
     */
    public function addLabTemplate(Request $request)
    {
        try {           
            $request->validate([ 'tindakan_id' => 'required', ]);

            $exist = LabTemplate::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('lab_hasil_id',$request->lab_hasil_id)
                ->where('tindakan_id',$request->tindakan_id)
                ->first();

            if(!$exist) {
                $data = new LabTemplate();
                $data->lab_template_id  = $this->getLabTemplateId($this->clientId);
                $data->lab_hasil_id     = $request->lab_hasil_id;
                $data->tindakan_id      = $request->tindakan_id;
                $data->tindakan_nama    = $request->tindakan_nama;
                $data->hasil_nama       = $request->hasil_nama;
                $data->klasifikasi      = $request->klasifikasi;
                $data->sub_klasifikasi  = $request->sub_klasifikasi;
                $data->no_urut          = $request->no_urut;
                $data->is_aktif         = true;
                $data->client_id        = $this->clientId;
                $data->created_by       = Auth::user()->username;
                $data->updated_by       = Auth::user()->username;
                $success = $data->save();

                if(!$success) {
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data']);
                }
            }
            return response()->json(['success' => true,'message' => 'Data Item hasil lab Tindakan berhasil disimpan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambahkan item hasil lab tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function updateLabTemplate(Request $request)
    {
        try {           
            $request->validate([ 'tindakan_id' => 'required', ]);

            $exist = LabTemplate::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('lab_hasil_id',$request->lab_hasil_id)
                ->where('tindakan_id',$request->tindakan_id)
                ->first();

            if(!$exist) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data. data tidak ditemukan.']);
            }

            $success = LabTemplate::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('lab_hasil_id',$request->lab_hasil_id)
                ->where('tindakan_id',$request->tindakan_id)
                ->update([
                    'no_urut'=>$request->no_urut, 
                    'klasifikasi'=>$request->klasifikasi,
                    'sub_klasifikasi'=>$request->sub_klasifikasi,
                    'updated_by'=> Auth::user()->username
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data']);
            }
            
            return response()->json(['success' => true,'message' => 'Data Item hasil lab Tindakan berhasil disimpan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambahkan item hasil lab tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function deleteLabTemplate(Request $request, $id)
    {
        try {
            $data = LabTemplate::where('lab_template_id', $id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'data berhasil dinonaktifkan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Menonaktifkan Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getLabTemplateId($clientId) 
    {
        try {
            $id = $clientId.'-TLB00001';
            $maxId = LabTemplate::withTrashed()->where('lab_template_id','ILIKE',$clientId.'-TLB%')->max('lab_template_id');

            if (!$maxId) { $id = $clientId.'-TLB00001'; }
            else {
                $maxId = str_replace($clientId.'-TLB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-TLB000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-TLB00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-TLB0'.$count; } 
                else { $id = $clientId.'-TLB'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'TLB'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }


}
