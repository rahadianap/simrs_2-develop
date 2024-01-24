<?php

namespace Modules\Farmasi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon;

use Modules\Pendaftaran\Entities\Pendaftaran;
use Modules\Pendaftaran\Entities\RegPasien;
use Modules\Pendaftaran\Entities\SEP;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\DepoProduk;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\Produk;

use Modules\Transaksi\Entities\TrxBhp;
use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;

use RegistrasiHelper;

class ApotekController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function listResep(Request $request) {
        try {
            $per_page = 10;
            $aktif = 'true';
            $keyword = '%%';
            
            $query = TrxResep::query();
            $query = $query->where('client_id',$this->clientId)->where('jns_resep','ILIKE','APOTEK');
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = TrxResep::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('trx_id','ILIKE',$keyword)
                    ->orWhere('jns_resep','ILIKE',$keyword);
                })->orderBy('tgl_transaksi','ASC')
                ->paginate($per_page);
            
                foreach($list as $dt) {
                    $detail = TrxResepDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('trx_id',$dt['trx_id'])
                        ->orderBy('trx_id','ASC')
                        ->get();

                    $dt['detail'] = $detail;                        
                }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    public function dataResep(Request $request, $resepId) {
        try {
            $data = $this->getDataResep($resepId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function getDataResep($resepId) {
        try {
            $data = TrxResep::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$resepId)->first();
            
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data resep tidak ditemukan.']);
            }
            $data['items'] = TrxResepDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$resepId)->get();

            return ['success' => true, 'data' => $data];
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengambil data. Error: '.$e->getMessage()];
        }
    }

    public function saveResep(Request $request) {
        try {
            $trxId = RegistrasiHelper::instance()->ApotekTransactionId($this->clientId);
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_id)->first();
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Data depo tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            /**
             * create data resep
             */
            $trxResep                      = new TrxResep();
            $trxResep->reg_id              = $trxId;
            $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
            $trxResep->trx_id              = $trxId;
            $trxResep->reff_trx_id         = '-';
            $trxResep->status              = 'DRAFT';
            $trxResep->is_realisasi        = false;
            $trxResep->is_bayar            = false;
            $trxResep->is_aktif            = true;
            $trxResep->jns_transaksi       = 'APOTEK';
            $trxResep->jns_resep           = 'APOTEK';
            $trxResep->no_resep            = '-';
            $trxResep->no_transaksi        = '-';
            
            $trxResep->client_id           = $this->clientId;
            $trxResep->created_by          = Auth::user()->username;
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->tgl_resep           = date('Y-m-d H:i:s');
        
            $trxResep->penjamin_id         = '';
            $trxResep->penjamin_nama       = 'PRIBADI';            
            $trxResep->total               = $request->total;
            
            $trxResep->unit_id             = '';
            $trxResep->unit_nama           = '';
            
            $trxResep->depo_id             = $request->depo_id;
            $trxResep->depo_nama           = $depo->depo_nama;
            $trxResep->dokter_id           = '';
            $trxResep->dokter_nama         = $request->dokter_nama;
            $trxResep->peresep             = $request->dokter_nama;
            
            $trxResep->pasien_id           = '-';
            $trxResep->no_rm               = '-';
            $trxResep->nama_pasien         = $request->nama_pasien;
            
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            

            foreach($request->items as $dt) {
                /*** insert ke table resep detail */
                $detailId = $dt['detail_id'];
                
                $rsp = TrxResepDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId.'-APT'.date('Ymd').Uuid::uuid4()->getHex();
                    $rsp = new TrxResepDetail();
                    $rsp->detail_id     = $detailId;
                    $rsp->reg_id        = '';
                    $rsp->trx_id        = $trxId;
                    $rsp->reff_trx_id   = '';
                    $rsp->item_id       = $dt['item_id'];
                    $rsp->client_id     = $this->clientId;
                    $rsp->created_by    = Auth::user()->username;
                }

                $rsp->unit_id       = '';
                $rsp->unit_nama     = '';
                
                $rsp->depo_id           = $request->depo_id;
                $rsp->depo_nama         = $depo->depo_nama;
                $rsp->dokter_id         = $request->dokter_id;
                $rsp->dokter_nama       = $request->peresep;
                $rsp->item_nama         = $dt['item_nama'];
                $rsp->item_tipe         = $dt['item_tipe'];
                $rsp->klasifikasi       = '';
                $rsp->jumlah            = $dt['jumlah'];
                $rsp->jml_ambil         = $dt['jumlah'];
                $rsp->signa             = $dt['signa'];
                $rsp->signa_deskripsi   = $dt['signa_deskripsi'];
                $rsp->satuan            = $dt['satuan']; 
                
                if($request->item_tipe == 'HEADER_RACIKAN') {
                    $rsp->satuan_komposisi  = $dt['satuan_hasil'];
                    $rsp->jml_komposisi     = $dt['jumlah'];
                }
                
                $rsp->group_racikan     = $dt['group_racikan'];  
                $rsp->nilai             = $dt['nilai'];
                $rsp->is_racikan        = $dt['is_racikan'];
                $rsp->status            = 'DRAFT';
                $rsp->diskon_persen     = $dt['diskon_persen'];
                $rsp->diskon            = $dt['diskon'];
                $rsp->harga_diskon      = $dt['harga_diskon'];
                $rsp->subtotal          = $dt['subtotal'];
                $rsp->is_realisasi      = false;
                $rsp->is_aktif          = $dt['is_aktif'];
                $rsp->updated_by        = Auth::user()->username;
                
                $success = $rsp->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataResep($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function updateResep(Request $request) {
        try {
            $trxResep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$request->trx_id)->first();
            
            if(!$trxResep) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trxResep->is_konfirmasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $trxId = $request->trx_id;

            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_id)->first();
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Data depo tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->total               = $request->total;
            $trxResep->dokter_nama         = $request->dokter_nama;
            $trxResep->peresep             = $request->dokter_nama;
            $trxResep->nama_pasien         = $request->nama_pasien;
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            

            // update tb_resep_detail
            foreach($request->items as $dtl) {
                //check item produk
                $produk = Produk::where('produk_id',$dtl['item_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                    
                if($dtl['item_tipe'] !== 'HEADER_RACIKAN') {
                    if(!$produk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Item produk tidak ditemukan.']);
                    }
                }

                //check apakah item ada yang sama
                $detail = TrxResepDetail::where('client_id',$this->clientId)->where('trx_id',$trxId)->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();

                if(!$detail) {
                    $detailId = $this->getResepDetailId();
                    $detail = new TrxResepDetail();
                    $detail->detail_id = $detailId;
                    $detail->reg_id = $trxResep->reg_id;
                    $detail->trx_id = $trxId;
                    $detail->item_id = $produk->produk_id;
                    $detail->is_realisasi = false;                    
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }
                else { 
                    $detailId = $detail->detail_id; 
                }
                $detail->item_nama          = $dtl['item_nama'];
                $detail->item_tipe          = $dtl['item_tipe'];
                $detail->depo_id            = $depo->depo_id;
                $detail->depo_nama          = $depo->depo_nama;
                $detail->dokter_id          = '';
                $detail->dokter_nama        = $request->dokter_nama;
                $detail->satuan             = $dtl['satuan'];
                $detail->jumlah             = $dtl['jumlah'];
                $detail->jml_ambil          = $dtl['jml_ambil'];
                $detail->is_bhp             = false;
                if($dtl['is_racikan']) {
                    $detail->is_racikan         = $dtl['is_racikan'];
                    $detail->racikan_id         = $dtl['racikan_id'];
                    // $detail->racikan_nama       = $dtl['racikan_nama'];
                }
                else {
                    $detail->is_racikan         = false;
                }
                $detail->signa              = $dtl['signa'];
                $detail->signa_deskripsi    = $dtl['signa_deskripsi'];
                $detail->nilai              = $dtl['nilai'];
                $detail->diskon_persen      = $dtl['diskon_persen'];
                $detail->diskon             = $dtl['diskon'];
                $detail->harga_diskon       = $dtl['harga_diskon'];
                $detail->subtotal           = $dtl['subtotal'];
                //$detail->klasifikasi        = '';
                $detail->status             = $trxResep->status;
                $detail->updated_by         = Auth::user()->username;
                $detail->is_aktif           = $dtl['is_aktif'];
                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data item resep farmasi gagal disimpan.']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataResep($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }

    public function getResepDetailId()
    {
        try {
            $id = $this->clientId.'-'.'APTD'.'-000001';
            $maxId = TrxResepDetail::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.'APTD'.'-%')
                ->max('detail_id');

            if (!$maxId) { $id = $this->clientId.'-'.'APTD'.'-000001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.'APTD'.'-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.'APTD'.'-00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'APTD'.'-0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'APTD'.'-000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'APTD'.'-00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.'APTD'.'-0' . $count; } 
                else { $id = $this->clientId.'-'.'APTD'.'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }


    // public function realisasiResep(Request $request, $resep_id) {
    //     try {
    //         $trxResep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$resep_id)->first();
            
    //         if(!$trxResep) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
    //         if($trxResep->is_konfirmasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

    //         DB::connection('dbclient')->beginTransaction();
        
    //         $trxResep->updated_by          = Auth::user()->username;
    //         $trxResep->is_realisasi        = true;
    //         $trxResep->status              = 'REALISASI';
        
    //         $success = $trxResep->save();
    //         if(!$success) {
    //             DB::connection('dbclient')->rollBack();
    //             return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
    //         }
            
    //         $rsp = TrxResepDetail::where('client_id',$this->clientId)
    //                 ->where('trx_id',$resep_id)->where('is_aktif',1)
    //                 ->get();

    //         foreach($rsp as $dt) {
    //             $dt->jml_ambil     = $dt['jumlah'];
    //             $dt->jml_riil      = $dt['jumlah'];
    //             $dt->status        = 'REALISASI';
    //             $dt->is_realisasi  = true;
    //             $dt->updated_by    = Auth::user()->username;
                
    //             $success = $dt->save();
    //             if (!$success) {
    //                 DB::connection('dbclient')->rollBack();
    //                 return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
    //             }

    //             $stock = DepoProduk::where('client_id',$this->clientId)->where('produk_id', $dt['item_id'])->where('depo_id', 'CL2023-0001.DEPO-0001')->first();
    //             $stock->jml_keluar = $dt['jumlah'];

    //             $sukses = $stock->save();
    //             if (!$sukses) {
    //                 DB::connection('dbclient')->rollBack();
    //                 return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
    //             }
    //         }

    //         DB::connection('dbclient')->commit();

    //         $data = $this->getDataResep($resep_id);
    //         if(!$data['success']) {
    //             return response()->json(['success' => false, 'message' => $data['message']]);
    //         }
    //         return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $data['data']]);
    //     } 
    //     catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
    //     }
    // }
    public function realisasiResep(Request $request, $resep_id) {
        try {
            $trxResep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$resep_id)->first();
            if(!$trxResep) { 
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); 
            }
            if($trxResep->is_konfirmasi) { 
                return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); 
            }
            
            $transaksi = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$resep_id)->where('pasien_id',$trxResep->pasien_id)
                ->where('status','DAFTAR')->first();
            
            

            DB::connection('dbclient')->beginTransaction();
        
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->is_realisasi        = true;
            $trxResep->status              = 'REALISASI';
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }
            
            //create transaction
            if(!$transaksi) {
                $transaksi = new Transaksi();
                $transaksi->reg_id = $trxResep->reg_id;
                $transaksi->trx_id = $trxResep->trx_id;
                $transaksi->reff_trx_id = $trxResep->reff_trx_id;
                $transaksi->is_aktif = true;
                $transaksi->client_id = $this->clientId;
                $transaksi->created_by = Auth::user()->username;
                $transaksi->updated_by = Auth::user()->username;
                $transaksi->jns_transaksi = 'APOTEK';
            }

            // update tb_transaksi
            $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
            $transaksi->no_antrian          = $trxResep->no_antrian;
            $transaksi->no_transaksi        = 'TRX/'.date('Ymd').'/'.$trxResep->no_antrian;
            $transaksi->kelas_id            = '';
            $transaksi->kelas_harga_id      = '';
            $transaksi->kelas_penjamin_id   = '';
            $transaksi->penjamin_id         = '';//$trxResep->penjamin_id;
            $transaksi->penjamin_nama       = '';//$trxResep->penjamin_nama;
            $transaksi->buku_harga_id       = '';
            $transaksi->buku_harga          = '';
            $transaksi->total               = $trxResep->total;
            $transaksi->unit_id             = '';
            $transaksi->unit_nama           = '';
            $transaksi->ruang_id            = '';
            $transaksi->ruang_nama          = '';
            
            $transaksi->dokter_id           = $trxResep->dokter_id;
            $transaksi->dokter_nama         = $trxResep->dokter_nama;
            $transaksi->dokter_pengirim_id  = '';
            $transaksi->dokter_pengirim     = '';
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            $transaksi->pasien_id           = $trxResep->pasien_id;
            $transaksi->no_rm               = $trxResep->no_rm;
            $transaksi->nama_pasien         = $trxResep->nama_pasien;
            $transaksi->is_realisasi        = true;
            $transaksi->is_bayar            = false;
            $transaksi->status              = 'SELESAI';
                
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            $rsp = TrxResepDetail::where('client_id',$this->clientId)
                    ->where('trx_id',$resep_id)->where('is_aktif',1)->get();

            foreach($rsp as $dt) {
                $dt->status        = 'REALISASI';
                $dt->is_realisasi  = true;
                $dt->updated_by    = Auth::user()->username;                
                $success = $dt->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                }

                if($dt['item_tipe'] !== 'HEADER_RACIKAN') {
                    $stock = DepoProduk::where('client_id',$this->clientId)->where('produk_id', $dt['item_id'])->where('depo_id', $trxResep->depo_id)->first();
                    $stock->jml_keluar = $dt['jml_ambil'];
                    $sukses = $stock->save();
                    if (!$sukses) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                    }
                }
                
                /** insert ke detail Transaksi **/ 
                $detailId = $dt['detail_id'];
                $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxResep->trx_id)->first();

                if(!$trxDetail) {
                    $trxDetail = new TransaksiDetail();
                    $trxDetail->detail_id           = $detailId;
                    $trxDetail->reg_id              = $trxResep->reg_id;
                    $trxDetail->trx_id              = $trxResep->trx_id;
                    $trxDetail->jns_transaksi       = 'RESEP';
                    $trxDetail->client_id           = $this->clientId;
                    $trxDetail->created_by          = Auth::user()->username;
                    $trxDetail->item_id             = $dt['item_id'];
                }
                $trxDetail->kelas_harga_id      = '';//$request->kelas_harga_id;
                $trxDetail->buku_harga_id       = '';//$trxResep->buku_harga_id;
                $trxDetail->penjamin_id         = '';//$trxResep->penjamin_id;
                $trxDetail->item_nama           = $dt['item_nama'];
                $trxDetail->jumlah              = $dt['jml_ambil'];
                $trxDetail->satuan              = $dt['satuan'];
                
                $trxDetail->nilai               = $dt['nilai'];
                $trxDetail->diskon_persen       = $dt['diskon_persen'];
                $trxDetail->diskon              = $dt['diskon'];
                $trxDetail->harga_diskon        = $dt['harga_diskon'];
                $trxDetail->subtotal            = $dt['subtotal'];
                $trxDetail->dokter_id           = $trxResep->dokter_id;
                $trxDetail->dokter_pengirim_id  = '';
                $trxDetail->is_hitung_adm       = false;
                $trxDetail->is_aktif            = $dt['is_aktif'];
                $trxDetail->updated_by          = Auth::user()->username;
                $success = $trxDetail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi Resep.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $trxResep]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses realisasi data', 'error' => $e->getMessage()]);
        }
    }

    public function derealisasiResep(Request $request, $resep_id) {
        try {

            $trxResep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$resep_id)->first();
            if(!$trxResep) { 
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); 
            }
            if(!$trxResep->is_realisasi) { 
                return response()->json(['success' => false, 'message' => 'Proses tidak dapat dilanjutkan.']); 
            }
            
            // $transaksi = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
            //     ->where('trx_id',$resep_id)->where('pasien_id',$trxResep->pasien_id)
            //     ->where('status','DAFTAR')->first();
            $transaksi = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$resep_id)->where('pasien_id',$trxResep->pasien_id)->first();
            
            if(!$transaksi) { 
                return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']); 
            }

            if($transaksi->is_bayar) {
                return response()->json(['success' => false, 'message' => 'Pembayaran sudah dilakukan. Batalkan terlebih dahulu pembayaran untuk mengubah data.']); 
            }

            $rsp = TransaksiDetail::where('client_id',$this->clientId)->where('trx_id',$resep_id)->where('is_aktif',1)->get();
            if(!$rsp) { 
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); 
            }

            DB::connection('dbclient')->beginTransaction();
        
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->is_realisasi        = false;
            $trxResep->status              = 'DRAFT';
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }

            //delete transaksi
            $transaksi->trx_id              = date('ymdHis').'-'.$resep_id;
            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->is_realisasi        = false;
            $transaksi->is_aktif            = false;
            $transaksi->status              = 'BATAL';
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }
            
            foreach($rsp as $dt) {
                $dt->is_aktif       = false;
                $dt->updated_by    = Auth::user()->username;                
                $success = $dt->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menghapus data detail resep.']);
                }

                $stock = DepoProduk::where('client_id',$this->clientId)->where('produk_id', $dt['item_id'])->where('depo_id', $trxResep->depo_id)->first();
                if($stock) {
                    $stock->jml_masuk = $dt['jumlah'];
                    $sukses = $stock->save();
                    if (!$sukses) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                    }
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data pembatalan realisasi resep berhasil disimpan', 'data' => $trxResep]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses pembatalan realisasi data', 'error' => $e->getMessage()]);
        }
    }

    public function deleteResep(Request $request,$resepId) {
        try {
            $trx = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$resepId)
                ->first();
            
            if(!$trx) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trx->is_konfirmasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $trxDetail = TrxResepDetail::where('trx_id',$resepId)->where('reg_id',$trx->reg_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            if($trx) {
                $trx->updated_by = Auth::user()->username;
                $trx->is_aktif = false;
                $success = $trx->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data gagal dihapus.']);
                }
            }

            if($trxDetail) {
                $success = TrxResepDetail::where('trx_id',$resepId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal dihapus.']);
                }
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam menghapus data. Error: '.$e->getMessage()];
        }
    }

    public function listItemDepo(Request $request,$depo) {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

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
                    ->leftJoin('tb_depo','tb_depo_produk.depo_id','=','tb_depo.depo_id')
                    ->where('tb_depo_produk.client_id',$this->clientId)
                    ->where('tb_depo_produk.is_aktif','ILIKE',$aktif)
                    ->where('tb_depo_produk.depo_id',$depo)
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_depo_produk.depo_produk_id','ILIKE',$keyword)
                        ->orWhere('tb_depo_produk.produk_id','ILIKE',$keyword)
                        ->orWhere('tb_produk.produk_nama','ILIKE',$keyword);
                    })
                    ->select(

                        'tb_depo_produk.depo_produk_id',
                        'tb_depo.depo_nama',
                        'tb_depo.depo_id',
                        'tb_depo_produk.produk_id',
                        'tb_produk.produk_nama',
                        'tb_produk.jenis_produk',
                        'tb_produk.satuan',
                        'tb_produk.satuan_beli',
                        'tb_produk.harga_beli',
                        'tb_produk.hna',
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
}