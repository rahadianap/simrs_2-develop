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
use Modules\Pengadaan\Entities\PurchaseRequest;
use Modules\Pengadaan\Entities\PurchaseRequestDetail;
use Modules\MasterData\Entities\Depo;
use Modules\ManajemenUser\Entities\MemberDepo;


use Modules\MasterData\Entities\Produk;

use DB;

class PurchaseOrderController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request, $stat = null)
    {
        try{
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';
            $status = '%%';

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

            if($stat) { $status = '%'.$stat.'%'; }

            $data = PurchaseOrder::select('tb_pengadaan.*', 'tb_depo.depo_nama', 'tb_vendor.vendor_nama', 'tb_unit.unit_nama')
                    ->leftJoin('tb_depo', 'tb_pengadaan.depo_id', '=', 'tb_depo.depo_id')
                    ->leftJoin('tb_vendor', 'tb_pengadaan.vendor_id', '=', 'tb_vendor.vendor_id')
                    ->leftJoin('tb_unit', 'tb_pengadaan.unit_id', '=', 'tb_unit.unit_id')
                    ->where('tb_pengadaan.client_id',$this->clientId)
                    ->where('tb_pengadaan.is_aktif','ILIKE',$active)
                    ->where('tb_pengadaan.status','ILIKE',$status)
                    ->where('tb_pengadaan.jns_transaksi','ILIKE','Purchase Order')
                    ->where(function($q) use ($keyword) {
                        $q->where('pengadaan_id','ILIKE',$keyword)
                        ->orWhere('tb_pengadaan.unit_id','ILIKE',$keyword);
                    })->orderBy('pengadaan_id', 'ASC')->paginate($rowNumber);

            foreach($data->items() as $item) {
                $details = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('pengadaan_id',$item['pengadaan_id'])
                    ->where('trx_id',$item['trx_id'])
                    ->where('is_aktif',1)->get();
                
                foreach($details as $dt) {
                    $terima = PurchaseOrderDetail::where('client_id',$this->clientId)
                        ->where('pengadaan_id',$item['pengadaan_id'])
                        ->where('detail_id',$dt['detail_id'])
                        ->where('is_aktif',1)
                        ->select(
                            DB::raw('sum(jml_por) as jml_por'),
                            DB::raw('sum(jml_porr) as jml_porr'),
                        )->groupBy('detail_id')->first();
                    
                    if($terima) {
                        $dt['jml_por'] = $terima->jml_por;
                        $dt['jml_porr'] = $terima->jml_porr;
                    }
                }

                $item['detail'] = $details;                
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request,$trxId)
    {
        try{
            //$id = $request->pengadaan_id;
            $data = PurchaseOrder::where('trx_id',$trxId)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menampilkan data. Data tidak ditemukan.']);
            }
            $id = $data->pengadaan_id;
            $detail = PurchaseOrderDetail::where('pengadaan_id',$id)
                ->where('trx_id',$trxId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->get();

            foreach($detail as $dt) {
                $terima = PurchaseOrderDetail::where('client_id',$this->clientId)
                        ->where('pengadaan_id',$data['pengadaan_id'])
                        ->where('detail_id',$dt['detail_id'])
                        ->where('is_aktif',1)
                        ->select(
                            DB::raw('sum(jml_por) as jml_por'),
                            DB::raw('sum(jml_porr) as jml_porr'),
                        )->groupBy('detail_id')->first();

                if($terima) {
                    $dt['jml_por'] = $terima->jml_por;
                    $dt['jml_porr'] = $terima->jml_porr;

                    $selisih = $dt['jml_po'] + $dt['jml_porr'] - $dt['jml_por'];
                    $totalSelisih = $selisih * $dt['konversi'];
                    
                    $dt['jml_terima'] = $selisih;
                    $dt['total_terima'] = $totalSelisih;
                }

                /**
                 * maksimal pengembalian
                 */

                $kembali = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('pengadaan_id',$data['pengadaan_id'])
                    ->where('detail_id',$dt['detail_id'])
                    ->where('status','SELESAI')
                    ->where('is_aktif',1)
                    ->select(
                        DB::raw('sum(jml_por) as jml_por'),
                        DB::raw('sum(jml_porr) as jml_porr'),
                    )->groupBy('detail_id')->first();

                if($kembali) {
                    $dt['jml_por'] = $terima->jml_por;
                    $dt['jml_porr'] = $terima->jml_porr;

                    $sel = $dt['jml_por'] - $dt['jml_porr'];
                    $totalSel = $sel * $dt['konversi'];
                    
                    $dt['jml_kembali'] = $sel;
                    $dt['total_kembali'] = $totalSel;
                }

                $dt['items'] = PurchaseRequestDetail::where('produk_id',$dt['produk_id'])->where('pengadaan_id',$id)->where('is_aktif',1)->get();                
            }
            $data['detail_po'] = $detail; 

            // $data = PurchaseOrder::select('tb_pengadaan.*', 'tb_produk.*', 'tb_pengadaan_detail.*', 'tb_vendor.vendor_nama')
            // ->leftJoin('tb_pengadaan_detail', 'tb_pengadaan.pengadaan_id', '=', 'tb_pengadaan_detail.pengadaan_id')
            // ->leftJoin('tb_produk', 'tb_pengadaan_detail.produk_id', '=', 'tb_produk.produk_id')
            // ->leftJoin('tb_vendor', 'tb_pengadaan.vendor_id', '=', 'tb_vendor.vendor_id')
            // ->where('tb_pengadaan.client_id',$this->clientId)->where('tb_pengadaan.is_aktif',1)->where('tb_pengadaan.pengadaan_id', $pengadaan_id)->get();

            // if(!$data) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            // }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * CREATE PO FROM PR
     */
    // public function createPOFromPR(Request $request)
    // {
    //     try{
    //         return response()->json(['success' => false,'message' => 'nang create PO from PR.']);
    //         $id = $this->getPOId(); 
    //         $no_transaksi = $this->getNoPO();            
    //         $trx = $request[0]['pr_id'] ? $request[0]['pr_id'] : $this->getTrxPO();    

    //         if(count($request->detail_po) <= 0) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Detail PO tidak ditemukan.']);
    //         }

    //         DB::connection('dbclient')->beginTransaction();
    //         $data = new PurchaseOrder();
    //         $data->pengadaan_id = $id;
    //         $data->trx_id = $trx;
    //         $data->reff_trx_id = $trx;

    //         $data->jns_transaksi = 'Purchase Order';
    //         $data->tgl_transaksi = Carbon::now()->format('Y-m-d H:i:s');            
    //         $data->no_transaksi = $no_transaksi;
    //         $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;

    //         $data->termin = $request->termin;
    //         $data->jenis_bayar = $request->jenis_bayar;

    //         $data->vendor_id = $request->vendor_id;
    //         $data->vendor_nama = $request->vendor_nama;
    //         $data->unit_id = $request->unit_id;
    //         $data->unit_nama = $request->unit_nama;
            
    //         $data->depo_id = $request->depo_id;
    //         $data->depo_nama = $request->depo_nama;
            
    //         $data->total_pajak = $request->total_pajak;
    //         $data->total_diskon = $request->total_diskon;
    //         $data->subtotal = $request->subtotal;
    //         $data->total = $request->total;
            
    //         $data->catatan = $request->catatan;
    //         $data->tgl_rencana_bayar = $request->tgl_rencana_bayar;
    //         $data->data_versi = Uuid::uuid4()->getHex();
    //         $data->status = 'DRAFT';
    //         $data->is_aktif = 1;
    //         $data->created_by = Auth::user()->username;
    //         $data->updated_by = Auth::user()->username;
    //         $data->client_id = $this->clientId;
    //         $success = $data->save();

    //         if(!$success) {
    //             DB::connection('dbclient')->rollBack();
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
    //         }

    //         foreach($request->detail_po as $value) 
    //         {
    //             /**
    //              * insert item PO
    //              */
    //             $detailId = $this->getDetailPO();
    //             $detailPO = new PurchaseOrderDetail();
    //             $detailPO->detail_id = $detailId;
    //             $detailPO->pengadaan_id = $id;
    //             $detailPO->trx_id = $trx;
    //             $detailPO->reff_trx_id = $trx;
    //             $detailPO->depo_id = $request->depo_id;
                
    //             $detailPO->produk_id = $value['produk_id'];
    //             $detailPO->produk_nama = $value['produk_nama'];
    //             $detailPO->satuan = $value['satuan'];
    //             $detailPO->satuan_beli = $value['satuan'];
    //             $detailPO->konversi = 1;
    //             $detailPO->jml_po = $value['jumlah'];
    //             $detailPO->jml_total_po = $value['jumlah'];
    //             $detailPO->jml_por = 0.0;
    //             $detailPO->jml_total_por = 0.0;
    //             $detailPO->jml_porr = 0.0;
    //             $detailPO->jml_total_porr = 0.0;
    //             $detailPO->status = 'DRAFT';
    //             $detailPO->is_aktif = 1;
    //             $detailPO->created_by = Auth::user()->username;
    //             $detailPO->updated_by = Auth::user()->username;
    //             $detailPO->client_id = $this->clientId;
    //             $success = $detailPO->save();

    //             if(!$success) {
    //                 DB::connection('dbclient')->rollBack();
    //                 return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO.']);
    //             }

    //             //insert harga item PO

    //         }

            

    //         $dataDetailId = PurchaseOrderDetail::where('pengadaan_id', $data->pengadaan_id)->get();
    //         foreach($dataDetailId as $key => $value) {
    //             $hargaPO = new PurchaseOrderHarga();
    //             $hargaPO->detail_id = $value['detail_id'];
    //             $hargaPO->pengadaan_id = $data->pengadaan_id;
    //             $hargaPO->trx_id = $data->trx_id;
    //             $hargaPO->reff_trx_id = $data->reff_trx_id;
    //             $hargaPO->produk_id = $value->produk_id;
    //             $hargaPO->produk_nama = $value->produk_nama;
    //             $hargaPO->satuan_id = $value->satuan_id;
    //             $hargaPO->satuan_beli_id = $value->satuan_beli_id;
    //             $hargaPO->konversi = "0";
    //             $hargaPO->jml_transaksi = $value->jml_po;
    //             $hargaPO->jml_total_transaksi = $value->jml_po;
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
    //             foreach($request[0]['detail_po'] as $key => $result){
    //                 $hargaPO->harga = $result['harga'];
    //                 $hargaPO->harga_sblm_pajak = $result['harga'];
    //                 $hargaPO->harga_akhir = $hargaPO->harga_sblm_pajak - $hargaPO->diskon + $hargaPO->nilai_pajak ;
    //             }
    //             $success = $hargaPO->save();
    //         }

    //          // BUG HERE
    //          $req = PurchaseRequest::where('pr_id', $request[0]['pr_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
    //          $prdetail = PurchaseRequestDetail::where('pr_id', $req->pr_id)->where('produk_id', $request[0]['detail_po'][0]['produk_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
    //          $prdetail->status = 'PO';
    //          $prdetail->pengadaan_id = $data->pengadaan_id;
    //          $prdetail->trx_id = $data->pengadaan_id;
    //          $prdetail->updated_by = Auth::user()->username;
    //          $prdetail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
    //          $success = $prdetail->save();
 
    //          if(!$success) {
    //              return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
    //          }

    //         $this->updatePR($request);

    //         if(!$success) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
    //         }
    //         return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);    
            
    //     }
    //     catch(\Exception $e) {
    //         return response()->json(['success' => false,'message' =>'data tidak berhasil disimpanx','error'=>$e->getMessage()]);
    //     }
    // }

    public function isUserDepo($depoId){
        try {
            $data = MemberDepo::where('user_id',Auth::user()->user_id)
                // ->where('depo_id',$depoId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            if($data) { return true; }
            else { return false; }
        }
        catch(\Exception $e) {
            return false;
        }
    }

    public function isDepoAllowed($depoId){
        try {
            $data = Depo::where('is_purchase_order',1)
                // ->where('depo_id',$depoId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            if($data) { return true; }
            else { return false; }
        }
        catch(\Exception $e) {
            return false;
        }
    }

    /**
     * CREATE PO DIRECTLY
     */
    public function createPO(Request $request)
    {
        try{
            if(count($request->detail_po) <= 0) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Detail PO tidak ditemukan.']);
            }

            if(!$this->isUserDepo($request->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($request->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase order.']);
            }            

            $id = $this->getPOId(); 
            $no_transaksi = $this->getNoPO();
            //$trx = $request->pr_id ? $request->pr_id : $this->getTrxPO();      
            
            DB::connection('dbclient')->beginTransaction();
            $data = new PurchaseOrder();
            $data->pengadaan_id = $id;
            $data->trx_id = $id;
            $data->reff_trx_id = '';

            $data->jns_transaksi = 'Purchase Order';
            $data->tgl_transaksi = Carbon::now()->format('Y-m-d H:i:s');            
            $data->no_transaksi = $no_transaksi;
            $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;

            $data->termin = $request->termin;
            $data->jenis_bayar = $request->jenis_bayar;

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
            $data->tgl_rencana_bayar = $request->tgl_rencana_bayar;
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

            foreach($request->detail_po as $value) {
                /**
                 * insert item PO
                 */

                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$value['produk_id'])->where('is_aktif',1)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Produk tidak ditemukan.']);
                }

                $detailId = $this->getDetailPO();
                $detailPO = new PurchaseOrderDetail();
                $detailPO->detail_id = $detailId;
                $detailPO->pengadaan_id = $id;
                $detailPO->trx_id = $id;
                $detailPO->reff_trx_id = '';
                $detailPO->depo_id = $request->depo_id;
                
                $detailPO->jenis_produk = $value['jenis_produk'];
                $detailPO->produk_id = $value['produk_id'];
                $detailPO->produk_nama = $value['produk_nama'];                
                $detailPO->satuan_beli = $value['satuan_beli'];
                $detailPO->konversi = $produk->konversi;
                $detailPO->satuan = $produk->satuan;                

                $detailPO->jml_po = $value['jml_po'];
                $detailPO->jml_total_po = $value['jml_po'];
                $detailPO->harga = $value['harga'];
                $detailPO->diskon = $value['diskon'];
                $detailPO->nilai_diskon = $value['nilai_diskon'];
                $detailPO->subtotal = $value['subtotal'];
                $detailPO->is_pajak = $value['is_pajak'];
                $detailPO->nilai_pajak = $value['nilai_pajak'];
                $detailPO->persen_pajak = $value['persen_pajak'];
                
                $detailPO->jml_por = 0.0;
                $detailPO->jml_total_por = 0.0;
                $detailPO->jml_porr = 0.0;
                $detailPO->jml_total_porr = 0.0;
                $detailPO->status = 'DRAFT';
                $detailPO->is_aktif = 1;
                $detailPO->created_by = Auth::user()->username;
                $detailPO->updated_by = Auth::user()->username;
                $detailPO->client_id = $this->clientId;
                $success = $detailPO->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO.']);
                }

                /**update data PR detail */
                foreach($value['items'] as $itm) {
                    $prItem = PurchaseRequestDetail::where('client_id',$this->clientId)
                        ->where('pr_detail_id',$itm['pr_detail_id'])
                        ->where('is_aktif',1)->first();
                    
                    if($prItem) {
                        $purchaseReqId = $prItem->pr_id;
                                                
                        $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                            ->where('pr_detail_id',$itm['pr_detail_id'])
                            ->where('is_aktif',1)->update([
                                'pengadaan_id' => $id, 
                                'status' => 'PROSES',
                                'updated_by' => Auth::user()->username,
                            ]);
                        
                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO (update detil PR).']);
                        }
                    }
                }

                //insert harga item PO
                // $hargaPO = new PurchaseOrderHarga();
                // $hargaPO->detail_id = $id;
                // $hargaPO->pengadaan_id = $id;
                
                // $hargaPO->trx_id = $data->trx_id;
                // $hargaPO->reff_trx_id = $data->reff_trx_id;
                // $hargaPO->produk_id = $value->produk_id;
                // $hargaPO->produk_nama = $value->produk_nama;
                // $hargaPO->satuan_id = $value->satuan_id;
                // $hargaPO->satuan_beli_id = $value->satuan_beli_id;
                // $hargaPO->konversi = "0";
                // $hargaPO->jml_transaksi = $value->jml_po;
                // $hargaPO->jml_total_transaksi = $value->jml_po;
                // $hargaPO->is_pajak = '';
                // $hargaPO->persen_pajak = 11;
                // $hargaPO->nilai_pajak = $value;
                // $hargaPO->is_diskon_persen = 0;
                // $hargaPO->diskon = 0.0;
                // $hargaPO->subtotal = 0.0;
                // $hargaPO->hna = 0.0;

                // // BUG HERE
                // foreach($request->detail_po as $key => $result){
                //     $hargaPO->harga = $result['harga'];
                //     $hargaPO->harga_sblm_pajak = $result['harga'];
                //     $hargaPO->harga_akhir = $hargaPO->harga_sblm_pajak - $hargaPO->diskon + $hargaPO->nilai_pajak;
                // }
                // $hargaPO->is_aktif = 1;
                // $hargaPO->created_by = Auth::user()->username;
                // $hargaPO->updated_by = Auth::user()->username;
                // $hargaPO->client_id = $this->clientId;
                // $success = $hargaPO->save();
                // if(!$success) {
                //     DB::connection('dbclient')->rollBack();
                //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO.']);
                // }

            }

            // if(!$success) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            // }

            // $dataDetailId = PurchaseOrderDetail::where('pengadaan_id', $data->pengadaan_id)->get();
            // foreach($dataDetailId as $key => $value) {
            //     $hargaPO = new PurchaseOrderHarga();
            //     $hargaPO->detail_id = $value['detail_id'];
            //     $hargaPO->pengadaan_id = $data->pengadaan_id;
            //     $hargaPO->trx_id = $data->trx_id;
            //     $hargaPO->reff_trx_id = $data->reff_trx_id;
            //     $hargaPO->produk_id = $value->produk_id;
            //     $hargaPO->produk_nama = $value->produk_nama;
            //     $hargaPO->satuan_id = $value->satuan_id;
            //     $hargaPO->satuan_beli_id = $value->satuan_beli_id;
            //     $hargaPO->konversi = "0";
            //     $hargaPO->jml_transaksi = $value->jml_po;
            //     $hargaPO->jml_total_transaksi = $value->jml_po;
            //     $hargaPO->tipe_pajak = '';
            //     $hargaPO->persen_pajak = 0.0;
            //     $hargaPO->nilai_pajak = 0.0;
            //     $hargaPO->is_diskon_persen = 0;
            //     $hargaPO->diskon = 0.0;
            //     $hargaPO->subtotal = 0.0;
            //     $hargaPO->hna = 0.0;
            //     // BUG HERE
            //     foreach($request->detail_po as $key => $result){
            //         $hargaPO->harga = $result['harga'];
            //         $hargaPO->harga_sblm_pajak = $result['harga'];
            //         $hargaPO->harga_akhir = $hargaPO->harga_sblm_pajak - $hargaPO->diskon + $hargaPO->nilai_pajak;
            //     }
            //     $hargaPO->is_aktif = 1;
            //     $hargaPO->created_by = Auth::user()->username;
            //     $hargaPO->updated_by = Auth::user()->username;
            //     $hargaPO->client_id = $this->clientId;
            //     $success = $hargaPO->save();
            // }

            // if(!$success) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            // }

            // if(!$success) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            // }
            DB::connection('dbclient')->commit();
        
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);    
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function updatePO(Request $request) {
        try{
            $id = $request->pengadaan_id;
            $trxId = $request->trx_id;

            if(count($request->detail_po) <= 0) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Detail PO tidak ditemukan.']);
            }

            $data = PurchaseOrder::where('pengadaan_id',$id)->where('trx_id',$trxId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('status','DRAFT')
                ->first();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'data tidak ditemukan atau sudah tidak aktif atau berubah status']);    
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase order.']);
            }

            $no_transaksi = $data->no_transaksi;
            $trx = $data->trx_id;      
            
            DB::connection('dbclient')->beginTransaction();
            $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;
            $data->termin = $request->termin;
            $data->jenis_bayar = $request->jenis_bayar;
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
            $data->tgl_rencana_bayar = $request->tgl_rencana_bayar;
            $data->data_versi = Uuid::uuid4()->getHex();
            $data->status = 'DRAFT';
            $data->is_aktif = 1;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }

            /**
             * update flag purchase request detail
             */
            $prDetail = PurchaseRequestDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('pengadaan_id',$id)
                ->first();
            if($prDetail) {
                $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('pengadaan_id',$id)
                    ->update(['pengadaan_id'=>null, 'status'=>'PENGAJUAN']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Gagal ubah status detail PR']);
                }                
            }
            
            foreach($request->detail_po as $value) {
                /**
                 * insert item PO
                 */
                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$value['produk_id'])->where('is_aktif',1)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Produk tidak ditemukan.']);
                }

                $detailPO = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('pengadaan_id',$id)
                    ->where('trx_id',$trx)
                    ->where('detail_id',$value['detail_id'])
                    ->where('is_aktif',1)
                    ->first();

                if(!$detailPO) {
                    $detailId = $this->getDetailPO();

                    $detailPO = new PurchaseOrderDetail();
                    $detailPO->detail_id = $detailId;
                    $detailPO->pengadaan_id = $id;
                    $detailPO->trx_id = $trx;
                    $detailPO->reff_trx_id = '';
                    $detailPO->depo_id = $request->depo_id;
                    
                    $detailPO->jenis_produk = $value['jenis_produk'];
                    $detailPO->produk_id = $value['produk_id'];
                    $detailPO->produk_nama = $value['produk_nama'];                
                    $detailPO->satuan_beli = $value['satuan_beli'];
                    $detailPO->konversi = $produk->konversi;
                    $detailPO->satuan = $produk->satuan;                

                    $detailPO->jml_po = $value['jml_po'];
                    $detailPO->harga = $value['harga'];
                    $detailPO->diskon = $value['diskon'];
                    $detailPO->nilai_diskon = $value['nilai_diskon'];
                    $detailPO->subtotal = $value['subtotal'];
                    $detailPO->is_pajak = $value['is_pajak'];
                    $detailPO->nilai_pajak = $value['nilai_pajak'];
                    $detailPO->persen_pajak = $value['persen_pajak'];
                    
                    $detailPO->jml_por = 0.0;
                    $detailPO->jml_total_por = 0.0;
                    $detailPO->jml_porr = 0.0;
                    $detailPO->jml_total_porr = 0.0;
                    $detailPO->status = 'DRAFT';
                    $detailPO->is_aktif = 1;
                    $detailPO->created_by = Auth::user()->username;
                    $detailPO->updated_by = Auth::user()->username;
                    $detailPO->client_id = $this->clientId;
                    $success = $detailPO->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO.']);
                    }
                }
                else {
                    $detailPO->satuan_beli = $value['satuan_beli'];
                    $detailPO->konversi = $produk->konversi;
                    $detailPO->satuan = $produk->satuan;       
                    $detailPO->jml_po = $value['jml_po'];
                    $detailPO->harga = $value['harga'];
                    $detailPO->diskon = $value['diskon'];
                    $detailPO->nilai_diskon = $value['nilai_diskon'];
                    $detailPO->subtotal = $value['subtotal'];
                    $detailPO->is_pajak = $value['is_pajak'];
                    $detailPO->nilai_pajak = $value['nilai_pajak'];
                    $detailPO->persen_pajak = $value['persen_pajak'];
                    $detailPO->jml_por = 0.0;
                    $detailPO->jml_total_por = 0.0;
                    $detailPO->jml_porr = 0.0;
                    $detailPO->jml_total_porr = 0.0;
                    $detailPO->status = 'DRAFT';
                    $detailPO->is_aktif = $value['is_aktif'];
                    $detailPO->updated_by = Auth::user()->username;
                    $success = $detailPO->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO.']);
                    }
                }

                /**update data PR detail */
                foreach($value['items'] as $itm) {
                    $prItem = PurchaseRequestDetail::where('client_id',$this->clientId)
                        ->where('pr_detail_id',$itm['pr_detail_id'])
                        ->where('is_aktif',1)
                        ->where('pengadaan_id',null)->first();
                    
                    if($prItem) {
                        $purchaseReqId = $prItem->pr_id;
                                                
                        $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                            ->where('pr_detail_id',$itm['pr_detail_id'])
                            ->where('is_aktif',1)
                            ->where('pengadaan_id',null)->update([
                                'pengadaan_id' => $id, 
                                'status' => 'PROSES',
                                'updated_by' => Auth::user()->username,
                            ]);
                        
                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data detail PO (update detil PR).']);
                        }
                    }
                }
            }
            DB::connection('dbclient')->commit();
        
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);    
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    // public function updatePO(Request $request)
    // {
    //     try{
    //         $dist_gizi_id = $request->dist_gizi_id;
    //         $data = DistribusiGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('dist_gizi_id', $dist_gizi_id)->first();
    //         if(!$data) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
    //         }

    //         $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;
    //         $data->unit_id = $request->unit_id;
    //         $data->catatan = $request->catatan;
    //         $data->is_aktif = 1;
    //         $data->updated_by = Auth::user()->username;

    //         $success = $data->save();

    //         if(!$success) {
    //             return response()->json(['success'=>true,'message'=>'Ada kesalahan saat mengubah data.']);    
    //         }
    //         return response()->json(['success'=>true,'message'=>'data berhasil disimpan','data'=>$data]);
    //     }
    //     catch(\Exception $e) {
    //         return response()->json(['success' => false,'message' =>'Ada kesalahan saat mengubah data','error'=>$e->getMessage()]);
    //     }
    // }

    public function approvePO(Request $request, $trxId)
    {
        try {
            $data = PurchaseOrder::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id', $trxId)->where('status','DRAFT')->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan / sudah berubah status.']);
            }
            $id = $data->pengadaan_id;

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase order.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $data->status = 'DISETUJUI';
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam approve pembelian.']);
            }

            $detail = PurchaseOrderDetail::where('pengadaan_id', $id)->where('trx_id', $trxId)
                ->where('status', 'DRAFT')->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            
            if($detail) {
                $success = PurchaseOrderDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('pengadaan_id', $id)
                    ->where('trx_id', $trxId)
                    ->update([
                        'status'=>'DISETUJUI',
                        'updated_by'=>Auth::user()->username
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam proses pembelian.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true,'message' => 'Berhasil approve pembelian']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam approve pembelian.','error'=> $e->getMessage()]);
        }
    }

    // public function cancelPO(Request $request, $id)
    // {
    //     try{
    //         $data = PurchaseOrder::where('client_id',$this->clientId)->where('is_aktif',1)->where('pengadaan_id', $id)->first();
    //         if(!$data) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
    //         }

    //         $data->status = 'CANCELLED';
    //         $data->updated_by = Auth::user()->username;
    //         $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
    //         $success = $data->save();

    //         $detail = PurchaseOrderDetail::where('client_id',$this->clientId)->where('is_aktif',1)->where('pengadaan_id', $data->pengadaan_id)->first();
    //         foreach($detail as $key => $value) {
    //             $detail->status = 'CANCELLED';
    //             $detail->updated_by = Auth::user()->username;
    //             $detail->updated_at = Carbon::now()->format('Y-m-d H:i:s');
    //             $success = $detail->save();
    //         }

    //         if(!$success) {
    //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel pembelian.']);
    //         }
    //         return response()->json(['success' => true,'message' => 'Berhasil cancel pembelian']);   
           
    //     }
    //     catch(\Exception $e) {
    //         return response()->json(['success' => false,'message' =>'ada kesalahan dalam cancel pembelian.','error'=> $e->getMessage()]);
    //     }
    // }

    public function prosesPO(Request $request, $trxId)
    {
        try{
            $data = PurchaseOrder::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id', $trxId)->where('status','DISETUJUI')->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan atau berubah status.']);
            }
            $id = $data->pengadaan_id;

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase order.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $data->status = 'PEMBELIAN';
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam proses pembelian.']);
            }

            $detail = PurchaseOrderDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('pengadaan_id', $id)
                ->where('trx_id', $data->trx_id)
                ->first();
            if($detail) {
                $success = PurchaseOrderDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('pengadaan_id', $id)
                    ->where('trx_id', $trxId)
                    ->update([ 'status'=>'PEMBELIAN', 'updated_by'=>Auth::user()->username]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam proses pembelian.']);
                }
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true,'message' => 'Berhasil proses pembelian']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam cancel pembelian.','error'=> $e->getMessage()]);
        }
    }

    public function deletePO(Request $request, $trxId) {
        try {
            $data = PurchaseOrder::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->whereIn('status',['DRAFT','DISETUJUI','PEMBELIAN'])
                ->where('trx_id', $trxId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase order.']);
            }

            $id = $data->pengadaan_id;

            DB::connection('dbclient')->beginTransaction();

            $data->status = 'BATAL';
            $data->is_aktif = false;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel pembelian.']);
            }

            $detail = PurchaseOrderDetail::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('pengadaan_id', $data->pengadaan_id)
                ->where('trx_id', $trxId)
                ->first();

            if($detail) {
                $success = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('pengadaan_id', $data->pengadaan_id)
                    ->where('trx_id', $trxId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status'=>'BATAL' ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel pembelian.']);
                }
            }

            $detailPR = PurchaseRequestDetail::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('pengadaan_id', $data->pengadaan_id)
                ->first();

            if($detailPR) {
                $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('pengadaan_id', $data->pengadaan_id)
                    ->update(['status'=>'PENGAJUAN', 'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel pembelian.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true,'message' => 'Berhasil cancel pembelian']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam cancel pembelian.','error'=> $e->getMessage()]);
        }
    }

    public function getPOId()
    {
        try {
            $id = $this->clientId.'.'.date('Ym').'-PO0001';
            $maxId = PurchaseOrder::withTrashed()->where('client_id', $this->clientId)
                ->where('pengadaan_id','LIKE',$this->clientId.'.'.date('Ym').'-PO%')
                ->max('pengadaan_id');

            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'-PO0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'-PO','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'-PO000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'-PO00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'-PO0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'-PO'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'-PO' . Uuid::uuid4()->getHex();
        }
    }

    public function getNoPO() 
    {
        try {
            $id = Carbon::now()->format('Y').'.PO-0001';
            $maxId = PurchaseOrder::withTrashed()->where('client_id', Carbon::now()->format('Y'))->where('pengadaan_id','LIKE',Carbon::now()->format('Y').'.PO-%')->max('pengadaan_id');
            if (!$maxId) { $id = Carbon::now()->format('Y').'.PO-0001'; }
            else {
                $maxId = str_replace(Carbon::now()->format('Y').'.PO-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = Carbon::now()->format('Y').'.PO-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = Carbon::now()->format('Y').'.PO-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = Carbon::now()->format('Y').'.PO-0'.$count; } 
                else { $id = Carbon::now()->format('Y').'.PO-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return Carbon::now()->format('Y').'.PO-' . Uuid::uuid4()->getHex();
        }
    }

    // public function getTrxPO() 
    // {
    //     try {
    //         $id = $this->clientId.'.'.date('Ym').'-TRPO0001';
    //         $maxId = PurchaseOrder::withTrashed()->where('client_id', $this->clientId)->where('trx_id','LIKE',$this->clientId.'.'.date('Ym').'-TRPO%')->max('trx_id');
    //         if (!$maxId) { $id = $this->clientId.'.TRPO-0001'; }
    //         else {
    //             $maxId = str_replace($this->clientId.'.TRPO-','',$maxId);
    //             $count = $maxId + 1;
    //             if ($count < 10) { $id = $this->clientId.'.TRPO-000'.$count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.TRPO-00'.$count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.TRPO-0'.$count; } 
    //             else { $id = $this->clientId.'.TRPO-'.$count; } 
    //         }
    //         return $id;
    //     } catch(\Exception $e){
    //         return $this->clientId.'.TRPO-' . Uuid::uuid4()->getHex();
    //     }
    // }

    public function getDetailPO() 
    {
        try {
            $id = $this->clientId.'.POD-0001';
            $maxId = PurchaseOrderDetail::withTrashed()->where('client_id', $this->clientId)->where('detail_id','LIKE',$this->clientId.'.POD-%')->max('detail_id');
            if (!$maxId) { $id = $this->clientId.'.POD-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.POD-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.POD-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.POD-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.POD-0'.$count; } 
                else { $id = $this->clientId.'.POD-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.POD-' . Uuid::uuid4()->getHex();
        }
    }

    public function updatePR(Request $request) {
        try {
            $req = PurchaseRequest::where('pr_id', $request[0]['pr_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $req->status = $request[0]['detail_po'][0]['is_selected_all'] == true ? 'PO' : 'PARTIAL';
            $req->updated_by = Auth::user()->username;
            $success = $req->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }

        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam cancel pembelian.','error'=> $e->getMessage()]);
        }
    }
}
