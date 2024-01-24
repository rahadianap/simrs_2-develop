<?php

namespace Modules\Persediaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Carbon;

use Modules\Persediaan\Entities\InventoryIssue;
use Modules\Persediaan\Entities\InventoryIssueDetail;
use Modules\Persediaan\Entities\KartuStock;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\Depo;

use Modules\ManajemenUser\Entities\MemberDepo;


class InventoryIssueController extends Controller
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
            $per_page = 10;
            $keyword = '%%';
            
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = InventoryIssue::where('client_id',$this->clientId)->count(); }
            }
 
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

            $list = InventoryIssue::where('tb_inven_issue.client_id',$this->clientId)
                    ->where('tb_inven_issue.is_aktif','ILIKE',$aktif)
                    ->leftJoin('tb_depo','tb_inven_issue.depo_id','=','tb_depo.depo_id')
                    ->select('tb_inven_issue.*', 'tb_depo.depo_nama')
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_depo.depo_nama','ILIKE',$keyword)
                        ->orWhere('tb_inven_issue.approver','ILIKE',$keyword)
                        ->orWhere('tb_inven_issue.status','ILIKE',$keyword);
                    })->orderBy('tb_inven_issue.created_at','DESC'); 
            
            if($kolom != ""){
                $tgl_pencarian_awal = $request->tgl_pencarian_awal;
                $tgl_pencarian_akhir = $request->tgl_pencarian_akhir;
                $list->whereBetween($kolom,[$tgl_pencarian_awal,$tgl_pencarian_akhir]);
            }

            $list = $list->paginate($per_page);
            foreach($list->items() as $item) {
                $detail = InventoryIssueDetail::where('inven_issue_id',$item['inven_issue_id'])
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->orderBy('produk_nama')->get(); 

                // foreach($detail as $dt) {
                //     $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$item['depo_id'])->where('produk_id',$dt['produk_id'])->first();
                //     if($depoProduk) {
                //         $dt['total_awal'] = $depoProduk->jml_total;
                //         $dt['total_akhir'] = ($depoProduk->jml_total - $dt['jml_penyesuaian']);
                //     }
                // }            
                $item['items'] = $detail;
            }
            

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List Inventory Issue tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
    
    public function data(Request $request, $id) 
    {
        try {
            // $data = DB::connection('dbclient')->table('tb_inven_issue as inv')
            //             ->join('tb_inven_issue_detail as invd','inv.inven_issue_id','invd.inven_issue_id')
            //             ->join('tb_depo_produk as dp', function($join){
            //                 $join->on('invd.produk_id','=','dp.produk_id')
            //                     ->on('invd.depo_id','=','dp.depo_id');
            //             })
            //             ->where('inv.inven_issue_id',$id)
            //             ->where('inv.client_id',$this->clientId)
            //             ->where('inv.is_aktif','TRUE')
            //             ->select('invd.*','dp.*')
            //             // ->paginate(10); 
            //             ->get(); 
            
            $data = $this->getDataInventoryIssue($id);
            if($data) {
                $memberDepo = MemberDepo::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('user_id',Auth::user()->user_id)
                    ->where('depo_id',$data->depo_id)->first();
                
                if(!$memberDepo) {
                    return response()->json(['success'=>false,'message'=>'Anda tidak memiliki otorisasi pada depo']); 
                }

                $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->first();
                
                if(!$depo) {
                    return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat diproses.']);
                }

                if($depo->is_lock) {
                    return response()->json(['success' => false, 'message' => 'Depo dalam posisi terkunci. Data tidak dapat disimpan.']);
                }

                return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
            }

            else {
                return response()->json(['success'=>false,'message'=>'Data transaksi tidak ditemukan.']);
            }            
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian Inventory Issue tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function getDataInventoryIssue($id)
    {
        try {
            $data = InventoryIssue::where('inven_issue_id',$id)
                ->where('client_id',$this->clientId)->first();

            $detail = InventoryIssueDetail::where('inven_issue_id',$id)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->orderBy('produk_nama')->get(); 
            
            foreach($detail as $dt) {
                $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('depo_id',$data->depo_id)->where('is_aktif',1)->where('produk_id',$dt['produk_id'])->first();
                if($depoProduk) {
                    $dt['total_awal'] = $depoProduk->jml_total;
                    $dt['total_akhir'] = ($depoProduk->jml_total - $dt['jml_penyesuaian']);
                }
            }            
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

            $tglTransaksi = date('Y-m-d H:i:s');
            $id = $this->getInventoryIssueId($this->clientId);
            
            DB::connection('dbclient')->beginTransaction();
            // Create Adjustment
            $data = new InventoryIssue();
            $data->inven_issue_id   = $id;
            $data->tgl_dibuat       = $tglTransaksi;
            $data->tgl_issue        = $tglTransaksi;
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
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Inventory Issue']);
            }

            // Create Adjustment Detail
            foreach($request->items as $produk){
                $item = Produk::where('produk_id',$produk['produk_id'])->select('produk_nama')->first();
                if(!$item) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Inventory Issue(detail)']);
                }

                $produk["produk_nama"] = $item['produk_nama'];

                // dd($produk);

                $createSADetail = new InventoryIssueDetail();
                $createSADetail->inven_issue_detail_id  = $this->getInventoryIssueDetailId($this->clientId);
                $createSADetail->inven_issue_id         = $id;
                $createSADetail->depo_id                = $data->depo_id;
                $createSADetail->produk_id              = $produk["produk_id"];
                $createSADetail->depo_nama              = $data->depo_nama;
                $createSADetail->produk_nama            = $produk["produk_nama"];
                $createSADetail->total_awal             = $produk["total_awal"];
                $createSADetail->total_akhir            = $produk["total_akhir"];
                // $createSADetail->total_issue            = $produk["total_akhir"];
                $createSADetail->jml_penyesuaian        = $produk["jml_penyesuaian"];
                // $createSADetail->selisih                = $produk["jml_penyesuaian"];
                $createSADetail->satuan                 = $produk["satuan"];
                $createSADetail->status                 = $status;
                $createSADetail->is_aktif               = true;
                $createSADetail->client_id              = $this->clientId;
                $createSADetail->created_by             = Auth::user()->username;
                $createSADetail->updated_by             = Auth::user()->username;
                
                $success = $createSADetail->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Inventory Issue(detail)']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataInventoryIssue($id);
            return response()->json(['success' => true,'message' => 'Data Inventory Issue berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Tambah Inventory Issue tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id   = $request->inven_issue_id;
            $data = InventoryIssue::where('inven_issue_id', $id)->where('client_id', $this->clientId)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Inventory Issue tidak ditemukan.']);
            } 
            
            if ($data->status !== "RENCANA") {
                return response()->json(['success' => false, 'message' => 'Staus sekarang adalah '. $data->status .'. Anda tidak bisa mengubah DATA dengan status selain RENCANA.']);
            }

            $memberDepo = MemberDepo::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('user_id',Auth::user()->user_id)
                ->where('depo_id',$data->depo_id)->first();
            
            if(!$memberDepo) {
                return response()->json(['success'=>false,'message'=>'Anda tidak memiliki otorisasi pada depo']); 
            }

            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat diproses.']);
            }

            if($depo->is_lock) {
                return response()->json(['success' => false, 'message' => 'Depo dalam posisi terkunci. Data tidak dapat disimpan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $data->update([
                $data->catatan          = $request->catatan,
                $data->unit_id          = $request->unit_id,
                $data->updated_by    = Auth::user()->username,
            ]);

            if (!$data) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ubah Inventory Issue tidak berhasil.']);

            } 
            else {
                $detailArr = [];
                foreach($request->items as $produk){
                    $item = Produk::where('produk_id',$produk['produk_id'])->select('produk_nama')->first();
                    if(!$item) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ubah data tidak berhasil.Produk tidak ditemukan.']);
                    }
                    
                    $produk["produk_nama"] = $item['produk_nama'];
                    $detail = InventoryIssueDetail::where('inven_issue_detail_id', $produk["inven_issue_detail_id"])
                        ->where('client_id', $this->clientId)
                        ->first();

                    if(!$detail) {
                        $detail = new InventoryIssueDetail();
                        $detail->inven_issue_detail_id  = $this->getInventoryIssueDetailId($this->clientId);
                        $detail->inven_issue_id         = $data->inven_issue_id;
                        $detail->depo_id                = $data->depo_id;
                        $detail->produk_id              = $produk["produk_id"];
                        $detail->depo_nama              = $data->depo_nama;
                        $detail->produk_nama            = $produk["produk_nama"];
                        $detail->total_awal             = $produk["total_awal"];
                        $detail->total_akhir            = $produk["total_akhir"];
                        $detail->jml_penyesuaian        = $produk["jml_penyesuaian"];
                        $detail->satuan                 = $produk["satuan"];
                        $detail->status                 = $status;
                        $detail->is_aktif               = true;
                        $detail->client_id              = $this->clientId;
                        $detail->created_by             = Auth::user()->username;
                        $detail->updated_by             = Auth::user()->username;
                    }
                    else {
                        $detail->produk_nama        = $produk["produk_nama"];
                        $detail->total_awal         = $produk["total_awal"];
                        $detail->total_akhir        = $produk["total_akhir"];
                        $detail->jml_penyesuaian    = $produk["jml_penyesuaian"];
                        $detail->satuan             = $produk["satuan"];
                        $detail->updated_by         = Auth::user()->username;
                        $detail->is_aktif           = $produk['is_aktif'];
                    }
                   
                    $success = $detail->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data detail pengeluaran persediaan.']);
                    }
                }
                DB::connection('dbclient')->commit();
            }

            // $detail = InventoryIssueDetail::where('inven_issue_id', $id)
            //             ->where('client_id',  $this->clientId)
            //             ->paginate(10); 
                        
            // // Hasil update produk
            // $data->detail = $detail;
            DB::connection('dbclient')->commit();

            $data = $this->getDataInventoryIssue($id);
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah Inventory Issue tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        
        try {
            $data = InventoryIssue::where('inven_issue_id', $id)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'inventory issue tidak ditemukan.']);
            }

            $memberDepo = MemberDepo::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('user_id',Auth::user()->user_id)
                ->where('depo_id',$data->depo_id)->first();
            
            if(!$memberDepo) {
                return response()->json(['success'=>false,'message'=>'Anda tidak memiliki otorisasi pada depo']); 
            }

            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$data->depo_id)->first();
            
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Depo tidak diketahui / anda tidak memiliki akses ke depo tersebut. Data tidak dapat diproses.']);
            }

            if($depo->is_lock) {
                return response()->json(['success' => false, 'message' => 'Depo dalam posisi terkunci. Data tidak dapat disimpan.']);
            }

            if($data->status == "SELESAI"){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Status Inventory Issue sekarang adalah '. $data->status .' hanya status RENCANA dan SUBMIT yang bisa di CANCEL']);
            }

            DB::connection('dbclient')->beginTransaction();
            $success = $data->update([
                'status'        => "BATAL",
                'updated_by'    => Auth::user()->username,
                'updated_at'    => now(),
                'is_aktif'      => false
            ]);

            if(!$success){
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam NON-AKTIFKAN data pengeluaran persediaan.']);
            }

            /**
             * update data detail
             */
            if(InventoryIssueDetail::where('inven_issue_id', $id)->where('client_id',$this->clientId)->where('is_aktif',1)->first()) {
                $success = InventoryIssueDetail::where('inven_issue_id', $id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username,'status'=>'BATAL']);

                if(!$success){
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam NON-AKTIFKAN data detail pengeluaran persediaan.']);    
                }
            }

            DB::connection('dbclient')->commit();

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus.']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Hapus Inventory Issue tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getInventoryIssueId($clientId) 
    {
        try {
            $id = $clientId.'.'.date('Ym').'-INV0001';
            $maxId = InventoryIssue::withTrashed()->where('inven_issue_id','LIKE',$clientId.'.'.date('Ym').'-INV%')->max('inven_issue_id');
            if (!$maxId) { $id = $clientId.'.'.date('Ym').'-INV0001'; }
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-INV','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-INV000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-INV00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-INV0'.$count; } 
                else { $id = $clientId.'.'.date('Ym').'-INV'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'INV-'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }

    public function getInventoryIssueDetailId($clientId) 
    {
        try {
            $id = $clientId.'.'.date('Ym').'-INVD00001';
            $maxId = InventoryIssueDetail::withTrashed()->where('inven_issue_detail_id','LIKE',$clientId.'.'.date('Ym').'-INVD%')->max('inven_issue_detail_id');

            if (!$maxId) { $id = $clientId.'.'.date('Ym').'-INVD00001'; }
            else {
                $maxId = str_replace($clientId.'.'.date('Ym').'-INVD','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'.'.date('Ym').'-INVD0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'.'.date('Ym').'-INVD000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'.'.date('Ym').'-INVD00'.$count; } 
                elseif ($count >= 100 && $count < 10000) { $id = $clientId.'.'.date('Ym').'-INVD0'.$count; } 
                else { $id = $clientId.'.'.date('Ym').'-INVD'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'INVD'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

    // Status SELESAI
    public function approve(Request $request, $id)
    {
        try {
            $data = InventoryIssue::where('inven_issue_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->where('status','RENCANA')->first();

            if(!$data){
                return response()->json(['success'=>false,'message'=>'Inventory Issue sudah dinon-aktifkan.','data'=>$data]);
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
            $detail = InventoryIssueDetail::where('inven_issue_id', $id)->where('client_id',$this->clientId)->where('is_aktif','true')->get();
            if(!$detail){
                return response()->json(['success'=>false,'message'=>'Inventory Issue Detail tidak ditemukan.','data'=>$data]);
            }

            $tglSelesai = date('Y-m-d H:i:s');

            DB::connection('dbclient')->beginTransaction();
            $success = $data->update([
                    'tgl_selesai'   => now(),
                    'status'        => 'SELESAI',
                    'updated_by'    => Auth::user()->username,
                    'approver'      => Auth::user()->username,
                    'approver_id'   => Auth::user()->user_id,
                    'updated_at'    => now()
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json([
                    'success'=>false,
                    'message'=>'Status Inventory Issue sekarang adalah '. $data->status .'. Hanya status RENCANA atau SELESAI yang bisa dieksekusi.'
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
                if($dt['jml_penyesuaian'] > $depoProduk->jml_total) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo akan menjadi kurang dari nol jika proses dilanjutkan.']);
                }

                $dt['jml_total'] = $depoProduk->jml_total;

                /**
                 * update data persediaan
                 */
                $success = $depoProduk->update([
                        'updated_by' => Auth::user()->username,
                        'jml_keluar' => DB::raw('jml_keluar + '.$dt['jml_penyesuaian']),
                        'jml_total' => DB::raw('jml_total - '.$dt['jml_penyesuaian']),
                    ]);
                    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /**
                 * update status detail
                 */
                $success = InventoryIssueDetail::where('client_id',$this->clientId)
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
                $kartuStock->reg_id           = $data->inven_issue_id;
                $kartuStock->trx_id           = $data->inven_issue_id;
                $kartuStock->sub_trx_id       = $data->inven_issue_id;
                $kartuStock->detail_id        = $dt['inven_issue_detail_id'];
                $kartuStock->produk_id        = $dt["produk_id"];
                $kartuStock->tgl_log          = date('Y-m-d H:i:s');
                $kartuStock->tgl_transaksi    = $tglSelesai;
                $kartuStock->jns_transaksi    = "INVENTORY ISSUE";
                $kartuStock->aksi             = "SIMPAN";
                // Sementara Jenis Produk menggunakan MEDIS
                $kartuStock->jns_produk       = $dt['jenis_produk'];
                $kartuStock->produk_nama      = $dt["produk_nama"];
                $kartuStock->unit_id          = $dt["depo_id"];
                $kartuStock->depo_id          = $dt["depo_id"];
                $kartuStock->jml_awal         = 0;    
                $kartuStock->jml_masuk        = 0;
                $kartuStock->jml_keluar       = $dt['jml_penyesuaian'];
                $kartuStock->jml_penyesuaian  = 0;
                $kartuStock->jml_akhir        = ($dt['jml_total'] - $dt["jml_penyesuaian"])*1;
                $kartuStock->catatan          = $data->catatan;
                $kartuStock->keterangan       = "Pengeluaran stok depo ".$data->depo_nama;
                $kartuStock->client_id        = $this->clientId;
                $kartuStock->created_by       = Auth::user()->username;
                $kartuStock->updated_by       = Auth::user()->username;
                
                $success = $kartuStock->save();
    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item pengeluaran di depo tidak dapat disimpan.']);
                } 
            } 

            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'OK']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' =>'Ubah status Inventory Issue tidak berhasil.','error'=>$e->getMessage()]);
        }
    }
}
