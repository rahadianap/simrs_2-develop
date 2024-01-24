<?php

namespace Modules\Transaksi\Http\Controllers;

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

use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\DepoProduk;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use Modules\Transaksi\Entities\TrxBhp;
use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;

use RegistrasiHelper;

class ResepController extends Controller
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
            $query = $query->where('client_id',$this->clientId);
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
                        ->orderBy('group_racikan','ASC')
                        ->get();

                    $dt['detail'] = $detail;                        
                }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    public function listDepo(Request $request) {
        try {
            $data = Depo::where('client_id', $this->clientId)
                ->where('is_sell',true)->where('is_aktif',true)
                ->get();

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
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
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$resepId)->get();

            return ['success' => true, 'data' => $data];
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengambil data. Error: '.$e->getMessage()];
        }
    }

    public function saveResep(Request $request) {
        try {
            // dd($request);
            $regId = $request->reg_id;
            $reffTrxId = $request->reff_trx_id;            
            $trxId = RegistrasiHelper::instance()->ResepTransactionId($this->clientId);

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();

            DB::connection('dbclient')->beginTransaction();
            /**
             * create data transaksi
             */
            $trxResep                      = new TrxResep();
            $trxResep->reg_id              = $regId;
            $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
            $trxResep->trx_id              = $trxId;
            $trxResep->reff_trx_id         = $reffTrxId;
            // $trxResep->sub_trx_id          = $reffTrxId;
            $trxResep->status              = 'DRAFT';
            $trxResep->is_realisasi        = false;
            $trxResep->is_bayar            = false;
            $trxResep->is_aktif            = true;
            $trxResep->jns_transaksi       = $request->jns_transaksi;
            $trxResep->no_resep            = '-';
            $trxResep->no_transaksi        = '-';
            
            $trxResep->client_id           = $this->clientId;
            $trxResep->created_by          = Auth::user()->username;
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->tgl_resep           = date('Y-m-d H:i:s');
        
            $trxResep->penjamin_id         = $request->penjamin_id;
            $trxResep->penjamin_nama       = $request->penjamin_nama;            
            $trxResep->total               = $request->total;
            if($unit) {
                $trxResep->unit_id         = $unit->unit_id;
                $trxResep->unit_nama       = $unit->unit_nama;
                // $trxResep->unit_depo_id    = $request->unit_depo_id;
                // $trxResep->unit_depo_nama  = $unit->unit_depo_nama;
            }
            else {
                $trxResep->unit_id             = '';
                $trxResep->unit_nama           = '';
                // $trxResep->unit_depo_id        = '';
                // $trxResep->unit_depo_nama      = '';
            }
            $trxResep->depo_id             = '';
            $trxResep->depo_nama             = '';
            $trxResep->dokter_id           = $request->dokter_id;
            $trxResep->dokter_nama         = $request->dokter_nama;
            $trxResep->peresep             = $request->peresep;
            
            $trxResep->pasien_id           = $request->pasien_id;
            $trxResep->no_rm               = $request->no_rm;
            $trxResep->nama_pasien         = $request->nama_pasien;
            $trxResep->jns_resep           = 'RESEP';
            // if($request->is_racikan = false) {
                //$trxResep->jns_resep           = 'RESEP RAWAT JALAN';
                // $trxResep->group_racikan       = null;
            //}
            // $trxResep->jns_resep           = 'RESEP RACIKAN';
            // $trxResep->group_racikan       = $request->no_racikan;
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            

            foreach($request->items as $dt) {
                /*** insert ke table resep detail */
                $detailId = $this->clientId.'-RSP'.date('Ymd').Uuid::uuid4()->getHex();
                $rsp = TrxResepDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $rsp = new TrxResepDetail();
                    $rsp->detail_id     = $detailId;
                    $rsp->reg_id        = $regId;
                    $rsp->trx_id        = $trxId;
                    $rsp->reff_trx_id   = $reffTrxId;
                    // $rsp->sub_trx_id    = $reffTrxId;
                    $rsp->item_id       = $dt['item_id'];
                    $rsp->client_id     = $this->clientId;
                    $rsp->created_by    = Auth::user()->username;
                }

                if($unit) {
                    $rsp->unit_id       = $request->unit_id;
                    $rsp->unit_nama     = $unit->unit_nama;
                }
                else {
                    $rsp->unit_id       = '';
                    $rsp->unit_nama     = '';
                }
                
                $rsp->depo_id           = '';
                $rsp->depo_nama         = '';
                $rsp->dokter_id         = $request->dokter_id;
                $rsp->dokter_nama       = $request->peresep;
                $rsp->item_nama         = $dt['item_nama'];
                $rsp->item_tipe         = $dt['item_tipe'];
                $rsp->klasifikasi       = '';
                $rsp->jumlah            = $dt['jumlah'];
                // $rsp->jml_resep      = $dt['jumlah'];
                $rsp->jml_ambil         = 0;
                $rsp->signa             = $dt['signa'];
                $rsp->signa_deskripsi   = $dt['signa_deskripsi'];
                $rsp->satuan            = $dt['satuan'];
                
                // $rsp->satuan_komposisi  = $dt['satuan_komposisi'];  
                // $rsp->jml_komposisi     = $dt['jumlah_komposisi'];              
                
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
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
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
            $trxResep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->trx_id)->first();
            
            if(!$trxResep) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trxResep->is_konfirmasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $regId = $trxResep->reg_id;
            $reffTrxId = $trxResep->reff_trx_id;            
            $trxId = $request->trx_id;

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();
            //$ruang = RuangPelayanan::where('ruang_id',$trx->ruang_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            DB::connection('dbclient')->beginTransaction();
            //$trxResep = new Transaksi();
            /**
             * update data transaksi
             */

            // $trxResep                      = new TrxResep();
            // $trxResep->reg_id              = $regId;
            // $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
            // $trxResep->trx_id              = $trxId;
            // $trxResep->reff_trx_id         = $reffTrxId;
            // $trxResep->status              = 'DRAFT';
            // $trxResep->is_realisasi        = false;
            // $trxResep->is_bayar            = false;
            // $trxResep->is_aktif            = true;
            // $trxResep->jns_transaksi       = $request->jns_transaksi;
            // $trxResep->no_resep            = '-';
            // $trxResep->no_transaksi        = '-';            
            // $trxResep->client_id           = $this->clientId;
            // $trxResep->created_by          = Auth::user()->username;
            //$trxResep->tgl_resep           = date('Y-m-d H:i:s');
        
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->penjamin_id         = $penjamin->penjamin_id;
            $trxResep->penjamin_nama       = $penjamin->penjamin_nama;            
            $trxResep->total               = $request->total;
            if($unit) {
                $trxResep->unit_id         = $unit->unit_id;
                $trxResep->unit_nama       = $unit->unit_nama;
            }
            else {
                $trxResep->unit_id             = '';
                $trxResep->unit_nama           = '';
            }
            $trxResep->dokter_id           = $request->dokter_id;
            $trxResep->dokter_nama         = $dokter->dokter_nama;
            $trxResep->peresep             = $dokter->dokter_nama;
            
            $trxResep->pasien_id           = $request->pasien_id;
            $trxResep->no_rm               = $pasien->no_rm;
            $trxResep->nama_pasien         = $pasien->nama_pasien;
            $trxResep->jns_resep           = 'RESEP RAWAT JALAN';
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            

            foreach($request->items as $dt) {
                /*** insert ke table resep detail */
                $detailId = $dt['detail_id'];
                $rsp = TrxResepDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $rsp = new TrxResepDetail();
                    $rsp->detail_id     = $detailId;
                    $rsp->reg_id        = $regId;
                    $rsp->trx_id        = $trxId;
                    $rsp->reff_trx_id   = $reffTrxId;
                    $rsp->item_id       = $dt['item_id'];
                    $rsp->client_id     = $this->clientId;
                    $rsp->created_by    = Auth::user()->username;
                }

                if($unit) {
                    $rsp->unit_id       = $request->unit_id;
                    $rsp->unit_nama     = $unit->unit_nama;
                }
                else {
                    $rsp->unit_id       = '';
                    $rsp->unit_nama     = '';
                }
                
                // $rsp->depo_id       = $dt['depo_id'];
                // $rsp->depo_nama     = $dt['depo_nama'];
                $rsp->dokter_id     = $dokter->dokter_id;
                $rsp->dokter_nama   = $dokter->dokter_nama;
                $rsp->item_nama     = $dt['item_nama'];

                $rsp->item_tipe     = $dt['item_tipe'];
                
                $rsp->klasifikasi   = ''; //$dt['klasifikasi'];
                $rsp->satuan        = $dt['satuan'];
                $rsp->signa         = $dt['signa'];
                $rsp->signa_deskripsi = $dt['signa_deskripsi'];                
                
                $rsp->jumlah        = $dt['jumlah'];
                $rsp->jml_ambil     = 0;

                $rsp->nilai         = $dt['nilai'];
                $rsp->diskon_persen = $dt['diskon_persen'];
                $rsp->diskon        = $dt['diskon'];
                $rsp->harga_diskon  = $dt['harga_diskon'];
                $rsp->subtotal      = $dt['subtotal'];
                $rsp->is_realisasi  = false;
                $rsp->is_aktif      = $dt['is_aktif'];
                $rsp->updated_by    = Auth::user()->username;
                
                $success = $rsp->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
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

    public function realisasiResep(Request $request, $resep_id) {
        try {
            $trxResep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$resep_id)->first();
            
            if(!$trxResep) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trxResep->is_konfirmasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            DB::connection('dbclient')->beginTransaction();
        
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->is_realisasi        = true;
            $trxResep->status              = 'REALISASI';
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }
            
            $rsp = TrxResepDetail::where('client_id',$this->clientId)
                    ->where('trx_id',$resep_id)->where('is_aktif',1)
                    ->get();

            foreach($rsp as $dt) {
                $dt->jml_ambil     = 0;
                $dt->jml_riil      = $dt['jumlah'];
                $dt->status        = 'REALISASI';
                $dt->is_realisasi  = true;
                $dt->updated_by    = Auth::user()->username;
                
                $success = $dt->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
                }

                $stock = DepoProduk::where('client_id',$this->clientId)->where('produk_id', $dt['item_id'])->where('depo_id', 'CL2023-0001.DEPO-0001')->first();
                $stock->jml_keluar = $dt['jumlah'];

                $sukses = $stock->save();
                if (!$sukses) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataResep($resep_id);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
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
}