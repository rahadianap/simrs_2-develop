<?php

namespace Modules\Persediaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Carbon;

use Modules\Persediaan\Entities\StockOpname;
use Modules\Persediaan\Entities\StockOpnameDetail;
use Modules\Persediaan\Entities\KartuStock;
use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\DepoUnit;

use Modules\ManajemenUser\Entities\MemberDepo;

class StockOpnameController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'Tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request) {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = StockOpname::where('client_id',$this->clientId)->where('is_aktif',1)->count(); }
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

            $list = StockOpname::where('tb_stock_opname.client_id',$this->clientId)
                    ->where('tb_stock_opname.is_aktif','LIKE',$aktif)
                    ->leftJoin('tb_depo','tb_stock_opname.depo_id','=','tb_depo.depo_id')
                    ->select('tb_stock_opname.*', 'tb_depo.depo_nama')
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_depo.depo_nama','LIKE',$keyword)
                        ->orWhere('tb_stock_opname.approver','LIKE',$keyword)
                        ->orWhere('tb_stock_opname.status','LIKE',$keyword);
                    })->orderBy('tb_stock_opname.created_at','DESC'); 
            
            if($kolom != ""){
                $tgl_pencarian_awal = $request->tgl_pencarian_awal;
                $tgl_pencarian_akhir = $request->tgl_pencarian_akhir;
                $list->whereBetween($kolom,[$tgl_pencarian_awal,$tgl_pencarian_akhir]);
            }

            $list = $list->paginate($per_page);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List stok opname tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function listDepo(Request $request)
    {
        try {
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
                
            $lists = Depo::where('client_id',$this->clientId)->where('is_aktif',1)
                //->where('is_lock',0)
                ->whereIn('depo_id',$memberDepo)->get();
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List Depo tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
    
    public function data(Request $request, $id)
    {
        try {
            $data = StockOpname::where('so_id',$id)->where('client_id',$this->clientId)->first();             
            $detail = StockOpnameDetail::where('so_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)
                        ->orderBy('produk_nama','ASC')->get(); 

            $data->produk = $detail;

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian Stok Opname tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function dataProdukDepo(Request $request, $depoId) {
        try {
            $per_page = 10;
            if($request->has('per_page')) { $per_page = $request->input('per_page'); }

            $lists = DepoProduk::where('tb_depo_produk.depo_id',$depoId)
                    ->where('tb_depo_produk.client_id',$this->clientId)->where('tb_depo_produk.is_aktif',1)
                    ->where('tb_produk.produk_nama','ILIKE','%'.$request->input('keyword').'%')
                    ->join('tb_produk','tb_depo_produk.produk_id','=','tb_produk.produk_id')
                    ->select('tb_depo_produk.*','tb_produk.produk_nama')
                    ->orderBy('tb_produk.produk_nama')
                    ->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian Stok Opname tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('user_id',Auth::user()->user_id)
                ->where('depo_id',$request->depo_id)
                ->select('depo_id')->first();
            if(!$memberDepo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak masuk dalam otorisasi-mu. Data tidak dapat disimpan.']);
            }

            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_id)->first();
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            $ids = $this->getStockOpnameId();            
            
            $data = new StockOpname();
            $data->so_id = $ids;
            $data->tgl_rencana = $request->tgl_rencana;
            $data->tgl_dibuat = date('Y-m-d H:i:s');
            $data->nilai_awal = $request->nilai_awal;
            $data->catatan = $request->catatan;
            $data->depo_id = $request->depo_id;
            $data->depo_nama = $depo->depo_nama;
            $data->unit_id = '';//$depo->unit_id;
            // Status otomatis ke dalam RENCANA ketika Stok Opname pertama kali dibuat
            $data->status = "RENCANA";
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $data->approver_id = '';   
            $data->approver = '';            
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Stok Opname']);
            }

            // Lock Depo agar jumlah tidak berubah-ubah
            $depo = Depo::where('depo_id', $request->depo_id)->where('client_id', $this->clientId)->first();
            $depo->update([
                $depo->is_lock = "True",
            ]);

            if (!$depo) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ubah Depo Lock tidak berhasil.']);
            }

            return response()->json(['success' => true,'message' => 'Data Stok Opname berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Stok Opname tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function updateData(Request $request)
    {
        try {
            $id = $request->so_id;
            $data = StockOpname::where('so_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->where('status','RENCANA')->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Stok Opname tidak ditemukan/sudah diubah statusnya.']);
            } 
            
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            $success = $data->update([
                $data->tgl_rencana    = $request->tgl_rencana,
                $data->nilai_awal    = $request->nilai_awal,
                $data->catatan       = $request->catatan,
                $data->updated_by    = Auth::user()->username,
            ]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'Ubah Stok Opname tidak berhasil.']);
            } 
            return response()->json(['success' => true, 'message' => 'Ubah Stok Opname berhasil.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah data stok Opname tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->so_id;
            $data = StockOpname::where('so_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Stok Opname tidak ditemukan.']);
            } 

            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            if ($data->status !== "PROSES") {
                return response()->json(['success' => false, 'message' => 'Status sekarang adalah '. $data->status .'. Anda tidak bisa mengubah DATA dengan status selain PROSES.']);
            }

            DB::connection('dbclient')->beginTransaction();

            foreach($request->produk as $pr) {
                $success = DepoProduk::where('depo_produk_id', $pr["depo_produk_id"])
                    ->where('depo_id', $data->depo_id)->where('client_id', $this->clientId)
                    ->update(['total_so' => $pr["total_so"], 'selisih_so' => $pr["selisih_so"]]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data temporary stok opname tidak berhasil disimpan.']);
                } 
            }
            DB::connection('dbclient')->commit();

            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Stok Opname tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = StockOpname::where('so_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data tidak ditemukan']);
            }
            $depoId = $data->depo_id; 
            //check apakah depo dibawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->where('depo_id',$depoId)->first();
            if(!$memberDepo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            DB::connection('dbclient')->beginTransaction();
        
            if($data->status == "RENCANA"){
                $success = StockOpname::where('so_id', $id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>0, 'status' => 'BATAL', 'updated_by' => Auth::user()->username,'updated_at' => date('Y-m-d H:i:s') ]);

                if(!$success){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data.']);
                }
            } 

            else if ($data->status == "PROSES") {
                $success = StockOpname::where('so_id', $id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>0, 'status' => 'BATAL', 'updated_by'=>Auth::user()->username,'updated_at'=>date('Y-m-d H:i:s') ]);

                if(!$success){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data.']);
                }

                /**
                 * bersihkan depo
                 */
                if(DepoProduk::where('depo_id', $depoId)->where('is_aktif', 1)->where('client_id', $this->clientId)->where('total_so','>',0)->first()) {
                    $success = DepoProduk::where('depo_id', $depoId)
                        ->where('is_aktif', 1)
                        ->where('client_id', $this->clientId)
                        ->update(['updated_by' => Auth::user()->username,'total_so' => null, 'selisih_so' => null ]);

                    if(!$success){
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data.']);
                    }
                }
                
                /**
                 * buka kuncian depo
                 */
                if(Depo::where('depo_id', $depoId)->where('is_aktif', 1)->where('client_id', $this->clientId)->where('is_lock',1)->first()){
                    $success = Depo::where('depo_id', $depoId)
                            ->where('is_aktif', 1)
                            ->where('client_id', $this->clientId)
                            ->update(['updated_by' => Auth::user()->username,'is_lock' => false ]);

                    if(!$success){
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data (unlock depo).']);
                    }
                }
            } 

            DB::connection('dbclient')->commit();

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Hapus Stok Opname tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getStockOpnameId()
    {
        try {
            $id = $this->clientId.'-'.date('Y').'-SO0001';
            $maxId = StockOpname::withTrashed()->where('so_id','LIKE',$this->clientId.'-'.date('Y').'-SO%')->max('so_id');

            if (!$maxId) { $id = $this->clientId.'-'.date('Y').'-SO0001'; }
            else {
                $maxId = str_replace($this->clientId.'-'.date('Y').'-SO','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Y').'-SO000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Y').'-SO00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Y').'-SO0'.$count; } 
                else { $id = $this->clientId.'-'.date('Y').'-SO'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'SO'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }

    public function getStockOpnameDetailId() 
    {
        try {
            $id = $this->clientId.'-SOD'.'-00001';
            $maxId = StockOpnameDetail::withTrashed()->where('so_detail_id','LIKE',$this->clientId.'-SOD'.'%')->max('so_detail_id');

            if (!$maxId) { $id = $this->clientId.'-SOD'.'-00001'; }
            else {
                $maxId = str_replace($this->clientId.'-SOD'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-SOD'.'-0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-SOD'.'-000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-SOD'.'-00'.$count; } 
                elseif ($count >= 100 && $count < 10000) { $id = $this->clientId.'-SOD'.'-0'.$count; } 
                else { $id = $this->clientId.'-SOD'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'SOD'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

    // Status Pertama
    // Keterangan : Status diubah dari PROSES ke RENCANA
    public function draft(Request $request, $id)
    {
        try {
            $data = StockOpname::where('so_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->first();

            if(!$data){
                return response()->json(['success'=>false,'message'=>'Stok Opname sudah dinon-aktifkan.','data'=>$data]);
            }
            
            if($data->status == "PROSES") {
                $data->update([
                    'status' => 'RENCANA',
                    'updated_by' => Auth::user()->username,
                    'updated_at'=> now()
                ]);
            } 
            else {
                return response()->json([
                    'success'=>false,
                    'message'=>'Status Stok Opname sekarang adalah '. $data->status .'. Hanya status PROSES yang bisa diubah ke status RENCANA.'
                ]);
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah status Stok Opname tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // Status Kedua
    // Keterangan : Status diubah dari RENCANA ke PROSES
    public function proceed(Request $request, $id)
    {
        try {

            $data = StockOpname::where('so_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->where('status','RENCANA')->first();
            if(!$data){
                return response()->json(['success'=>false,'message'=>'Stok Opname sudah dinonaktifkan / tidak ditemukan / sudah berubah status.']);
            }

            $depoId = $data->depo_id;
            //check apakah depo dibawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('depo_id',$depoId)
                ->where('user_id',Auth::user()->user_id)
                ->select('depo_id')
                ->first();

            if(!$memberDepo) {
                return response()->json(['success' => false, 'message' => 'anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }
            
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$depoId)->first();
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui. Data tidak dapat disimpan.']);
            }
            /**
             * ubah status
             */
            DB::connection('dbclient')->beginTransaction();

            $success = StockOpname::where('so_id', $id)->where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('status','RENCANA')
                ->update(['status'=>'PROSES', 'tgl_so'=>date('Y-m-d H:i:s') ,'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s')]);
            
            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah status data.']);
            }

            /** lock depo */
            if(Depo::where('depo_id',$depoId)->where('is_lock','<>',1)->where('client_id',$this->clientId)->where('is_aktif',1)->first()) {
                $success = Depo::where('depo_id', $depoId)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_lock'=>true,'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s')]);
                
                if(!$success){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah status depo (lock depo).']);
                }
            }

            /**
             * kosongkan depo produk
             */            
            if($data->nilai_awal == 'SISTEM') {
                $success = DepoProduk::where('depo_id',$depoId)->where('client_id',$this->clientId)
                    ->where('is_aktif',1)->update(['selisih_so'=> 0, 'total_so'=>DB::raw('jml_total')]);
            }
            else if($data->nilai_awal == 'NOL') {
                // $success = DepoProduk::where('depo_id',$depoId)->where('client_id',$this->clientId)
                //     ->where('is_aktif',1)->update([
                //         'selisih_so'=>DB::raw('jml_total' * (-1)), 
                //         'total_so'=>0
                //     ]);

                $success = DB::connection('dbclient')->update('update tb_depo_produk set selisih_so = (jml_total * -1), total_so = 0 where (depo_id = ? and client_id = ? and is_aktif = true)',[$depoId , $this->clientId]);
            }
            else {
                $success = DepoProduk::where('depo_id',$depoId)->where('client_id',$this->clientId)
                    ->where('is_aktif',1)->update(['selisih_so'=>null, 'total_so'=>null]);
            }
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah status opname (kosongkan stok).']);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'Data berhasil diubah status dari Draft ke Proceed','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah status Stok Opname tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // Status Ketiga
    // Keterangan : Status diubah dari PROSES ke APPROVE
    public function approve(Request $request, $id)
    {
        try {
            $data = StockOpname::where('so_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->where('status','PROSES')->first();
            if(!$data){
                return response()->json(['success'=>false,'message'=>'Stok Opname sudah dinonaktifkan / tidak ditemukan / sudah berubah status.']);
            }

            //check apakah depo dibawah otorisasi pengguna
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)->where('user_id',Auth::user()->user_id)->select('depo_id')->get();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->whereIn('depo_id',$memberDepo)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat disimpan.']);
            }

            //jika depo tidak dikunci data tidak dapat disimpan.
            if(!$depo->is_lock) {
                return response()->json(['success' => false, 'message' => 'Depo belum dikunci. Data tidak dapat disimpan.']);
            }

            DB::connection('dbclient')->beginTransaction();

            /**
             * update status stock opname
             */
            $tglSelesai = date('Y-m-d H:i:s');
            $success = StockOpname::where('so_id', $id)->where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('status','PROSES')
                ->update([
                    'status'=>'SELESAI',
                    'tgl_selesai'=>$tglSelesai,
                    'approver'=>Auth::user()->username,
                    'approver_id'=>Auth::user()->user_id,
                    'updated_by'=>Auth::user()->username
                ]);

            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah status stock opname.']);
            }
            
            /**
             * copy to stock opname detail
             */
            DepoProduk::where('tb_depo_produk.depo_id', $data->depo_id)->where('tb_depo_produk.is_aktif', 1)->where('tb_depo_produk.client_id', $this->clientId)
                ->leftJoin('tb_produk','tb_produk.produk_id','=','tb_depo_produk.produk_id')
                ->leftJoin('tb_depo','tb_depo.depo_id','=','tb_depo_produk.depo_id')
                ->select([
                    'tb_produk.produk_id as produk_id',
                    'tb_produk.produk_nama as produk_nama',
                    'tb_produk.jenis_produk as jenis_produk',
                    'tb_depo.depo_id as depo_id',
                    'tb_depo.depo_nama as depo_nama',
                    'tb_depo_produk.jml_total as jml_total',
                    'tb_depo_produk.total_so as total_so',
                    'tb_depo_produk.selisih_so as selisih_so'
                ])
                ->chunk(100, function ($depos) use($id, $tglSelesai, $data) {
                    foreach ($depos as $depo) {
                        // write your email send code here
                        $soDetailId = $id.'-'.Uuid::uuid4()->getHex();
                        $depo['so_detail_id'] = $soDetailId;
                        $depo['tgl_selesai'] = $tglSelesai;

                        $success = StockOpnameDetail::create([
                            'so_detail_id'  => $soDetailId,
                            'so_id'         => $id,
                            'depo_id'       => $depo->depo_id,
                            'produk_id'     => $depo->produk_id,
                            'depo_nama'     => $depo->depo_nama,
                            'produk_nama'   => $depo->produk_nama,
                            'jenis_produk'  => $depo->jenis_produk,
                            'total_awal'    => $depo->jml_total,
                            'total_so'      => $depo->total_so,
                            'selisih'       => $depo->selisih_so,
                            'status'        => 'SELESAI',
                            'is_aktif'      => true,
                            'client_id'     => $this->clientId,
                            'created_by'    => Auth::user()->username,
                            'updated_by'    => Auth::user()->username,
                        ]);

                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menyetujui opname (copy opname detail).']);
                        }

                        /**create kartu stock */
                        $success = KartuStock::create([
                            'stock_log_id'      => $this->clientId."-".Uuid::uuid4()->getHex(),
                            'reg_id'            => $data->so_id, 
                            'trx_id'            => $data->so_id, 
                            'sub_trx_id'        => $data->so_id, 
                            'detail_id'         => $depo['so_detail_id'], 
                            'produk_id'         => $depo["produk_id"], 
                            'tgl_log'           => date('Y-m-d H:i:s'),
                            'tgl_transaksi'     => $tglSelesai,
                            'jns_transaksi'     => "STOCK OPNAME",
                            'aksi'              => "SIMPAN",
                            'jns_produk'        => $depo["jenis_produk"],
                            'produk_nama'       => $depo["produk_nama"],
                            'unit_id'           => $data->depo_id,
                            'depo_id'           => $data->depo_id,
                            'jml_awal'          => 0,
                            'jml_masuk'         => 0,
                            'jml_keluar'        => 0,
                            'jml_penyesuaian'   => $depo["selisih_so"],
                            'jml_akhir'         => $depo["total_so"],
                            'catatan'           => $data->catatan,
                            'keterangan'        => 'Stock opname '.$depo["depo_nama"],
                            'client_id'         => $this->clientId,
                            'created_by'        => Auth::user()->username,
                            'updated_by'        => Auth::user()->username,
                        ]);

                        if(!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menyetujui opname (copy opname detail).']);
                        }
                    }
                });
            
            /**
             * update depo produk
             */
            $success = DepoProduk::where('depo_id', $data->depo_id)->where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->update([
                    'updated_by' => Auth::user()->username, 
                    'jml_total' => DB::raw('total_so'), 
                    'jml_penyesuaian' => DB::raw('jml_penyesuaian + selisih_so'),
                ]);

            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data total akhir.']);
            }

            /**
             * buka kuncian depo
             */
            if(Depo::where('depo_id', $data->depo_id)->where('is_aktif', true)->where('client_id', $this->clientId)->where('is_lock',true)->first())
            {
                $success = Depo::where('depo_id', $data->depo_id)
                        ->where('is_aktif', 1)
                        ->where('client_id', $this->clientId)
                        ->update(['updated_by' => Auth::user()->username,'is_lock' => false ]);

                if(!$success){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam menghapus data (unlock depo).']);
                }
            }

            DB::connection('dbclient')->commit();

            /** clear SO depo produk **/
            DepoProduk::where('depo_id', $data->depo_id)->where('is_aktif', 1)->where('client_id', $this->clientId)->update(['selisih_so' => null, 'total_so' => null]);

            return response()->json(['success' => true,'message' => 'Data berhasil disimpan']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Persetujuan Stok Opname tidak berhasil dilakukan.','error'=>$e->getMessage()]);
        }
    }


    // public function createStockCard($master,$detail) {
    //     try {
    //         // Kartu Stok Opname
    //         return response()->json(['success' => false,'message' => 'di create stock card.']);
    //         $stockCard = new KartuStock();
    //         $ids = $this->clientId."-".Uuid::uuid4()->getHex();
    //         $stockCard->stock_log_id     = $ids;
    //         $stockCard->reg_id           = $master->so_id; 
    //         $stockCard->trx_id           = $master->so_id; 
    //         $stockCard->sub_trx_id       = $master->so_id; 
    //         $stockCard->detail_id        = $detail['so_detail_id']; 
    //         $stockCard->produk_id        = $detail["produk_id"]; 
    //         $stockCard->tgl_log          = date('Y-m-d H:i:s');
    //         $stockCard->tgl_transaksi    = $detail["tgl_selesai"];
    //         $stockCard->jns_transaksi    = "STOCK OPNAME";
    //         $stockCard->aksi             = "SIMPAN";
    //         $stockCard->jns_produk       = $detail["jenis_produk"];
    //         $stockCard->produk_nama      = $detail["produk_nama"];
    //         $stockCard->unit_id          = $detail["unit_id"]; 
    //         $stockCard->depo_id          = $master->depo_id;
    //         $stockCard->jml_awal         = 0;
    //         $stockCard->jml_masuk        = 0;
    //         $stockCard->jml_keluar       = 0;
    //         $stockCard->jml_penyesuaian  = $detail["selisih_so"];
    //         $stockCard->jml_akhir        = $detail["total_so"];
    //         $stockCard->catatan          = $master->catatan;
    //         $stockCard->keterangan       = 'Stock opname '.$detail["depo_nama"];
    //         $stockCard->client_id        = $this->clientId;
    //         $stockCard->created_by       = Auth::user()->username;
    //         $stockCard->updated_by       = Auth::user()->username;

    //         $success = $stockCard->save();

    //         if(!$success) { DB::connection('dbclient')->rollBack(); return false; }             
    //         return true;
    //     }
    //     catch (\Exception $e) {
            
    //         DB::connection('dbclient')->rollBack();
    //         return false;
    //     }
    // }

}
