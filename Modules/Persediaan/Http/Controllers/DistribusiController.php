<?php

namespace Modules\Persediaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
// use Illuminate\Support\Facades\DB;

use Carbon;
use Modules\Persediaan\Entities\Distribusi;
use Modules\Persediaan\Entities\DistribusiDetail;
use Modules\Persediaan\Entities\KartuStock;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\Depo;

use Modules\ManajemenUser\Entities\MemberDepo;
use DB;

class DistribusiController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request,$status = 'all')
    {
        try {
            if ($status == 'requests') { $status = '%PERMINTAAN%'; }
            else if ($status == 'approved') { $status = '%DIKIRIM%'; }
            else if ($status == 'received') { $status = '%DITERIMA%'; }
            else if ($status == 'cancelled') { $status = '%BATAL%'; }
            else { $status = '%%'; }

            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Distribusi::where('client_id',$this->clientId)->count(); }
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

            $list = Distribusi::where('tb_distribusi.client_id',$this->clientId)
                    ->where('tb_distribusi.status','ILIKE',$status)
                    ->where('tb_distribusi.is_aktif','ILIKE',$aktif)
                    ->leftJoin('tb_depo as dp_penerima','depo_penerima_id','=','dp_penerima.depo_id')
                    ->leftJoin('tb_depo as dp_pengirim','depo_pengirim_id','=','dp_pengirim.depo_id')
                    ->select('tb_distribusi.*', 'dp_penerima.depo_nama as depo_penerima', 'dp_pengirim.depo_nama as depo_pengirim')
                    ->where(function($q) use ($keyword) {
                        $q->where('dp_penerima.depo_nama','ILIKE',$keyword)
                        ->orWhere('dp_pengirim.depo_nama','ILIKE',$keyword)
                        ->orWhere('tb_distribusi.distribusi_id','ILIKE',$keyword);
                    })->orderBy('tb_distribusi.created_at','DESC'); 
            
            if($kolom != ""){
                $tgl_pencarian_awal = $request->tgl_pencarian_awal;
                $tgl_pencarian_akhir = $request->tgl_pencarian_akhir;
                $list->whereBetween($kolom,[$tgl_pencarian_awal,$tgl_pencarian_akhir]);
            }

            $list = $list->paginate($per_page);

            foreach($list->items() as $item) {
                $item['items'] = DistribusiDetail::where('tb_distribusi_detail.client_id',$this->clientId)
                    ->where('tb_distribusi_detail.distribusi_id',$item['distribusi_id'])
                    ->where('tb_distribusi_detail.is_aktif',1)
                    ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_distribusi_detail.produk_id')
                    ->select(['tb_distribusi_detail.*','tb_produk.produk_nama'])
                    ->get();
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List distribusi tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
    
    public function data(Request $request, $id)
    {
        try {
            $list = Distribusi::where('client_id',$this->clientId)->where('distribusi_id',$id)->first();
            $list['items'] = DistribusiDetail::where('tb_distribusi_detail.client_id',$this->clientId)
                ->where('tb_distribusi_detail.distribusi_id',$id)
                ->where('tb_distribusi_detail.is_aktif',1)
                ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_distribusi_detail.produk_id')
                ->select(['tb_distribusi_detail.*','tb_produk.produk_nama'])
                ->get();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian distribusi tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_penerima_id)->whereIn('depo_id',$memberDepo)->first();            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            $id = $this->getDistribusiId($this->clientId);
        
            DB::connection('dbclient')->beginTransaction();
            $data = new Distribusi();
            $data->distribusi_id     = $id;
            $data->tgl_dibuat        = date('Y-m-d H:i:s');
            $data->tgl_dibutuhkan    = $request->tgl_dibutuhkan;
            $data->tgl_realisasi     = null;
            $data->catatan           = $request->catatan;
            $data->depo_penerima_id  = $request->depo_penerima_id;
            $data->depo_pengirim_id  = $request->depo_pengirim_id;
            $data->depo_penerima     = $request->depo_penerima;
            $data->depo_pengirim     = $request->depo_pengirim;
            // Status otomatis ke dalam PERMINTAAN ketika distribusi pertama kali dibuat
            $data->status            = "PERMINTAAN";
            $data->is_aktif          = 1;
            $data->client_id         = $this->clientId;
            $data->created_by        = Auth::user()->username;
            $data->updated_by        = Auth::user()->username;
            
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data distribusi.']);
            } 
            
            /**
             * simpan detail permintaan
             */
            foreach($request->items as $detail) {
                $createDetail = new DistribusiDetail();
                $createDetail->distribusi_detail_id = $id.Uuid::uuid4()->getHex();
                $createDetail->distribusi_id        = $id;
                $createDetail->depo_pengirim_id     = $request->depo_pengirim_id;
                $createDetail->depo_pengirim        = $request->depo_pengirim;
                $createDetail->depo_penerima_id     = $request->depo_penerima_id;
                $createDetail->depo_penerima        = $request->depo_penerima;
                
                $createDetail->depo_produk_id       = $detail["depo_produk_id"];
                $createDetail->produk_id            = $detail["produk_id"];
                $createDetail->produk_nama          = $detail["produk_nama"];
                // $createDetail->satuan_id            = $detail["satuan"];
                $createDetail->satuan               = $detail["satuan"];
                $createDetail->jml_diminta          = $detail["jml_diminta"];
                $createDetail->jml_dikirim          = 0;
                $createDetail->status               = "PERMINTAAN";
                $createDetail->pr_id                = null;
                $createDetail->po_id                = null;
                $createDetail->is_aktif             = 1;
                $createDetail->client_id            = $this->clientId;
                $createDetail->created_by           = Auth::user()->username;
                $createDetail->updated_by           = Auth::user()->username;
                
                $success = $createDetail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data distribusi']);
                } 
            }

            DB::connection('dbclient')->commit();

            $data = Distribusi::where('client_id',$this->clientId)->where('distribusi_id',$id)->first();
            $data['items'] = DistribusiDetail::where('tb_distribusi_detail.client_id',$this->clientId)
                ->where('tb_distribusi_detail.distribusi_id',$id)
                ->where('tb_distribusi_detail.is_aktif',1)
                ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_distribusi_detail.produk_id')
                ->select(['tb_distribusi_detail.*','tb_produk.produk_nama'])
                ->get();

            return response()->json(['success' => true,'message' => 'Data distribusi berhasil disimpan','data'=>$data]);
        } 
        catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Tambah distribusi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->distribusi_id;
            $data = Distribusi::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data distribusi tidak ditemukan.']);
            }
            if($data->status == "DISETUJUI" || $data->status == "DITERIMA" || $data->status == "BATAL" || $data->status == "PENGADAAN"){
                return response()->json(['success' => false, 'message' => 'Staus : '. $data->status .'. Anda tidak bisa mengubah DATA dengan status Selain PERMINTAAN.']);
            }
            
            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_penerima_id)->whereIn('depo_id',$memberDepo)->first();            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $success = Distribusi::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)
                    ->update([
                        'tgl_dibutuhkan' => $request->tgl_dibutuhkan,
                        'catatan'        => $request->catatan,
                        'updated_by'     => Auth::user()->username,
                    ]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ubah distribusi tidak berhasil.']);
            } 

            /**
             * simpan detail permintaan
             */
            foreach($request->items as $detail) {
                $createDetail = DistribusiDetail::where('client_id',$this->clientId)->where('distribusi_id',$id)->where('is_aktif',1)->where('produk_id',$detail['produk_id'])->first();
                if(!$createDetail) {
                    $createDetail = new DistribusiDetail();
                    $createDetail->distribusi_detail_id = $id.Uuid::uuid4()->getHex();
                    $createDetail->distribusi_id        = $id;
                    $createDetail->depo_pengirim_id     = $request->depo_pengirim_id;
                    $createDetail->depo_pengirim        = $request->depo_pengirim;
                    $createDetail->depo_penerima_id     = $request->depo_penerima_id;
                    $createDetail->depo_penerima        = $request->depo_penerima;                    
                    $createDetail->depo_produk_id       = $detail["depo_produk_id"];
                    $createDetail->produk_id            = $detail["produk_id"];
                    $createDetail->satuan               = $detail["satuan"];
                    $createDetail->pr_id                = null;
                    $createDetail->po_id                = null;
                    $createDetail->is_aktif             = 1;
                    $createDetail->client_id            = $this->clientId;
                    $createDetail->created_by           = Auth::user()->username;
                    $createDetail->jml_dikirim          = 0;
                }
                else {
                    $createDetail->is_aktif             = $detail['is_aktif'];
                }
                
                $createDetail->produk_nama          = $detail["produk_nama"];
                $createDetail->jml_diminta          = $detail["jml_diminta"];
                $createDetail->status               = "PERMINTAAN";
                $createDetail->updated_by           = Auth::user()->username;
                
                $success = $createDetail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data distribusi']);
                } 
            }

            DB::connection('dbclient')->commit();

            $data = Distribusi::where('client_id',$this->clientId)->where('distribusi_id',$id)->first();
            $data['items'] = DistribusiDetail::where('tb_distribusi_detail.client_id',$this->clientId)
                ->where('tb_distribusi_detail.distribusi_id',$id)
                ->where('tb_distribusi_detail.is_aktif',1)
                ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_distribusi_detail.produk_id')
                ->select(['tb_distribusi_detail.*','tb_produk.produk_nama'])
                ->get();
            
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah distribusi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id) {        
        try {
            $data = Distribusi::where('distribusi_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $status = "";
            $is_aktif = 1;
            
            if($data->status !== "PERMINTAAN"){
                return response()->json(['success' => false, 'message' => 'Staus anda adala '. $data->status .'. Anda tidak bisa menghapus DATA dengan status selain PERMINTAAN.']);
            } 

            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->whereIn('depo_id',$memberDepo)
                ->where(function($q) use($data){
                    $q->where('depo_id',$data->depo_penerima_id)
                    ->orWhere('depo_id',$data->depo_pengirim_id);
                })->first();   

            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            $detail = DistribusiDetail::where('distribusi_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            DB::connection('dbclient')->beginTransaction();

            $success =  Distribusi::where('distribusi_id', $id)
                ->where('client_id',$this->clientId)->where('is_aktif',1)->update([
                    'updated_by'    => Auth::user()->username,
                    'status'        => "BATAL",
                    'updated_at'    => now(),
                    'is_aktif'      => 0
                ]);

            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam NON-AKTIFKAN data Distribusi']);
            } 
            
            if($detail){
                $success =  DistribusiDetail::where('distribusi_id', $id)
                    ->where('client_id',$this->clientId)->where('is_aktif',1)->update([
                        'updated_by'    => Auth::user()->username,
                        'status'        => "BATAL",
                        'updated_at'    => now(),
                        'is_aktif'      => 0
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam NON-AKTIFKAN data detail Distribusi']);
                } 
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'Data berhasil dibatalkan']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Hapus distribusi tidak berhasil.','error'=>$e->getMessage()]);
        }        
    }
    
    public function getDistribusiId($clientId) {
        try {
            $id = $this->clientId.'-DST-'.date('Ym').'00001';
            $maxId = Distribusi::withTrashed()->where('distribusi_id','LIKE',$this->clientId.'-DST-'.date('Ym').'%')->max('distribusi_id');

            if (!$maxId) { $id = $this->clientId.'-DST-'.date('Ym').'00001'; }
            else {
                $maxId = str_replace($this->clientId.'-DST-'.date('Ym'),'',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-DST-'.date('Ym').'0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-DST-'.date('Ym').'000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-DST-'.date('Ym').'00'.$count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-DST-'.date('Ym').'0'.$count; } 
                else { $id = $this->clientId.'-DST-'.date('Ym').$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return null;
            //return 'DST'.date('Y.m').'-'.Uuid::uuid4()->getHex();
        }
    }
    // Keterangan : Status diupgrade dari PERMINTAAN ke DISETUJUI
    // belum mengubah jumlah persediaan
    public function approve(Request $request){
        try {
            $id = $request->distribusi_id;
            $data = Distribusi::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data distribusi tidak ditemukan.']);
            }
            if( $data->status == "DITERIMA" || $data->status == "BATAL" || $data->status == "PENGADAAN"){
                return response()->json(['success' => false, 'message' => 'Staus : '. $data->status .'. Anda tidak bisa mengubah DATA dengan status Selain PERMINTAAN DAN DISETUJUI.']);
            }

            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->whereIn('depo_id',$memberDepo)
                ->where('depo_id',$data->depo_pengirim_id)
                ->first();   

            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }
            
            DB::connection('dbclient')->beginTransaction();
            $success = Distribusi::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)
                    ->update([
                        'status'         => 'DISETUJUI',
                        'tgl_realisasi'  => date('Y-m-d H:i:s'),
                        'updated_by'     => Auth::user()->username,
                        'approved_by'    => Auth::user()->username,
                    ]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil.']);
            } 

            /**
             * simpan detail permintaan
             */
            foreach($request->items as $detail) {
                $createDetail = DistribusiDetail::where('client_id',$this->clientId)->where('distribusi_id',$id)->where('is_aktif',1)->where('produk_id',$detail['produk_id'])->first();
                if(!$createDetail) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. Data tidak ditemukan.']);
                }
                
                if($detail['jml_dikirim'] > $detail['jml_diminta']) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. jumlah yang dikirim tidak boleh lebih besar dari yang diminta.']);
                }
                
                $createDetail->jml_dikirim          = $detail["jml_dikirim"];
                $createDetail->status               = "DISETUJUI";
                $createDetail->updated_by           = Auth::user()->username;
                $success = $createDetail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam persetujuan data distribusi']);
                } 
            }

            DB::connection('dbclient')->commit();

            $data = Distribusi::where('client_id',$this->clientId)->where('distribusi_id',$id)->first();
            $data['items'] = DistribusiDetail::where('tb_distribusi_detail.client_id',$this->clientId)
                ->where('tb_distribusi_detail.distribusi_id',$id)
                ->where('tb_distribusi_detail.is_aktif',1)
                ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_distribusi_detail.produk_id')
                ->select(['tb_distribusi_detail.*','tb_produk.produk_nama'])
                ->get();
            
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah distribusi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // Keterangan : Status diupgrade dari DISETUJUI ke DITERIMA
    // dan mengubah data jumlah persediaan 
    public function receive(Request $request, $id){
        try {
            $data = Distribusi::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data distribusi tidak ditemukan.']);
            }
            if( $data->status !== "DISETUJUI"){
                return response()->json(['success' => false, 'message' => 'Staus : '. $data->status .'. Anda tidak bisa mengubah DATA dengan status Selain PERMINTAAN DAN DISETUJUI.']);
            }
            
            //check apakah depo ada di bawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->whereIn('depo_id',$memberDepo)
                ->where('depo_id',$data->depo_penerima_id)
                ->first();   

            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }
            //TRANSAKSI TIDAK DAPAT DILAKUKAN JIKA DEPO PENERIMA DALAM POSISI TERKUNCI
            if($depo->is_lock == 1) {
                return response()->json(['success' => false, 'message' => 'Depo penerima sedang dikunci. Data tidak dapat diproses.']);
            }

            //JIKA depo pengirim terkunci proses tidak dapat dilanjutkan.
            $depoPengirimLocked = Depo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('is_lock',1)->where('depo_id',$data->depo_pengirim_id)->first();
            
            if($depoPengirimLocked) {
                return response()->json(['success' => false, 'message' => 'Depo pengirim sedang dikunci. Data tidak dapat diproses lebih lanjut.']);
            }


            $details = DistribusiDetail::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->get();
            $tglRealisasi = date('Y-m-d H:i:s');

            DB::connection('dbclient')->beginTransaction();
            $success = Distribusi::where('distribusi_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)
                    ->update([
                        'status'         => 'DITERIMA',
                        'tgl_realisasi'  => $tglRealisasi,
                        'updated_by'     => Auth::user()->username,
                        'received_by'    => Auth::user()->username,
                    ]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Penerimaan distribusi tidak berhasil.']);
            } 

            /**
             * simpan detail permintaan dan update 
             */
            foreach($details as $dt) {
                /*** check if depo pengirim stocknya cukup **/
                $isStockReady = DepoProduk::where('depo_id',$data->depo_pengirim_id)
                    ->where('produk_id',$dt['produk_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)          
                    ->first();
                
                if(!$isStockReady) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. item tidak ditemukan di depo pengirim.']);
                }
                if($isStockReady->jml_total < $dt['jml_dikirim']){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. Stock untuk dikirim tidak mencukupi.']);
                }

                $produk = Produk::where('produk_id',$dt['produk_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
                
                $dt['distribusi_id'] = $id; 
                $dt['produk_nama'] = $produk->produk_nama;
                $dt['jenis_produk'] = $produk->jenis_produk;
                $dt['tgl_realisasi'] = $tglRealisasi;
                
                /**
                 * update detail distribusi
                 */
                $success = DistribusiDetail::where('client_id',$this->clientId)
                    ->where('distribusi_id',$id)->where('is_aktif',1)
                    ->where('produk_id',$dt['produk_id'])->update(['updated_by' => Auth::user()->username,'status' => 'DITERIMA']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. data detail gagal diupdate.']);
                }
                
                /**
                 * ambil data di depo produk pengirim dan update stock
                 */
                $depoKirim = DepoProduk::where('depo_id',$dt['depo_pengirim_id'])
                    ->where('produk_id',$dt['produk_id'])->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                
                if(!$depoKirim) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. data detail produk tidak ditemukan di depo.']);
                }
                $dt['jml_total_sblm_kirim'] = $depoKirim->jml_total;

                $success = $depoKirim->update([
                        'jml_keluar' => DB::raw('jml_keluar + '.$dt['jml_dikirim']),
                        'jml_total' => DB::raw('jml_total - '.$dt['jml_dikirim']),
                    ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. data depo detail gagal diupdate.']);
                }

                /**
                 * ambil data di depo produk penerima dan update stock
                 */
                
                 $depoTerima = DepoProduk::where('depo_id',$dt['depo_penerima_id'])
                    ->where('produk_id',$dt['produk_id'])->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                                
                if(!$depoTerima) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. item '.$produk->produk_nama.' belum termapping (tidak ditemukan) di depo penerima.']);
                }

                $dt['jml_total_sblm_terima'] = $depoTerima->jml_total;

                $success = $depoTerima->update([
                        'jml_masuk' => DB::raw('jml_masuk + '.$dt['jml_dikirim']),
                        'jml_total' => DB::raw('jml_total + '.$dt['jml_dikirim']),
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. data depo detail gagal diupdate.']);
                }

                /**
                 * buat kartu stock
                 */
                if(!$this->createKartuStock($data, $dt)){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Persetujuan distribusi tidak berhasil. kartu stok gagal dibuat.']);
                }
            }

            DB::connection('dbclient')->commit();
            /**
             * pengembalian data ke front end
             */
            $data = Distribusi::where('client_id',$this->clientId)->where('distribusi_id',$id)->first();
            $data['items'] = DistribusiDetail::where('tb_distribusi_detail.client_id',$this->clientId)
                ->where('tb_distribusi_detail.distribusi_id',$id)
                ->where('tb_distribusi_detail.is_aktif',1)
                ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_distribusi_detail.produk_id')
                ->select(['tb_distribusi_detail.*','tb_produk.produk_nama'])
                ->get();
            
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah distribusi tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // Realisasi PRODUKSI di depo_produk setelah status menjadi APPROVE
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
                $updateDepoProduk->created_by       = Auth::user()->username,
                $updateDepoProduk->updated_by       = Auth::user()->username
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
    function createKartuStock($master,$detail)
    {
        try {
            // Kartu Stok Depo penerima Masuk
            $createStokTambah = new KartuStock();
            $ids = $this->clientId."-".Uuid::uuid4()->getHex();
            $createStokTambah->stock_log_id     = $ids;
            $createStokTambah->reg_id           = $master->distribusi_id; 
            $createStokTambah->trx_id           = $master->distribusi_id; 
            $createStokTambah->sub_trx_id       = $master->distribusi_id; 
            $createStokTambah->detail_id        = $detail['distribusi_detail_id']; 
            $createStokTambah->produk_id        = $detail["produk_id"]; 
            $createStokTambah->tgl_log          = date('Y-m-d H:i:s');
            $createStokTambah->tgl_transaksi    = $detail["tgl_realisasi"];
            $createStokTambah->jns_transaksi    = "DISTRIBUSI";
            $createStokTambah->aksi             = "TERIMA";
            $createStokTambah->jns_produk       = $detail['jenis_produk'];
            $createStokTambah->produk_nama      = $detail["produk_nama"];
            $createStokTambah->unit_id          = '';//$detail["unit_id"]; 
            $createStokTambah->depo_id          = $detail["depo_penerima_id"];
            $createStokTambah->jml_awal         = 0;
            $createStokTambah->jml_masuk        = $detail["jml_dikirim"];
            $createStokTambah->jml_keluar       = 0;
            $createStokTambah->jml_penyesuaian  = 0;
            $createStokTambah->jml_akhir        = ($detail["jml_dikirim"] + $detail['jml_total_sblm_terima'])*1;
            $createStokTambah->catatan          = $master->catatan;
            $createStokTambah->keterangan       = 'Penerimaan item distribusi, dari '.$master->depo_pengirim;
            $createStokTambah->client_id        = $this->clientId;
            $createStokTambah->created_by       = Auth::user()->username;
            $createStokTambah->updated_by       = Auth::user()->username;
            
            $successBertambah = $createStokTambah->save();

            if(!$successBertambah) {
                DB::connection('dbclient')->rollBack();
                return false;
            } 

            // Kartu Stok Keluar
            $createStokBerkurang = new KartuStock();
            $ids = $this->clientId."-".Uuid::uuid4()->getHex();
            $createStokBerkurang->stock_log_id     = $ids;
            $createStokBerkurang->reg_id           = $master->distribusi_id; 
            $createStokBerkurang->trx_id           = $master->distribusi_id; 
            $createStokBerkurang->sub_trx_id       = $master->distribusi_id; 
            $createStokBerkurang->detail_id        = $detail['distribusi_detail_id']; 
            $createStokBerkurang->produk_id        = $detail["produk_id"]; 
            $createStokBerkurang->tgl_log          = date('Y-m-d H:i:s');
            $createStokBerkurang->tgl_transaksi    = $detail["tgl_realisasi"];
            $createStokBerkurang->jns_transaksi    = "DISTRIBUSI";
            $createStokBerkurang->aksi             = "KIRIM";
            $createStokBerkurang->jns_produk       = $detail["jenis_produk"];
            $createStokBerkurang->produk_nama      = $detail["produk_nama"];
            $createStokBerkurang->unit_id          = '';//$detail["unit_id"]; 
            $createStokBerkurang->depo_id          = $detail["depo_pengirim_id"];
            $createStokBerkurang->jml_awal         = 0;
            $createStokBerkurang->jml_masuk        = 0;
            $createStokBerkurang->jml_keluar       = $detail["jml_dikirim"];
            $createStokBerkurang->jml_penyesuaian  = 0;
            $createStokBerkurang->jml_akhir        = ($detail["jml_total_sblm_kirim"] - $detail["jml_dikirim"])*1;
            $createStokBerkurang->catatan          = $master->catatan;
            $createStokBerkurang->keterangan       = 'Pengiriman item distribusi ke '.$master->depo_penerima;
            $createStokBerkurang->client_id        = $this->clientId;
            $createStokBerkurang->created_by       = Auth::user()->username;
            $createStokBerkurang->updated_by       = Auth::user()->username;

            $successBerkurang = $createStokBerkurang->save();

            if(!$successBerkurang) {
                DB::connection('dbclient')->rollBack();
                return false;
            } 
            
            return true;

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            dd($e->getMessage());
            return false;
            //return response()->json(['success' => false,'message' =>'Tambah ITEM Kartu Stok tidak berhasil.','error'=>$e->getMessage()]);
        }
    }
}
