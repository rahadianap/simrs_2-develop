<?php

namespace Modules\Laboratorium\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\SubTindakan;
use Modules\MasterData\Entities\UnitTindakan;
use Modules\MasterData\Entities\TindakanBhp;

use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\Laboratorium\Entities\LabTemplate;
use Modules\Laboratorium\Entities\LabNormal;

use Modules\Laboratorium\Entities\LabItemHasil;
/**
 * MASTER PEMERIKSAAN LABORATORIUM
 */
class PemeriksaanLabController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function lists(Request $request)
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
            
            $klasifikasi = 'LABORATORIUM'; 

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            $list = Tindakan::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('klasifikasi',$klasifikasi)
                    ->where(function($q) use ($keyword) {
                        $q->where('tindakan_nama','ILIKE',$keyword)
                        ->orWhere('tindakan_id','ILIKE',$keyword);
                    })
                    ->with(['kelompok_tindakan','kelompok_tagihan','kelompok_vclaim'])
                    ->orderBy('created_at','DESC')->orderBy('klasifikasi','ASC')->orderBy('tindakan_nama','ASC')->paginate($per_page); 
            
            foreach($list->items() as $item) {
                if($item['klasifikasi'] == 'LABORATORIUM') {
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

    /**
     * Show the specified resource.
     * @param int $id
     */
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
                ->orderBy('klasifikasi','ASC')
                ->orderBy('no_urut','ASC')
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

    /**
     * create ID tindakan
     */
    public function getTindakanId() 
    {
        try {
            $id = $this->clientId.'-LAB0001';
            $maxId = Tindakan::withTrashed()->where('tindakan_id','LIKE',$this->clientId.'-LAB%')->max('tindakan_id');
            if (!$maxId) { $id = $this->clientId.'-LAB0001'; }
            else {
                $maxId = str_replace($this->clientId.'-LAB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-LAB000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-LAB00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-LAB0'.$count; } 
                else { $id = $this->clientId.'-LAB'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'LAB'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }

    /**
     * create ID item hasil id
     */
    public function getItemLabId() {
        try {
            $id = $this->clientId.'-ILB0001';
            $maxId = LabItemHasil::withTrashed()->where('lab_hasil_id','LIKE',$this->clientId.'-ILB%')->max('lab_hasil_id');
            if (!$maxId) { $id = $this->clientId.'-ILB0001'; }
            else {
                $maxId = str_replace($this->clientId.'-ILB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-ILB000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-ILB00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-ILB0'.$count; } 
                else { $id = $this->clientId.'-ILB'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'ILB'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

    /**
     * create template id
     */
    public function getTemplateId() 
    {
        try {
            $id = $this->clientId.'-TLB00001';
            $maxId = LabTemplate::withTrashed()->where('lab_template_id','ILIKE',$this->clientId.'-TLB%')->max('lab_template_id');

            if (!$maxId) { $id = $this->clientId.'-TLB00001'; }
            else {
                $maxId = str_replace($this->clientId.'-TLB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-TLB000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-TLB00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-TLB0'.$count; } 
                else { $id = $this->clientId.'-TLB'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'TLB'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }

    /**
     * creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $request->validate(['tindakan_nama' => 'required',]);
            $id = $this->getTindakanId(); 

            DB::connection('dbclient')->beginTransaction();
            $data                       = new Tindakan();
            $data->tindakan_id          = $id;
            $data->tindakan_nama        = $request->tindakan_nama;
            $data->klasifikasi          = 'LABORATORIUM';
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
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data tindakan Lab.']);
            }

            /**
             * JIKA bukan paket pemeriksaan, maka tambahkan ke item hasil lab
             */
            if(!$request->is_paket) {
                $labItemId = $this->getItemLabId();                
                $labItem = new LabItemHasil();
                $labItem->lab_hasil_id = $labItemId;
                $labItem->hasil_nama = $request->tindakan_nama;
                $labItem->klasifikasi = $request->hasil['klasifikasi'];
                $labItem->sub_klasifikasi = $request->hasil['sub_klasifikasi'];
                $labItem->no_urut = $request->hasil['no_urut'];
                $labItem->kode_rl = $request->hasil['kode_rl'];
                $labItem->is_aktif = true;
                $labItem->client_id = $this->clientId;
                $labItem->created_by = Auth::user()->username;
                $labItem->updated_by = Auth::user()->username;
        
                $success = $labItem->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data item lab hasil.']);
                }

                /**
                 * create template hasil
                 */
                $labTemplateId = $this->getTemplateId();
            
                $labTemplate = new LabTemplate();
                $labTemplate->tindakan_id = $id;
                $labTemplate->lab_hasil_id = $labItemId;
                $labTemplate->lab_template_id = $labTemplateId;
                
                $labTemplate->hasil_nama = $request->tindakan_nama;            
                $labTemplate->tindakan_nama = $request->tindakan_nama;
                $labTemplate->klasifikasi = $request->hasil['klasifikasi'];
                $labTemplate->sub_klasifikasi = $request->hasil['sub_klasifikasi'];
                $labTemplate->no_urut = $request->hasil['no_urut'];
                $labTemplate->level = $request->hasil['level'];
                $labTemplate->is_aktif = true;
                $labTemplate->client_id = $this->clientId;
                $labTemplate->created_by = Auth::user()->username;
                $labTemplate->updated_by = Auth::user()->username;
        
                $success = $labTemplate->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data template item lab hasil.']);
                }
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
            return response()->json(['success' => true,'message' => 'Data Tindakan Lab berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Tindakan Lab tidak berhasil.','error'=>$e->getMessage()]);
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('laboratorium::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
