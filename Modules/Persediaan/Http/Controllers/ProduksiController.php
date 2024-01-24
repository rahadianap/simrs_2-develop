<?php

namespace Modules\Persediaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Carbon;

use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\DepoProduk;
use Modules\Persediaan\Entities\Produksi;
use Modules\Persediaan\Entities\ProduksiDetail;
use Modules\Persediaan\Entities\KartuStock;

use Modules\ManajemenUser\Entities\MemberDepo;

class ProduksiController extends Controller
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
        try {            
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Produksi::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
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
            
            $list = Produksi::where('tb_produksi.client_id',$this->clientId)
                    ->where('tb_produksi.is_aktif','ILIKE',$aktif)
                    ->leftJoin('tb_depo as depo','tb_produksi.depo_id','=','depo.depo_id')
                    ->select('tb_produksi.*','depo.depo_nama')
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_produksi.status','LIKE',$keyword)
                        ->orWhere('depo.depo_nama','LIKE',$keyword);
                    })->orderBy('tb_produksi.created_at','DESC'); 
            
            if($kolom != ""){
                $tgl_pencarian_awal = $request->tgl_pencarian_awal;
                $tgl_pencarian_akhir = $request->tgl_pencarian_akhir;
                $list->whereBetween($kolom,[$tgl_pencarian_awal,$tgl_pencarian_akhir]);
            }
            $list = $list->paginate($per_page);

            foreach($list->items() as $item) {
                $item['items'] = ProduksiDetail::where('produksi_id',$item['produksi_id'])
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->orderBy('jml_hasil','DESC')
                    ->get(); 
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List produksi tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function listItemResults(Request $request,$depo)
    {
        try {
            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = DepoProduk::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }            
            
            $list = DepoProduk::leftJoin('tb_produk','tb_produk.produk_id','=','tb_depo_produk.produk_id') 
                    ->where('tb_depo_produk.client_id',$this->clientId)
                    ->where('tb_depo_produk.is_aktif',1)
                    ->where('tb_depo_produk.depo_id',$depo)
                    ->where('tb_produk.is_hasil_produksi',true)
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_depo_produk.depo_produk_id','ILIKE',$keyword)
                        ->orWhere('tb_depo_produk.produk_id','ILIKE',$keyword)
                        ->orWhere('tb_produk.produk_nama','ILIKE',$keyword);
                    })
                    ->select(
                        'tb_depo_produk.depo_produk_id',
                        'tb_depo_produk.produk_id',
                        'tb_produk.produk_nama',
                        'tb_produk.jenis_produk',
                        'tb_produk.satuan',
                        'tb_depo_produk.is_aktif',
                        'tb_depo_produk.jml_awal',
                        'tb_depo_produk.jml_masuk',
                        'tb_depo_produk.jml_keluar',
                        'tb_depo_produk.jml_penyesuaian',
                        'tb_depo_produk.jml_total',
                        )
                    ->orderBy('tb_depo_produk.jenis_produk','ASC')
                    ->orderBy('tb_depo_produk.depo_produk_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List depo produk tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
    
    public function data(Request $request, $id)
    {
        try {          
            $authorized = false;  
            $data = Produksi::where('produksi_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data produksi tidak ditemukan.']);
            }
            $depo = Depo::where('client_id',$this->clientId)->where('depo_id',$data->depo_id)->first();            
           
            $data['depo_nama'] = $depo->depo_nama;
            $data['items'] = ProduksiDetail::where('produksi_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->where('is_hasil',false)->get(); 
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian produksi tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {        
        try {
            // dd($request);
            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_id)->whereIn('depo_id',$memberDepo)->first();            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            $ids = $this->getProduksiId();
            DB::connection('dbclient')->beginTransaction();
            $data = new Produksi();
            $data->produksi_id      = $ids;
            $data->depo_id          = $request->depo_id;
            //$data->produk_id        = $request->produk_hasil_id;
            $data->catatan          = $request->catatan;
            $data->tgl_produksi     = date('Y-m-d H:i:s');
            $data->tgl_selesai      = $request->tgl_selesai;
            //$data->unit_id          = $request->unit_id;
            // Status otomatis ke dalam RENCANA ketika produksi pertama kali dibuat
            $data->produk_hasil_id = $request->produk_hasil_id;
            $data->produk_hasil  = $request->produk_hasil;
            $data->jml_hasil     = $request->jml_hasil;
            $data->satuan        = $request->satuan;            
            $data->status        = "RENCANA";
            $data->is_aktif      = 1;
            $data->client_id     = $this->clientId;
            $data->created_by    = Auth::user()->username;
            $data->updated_by    = Auth::user()->username;
            
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produksi']);
            } 
            
            /**
             * simpan detail hasil produksi
             * produk yang akan dihasilkan
             */
            // foreach($request->items as $dt) {
            //     $createDetail = new ProduksiDetail();
            //     $createDetail->produksi_detail_id = $ids.'-'.Uuid::uuid4()->getHex();
            //     $createDetail->produksi_id        = $ids;
            //     $createDetail->produk_id          = $dt['produk_id'];
            //     $createDetail->produk_nama        = $dt['produk_nama'];
            //     $createDetail->depo_id            = $dt['depo_id'];
            //     $createDetail->satuan             = $dt['satuan'];
            //     $createDetail->is_hasil           = true;
            //     $createDetail->jml_hasil          = $dt['jml_hasil'];
            //     $createDetail->jml_bahan          = $dt['jml_bahan'];
            //     $createDetail->status             = "RENCANA";
            //     $createDetail->is_aktif           = 1;
            //     $createDetail->client_id          = $this->clientId;
            //     $createDetail->created_by         = Auth::user()->username;
            //     $createDetail->updated_by         = Auth::user()->username;
            //     $success = $createDetail->save();
            // }
            $createDetail = new ProduksiDetail();
            $createDetail->produksi_detail_id = $ids.'-'.Uuid::uuid4()->getHex();
            $createDetail->produksi_id        = $ids;
            $createDetail->produk_id          = $request->produk_hasil_id;
            $createDetail->produk_nama        = $request->produk_hasil;
            $createDetail->depo_id            = $request->depo_id;
            $createDetail->satuan             = $request->satuan;
            $createDetail->is_hasil           = true;
            $createDetail->jml_hasil          = $request->jml_hasil;
            $createDetail->jml_bahan          = 0;
            $createDetail->status             = "RENCANA";
            $createDetail->is_aktif           = 1;
            $createDetail->client_id          = $this->clientId;
            $createDetail->created_by         = Auth::user()->username;
            $createDetail->updated_by         = Auth::user()->username;
            $success = $createDetail->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produksi Detail']);
            } 

            // Produksi berupa bahan
            foreach($request->items as $dt) {
                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$dt['produk_id'])->where('is_aktif',1)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Data produk persediaan tidak ditemukan. data tidak dapat disimpan.']);
                }

                $createDetail = new ProduksiDetail();
                $createDetail->produksi_detail_id = $ids.'-'.Uuid::uuid4()->getHex();
                $createDetail->produksi_id        = $ids;
                $createDetail->produk_id          = $dt["produk_id"];
                $createDetail->produk_nama        = $produk->produk_nama; //$dt["produk_nama"];
                $createDetail->depo_id            = $dt["depo_id"];
                $createDetail->satuan             = $dt["satuan"];
                $createDetail->is_hasil           = $dt["is_hasil"];
                $createDetail->jml_hasil          = $dt["jml_hasil"];
                $createDetail->jml_bahan          = $dt["jml_bahan"];
                $createDetail->status             = "RENCANA";
                $createDetail->is_aktif           = 1;
                $createDetail->client_id          = $this->clientId;
                $createDetail->created_by         = Auth::user()->username;
                $createDetail->updated_by         = Auth::user()->username;
                $success = $createDetail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produksi Detail']);
                } 
            } 

            DB::connection('dbclient')->commit();

            $data = Produksi::where('client_id',$this->clientId)->where('produksi_id',$ids)->first();
            $data['items'] = ProduksiDetail::where('client_id',$this->clientId)->where('produksi_id',$ids)->where('is_aktif',1)->where('is_hasil',false)->get();

            return response()->json(['success' => true,'message' => 'Data Produksi berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Tambah Produksi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {            
            /**
             * hanya data dengan status RENCANA saja yang bisa diupdate
             */
            $id = $request->produksi_id;
            $data = Produksi::where('produksi_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) { return response()->json(['success' => false, 'message' => 'Data produksi tidak ditemukan.']); } 

            if ($data->status != "RENCANA") {
                return response()->json(['success' => false, 'message' => 'Staus anda adala '. $data->status .'. Anda tidak bisa mengubah DATA dengan status selain RENCANA.']);
            }

            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            // UPDATE PRODUKSI
            $data->update([
                $data->catatan       = $request->catatan,
                $data->jml_hasil     = $request->jml_hasil,
                $data->satuan        = $request->satuan,
                $data->updated_by    = Auth::user()->username,
            ]);

            if (!$data) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ubah produksi tidak berhasil.']);
            } 

            $success = ProduksiDetail::where('client_id',$this->clientId)
                ->where('is_aktif',true)->where('is_hasil',true)
                ->where('produksi_id',$id)->where('produk_id',$request->produk_hasil_id)
                ->update([
                    'satuan' => $request->satuan,
                    'is_hasil' => true,
                    'jml_hasil' => $request->jml_hasil,
                    'jml_bahan' => 0,
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produksi Detail']);
            } 

            foreach($request->items as $dt) {
                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$dt['produk_id'])->where('is_aktif',1)->first();
                if(!$produk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Data produk persediaan tidak ditemukan. data tidak dapat disimpan.']);
                }

                $createDetail = ProduksiDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',true)->where('is_hasil',false)
                    ->where('produksi_id',$id)->where('produk_id',$dt['produk_id'])
                    ->where('produksi_detail_id',$dt['produksi_detail_id'])
                    ->first();
                
                if(!$createDetail) {
                    $createDetail = new ProduksiDetail();
                    $createDetail->produksi_detail_id = $id.'-'.Uuid::uuid4()->getHex();
                    $createDetail->produksi_id        = $id;
                    $createDetail->produk_id          = $dt["produk_id"];
                    $createDetail->produk_nama        = $dt["produk_nama"];
                    $createDetail->depo_id            = $dt["depo_id"];
                    $createDetail->client_id          = $this->clientId;
                    $createDetail->created_by         = Auth::user()->username;
                }

                $createDetail->satuan             = $dt["satuan"];
                $createDetail->is_hasil           = $dt["is_hasil"];
                $createDetail->jml_hasil          = $dt["jml_hasil"];
                $createDetail->jml_bahan          = $dt["jml_bahan"];
                $createDetail->status             = "RENCANA";
                $createDetail->is_aktif           = $dt["is_aktif"];
                $createDetail->updated_by         = Auth::user()->username;
                $success = $createDetail->save();
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produksi Detail']);
                } 
            } 


            DB::connection('dbclient')->commit();

            $data = Produksi::where('client_id',$this->clientId)->where('produksi_id',$id)->first();
            $data['items'] = ProduksiDetail::where('client_id',$this->clientId)->where('produksi_id',$id)->where('is_aktif',1)->where('is_hasil',false)->get();
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah produksi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id) {
        try {
            $data = Produksi::where('produksi_id', $id)->where('client_id',$this->clientId)->where('is_aktif',true)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }
            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            $details = ProduksiDetail::leftJoin('tb_produk','tb_produk.produk_id','=','tb_produksi_detail.produk_id')
                ->leftJoin('tb_depo','tb_depo.depo_id','=','tb_produksi_detail.depo_id' )
                ->where('tb_produksi_detail.produksi_id', $id)
                ->where('tb_produksi_detail.client_id',$this->clientId)
                ->where('tb_produksi_detail.is_aktif',true)
                ->select(
                    'tb_produksi_detail.produksi_detail_id',
                    'tb_produksi_detail.produksi_id',
                    'tb_produksi_detail.depo_id',
                    'tb_produksi_detail.produk_id',
                    'tb_produksi_detail.is_hasil',
                    'tb_produksi_detail.jml_hasil',
                    'tb_produksi_detail.jml_bahan',                    
                    'tb_produk.jenis_produk',
                    'tb_produk.produk_nama',
                    'tb_depo.depo_nama')
                ->get();

            DB::connection('dbclient')->beginTransaction();
            /**
             * update status
             */
            $success = Produksi::where('produksi_id', $id)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',true)
                ->update([
                    'updated_by'=>Auth::user()->username,
                    'is_aktif'=>false,
                    'status'=>'BATAL'
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data gagal dihapus. Ulangi kembali']);
            }

            /** * update stock bahan dan hasil bila status sudah PRODUKSI */
            /*** update stock hasil */
            

            /*** kurangi stock bahan item */
            $tglSelesai = date('Y-m-d H:i:s');
            if($details) {
                foreach($details as $dt) {  
                    $produk = Produk::where('client_id',$this->clientId)
                        ->where('produk_id',$dt['produk_id'])
                        ->where('is_aktif',1)->first();

                    if(!$produk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Data produk persediaan tidak ditemukan. data tidak dapat disimpan.']);
                    }
                    
                    $depoProduk =  DepoProduk::where('depo_id',$data->depo_id)
                        ->where('produk_id',$dt['produk_id'])->where('is_aktif',true)
                        ->where('client_id',$this->clientId)->first();
                    
                    if(!$depoProduk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json([ 'success'=>false,'message'=>'Status Produksi gagal diupdate.','data'=> $dt['produk_id'], 'depo'=>$data->depo_id ]);
                    }

                    $dt['jml_awal'] = $depoProduk->jml_total;
                    $jmlTotal = 0;
                    $keterangan = "";

                    if($dt['is_hasil'] == true) {
                        $jmlTotal = ($dt['jml_awal'] - $dt['jml_hasil']) * 1;
                        $keterangan = "Pembatalan hasil produksi, depo : ".$dt['depo_nama'];

                        if($depoProduk->jml_total < $dt['jml_hasil'] ) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success'=>false,'message'=>'Item hasil produksi gagal di update stock / jumlah stok hasil kurang dan akan menyebabkan stok minus.']);
                        }

                        $success = DepoProduk::where('depo_id',$data->depo_id)
                            ->where('produk_id',$dt['produk_id'])
                            ->where('is_aktif',true)
                            ->where('client_id',$this->clientId)
                            ->update([
                                'jml_keluar' => DB::raw('jml_keluar + '.$dt['jml_hasil']),
                                'jml_total' => DB::raw('jml_total - '.$dt['jml_hasil'])                    
                            ]);
                        
                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success'=>false,'message'=>'Item hasil produksi gagal di update stock.']);
                        }                        
                    }
                    else {
                        $jmlTotal = ($dt['jml_awal'] + $dt['jml_bahan']) * 1;
                        $keterangan = "Pembatalan bahan produksi, depo : ".$depo->depo_nama;

                        $success = DepoProduk::where('depo_id',$data->depo_id)
                            ->where('produk_id',$dt['produk_id'])
                            ->where('is_aktif',true)
                            ->where('client_id',$this->clientId)
                            ->update([
                                'jml_masuk' => DB::raw('jml_masuk + '.$dt['jml_bahan']),
                                'jml_total' => DB::raw('jml_total + '.$dt['jml_bahan'])                    
                            ]);

                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success'=>false,'message'=>'Item bahan produksi gagal di update stock / jumlah stok bahan kurang.']);
                        }
                    }

                    /**create kartu stock */
                    $stockCard = new KartuStock();
                    $stockCard->stock_log_id     = $this->clientId."-".Uuid::uuid4()->getHex();
                    $stockCard->reg_id           = $dt['produksi_id']; 
                    $stockCard->trx_id           = $dt['produksi_id']; 
                    $stockCard->sub_trx_id       = $dt['produksi_id']; 
                    $stockCard->detail_id        = $dt['produksi_detail_id']; 
                    $stockCard->produk_id        = $dt["produk_id"]; 
                    $stockCard->tgl_log          = NOW();
                    $stockCard->tgl_transaksi    = $tglSelesai;
                    $stockCard->jns_transaksi    = "PRODUKSI";
                    $stockCard->aksi             = "BATAL";
                    $stockCard->jns_produk       = $produk->jenis_produk;
                    $stockCard->produk_nama      = $produk->produk_nama;
                    $stockCard->unit_id          = $data->depo_id; 
                    $stockCard->depo_id          = $data->depo_id;
                    $stockCard->jml_awal         = 0;
                    $stockCard->jml_masuk        = $dt["jml_bahan"];
                    $stockCard->jml_keluar       = $dt["jml_hasil"];
                    $stockCard->jml_penyesuaian  = 0;
                    $stockCard->jml_akhir        = $jmlTotal;
                    $stockCard->catatan          = $data->catatan;
                    $stockCard->keterangan       = $keterangan;
                    $stockCard->client_id        = $this->clientId;
                    $stockCard->created_by       = Auth::user()->username;
                    $stockCard->updated_by       = Auth::user()->username;

                    $success = $stockCard->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'Data gagal disimpan. Pembuatan kartu stok tidak berhasil.']);
                    } 
                }
            }

            /*** nonaktifkan update detail */
            if(ProduksiDetail::where('client_id',$this->clientId)->where('produksi_id',$id)->where('is_aktif',1)->first()){
                $success = ProduksiDetail::where('produksi_id', $id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',true)
                    ->update([
                        'updated_by'=>Auth::user()->username,
                        'is_aktif'=>false,
                        'status'=>'BATAL'
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal dihapus. Ulangi kembali']);
                }    
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus.']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Hapus Produksi tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getProduksiId(){
        try {
            $id = $this->clientId.'-'.date('Ymd').'-PDS0001';
            $maxId = Produksi::withTrashed()->where('produksi_id','LIKE',$this->clientId.'-'.date('Ymd').'-PDS'.'%')->max('produksi_id');

            if (!$maxId) { $id = $this->clientId.'-'.date('Ymd').'-PDS0001'; }
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'-PDS','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-PDS000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-PDS00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-PDS0'.$count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'-PDS'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'PDS'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }

    // Keterangan : Status diupgrade dari DRAFT ke APPROVE
    public function approve(Request $request, $id) {
        try {
            $data = Produksi::where('produksi_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->first();

            if(!$data){ return response()->json(['success'=>false,'message'=>'Produksi sudah dinon-aktifkan.']); }
            if($data->status !== 'RENCANA' ) {
                return response()->json(['success'=>false,'message'=>'Produksi sudah berubah status dan tidak dapat diproses ulang.']);
            }

            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }
            //jika depo persediaan dalam status TERKUNCI, proses tidak dapat dilanjutkan
            if($depo->is_lock == 1) {
                return response()->json(['success' => false, 'message' => 'Depo sedang dikunci. Data tidak dapat disimpan.']);
            }

            $detail = ProduksiDetail::leftJoin('tb_produk','tb_produk.produk_id','=','tb_produksi_detail.produk_id')
                ->leftJoin('tb_depo','tb_depo.depo_id','=','tb_produksi_detail.depo_id' )
                ->where('tb_produksi_detail.produksi_id', $id)
                ->where('tb_produksi_detail.client_id',$this->clientId)
                ->where('tb_produksi_detail.is_aktif',true)
                ->select(
                    'tb_produksi_detail.produksi_detail_id',
                    'tb_produksi_detail.produksi_id',
                    'tb_produksi_detail.depo_id',
                    'tb_produksi_detail.produk_id',
                    'tb_produksi_detail.is_hasil',
                    'tb_produksi_detail.jml_hasil',
                    'tb_produksi_detail.jml_bahan',                    
                    'tb_produk.jenis_produk',
                    'tb_produk.produk_nama',
                    'tb_depo.depo_nama')
                ->get();

            DB::connection('dbclient')->beginTransaction();

            /**
             * update status produksi
             */
            $tglSelesai = date('Y-m-d H:i:s');
            $success = Produksi::where('produksi_id', $id)->where('client_id',$this->clientId)
                ->where('is_aktif','true')->where('status','RENCANA')
                ->update([
                    'status' => 'PRODUKSI',
                    'tgl_selesai' => $tglSelesai,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now()
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'Status Produksi gagal diupdate.']);
            }
            
            /**
             * kurangi stock bahan item dan tambah stock hasil
             */
            if($detail) {
                foreach($detail as $dt) { 
                    $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$dt['produk_id'])->where('is_aktif',1)->first();
                    if(!$produk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Data produk persediaan tidak ditemukan. data tidak dapat disimpan.']);
                    }
                    
                    $depoProduk =  DepoProduk::where('depo_id',$data->depo_id)
                        ->where('produk_id',$dt['produk_id'])
                        ->where('is_aktif',true)
                        ->where('client_id',$this->clientId)
                        ->first();

                    if(!$depoProduk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json([ 'success'=>false,'message'=>'Status Produksi gagal diupdate.','data'=> $dt['produk_id'], 'depo'=>$data->depo_id ]);
                    }
                    
                    $dt['jml_awal'] = $depoProduk->jml_total;
                    $dt['tgl_selesai'] = $tglSelesai;
                    $keterangan = "";
                    $jmlTotal = 0;

                    if($dt['is_hasil'] == true) {
                        $keterangan = "Item hasil produksi, depo :".$depo->depo_nama;                   
                        $jmlTotal = ($dt['jml_awal'] + $dt['jml_hasil']) * 1;                    
                        
                        $success = $depoProduk->update([
                                'jml_masuk' => DB::raw('jml_masuk + '.$dt['jml_hasil']),
                                'jml_total' => DB::raw('jml_total + '.$dt['jml_hasil'])                    
                            ]);
                        
                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success'=>false,'message'=>'Item hasil produksi gagal di update stock.']);
                        }
                    }

                    else {
                        $keterangan = "Item bahan produksi, depo :".$depo->depo_nama;
                        $jmlTotal = ($dt['jml_awal'] - $dt['jml_bahan']) * 1;

                        if($depoProduk->jml_total < $dt['jml_bahan'] ) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success'=>false,'message'=>'Item bahan produksi gagal di update stock / jumlah stok bahan kurang.']);
                        }

                        $success = $depoProduk->update([
                                'jml_keluar' => DB::raw('jml_keluar + '.$dt['jml_bahan']),
                                'jml_total' => DB::raw('jml_total - '.$dt['jml_bahan'])                    
                            ]);

                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success'=>false,'message'=>'Item bahan produksi gagal di update stock.']);
                        }
                    } 

                    $stockCard = new KartuStock();
                    $stockCard->stock_log_id     = $this->clientId."-".Uuid::uuid4()->getHex();
                    $stockCard->reg_id           = $dt['produksi_id']; 
                    $stockCard->trx_id           = $dt['produksi_id']; 
                    $stockCard->sub_trx_id       = $dt['produksi_id']; 
                    $stockCard->detail_id        = $dt['produksi_detail_id']; 
                    $stockCard->produk_id        = $dt["produk_id"]; 
                    $stockCard->tgl_log          = NOW();
                    $stockCard->tgl_transaksi    = $tglSelesai;
                    $stockCard->jns_transaksi    = "PRODUKSI";
                    $stockCard->aksi             = "SIMPAN";
                    $stockCard->jns_produk       = $produk->jenis_produk;  //$detail['jenis_produk'];
                    $stockCard->produk_nama      = $produk->produk_nama;  //$detail["produk_nama"];
                    $stockCard->unit_id          = $data->depo_id; 
                    $stockCard->depo_id          = $data->depo_id;
                    $stockCard->jml_awal         = 0;
                    $stockCard->jml_masuk        = $dt["jml_hasil"];
                    $stockCard->jml_keluar       = $dt["jml_bahan"];
                    $stockCard->jml_penyesuaian  = 0;
                    $stockCard->jml_akhir        = $jmlTotal;
                    $stockCard->catatan          = $data->catatan;
                    $stockCard->keterangan       = $keterangan;
                    $stockCard->client_id        = $this->clientId;
                    $stockCard->created_by    = Auth::user()->username;
                    $stockCard->updated_by    = Auth::user()->username;

                    $success = $stockCard->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'Data gagal disimpan. Pembuatan kartu stok tidak berhasil.']);
                    } 
                }
            }
            
            DB::connection('dbclient')->commit();
            
            $detail = ProduksiDetail::where('produksi_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->where('is_hasil','false')->get();
            $data->items = $detail;
            return response()->json(['success'=>true,'message'=>'Produksi berhasil disetujui.','data'=>$data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah status Produksi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // Realisasi PRODUKSI di depo_produk setelah status menjadi PRODUKSI
    function updateDepoProduk($clientId,$detail)
    {
        DB::connection('dbclient')->beginTransaction();

        try {
            $updateDepoProduk = DepoProduk::where('tb_depo_produk.depo_id', $detail['depo_id'])
                ->join('tb_produk', 'tb_produk.produk_id','=','tb_depo_produk.produk_id')
                ->where('tb_depo_produk.produk_id', $detail['produk_id'])
                ->where('tb_depo_produk.client_id', $clientId)
                ->select('tb_depo_produk.*', 'tb_produk.produk_nama')
                ->first();
            
            // Cek stok di depo_produk
            if($updateDepoProduk['jml_total'] == 0 AND ($detail["jml_hasil"] != "TRUE" or $detail["jml_hasil"] != true)){
                DB::connection('dbclient')->rollBack();
                return "Stok dengan item " . $updateDepoProduk['produk_nama'] . " berjumlah 0" ;
            }

            // FLagging is_hasil == true
            $jml_total = 0;
            if($detail["jml_hasil"] == "TRUE" or $detail["jml_hasil"] == true){
                $jml_total = $updateDepoProduk->jml_total + $detail["jml_hasil"];
            } else {
                $jml_total = $updateDepoProduk->jml_total - $detail["jml_bahan"];
            }

            // Cek stok setelah diproses pada depo_produk
            if($jml_total < 0){
                DB::connection('dbclient')->rollBack();
                return "Stok dengan item " . $updateDepoProduk['produk_nama'] . " jika diproses akan berjumlah KURANG DARI 0" ;
            }

            $updateDepoProduk->update([
                $updateDepoProduk->jml_masuk        = $detail["jml_hasil"],
                $updateDepoProduk->jml_keluar       = $detail["jml_bahan"],
                $updateDepoProduk->jml_total        = $jml_total,
                // $updateDepoProduk->created_by    = Auth::user()->username,
                $updateDepoProduk->created_by       = "hafiizh_update",
                // $updateDepoProduk->updated_by    = Auth::user()->username
                $updateDepoProduk->updated_by       = "hafiizh_update"
            ]);

            $success = $updateDepoProduk->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return "false";
            } 
            
            DB::connection('dbclient')->commit();

            return "true";

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah Depo Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }
    
    // CREATE Kartu Stock
    function createCardStock($master,$detail)
    {
        try {
            $keterangan = "Item bahan produksi, depo :".$detail['depo_nama'];
            $jmlTotal = ($detail['jml_awal'] - $detail['jml_bahan']) * 1;
            
            $jmlMasuk = 0;
            $jmlKeluar = 0;
            $jnsTransaksi = "";
            $jnsAksi = "";
            
            if($detail['is_hasil'] == true) {
                $keterangan = "Item hasil produksi depo :".$detail['depo_nama'];
                $jmlTotal = ($detail['jml_awal'] + $detail['jml_hasil']) * 1;
            }
            
            $createKartuStok = new KartuStock();
            $createKartuStok->stock_log_id     = $clientId."-".Uuid::uuid4()->getHex();
            $createKartuStok->reg_id           = $detail['produksi_id']; 
            $createKartuStok->trx_id           = $detail['produksi_id']; 
            $createKartuStok->sub_trx_id       = $detail['produksi_id']; 
            $createKartuStok->detail_id        = $detail['produksi_detail_id']; 
            $createKartuStok->produk_id        = $detail["produk_id"]; 
            $createKartuStok->tgl_log          = NOW();
            $createKartuStok->tgl_transaksi    = $detail["tgl_selesai"];
            $createKartuStok->jns_transaksi    = "PRODUKSI";
            $createKartuStok->aksi             = "SIMPAN";
            $createKartuStok->jns_produk       = $detail['jenis_produk'];
            $createKartuStok->produk_nama      = $detail["produk_nama"];
            $createKartuStok->unit_id          = $detail["unit_id"]; 
            $createKartuStok->depo_id          = $detail["depo_id"];
            $createKartuStok->jml_awal         = 0;
            $createKartuStok->jml_masuk        = $detail["jml_hasil"];
            $createKartuStok->jml_keluar       = $detail["jml_bahan"];
            $createKartuStok->jml_penyesuaian  = 0;
            $createKartuStok->jml_akhir        = $jmlTotal;
            $createKartuStok->catatan          = $master->catatan;
            $createKartuStok->keterangan       = $keterangan;
            $createKartuStok->client_id        = $this->clientId;
            $createKartuStok->created_by    = Auth::user()->username;
            $createKartuStok->updated_by    = Auth::user()->username;

            $success = $createKartuStok->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return false;
            } 
            
            return true;

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return false;
        }
    }

}
