<?php

namespace Modules\Pengadaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Ramsey\Uuid\Uuid;

use Modules\Pengadaan\Entities\PurchaseOrder;
use Modules\Pengadaan\Entities\PurchaseOrderDetail;
use Modules\Pengadaan\Entities\PurchaseOrderHarga;

use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\Unit;
use Modules\ManajemenUser\Entities\MemberDepo;
use Modules\ManajemenUser\Entities\MemberUnit;

use Modules\Persediaan\Entities\KartuStock;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\Produk;

use DB;

class PurchaseReturnController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request)
    {
        try{
            // $keyword = '%%';
            // $rowNumber = 10;
            // $active = 'true';

            // if($request->has('keyword')) {
            //     $keyword = $keyword = '%'.$request->input('keyword').'%';
            // }
            // if($request->has('is_aktif')) {
            //     $active = '%'.$request->input('is_aktif').'%';
            // }
            // if($request->has('per_page')) {
            //     $rowNumber = $request->input('per_page');
            //     if($rowNumber == 'ALL') { 
            //         $rowNumber = PurchaseOrder::count();
            //     }
            // }

            // $data = PurchaseOrder::where('client_id',$this->clientId)
            //         ->where('is_aktif','ILIKE',$active)
            //         ->where('status', 'DONE')
            //         ->where(function($q) use ($keyword) {
            //             $q->where('pengadaan_id','ILIKE',$keyword)
            //             ->orWhere('unit_id','ILIKE',$keyword);
            //         })->orderBy('pengadaan_id', 'ASC')->paginate($rowNumber);
            
            // return response()->json(['success'=>true,'message'=>'OK','data'=>$data]); 
            
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = PurchaseOrder::count();
                }
            }

            $data = PurchaseOrder::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where('jns_transaksi', 'Purchase Return')
                    ->where(function($q) use ($keyword) {
                        $q->where('pengadaan_id','ILIKE',$keyword)
                        ->orWhere('unit_id','ILIKE',$keyword);
                    })->orderBy('pengadaan_id', 'ASC')->paginate($rowNumber);
            
            foreach($data->items() as $dt) {
                $dt['detail'] = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('pengadaan_id',$dt['pengadaan_id'])
                    ->where('trx_id',$dt['trx_id'])
                    ->where('is_aktif',1)
                    ->get();
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $trxId)
    {
        try{
            // if(!$this->clientId) {
            //     return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            // }

            // $data = PurchaseOrder::select('tb_pengadaan.*', 'tb_pengadaan_detail.*', 'tb_pengadaan_harga.*', 'tb_vendor.vendor_nama', 'tb_produk.jenis_produk')
            // ->leftJoin('tb_pengadaan_detail', 'tb_pengadaan.pengadaan_id', '=', 'tb_pengadaan_detail.pengadaan_id')
            // ->leftJoin('tb_pengadaan_harga', 'tb_pengadaan_detail.detail_id', '=', 'tb_pengadaan_harga.detail_id')
            // ->leftJoin('tb_vendor', 'tb_pengadaan.vendor_id', '=', 'tb_vendor.vendor_id')
            // ->leftJoin('tb_produk', 'tb_pengadaan_detail.produk_id', '=', 'tb_produk.produk_id')
            // ->where('tb_pengadaan.client_id',$this->clientId)->where('tb_pengadaan.is_aktif',1)->where('tb_pengadaan.pengadaan_id', $pengadaan_id)->where('tb_pengadaan_detail.trx_id', $pengadaan_id)->get();

            // if(!$data) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            // }
            $data = $this->getDataPORR($trxId);
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $memberDepo = MemberDepo::join('tb_depo','tb_depo.depo_id','=','tb_member_depo.depo_id')
                ->where('tb_member_depo.depo_id',$data->depo_id)
                ->where('tb_member_depo.client_id',$this->clientId)
                ->where('tb_member_depo.user_id',Auth::user()->user_id)
                ->where('tb_depo.is_aktif',1)
                ->where('tb_member_depo.is_aktif',1)
                ->first();
            
            if(!$memberDepo) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Depo tidak dibawah otorisasi-mu.']);
            }

            $memberUnit = MemberUnit::join('tb_unit','tb_unit.unit_id','=','tb_member_unit.unit_id')
                ->where('tb_member_unit.unit_id',$data->unit_id)
                ->where('tb_member_unit.client_id',$this->clientId)
                ->where('tb_member_unit.user_id',Auth::user()->user_id)
                ->where('tb_unit.is_aktif',1)
                ->where('tb_member_unit.is_aktif',1)
                ->first();
            
            if(!$memberUnit) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Unit tidak dibawah otorisasi-mu.']);
            }


            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }


    public function getDataPORR($trxId) {
        try {
            $data = PurchaseOrder::where('client_id',$this->clientId)->where('trx_id',$trxId)->where('jns_transaksi','Purchase Return')->first();
            if(!$data) { return null; }
            
            $data['detail'] = PurchaseOrderDetail::where('client_id',$this->clientId)
                ->where('trx_id',$trxId)->where('pengadaan_id',$data->pengadaan_id)
                ->where('is_aktif',1)->get();

            $data['order'] = PurchaseOrder::where('client_id',$this->clientId)
                ->where('trx_id',$data->pengadaan_id)
                ->where('pengadaan_id',$data->pengadaan_id)->first();

            return $data;
        }
        catch(\Exception $e) { return null; }
    }

    public function isUserDepo($depoId){
        try {
            $data = MemberDepo::where('user_id',Auth::user()->user_id)
                ->where('depo_id',$depoId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            if($data) { return true; }
            else { return false; }
        }
        catch(\Exception $e) { return false; }
    }

    public function isDepoAllowed($depoId){
        try {
            $data = Depo::where('depo_id',$depoId)
                ->where('is_purchase_return',1)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            if($data) { return true; }
            else { return false; }
        }
        catch(\Exception $e) { return false; }
    }

    public function createPORR(Request $request) {
        try {

            $po = PurchaseOrder::where('client_id',$this->clientId)
                ->where('pengadaan_id',$request->pengadaan_id)
                ->where('trx_id',$request->reff_trx_id)
                ->where('is_aktif',true)->first();

            if(!$po) {
                return response()->json(['success' => false,'message' => 'Data referensi pembelian tidak ditemukan.']);
            }

            if(count($request->detail_receive) <= 0) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Detail Pengembalian tidak ditemukan.']);
            }

            if(!$this->isUserDepo($request->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($request->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase return.']);
            }   

            $id = $this->getPORRId(); 
            $no_transaksi = $this->getNoPORR();
            
            DB::connection('dbclient')->beginTransaction();

            $data = new PurchaseOrder();
            $data->pengadaan_id = $request->pengadaan_id;
            $data->trx_id = $id;
            $data->reff_trx_id = $request->reff_trx_id;            
            
            $data->jns_transaksi = 'Purchase Return';
            $data->tgl_transaksi = $request->tgl_transaksi;            
            $data->no_transaksi = $no_transaksi;
            $data->tgl_dibutuhkan = $request->tgl_transaksi; 

            $data->vendor_id = $request->vendor_id;
            $data->vendor_nama = $request->vendor_nama;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $request->unit_nama;
            $data->depo_id = $request->depo_id;
            $data->depo_nama = $request->depo_nama;
            
            $data->total_pajak = $request->total_pajak;
            $data->total_diskon = $request->total_diskon;
            $data->subtotal = $request->subtotal;
            $data->total = $request->total;

            $data->catatan = $request->catatan;
            $data->no_invoice = null;
            $data->tgl_invoice = null;
            $data->tgl_rencana_bayar = null;

            $data->data_versi = Uuid::uuid4()->getHex();
            
            $data->status = 'DRAFT';
            $data->is_aktif = 1;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $data->client_id = $this->clientId;
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }

            foreach($request->detail_receive as $value) {
                
                $selisih = $value['jml_por'] - $value['jml_porr'];  
                if($selisih < $value['jml_kembali']) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.Jumlah pengembalian lebih besar dari selisih PO.']);
                }
                
                $detailPO = new PurchaseOrderDetail();
                $detailPO->detail_id = $value['detail_id'];
                $detailPO->pengadaan_id = $request->pengadaan_id;
                $detailPO->trx_id = $id;
                $detailPO->reff_trx_id = $request->reff_trx_id;
                $detailPO->depo_id = $request->depo_id;
                $detailPO->jenis_produk = $value['jenis_produk'];
                $detailPO->produk_id = $value['produk_id'];
                $detailPO->produk_nama = $value['produk_nama'];
                $detailPO->satuan = $value['satuan'];
                $detailPO->satuan_beli = $value['satuan_beli'];
                $detailPO->konversi = $value['konversi'];

                $detailPO->harga = $value['harga'];
                $detailPO->diskon = $value['diskon'];
                $detailPO->nilai_diskon = $value['nilai_diskon'];
                $detailPO->subtotal = $value['subtotal'];
                $detailPO->is_pajak = $value['is_pajak'];
                $detailPO->nilai_pajak = $value['nilai_pajak'];
                $detailPO->persen_pajak = $value['persen_pajak'];

                $detailPO->jml_po = 0;
                $detailPO->jml_total_po = 0;
                $detailPO->jml_por = 0;
                $detailPO->jml_total_por = 0;
                $detailPO->jml_porr = $value['jml_kembali'];
                $detailPO->jml_total_porr =  $value['total_kembali'];
                $detailPO->status = 'DRAFT';
                $detailPO->is_aktif = 1;
                $detailPO->created_by = Auth::user()->username;
                $detailPO->updated_by = Auth::user()->username;
                $detailPO->client_id = $this->clientId;
                $success = $detailPO->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
                }
            }

            DB::connection('dbclient')->commit();
            
            $data = $this->getDataPORR($id);
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);

        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=> $e->getMessage()]);
        }
    }

    public function updatePORR(Request $request) {
        try {
            $data = PurchaseOrder::where('client_id',$this->clientId)
                ->where('status','DRAFT')
                ->where('jns_transaksi','Purchase Return')
                ->where('pengadaan_id',$request->pengadaan_id)
                ->where('trx_id',$request->trx_id)
                ->where('is_aktif',true)
                ->first();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data referensi pengembalian tidak ditemukan / sudah berubah status.']);
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase return.']);
            }

            $id = $data->trx_id;
            $success = PurchaseOrder::where('client_id',$this->clientId)
                ->where('status','DRAFT')
                ->where('jns_transaksi','Purchase Return')
                ->where('pengadaan_id',$request->pengadaan_id)
                ->where('trx_id',$id)
                ->where('is_aktif',true)
                ->update([
                    'catatan' => $request->catatan,
                    'data_versi' => Uuid::uuid4()->getHex(),
                    'updated_by' => Auth::user()->username,
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }

            $data = $this->getDataPORR($id);
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);

        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=> $e->getMessage()]);
        }
    }

    public function deletePORR(Request $request, $trxId) {
        try {
            $data = PurchaseOrder::where('client_id',$this->clientId)
                ->where('status','DRAFT')
                ->where('jns_transaksi','Purchase Return')
                ->where('trx_id',$trxId)
                ->where('is_aktif',true)
                ->first();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data pengembalian tidak ditemukan / sudah berubah status.']);
            }
            
            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase return.']);
            }

            $detail = PurchaseOrderDetail::where('client_id',$this->clientId)
                ->where('status','DRAFT')
                ->where('pengadaan_id',$data->pengadaan_id)
                ->where('trx_id',$trxId)
                ->where('is_aktif',true)
                ->first();

            DB::connection('dbclient')->beginTransaction();

            $success = PurchaseOrder::where('client_id',$this->clientId)
                ->where('status','DRAFT')->where('jns_transaksi','Purchase Return')
                ->where('pengadaan_id',$data->pengadaan_id)
                ->where('trx_id',$trxId)->where('is_aktif',true)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username, 'status' => 'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data.']);
            }

            if($detail) {                
                $success = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('status','DRAFT')->where('pengadaan_id',$data->pengadaan_id)
                    ->where('trx_id',$trxId)->where('is_aktif',true)
                    ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username, 'status' => 'BATAL']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data.']);
                }
            }

            DB::connection('dbclient')->commit();
            
            return response()->json(['success' => true,'message' => 'data berhasil dihapus']);

        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil dihapus','error'=> $e->getMessage()]);
        }
    }

    public function approvePORR(Request $request, $trxId) {
        try {
            $data = PurchaseOrder::where('client_id',$this->clientId)
                ->where('status','DRAFT')
                ->where('jns_transaksi','Purchase Return')
                ->where('trx_id',$trxId)
                ->where('is_aktif',true)
                ->first();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data pegembalian tidak ditemukan / sudah berubah status.']);
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase return.']);
            }

            $detail = PurchaseOrderDetail::where('client_id',$this->clientId)
                ->where('status','DRAFT')
                ->where('pengadaan_id',$data->pengadaan_id)
                ->where('trx_id',$trxId)->where('is_aktif',true)
                ->get();

            DB::connection('dbclient')->beginTransaction();
            //**update status penerimaan */
            $success = PurchaseOrder::where('client_id',$this->clientId)
                ->where('status','DRAFT')->where('jns_transaksi','Purchase Return')
                ->where('pengadaan_id',$data->pengadaan_id)
                ->where('trx_id',$trxId)->where('is_aktif',true)
                ->update(['updated_by' => Auth::user()->username, 'status' => 'SELESAI']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data.']);
            }

            if($detail) {                
                $success = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('status','DRAFT')->where('pengadaan_id',$data->pengadaan_id)
                    ->where('trx_id',$trxId)->where('is_aktif',true)
                    ->update(['updated_by' => Auth::user()->username, 'status' => 'SELESAI']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data.']);
                }

                /**
                 * buat kartu stock penerimaan dan merubah stock
                 */
                foreach($detail as $dt) {
                    $totalAkhir = 0;
                    /**
                     * check apakah depo terkait memiliki item produk / tidak
                     * buat baru jika tidak ada dan ubah stock
                     */
                    $itemDepo = DepoProduk::where('client_id',$this->clientId)
                        ->where('is_aktif',1)->where('depo_id',$data->depo_id)
                        ->where('produk_id',$dt['produk_id'])->first();
                    
                    if(!$itemDepo) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data. Produk tidak ditemukan di depo terkait']);
                    }
                    /**
                     * check produk
                     */
                    $produk = Produk::where('produk_id',$dt['produk_id'])
                        ->where('client_id',$this->clientId)->where('is_aktif',1)->first();
                    
                    if(!$produk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data. Produk persediaan tidak ditemukan.']);
                    }

                    $totalAkhir = $itemDepo->jml_total - $dt['jml_total_porr'];
                    if($totalAkhir < 0) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data. Jumlah persediaan Produk di depo terkait tidak mencukupi']);
                    }

                    $itemDepo->jml_keluar        = $itemDepo->jml_keluar + $dt['jml_total_porr'];
                    $itemDepo->jml_total        = $itemDepo->jml_total - $dt['jml_total_porr'];
                    $itemDepo->updated_by       = Auth::user()->username;            
                    $success = $itemDepo->save();
    
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data Depo Produk Baru']);
                    }
                    /**
                     * buat kartu stock
                     */
                    $kartuStock = new KartuStock();
                    $kartuStock->stock_log_id     = $this->clientId."-".Uuid::uuid4()->getHex();
                    $kartuStock->reg_id           = $data->pengadaan_id;
                    $kartuStock->trx_id           = $data->trx_id;
                    $kartuStock->sub_trx_id       = $data->trx_id;
                    $kartuStock->detail_id        = $dt['detail_id'];
                    $kartuStock->produk_id        = $dt["produk_id"];
                    $kartuStock->tgl_log          = date('Y-m-d H:i:s');
                    $kartuStock->tgl_transaksi    = date('Y-m-d H:i:s');
                    $kartuStock->jns_transaksi    = "PURCHASE RETURN";
                    $kartuStock->aksi             = "SIMPAN";
                    $kartuStock->jns_produk       = $produk->jenis_produk;
                    $kartuStock->produk_nama      = $dt["produk_nama"];
                    $kartuStock->unit_id          = $data->unit_id;
                    $kartuStock->depo_id          = $data->depo_id;
                    $kartuStock->jml_awal         = 0;    
                    $kartuStock->jml_masuk        = 0;
                    $kartuStock->jml_keluar       = $dt['jml_total_porr'];
                    $kartuStock->jml_penyesuaian  = 0;
                    $kartuStock->jml_akhir        = $totalAkhir;
                    $kartuStock->catatan          = $data->catatan;
                    $kartuStock->keterangan       = "Pengembalian pembelian depo ".$data->depo_nama;
                    $kartuStock->client_id        = $this->clientId;
                    $kartuStock->created_by       = Auth::user()->username;
                    $kartuStock->updated_by       = Auth::user()->username;
                    
                    $success = $kartuStock->save();
        
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'data item penerimaan di depo tidak dapat disimpan.']);
                    } 
                }
            }

            DB::connection('dbclient')->commit();
            
            return response()->json(['success' => true,'message' => 'data berhasil disimpan']);

        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=> $e->getMessage()]);
        }
    }

    // public function updatePORR(Request $request) {
    //     try {
    //         $updatePO = PurchaseOrder::where('pengadaan_id', $request->pengadaan_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
    //         $updatePO->status = 'RETURNED';
    //         $success = $updatePO->save();

    //         if(!$success) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
    //         }

    //         $id = $this->getPORRId(); 
    //         $no_transaksi = $this->getNoPORR();
    //         $trx = $request->pengadaan_id;    
    //         $data = new PurchaseOrder();
    //         $data->pengadaan_id = $id;
    //         $data->trx_id = $trx;
    //         $data->reff_trx_id = $trx;
    //         $data->jns_transaksi = 'Return Order';
    //         $data->tgl_transaksi = Carbon::now()->format('Y-m-d H:i:s');
    //         $data->no_transaksi = $no_transaksi;
    //         $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;
    //         $data->vendor_id = $request->vendor_id;
    //         $data->unit_id = $request->unit_id;
    //         $data->depo_id = $request->depo_id;
    //         $data->total_pajak = 0.0;
    //         $data->total_diskon = 0.0;
    //         $data->subtotal_harga = $request->detail_retur[0]['subtotal'];
    //         $data->total_harga = $request->detail_retur[0]['subtotal'];
    //         $data->catatan = $request->catatan;
    //         // $data->no_invoice = '';
    //         // $data->tgl_invoice = '';
    //         $data->tgl_rencana_bayar = $request->tgl_rencana_bayar;
    //         $data->data_versi = Uuid::uuid4()->getHex();
    //         $data->status = 'RETURNED';
    //         $data->is_aktif = 1;
    //         $data->created_by = Auth::user()->username;
    //         $data->updated_by = Auth::user()->username;
    //         $data->client_id = $this->clientId;
    //         $success = $data->save();

    //         if(!$success) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
    //         }

    //         foreach($request->detail_retur as $key => $value) {
    //             $detailId = $this->getDetailPORR();
    //             $detailPO = new PurchaseOrderDetail();
    //             $detailPO->detail_id = $detailId;
    //             $detailPO->pengadaan_id = $data->trx_id;
    //             $detailPO->trx_id = $data->trx_id;
    //             $detailPO->reff_trx_id = $data->reff_trx_id;
    //             $detailPO->depo_id = $data->depo_id;
    //             $detailPO->produk_id = $value['produk_id'];
    //             $detailPO->produk_nama = $value['produk_nama'];
    //             $detailPO->satuan_id = $value['satuan'];
    //             $detailPO->satuan_beli_id = $value['satuan'];
    //             $detailPO->konversi = "0";
    //             $detailPO->jml_po = 0.0;
    //             $detailPO->jml_total_po = 0.0;
    //             $detailPO->jml_por = $value['jumlah_diterima'];
    //             $detailPO->jml_total_por = $value['jumlah_diterima'];
    //             $detailPO->jml_porr = $value['jumlah_return'];
    //             $detailPO->jml_total_porr = $value['jumlah_return'];
    //             $detailPO->status = 'RETURNED';
    //             $detailPO->is_aktif = 1;
    //             $detailPO->created_by = Auth::user()->username;
    //             $detailPO->updated_by = Auth::user()->username;
    //             $detailPO->client_id = $this->clientId;
    //             $success = $detailPO->save();
    //         }

    //         if(!$success) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
    //         }

    //         $dataDetailId = PurchaseOrderDetail::where('pengadaan_id', $detailPO->pengadaan_id)->where('jml_porr','!=', 0)->get();
    //         foreach($dataDetailId as $key => $value) {
    //             $hargaPO = new PurchaseOrderHarga();
    //             $hargaPO->detail_id = $value->detail_id;
    //             $hargaPO->pengadaan_id = $detailPO->pengadaan_id;
    //             $hargaPO->trx_id = $detailPO->trx_id;
    //             $hargaPO->reff_trx_id = $detailPO->reff_trx_id;
    //             $hargaPO->produk_id = $value->produk_id;
    //             $hargaPO->produk_nama = $value->produk_nama;
    //             $hargaPO->satuan_id = $value->satuan_id;
    //             $hargaPO->satuan_beli_id = $value->satuan_beli_id;
    //             $hargaPO->konversi = "0";
    //             $hargaPO->jml_transaksi = $value->jml_por;
    //             $hargaPO->jml_total_transaksi = $value->jml_por;
    //             $hargaPO->tipe_pajak = '';
    //             $hargaPO->persen_pajak = 0.0;
    //             $hargaPO->nilai_pajak = 0.0;
    //             $hargaPO->is_diskon_persen = 0;
    //             $hargaPO->diskon = 0.0;
    //             $hargaPO->subtotal = 0.0;
    //             $hargaPO->hna = 0.0;
    //             $hargaPO->is_aktif = 1;
    //             $hargaPO->created_by = Auth::user()->username;
    //             $hargaPO->updated_by = Auth::user()->username;
    //             $hargaPO->client_id = $this->clientId;
    //             // BUG HERE
    //             foreach($request->detail_retur as $key => $result){
    //                 $hargaPO->harga = $result['harga'];
    //                 $hargaPO->harga_sblm_pajak = $result['harga'];
    //                 $hargaPO->harga_akhir = $hargaPO->harga_sblm_pajak - $hargaPO->diskon + $hargaPO->nilai_pajak ;
    //             }
    //             $success = $hargaPO->save();
    //         }

    //         return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);

    //     } catch(\Exception $e) {
    //         return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=> $e->getMessage()]);
    //     }
    // }

    public function getPORRId() 
    {
        try {
            $id = $this->clientId.'.'.date('Ym').'-PORR0001';
            $maxId = PurchaseOrder::withTrashed()->where('client_id', $this->clientId)->where('pengadaan_id','LIKE',$this->clientId.'.'.date('Ym').'-PORR%')->max('pengadaan_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'-PORR0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'-PORR','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'-PORR000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'-PORR00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'-PORR0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'-PORR'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'-PORR' . Uuid::uuid4()->getHex();
        }
    }

    public function getNoPORR() 
    {
        try {
            $id = Carbon::now()->format('Y').'.PORR-0001';
            $maxId = PurchaseOrder::withTrashed()->where('client_id', Carbon::now()->format('Y'))->where('pengadaan_id','LIKE',Carbon::now()->format('Y').'.PORR-%')->max('pengadaan_id');
            if (!$maxId) { $id = Carbon::now()->format('Y').'.PORR-0001'; }
            else {
                $maxId = str_replace(Carbon::now()->format('Y').'.PORR-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = Carbon::now()->format('Y').'.PORR-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = Carbon::now()->format('Y').'.PORR-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = Carbon::now()->format('Y').'.PORR-0'.$count; } 
                else { $id = Carbon::now()->format('Y').'.PORR-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return Carbon::now()->format('Y').'.PORR-' . Uuid::uuid4()->getHex();
        }
    }

    public function getDetailPORR() 
    {
        try {
            $id = $this->clientId.'.PORRD-0001';
            $maxId = PurchaseOrderDetail::withTrashed()->where('client_id', $this->clientId)->where('detail_id','LIKE',$this->clientId.'.PORRD-%')->max('detail_id');
            if (!$maxId) { $id = $this->clientId.'.PORRD-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.PORRD-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.PORRD-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.PORRD-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.PORRD-0'.$count; } 
                else { $id = $this->clientId.'.PORRD-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.PORRD-' . Uuid::uuid4()->getHex();
        }
    }
}
