<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\Penunjang\Entities\LinenDistribusi;
use Modules\Penunjang\Entities\LinenDetail;
use Modules\Penunjang\Entities\LinenTerima;
use Modules\Penunjang\Entities\LinenStatusLog;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\Produk;

class LinenProsesController extends Controller
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
     * @return Renderable
     */
    public function lists(Request $request) {
        try {
            $keyword = '%%';
            $rowNumber = 20;
            $status = '%%';
            $aktif = 'true';

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            if($request->has('status')) {
                $status = '%'.$request->input('status').'%';
            }
            
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = LinenTerima::count();
                }
            }

            $lists = LinenTerima::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('status','ILIKE',$status)                
                ->where(function($q) use ($keyword) {
                    $q->where('unit_pengirim_id','ILIKE',$keyword)
                    ->orWhere('unit_pengirim','ILIKE',$keyword);
                })->orderBy('tgl_terima', 'DESC')->paginate($rowNumber);

            if($lists) {
                foreach($lists->items() as $dt) {
                    $dt['items'] = LinenDetail::where('trx_linen_id',$dt['linen_terima_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->get();
                }
            }
    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource product linen.
     * @return Renderable
     */
    public function linenLists(Request $request) {
        try {
            $keyword = '%%';
            $rowNumber = 20;
            $aktif = 'true';

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }
            
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') {  $rowNumber = 20; }
            }

            $lists = Produk::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('is_laundry_item',true)                
                ->where(function($q) use ($keyword) {
                    $q->where('produk_id','ILIKE',$keyword)
                    ->orWhere('produk_nama','ILIKE',$keyword);
                })->orderBy('produk_nama', 'ASC')->paginate($rowNumber);
    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar produk linen', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request) {
        try {
            $unitPengirim = UnitPelayanan::where('unit_id',$request->unit_pengirim_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$unitPengirim) {
                return response()->json(['success' => false, 'message' => 'unit pengirim tidak ditemukan.']);
            }
            
            $id = $this->createLinenTerimaId();
            DB::connection('dbclient')->beginTransaction();

            $data = new LinenTerima();
            $data->linen_terima_id = $id;
            $data->unit_pengirim_id = $request->unit_pengirim_id;
            $data->unit_pengirim = $unitPengirim->unit_nama;
            
            $data->pengirim = strtoupper($request->pengirim);
            $data->penerima = Auth::user()->username;
            $data->tgl_terima = $request->tgl_terima;
            $data->jam_terima = $request->jam_terima;
           
            $data->berat = $request->berat;
            $data->satuan = $request->satuan;
            $data->is_infeksius = $request->is_infeksius;
            $data->status = 'TERIMA';
            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan linen tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $produk = Produk::where('produk_id',$item['produk_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'produk item penerimaan linen tidak ditemukan.']);
                }
                $detail = new LinenDetail();
                $detail->linen_detail_id = $id.Uuid::uuid4()->getHex();
                $detail->trx_linen_id = $id;
                $detail->tgl_transaksi = $request->tgl_terima;
                $detail->jam_transaksi = $request->jam_terima;
                $detail->unit_id = $request->unit_pengirim_id;
                $data->unit_nama = $unitPengirim->unit_nama;

                $detail->jenis_transaksi = 'PENERIMAAN';
                $detail->produk_id = $item['produk_id'];
                $detail->produk_nama = $produk->produk_nama;
                $detail->satuan = $item['satuan'];
                $detail->jml_terima = $item['jml_terima'];
                $detail->jml_rusak = 0;
                $detail->jml_perawatan = 0;
                $detail->jml_keluar = 0;
                $detail->jml_awal = 0;
                $detail->jml_penyesuaian = 0;
                $detail->status = 'TERIMA';
                $detail->kondisi = $item['kondisi'];
                $detail->is_aktif = true;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item penerimaan linen tidak berhasil disimpan.']);
                }
            }

            $status = new LinenStatusLog();
            $status->linen_status_id = 'STAT'.$id.'-'.Uuid::uuid4()->getHex();
            $status->trx_linen_id = $id;
            $status->tgl_status = $request->tgl_terima;
            $status->jam_status = $request->jam_terima;
            $status->status = 'TERIMA';
            $status->is_aktif = true;
            $status->client_id = $this->clientId;
            
            $success = $status->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan linen tidak berhasil disimpan.']);
            }
            
            DB::connection('dbclient')->commit();
            $data = LinenTerima::where('linen_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = LinenDetail::where('trx_linen_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Penerimaan linen berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createLinenTerimaId() {
        try {
            $id = $this->clientId.'.'.date('Ymd').'.LTR-0001';
            $maxId = LinenTerima::withTrashed()->where('client_id', $this->clientId)->where('linen_terima_id','LIKE',$this->clientId.'.'.date('Ymd').'.LTR-%')->max('linen_terima_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ymd').'.LTR-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ymd').'.LTR-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ymd').'.LTR-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ymd').'.LTR-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ymd').'.LTR-0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ymd').'.LTR-'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.'.date('Ymd').'.LTR-' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = LinenTerima::where('linen_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Penerimaan linen tidak ditemukan.']);
            }
            $data['items'] = LinenDetail::where('trx_linen_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Data penerimaan linen ditemukan.', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request) {
        try {
            $id = $request->linen_terima_id;
            $data = LinenTerima::where('client_id',$this->clientId)->where('linen_terima_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan linen tidak ditemukan.']);
            }

            $unitPengirim = UnitPelayanan::where('unit_id',$request->unit_pengirim_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$unitPengirim) {
                return response()->json(['success' => false, 'message' => 'unit pengirim tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();

            $data->unit_pengirim_id = $request->unit_pengirim_id;
            $data->unit_pengirim = $unitPengirim->unit_nama;            
            $data->pengirim = strtoupper($request->pengirim);
            $data->penerima = Auth::user()->username;
            $data->tgl_terima = $request->tgl_terima;
            $data->jam_terima = $request->jam_terima;            
            $data->berat = $request->berat;
            $data->satuan = $request->satuan;
            $data->is_infeksius = $request->is_infeksius;
            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan linen tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $produk = Produk::where('produk_id',$item['produk_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'produk item penerimaan linen tidak ditemukan.']);
                }

                $detail = LinenDetail::where('linen_detail_id',$item['linen_detail_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                
                if(!$detail) {
                    $detail = new LinenDetail();
                    $detail->linen_detail_id = $id.Uuid::uuid4()->getHex();
                    $detail->trx_linen_id = $id;
                    $detail->jenis_transaksi = 'PENERIMAAN';
                    $detail->produk_id = $item['produk_id'];                    
                    $detail->status = $request->status;
                    $detail->is_aktif = true;
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }

                $detail->tgl_transaksi = $request->tgl_terima;
                $detail->jam_transaksi = $request->jam_terima;
                $detail->unit_id = $request->unit_pengirim_id;
                $data->unit_nama = $unitPengirim->unit_nama;
                           
                $detail->kondisi = $item['kondisi'];
                $detail->produk_nama = $produk->produk_nama;
                $detail->satuan = $item['satuan'];
                $detail->jml_terima = $item['jml_terima'];
                $detail->jml_rusak = 0;
                $detail->jml_perawatan = 0;
                $detail->jml_keluar = 0;
                $detail->jml_awal = 0;
                $detail->jml_penyesuaian = 0;
                
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item penerimaan linen tidak berhasil disimpan.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            $data = LinenTerima::where('linen_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = LinenDetail::where('trx_linen_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Penerimaan linen berhasil diubah.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id) {
        try {
            $data = LinenTerima::where('client_id',$this->clientId)->where('trx_linen_id',$id)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan linen tidak ditemukan.']);
            }
            $detail = LinenDetail::where('client_id',$this->clientId)->where('trx_linen_id',$id)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**hapus data penerimaan */
            $success = LinenTerima::where('client_id',$this->clientId)
                ->where('linen_terima_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan linen tidak berhasil dihapus.']);
            }
            
            /**hapus data detail penerimaan */
            if($detail) {
                $success = LinenDetail::where('client_id',$this->clientId)
                    ->where('trx_linen_id',$id)
                    ->where('is_aktif',1)
                    ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item penerimaan linen tidak berhasil dihapus.']);
                }
            }
                        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Penerimaan linen berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }
}
