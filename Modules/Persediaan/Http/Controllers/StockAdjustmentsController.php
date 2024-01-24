<?php

namespace Modules\Persediaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Carbon;

use Modules\Persediaan\Entities\StockAdjustments;
use Modules\Persediaan\Entities\StockAdjustmentsDetail;
use Modules\Persediaan\Entities\KartuStock;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\Depo;

use Modules\ManajemenUser\Entities\MemberDepo;

class StockAdjustmentsController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'Tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }
    
    public function list(Request $request)
    {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }
 
            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }    

            $kolom = "";
            if($request->has('kolom')) {
                $kolom = $request->input('kolom');
            }

            $tgl_pencarian_awal = Carbon\Carbon::now();
            if($request->has('tgl_pencarian_awal')) {
                $tgl_pencarian_awal = $request->input('tgl_pencarian_awal');
            }    

            $tgl_pencarian_akhir = Carbon\Carbon::now();
            if($request->has('tgl_pencarian_akhir')) {
                $tgl_pencarian_akhir = $request->input('tgl_pencarian_akhir');
            } 

            $list = StockAdjustments::where('tb_stock_adjustment.client_id',$this->clientId)
                    ->where('tb_stock_adjustment.is_aktif','ILIKE',$aktif)
                    ->leftJoin('tb_depo','tb_stock_adjustment.depo_id','=','tb_depo.depo_id')
                    ->select('tb_stock_adjustment.*', 'tb_depo.depo_nama')
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_depo.depo_nama','ILIKE',$keyword)
                        ->orWhere('tb_stock_adjustment.approver','LIKE',$keyword)
                        ->orWhere('tb_stock_adjustment.status','LIKE',$keyword);
                    })->orderBy('tb_stock_adjustment.created_at','DESC'); 
            
            if($kolom != ""){
                $tgl_pencarian_awal = $request->tgl_pencarian_awal;
                $tgl_pencarian_akhir = $request->tgl_pencarian_akhir;
                $list->whereBetween($kolom,[$tgl_pencarian_awal,$tgl_pencarian_akhir]);
            }

            $list = $list->paginate($per_page);

            foreach($list->items() as $item) {
                $detail = StockAdjustmentsDetail::where('sa_id',$item['sa_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->orderBy('produk_nama')->get(); 
                // foreach($detail as $dt) {
                //     $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$item['depo_id'])->where('produk_id',$dt['produk_id'])->first();
                //     if($depoProduk) {
                //         $dt['total_awal'] = $depoProduk->jml_total;
                //         $dt['total_akhir'] = ($depoProduk->jml_total + $dt['jml_penyesuaian']);
                //     }
                // }            
                $item['items'] = $detail;
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List stok adjustment tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
    
    public function data(Request $request, $id)
    {
        try {
            // $data = DB::connection('dbclient')->table('tb_stock_adjustment as sa')
            //             ->join('tb_stock_adjustment_detail as sad','sa.sa_id','=','sad.sa_id')
            //             ->join('tb_depo_produk as dp','sa.depo_id','=','dp.depo_id')
            //             ->where('sa.sa_id',$id)
            //             //->where('sa.depo_id','dp.depo_id')
            //             ->where('sa.client_id',$this->clientId)
            //             ->where('sa.is_aktif','TRUE')
            //             ->get(); 

            // if(!$data) {
            //     return response()->json(['success'=>false,'message'=>'Data tidak ditemukan.']);
            // }

            // $detail = StockAdjustmentsDetail::where('sa_id', $id)
            //             ->where('client_id',  $this->clientId)
            //             ->paginate(10);
                        
            // $detail = DepoProduk::where('tb_depo_produk.depo_id',$depo_id)->where('tb_depo_produk.client_id',$this->clientId)->where('tb_depo_produk.is_aktif',1)
            //             ->join('tb_produk','tb_depo_produk.produk_id','=','tb_produk.produk_id')
            //             ->select('tb_depo_produk.*','tb_produk.produk_nama')
            //             ->orderBy('tb_produk.produk_nama')->paginate(10); 

            // $data->detail = $detail;

            $data = $this->getDataAdjustment($id);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian Stok Adjustment tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }
    
    public function getDataAdjustment($id)
    {
        try {
            $data = StockAdjustments::where('sa_id',$id)->where('client_id',$this->clientId)->first();
            $detail = StockAdjustmentsDetail::where('sa_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->orderBy('produk_nama')->get(); 
            
            // foreach($detail as $dt) {
            //     $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('depo_id',$data->depo_id)->where('is_aktif',1)->where('produk_id',$dt['produk_id'])->first();
            //     if($depoProduk) {
            //         //$dt['total_awal'] = $depoProduk->jml_total;
            //         //$dt['total_akhir'] = ($depoProduk->jml_total + $dt['jml_penyesuaian']);
            //     }
            // }            
            $data['items'] = $detail;
            return $data;
        } 
        catch(\Exception $e) {
            return null;
        }
    }

    public function create(Request $request)
    {
        try {
            $status = "RENCANA";
            //check apakah depo dibawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_id)->whereIn('depo_id',$memberDepo)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            if($depo->is_lock) {
                return response()->json(['success' => false, 'message' => 'Depo dalam posisi terkunci. Data tidak dapat disimpan.']);
            }

            // Create Adjustment
            $tglTransaksi = date('Y-m-d H:i:s');
            $id = $this->getStockAdjustmentsId();

            DB::connection('dbclient')->beginTransaction();
            $data = new StockAdjustments();
            $data->sa_id            = $this->getStockAdjustmentsId($this->clientId);
            $data->tgl_dibuat       = $tglTransaksi;
            $data->tgl_sa           = $request->tgl_sa;
            $data->tgl_selesai      = $request->tgl_selesai;
            $data->catatan          = $request->catatan;
            $data->depo_id          = $request->depo_id;
            $data->depo_nama        = $request->depo_nama;
            $data->unit_id          = $request->unit_id;
            $data->approver_id      = "-";
            $data->approver         = "-";
            $data->status           = $status;
            $data->is_aktif         = 1;
            $data->client_id        = $this->clientId;
            $data->created_by       = Auth::user()->username;
            $data->updated_by       = Auth::user()->username;
            
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data penyesuaian persediaan']);
            }

            // Create Adjustment Detail
            foreach($request->items as $produk){
                $item = Produk::where('produk_id',$produk['produk_id'])->select('produk_nama')->first();
                if(!$item) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data stock adjustment(detail)']);
                }

                $produk["produk_nama"] = $item['produk_nama'];

                $createSADetail = new StockAdjustmentsDetail();
                $createSADetail->sa_detail_id     = $this->clientId.date('Ymd').Uuid::uuid4()->getHex();
                $createSADetail->sa_id            = $data->sa_id;
                $createSADetail->depo_id          = $data->depo_id;
                $createSADetail->produk_id        = $produk["produk_id"];
                $createSADetail->depo_nama        = $data->depo_nama;
                $createSADetail->produk_nama      = $produk["produk_nama"];
                $createSADetail->total_awal       = $produk["total_awal"];
                $createSADetail->total_akhir      = $produk["total_akhir"];
                $createSADetail->jml_penyesuaian  = $produk["jml_penyesuaian"];
                $createSADetail->satuan           = $produk["satuan"];
                $createSADetail->status           = $status;
                $createSADetail->is_aktif         = 1;
                $createSADetail->client_id        = $this->clientId;
                $createSADetail->created_by       = Auth::user()->username;
                $createSADetail->updated_by       = Auth::user()->username;
                
                $success = $createSADetail->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data detail penyesuaian persediaan.']);
                }
            }

            DB::connection('dbclient')->commit();
            
            $data = $this->getDataAdjustment($id);
            return response()->json(['success' => true,'message' => 'Data penyesuaian persediaan berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Tambah Stok Adjustment tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id   = $request->sa_id;
            $data = StockAdjustments::where('sa_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data penyesuaian persediaan tidak ditemukan.']);
            } 
            
            if ($data->status !== "RENCANA") {
                return response()->json(['success' => false, 'message' => 'Staus sekarang adalah '. $data->status .'. Anda tidak bisa mengubah DATA dengan status selain RENCANA.']);
            }

            DB::connection('dbclient')->beginTransaction();

            $success = $data->update([
                    $data->catatan     = $request->catatan,
                    $data->unit_id     = $request->unit_id,
                    $data->updated_by  = Auth::user()->username,
                ]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ubah data tidak berhasil.']);
            } 
            
            // Create Adjustment Detail
            foreach($request->items as $dt){                
                $produk = Produk::where('produk_id',$dt['produk_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->select('produk_nama')->first();
                if (!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ubah data tidak berhasil. Produk tidak ditemukan.']);
                }                 
                
                $dt["produk_nama"] = $produk['produk_nama'];
                $detail = StockAdjustmentsDetail::where('produk_id',$dt['produk_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->where('sa_id',$id)->first();

                if(!$detail) {
                    $detail = new StockAdjustmentsDetail();
                    $detail->sa_detail_id     = $this->clientId.date('Ymd').Uuid::uuid4()->getHex();
                    $detail->sa_id            = $id;
                    $detail->depo_id          = $data->depo_id;
                    $detail->produk_id        = $dt["produk_id"];
                    $detail->depo_nama        = $data->depo_nama;
                    $detail->produk_nama      = $dt["produk_nama"];
                    $detail->total_awal       = $dt["total_awal"];
                    $detail->total_akhir      = $dt["total_akhir"];
                    $detail->jml_penyesuaian  = $dt["jml_penyesuaian"];
                    $detail->satuan           = $dt["satuan"];
                    $detail->status           = 'RENCANA';
                    $detail->is_aktif         = 1;
                    $detail->client_id        = $this->clientId;
                    $detail->created_by       = Auth::user()->username;
                    $detail->updated_by       = Auth::user()->username;
                
                }
                else {
                    $detail->produk_nama      = $dt['produk_nama'];
                    $detail->total_awal       = $dt["total_awal"];
                    $detail->total_akhir      = $dt["total_akhir"];
                    $detail->jml_penyesuaian  = $dt["jml_penyesuaian"];
                    $detail->is_aktif         = $dt['is_aktif'];
                    $detail->updated_by       = Auth::user()->username;                
                }

                $success = $detail->save();    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data detail penyesuaian persediaan.']);
                }                 
            }            

            DB::connection('dbclient')->commit();
            
            $data = $this->getDataAdjustment($id);
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'sUbah Stok Adjustment tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // Status APPROVE
    public function approve(Request $request, $id)
    {
        try {
            $data = StockAdjustments::where('sa_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->where('status','RENCANA')->first();

            if(!$data){
                return response()->json(['success'=>false,'message'=>'Stok Adjustment sudah dinon-aktifkan/sudah berubah status.','data'=>$data]);
            }

            //check apakah depo dibawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }
            if($depo->is_lock) {
                return response()->json(['success' => false, 'message' => 'Depo dalam posisi terkunci. Data tidak dapat disimpan.']);
            }

            /**
             * check detail stock adjustment
             */
            $detail = StockAdjustmentsDetail::where('sa_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->get();
            if(!$detail){
                return response()->json(['success'=>false,'message'=>'Stok Adjustment Detail tidak ditemukan.','data'=>$data]);
            }

            $tglSelesai = date('Y-m-d H:i:s');

            DB::connection('dbclient')->beginTransaction();
            $success = $data->update([
                    'tgl_selesai'   => $tglSelesai,
                    'status'        => 'SELESAI',
                    'updated_by'    => Auth::user()->username,
                    'approver'      => Auth::user()->username,
                    'approver_id'   => Auth::user()->user_id,
                    'updated_at'    => now()
                ]);

            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json([
                    'success'=>false,
                    'message'=>'Status Stok Adjustment sekarang adalah '. $data->status .'. Hanya status RENCANA atau APPROVE yang bisa dieksekusi.'
                ]);
            }

            foreach($detail as $dt) {
                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$dt['produk_id'])->where('is_aktif',1)->first();
                $dt['produk_nama'] = $produk->produk_nama;
                $dt['jenis_produk'] = $produk->jenis_produk;
                
                $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('produk_id',$dt['produk_id'])
                    ->where('depo_id',$data->depo_id)
                    ->where('is_aktif',1)->first();
                
                if(!$depoProduk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /**
                 * periksa apakah penyesuaian menyebabkan nilai persediaan menjadi minus
                 */
                
                if($dt['jml_penyesuaian'] < 0) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak boleh kurang dari nol.']);
                }

                $dt['jml_total'] = $depoProduk->jml_total;

                /**
                 * update data persediaan
                 */
                $success = $depoProduk->update([
                        'updated_by' => Auth::user()->username,
                        'jml_penyesuaian' => DB::raw('jml_penyesuaian + '.$dt['jml_penyesuaian']),
                        'jml_total' => DB::raw('jml_total + '.$dt['jml_penyesuaian']),
                    ]);
                    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /**
                 * update status detail
                 */
                $success = StockAdjustmentsDetail::where('client_id',$this->clientId)
                        ->where('produk_id',$dt['produk_id'])->where('is_aktif',1)
                        ->update(['updated_by' => Auth::user()->username,'status' => 'SELESAI']);
                    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data status item penyesuaian di depo tidak ditemukan.']);
                }

                /**
                 * buat kartu stock
                 */
                $kartuStock = new KartuStock();
                $kartuStock->stock_log_id     = $this->clientId."-".Uuid::uuid4()->getHex();
                $kartuStock->reg_id           = $data->sa_id;
                $kartuStock->trx_id           = $data->sa_id;
                $kartuStock->sub_trx_id       = $data->sa_id;
                $kartuStock->detail_id        = $dt['sa_detail_id'];
                $kartuStock->produk_id        = $dt["produk_id"];
                $kartuStock->tgl_log          = date('Y-m-d H:i:s');
                $kartuStock->tgl_transaksi    = $tglSelesai;
                $kartuStock->jns_transaksi    = "STOCK ADJUSTMENT";
                $kartuStock->aksi             = "SIMPAN";
                // Sementara Jenis Produk menggunakan MEDIS
                $kartuStock->jns_produk       = $dt['jenis_produk'];
                $kartuStock->produk_nama      = $dt["produk_nama"];
                $kartuStock->unit_id          = $dt["depo_id"];
                $kartuStock->depo_id          = $dt["depo_id"];
                $kartuStock->jml_awal         = 0;    
                $kartuStock->jml_masuk        = 0;
                $kartuStock->jml_keluar       = 0;
                $kartuStock->jml_penyesuaian  = $dt['jml_penyesuaian'];
                $kartuStock->jml_akhir        = ($dt["jml_penyesuaian"] + $dt['jml_total'])*1;
                $kartuStock->catatan          = $data->catatan;
                $kartuStock->keterangan       = "Stock Adjustment depo ".$data->depo_nama;
                $kartuStock->client_id        = $this->clientId;
                $kartuStock->created_by       = Auth::user()->username;
                $kartuStock->updated_by       = Auth::user()->username;
                
                $success = $kartuStock->save();
    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak dapat disimpan.']);
                } 
            }            

            DB::connection('dbclient')->commit();
            
            $data = $this->getDataAdjustment($id);
            return response()->json(['success'=>true,'message'=>'data item penyesuaian berhasil disimpan.','data'=>$data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah status Stok Adjustment tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = StockAdjustments::where('sa_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'penyesuaian persediaan tidak ditemukan.']);
            }

            if($data->status == "SELESAI"){
                return response()->json(['success' => false,'message' => 'Status penyesuaian sekarang adalah '. $data->status .' hanya status RENCANA yang bisa di BATALKAN']);
            }

            DB::connection('dbclient')->beginTransaction();
            /**
             * update master data
             */
            $success = $data->update([
                    'updated_by' => Auth::user()->username,
                    'status'        => "BATAL",
                    'updated_at'    => now(),
                    'is_aktif'      => false
                ]);
         
            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam NON-AKTIFKAN data penyesuaian persediaan.']);

            }
            /**
             * update data detail
             */
            if(StockAdjustmentsDetail::where('sa_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first()) {
                $success = StockAdjustmentsDetail::where('sa_id', $id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username,'status'=>'BATAL']);

                if(!$success){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam NON-AKTIFKAN data detail penyesuaian persediaan.']);    
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'Data berhasil dinonaktifkan.']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Hapus penyesuaian tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getStockAdjustmentsId() 
    {
        try {
            $id = $this->clientId.'-SA'.date('Ymd').'-0001';
            $maxId = StockAdjustments::withTrashed()->where('sa_id','ILIKE',$this->clientId.'-SA'.date('Ymd').'-%')->max('sa_id');
            if (!$maxId) { $id = $this->clientId.'-SA'.date('Ymd').'-0001'; }
            else {
                $maxId = str_replace($this->clientId.'-SA'.date('Ymd').'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-SA'.date('Ymd').'-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-SA'.date('Ymd').'-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-SA'.date('Ymd').'-0'.$count; } 
                else { $id = $this->clientId.'-SA'.date('Ymd').'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'SA-'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

}
