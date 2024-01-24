<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\Penunjang\Entities\LinenDistribusi;
use Modules\Penunjang\Entities\LinenTerima;
use Modules\Penunjang\Entities\LinenDetail;
use Modules\Penunjang\Entities\LinenStatusLog;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\Produk;

class LinenDistribusiController extends Controller
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
                    $rowNumber = LinenDistribusi::count();
                }
            }

            $lists = LinenDistribusi::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('status','ILIKE',$status)                
                ->where(function($q) use ($keyword) {
                    $q->where('unit_penerima_id','ILIKE',$keyword)
                    ->orWhere('unit_penerima','ILIKE',$keyword);
                })->orderBy('tgl_kirim', 'DESC')->paginate($rowNumber);

            if($lists) {
                foreach($lists->items() as $dt) {
                    $dt['items'] = LinenDetail::where('trx_linen_id',$dt['linen_dist_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->get();
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
            $rowNumber = 50;
            
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') {  $rowNumber = 50; }
            }

            $lists = LinenDetail::where('client_id',$this->clientId)->where('is_aktif',true)
                ->where(function($q) use ($keyword) {
                    $q->where('produk_id','ILIKE',$keyword)
                    ->orWhere('produk_nama','ILIKE',$keyword);
                })->select('produk_id',DB::raw('SUM(jml_awal + jml_terima + jml_Penyesuaian - jml_rusak - jml_keluar) as "jumlah"'))
                ->groupBy('produk_id')->paginate($rowNumber);
            
            if($lists) {
                foreach($lists->items() as $dt) {
                    $item = Produk::where('produk_id',$dt['produk_id'])->where('client_id',$this->clientId)->select('satuan','produk_nama')->first();
                    if($item) {
                        $dt['produk_nama'] = $item->produk_nama;
                        $dt['satuan'] = $item->satuan;                        
                    }
                }
            }
    
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
            $unitPenerima = UnitPelayanan::where('unit_id',$request->unit_penerima_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$unitPenerima) {
                return response()->json(['success' => false, 'message' => 'unit penerima tidak ditemukan.']);
            }
            
            $id = $this->createLinenKirimId();
            DB::connection('dbclient')->beginTransaction();

            $data = new LinenDistribusi();
            $data->linen_dist_id = $id;
            $data->unit_penerima_id = $request->unit_penerima_id;
            $data->unit_penerima = $unitPenerima->unit_nama;
            
            $data->penerima = strtoupper($request->penerima);
            $data->pengirim = Auth::user()->username;

            $data->tgl_kirim = $request->tgl_kirim;
            $data->jam_kirim = $request->jam_kirim;
            $data->tgl_terima = $request->tgl_kirim;
            $data->jam_terima = $request->jam_kirim;

            $data->status = 'KIRIM';
            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pengiriman linen tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $produk = Produk::where('produk_id',$item['produk_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'produk item pengiriman linen tidak ditemukan.']);
                }
                $detail = new LinenDetail();
                $detail->linen_detail_id = $id.Uuid::uuid4()->getHex();
                $detail->trx_linen_id = $id;
                $detail->tgl_transaksi = $request->tgl_kirim;
                $detail->jam_transaksi = $request->jam_kirim;
                $detail->unit_id = $request->unit_penerima_id;
                $data->unit_nama = $unitPenerima->unit_nama;

                $detail->jenis_transaksi = 'PENGIRIMAN';
                $detail->produk_id = $item['produk_id'];
                $detail->produk_nama = $produk->produk_nama;
                $detail->satuan = $item['satuan'];
                $detail->jml_terima = 0;
                $detail->jml_rusak = 0;
                $detail->jml_perawatan = 0;
                $detail->jml_keluar = $item['jml_keluar'];
                $detail->jml_awal = 0;
                $detail->jml_penyesuaian = 0;
                $detail->status = 'KIRIM';
                $detail->kondisi = 'BERSIH';
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
            $status->tgl_status = $request->tgl_kirim;
            $status->jam_status = $request->jam_kirim;
            $status->status = 'KIRIM';
            $status->is_aktif = true;
            $status->client_id = $this->clientId;
            
            $success = $status->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pengiriman linen tidak berhasil disimpan.']);
            }
            
            DB::connection('dbclient')->commit();
            $data = LinenDistribusi::where('linen_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = LinenDetail::where('trx_linen_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Pengiriman linen berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createLinenKirimId() {
        try {
            $id = $this->clientId.'.'.date('Ymd').'.LDR-0001';
            $maxId = LinenDistribusi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('linen_dist_id','LIKE',$this->clientId.'.'.date('Ymd').'.LDR-%')
                ->max('linen_dist_id');

            if (!$maxId) { $id = $this->clientId.'.'.date('Ymd').'.LDR-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ymd').'.LDR-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ymd').'.LDR-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ymd').'.LDR-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ymd').'.LDR-0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ymd').'.LDR-'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.'.date('Ymd').'.LDR-' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = LinenDistribusi::where('linen_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
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
            $id = $request->linen_dist_id;
            $data = LinenDistribusi::where('client_id',$this->clientId)->where('linen_dist_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan linen tidak ditemukan.']);
            }

            $unitPenerima = UnitPelayanan::where('unit_id',$request->unit_penerima_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$unitPenerima) {
                return response()->json(['success' => false, 'message' => 'unit penerima tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();

            $data->unit_penerima_id = $request->unit_penerima_id;
            $data->unit_penerima = $unitPenerima->unit_nama;  

            $data->penerima = strtoupper($request->penerima);
            $data->pengirim = Auth::user()->username;

            $data->tgl_terima = $request->tgl_kirim;
            $data->jam_terima = $request->jam_kirim;       
            $data->tgl_kirim = $request->tgl_kirim;
            $data->jam_kirim = $request->jam_kirim;

            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pengiriman linen tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $produk = Produk::where('produk_id',$item['produk_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'produk item pengiriman linen tidak ditemukan.']);
                }

                $detail = LinenDetail::where('linen_detail_id',$item['linen_detail_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                
                if(!$detail) {
                    $detail = new LinenDetail();
                    $detail->linen_detail_id = $id.Uuid::uuid4()->getHex();
                    $detail->trx_linen_id = $id;
                    $detail->jenis_transaksi = 'PENGIRIMAN';
                    $detail->produk_id = $item['produk_id'];                    
                    $detail->status = $request->status;
                    $detail->is_aktif = true;
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }

                $detail->tgl_transaksi = $request->tgl_kirim;
                $detail->jam_transaksi = $request->jam_kirim;
                $detail->unit_id = $request->unit_penerima_id;
                $data->unit_nama = $unitPenerima->unit_nama;
             
                $detail->kondisi = 'BERSIH';
                $detail->produk_nama = $produk->produk_nama;
                $detail->satuan = $item['satuan'];
                $detail->jml_terima = 0;
                $detail->jml_rusak = 0;
                $detail->jml_perawatan = 0;
                $detail->jml_keluar = $item['jml_keluar'];
                $detail->jml_awal = 0;
                $detail->jml_penyesuaian = 0;
                
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item pengiriman linen tidak berhasil disimpan.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            $data = LinenDistribusi::where('linen_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = LinenDetail::where('trx_linen_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Pengiriman linen berhasil diubah.', 'data' => $data]);                
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
            $data = LinenDistribusi::where('client_id',$this->clientId)->where('linen_dist_id',$id)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data pengiriman linen tidak ditemukan.']);
            }
            $detail = LinenDetail::where('client_id',$this->clientId)->where('trx_linen_id',$id)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**hapus data penerimaan */
            $success = LinenDistribusi::where('client_id',$this->clientId)
                ->where('linen_dist_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pengiriman linen tidak berhasil dihapus.']);
            }
            
            /**hapus data detail penerimaan */
            if($detail) {
                $success = LinenDetail::where('client_id',$this->clientId)
                    ->where('trx_linen_id',$id)
                    ->where('is_aktif',1)
                    ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item pengiriman linen tidak berhasil dihapus.']);
                }
            }
                        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Pengiriman linen berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }
}
