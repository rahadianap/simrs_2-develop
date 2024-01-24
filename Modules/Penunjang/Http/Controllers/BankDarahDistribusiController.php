<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\Penunjang\Entities\BankDarahDistribusi;
use Modules\Penunjang\Entities\BankDarahDetail;
use Modules\Penunjang\Entities\BankDarahTerima;
use Modules\Penunjang\Entities\BankDarahLog;


class BankDarahDistribusiController extends Controller
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
                    $rowNumber = CssdDistribusi::count();
                }
            }

            $lists = CssdDistribusi::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('status','ILIKE',$status)                
                ->where(function($q) use ($keyword) {
                    $q->where('unit_penerima_id','ILIKE',$keyword)
                    ->orWhere('unit_penerima','ILIKE',$keyword);
                })->orderBy('tgl_kirim', 'DESC')->paginate($rowNumber);

            if($lists) {
                foreach($lists->items() as $dt) {
                    $dt['items'] = CssdDetail::where('trx_cssd_id',$dt['cssd_dist_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->get();
                }
            }
    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource product cssd.
     * @return Renderable
     */
    public function itemLists(Request $request) {
        try {
            $keyword = '%%';
            $rowNumber = 50;
            // $group = '%%';
            // $golongan = '%%';
            // $rhesus = '%%';
            $terpakai = 'false';
            
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') {  $rowNumber = 50; }
            }
            
            if($request->has('terpakai')) {
                $terpakai = $request->input('terpakai');
                if($terpakai == 'ALL') {  $terpakai = '%%'; }
            }
            

            $lists = BankDarahDetail::where('client_id',$this->clientId)
                ->where('is_aktif',true)
                ->where('is_terpakai','ILIKE',$terpakai)
                ->where('is_musnah',false)                
                ->where(function($q) use ($keyword) {
                    $q->where('gol_darah','ILIKE',$keyword)
                    ->orWhere('group_darah','ILIKE',$keyword)
                    ->orWhere('rhesus','ILIKE',$keyword)
                    ->orWhere('no_labu','ILIKE',$keyword);
                })->orderBy('is_terpakai','ASC')
                ->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar persediaan', 'error' => $e->getMessage()]);
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
            
            $id = $this->createCssdKirimId();
            DB::connection('dbclient')->beginTransaction();

            $data = new CssdDistribusi();
            $data->cssd_dist_id = $id;
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
                return response()->json(['success' => false, 'message' => 'pengiriman cssd tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $produk = Produk::where('produk_id',$item['produk_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'produk item pengiriman cssd tidak ditemukan.']);
                }
                $detail = new CssdDetail();
                $detail->cssd_detail_id = $id.Uuid::uuid4()->getHex();
                $detail->trx_cssd_id = $id;
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
                $detail->kondisi = 'STERIL';
                $detail->is_aktif = true;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item penerimaan cssd tidak berhasil disimpan.']);
                }
            }

            $status = new CssdStatusLog();
            $status->cssd_status_id = 'STAT'.$id.'-'.Uuid::uuid4()->getHex();
            $status->trx_cssd_id = $id;
            $status->tgl_status = $request->tgl_kirim;
            $status->jam_status = $request->jam_kirim;
            $status->status = 'KIRIM';
            $status->is_aktif = true;
            $status->client_id = $this->clientId;
            
            $success = $status->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pengiriman cssd tidak berhasil disimpan.']);
            }
            
            DB::connection('dbclient')->commit();
            $data = CssdDistribusi::where('cssd_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = CssdDetail::where('trx_cssd_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Pengiriman cssd berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createCssdKirimId() {
        try {
            $id = $this->clientId.'.'.date('Ymd').'.CDR0001';
            $maxId = CssdDistribusi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('cssd_dist_id','LIKE',$this->clientId.'.'.date('Ymd').'.CDR%')
                ->max('cssd_dist_id');

            if (!$maxId) { $id = $this->clientId.'.'.date('Ymd').'.CDR0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ymd').'.CDR','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ymd').'.CDR000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ymd').'.CDR00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ymd').'.CDR0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ymd').'.CDR'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.'.date('Ymd').'.CDR' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = CssdDistribusi::where('cssd_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Penerimaan cssd tidak ditemukan.']);
            }
            $data['items'] = CssdDetail::where('trx_cssd_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Data penerimaan cssd ditemukan.', 'data' => $data]); 
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
            $id = $request->cssd_dist_id;
            $data = CssdDistribusi::where('client_id',$this->clientId)->where('cssd_dist_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan cssd tidak ditemukan.']);
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
                return response()->json(['success' => false, 'message' => 'pengiriman cssd tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $produk = Produk::where('produk_id',$item['produk_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'produk item pengiriman cssd tidak ditemukan.']);
                }

                $detail = CssdDetail::where('cssd_detail_id',$item['cssd_detail_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                
                if(!$detail) {
                    $detail = new CssdDetail();
                    $detail->cssd_detail_id = $id.Uuid::uuid4()->getHex();
                    $detail->trx_cssd_id = $id;
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
             
                $detail->kondisi = 'STERIL';
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
                    return response()->json(['success' => false, 'message' => 'item pengiriman cssd tidak berhasil disimpan.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            $data = CssdDistribusi::where('cssd_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = CssdDetail::where('trx_cssd_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Pengiriman cssd berhasil diubah.', 'data' => $data]);                
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
            $data = CssdDistribusi::where('client_id',$this->clientId)->where('cssd_dist_id',$id)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data pengiriman cssd tidak ditemukan.']);
            }
            $detail = CssdDetail::where('client_id',$this->clientId)->where('trx_cssd_id',$id)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**hapus data penerimaan */
            $success = CssdDistribusi::where('client_id',$this->clientId)
                ->where('cssd_dist_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pengiriman cssd tidak berhasil dihapus.']);
            }
            
            /**hapus data detail penerimaan */
            if($detail) {
                $success = CssdDetail::where('client_id',$this->clientId)
                    ->where('trx_cssd_id',$id)
                    ->where('is_aktif',1)
                    ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item pengiriman cssd tidak berhasil dihapus.']);
                }
            }
                        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Pengiriman cssd berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }
}
