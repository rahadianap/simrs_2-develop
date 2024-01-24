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

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use Modules\Transaksi\Entities\RawatJalan;
use Modules\Transaksi\Entities\Pemeriksaan;
use Modules\Transaksi\Entities\PemeriksaanDetail;

use Modules\MasterData\Entities\DepoProduk;
use Modules\Persediaan\Entities\KartuStock;
use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\Depo;


use RegistrasiHelper;

class PemeriksaanController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function dataPemeriksaan(Request $request, $trxId) {
        try {
            $data = $this->getDataPemeriksaan($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function getDataPemeriksaan($trxId) {
        try {
            $data = Pemeriksaan::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$trxId)->first();
            
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data bhp tidak ditemukan.']);
            }
            $items = PemeriksaanDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$trxId)->get();

            foreach($items as $item) {
                $item['komponen'] = TransaksiDetailKomp::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('detail_id',$item['detail_id'])->where('trx_id',$item['trx_id'])->get();
            }

            $data['items'] = $items;
            return ['success' => true, 'data' => $data];
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengambil data. Error: '.$e->getMessage()];
        }
    }

    public function savePemeriksaan(Request $request) {
        try {
            $trx = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->reff_trx_id)->first();
            
            if(!$trx) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trx->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $regId = $request->reg_id;
            $reffTrxId = $request->reff_trx_id;            
            $trxId = RegistrasiHelper::instance()->PemeriksaanId($this->clientId);

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**
             * create data pemeriksaan
             */
            $pemeriksaan                      = new Pemeriksaan();
            $pemeriksaan->trx_id              = $trxId;
            $pemeriksaan->reg_id              = $regId;
            $pemeriksaan->reff_trx_id         = $reffTrxId;
            // $pemeriksaan->sub_trx_id          = $reffTrxId;
            $pemeriksaan->tgl_transaksi       = date('Y-m-d H:i:s');
            if($unit) {
                $pemeriksaan->unit_id         = $unit->unit_id;
                $pemeriksaan->unit_nama       = $unit->unit_nama;
            }
            else {
                $pemeriksaan->unit_id             = '';
                $pemeriksaan->unit_nama           = '';
            }
            
            $pemeriksaan->jns_transaksi       = $trx->jns_transaksi;
            $pemeriksaan->no_antrian          = '-';
            $pemeriksaan->no_transaksi        = '-';
            
            $pemeriksaan->penjamin_id         = $penjamin->penjamin_id;
            $pemeriksaan->penjamin_nama       = $penjamin->penjamin_nama;            
            
            $pemeriksaan->dokter_id           = $request->dokter_id;
            $pemeriksaan->dokter_nama         = $dokter->dokter_nama;
            
            $pemeriksaan->pasien_id           = $request->pasien_id;
            $pemeriksaan->no_rm               = $pasien->no_rm;
            $pemeriksaan->nama_pasien         = $pasien->nama_pasien;
            
            $pemeriksaan->total               = $request->total;
            
            $pemeriksaan->status              = 'DRAFT';
            $pemeriksaan->is_aktif            = true;
            $pemeriksaan->is_realisasi        = false;
            $pemeriksaan->is_bayar            = false;
            
            $pemeriksaan->client_id           = $this->clientId;
            $pemeriksaan->created_by          = Auth::user()->username;
            $pemeriksaan->updated_by          = Auth::user()->username;
        
            $success = $pemeriksaan->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi bhp gagal disimpan'];
            }            

            //create data transaction
            /**
             * create data transaksi
             */
            $transaksi                      = new Transaksi();
            $transaksi->reg_id              = $regId;
            $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
            $transaksi->trx_id              = $trxId;
            $transaksi->reff_trx_id         = $reffTrxId;
            // $transaksi->sub_trx_id          = $reffTrxId;
            // $transaksi->is_sub_trx          = true;
            $transaksi->status              = 'DRAFT';
            $transaksi->is_realisasi        = false;
            $transaksi->is_bayar            = false;
            $transaksi->is_aktif            = true;
            $transaksi->jns_transaksi       = $trx->jns_transaksi;
            $transaksi->no_antrian          = '-';
            $transaksi->no_transaksi        = '-';
            
            $transaksi->kelas_id            = $request->kelas_harga_id;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;
            $transaksi->kelas_penjamin_id   = $request->kelas_harga;
            $transaksi->penjamin_id         = $request->penjamin_id;
            $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksi->buku_harga          = $penjamin->buku_harga;
            $transaksi->total               = $request->total;
            
            $transaksi->unit_id             = $request->unit_id;
            $transaksi->unit_nama           = $unit->unit_nama;
            $transaksi->ruang_id            = '';
            $transaksi->ruang_nama          = '';
            
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = '';
            
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            
            $transaksi->pasien_id           = $request->pasien_id;
            $transaksi->no_rm               = $pasien->no_rm;
            $transaksi->nama_pasien         = $pasien->nama_pasien;
            
            $transaksi->client_id           = $this->clientId;
            $transaksi->created_by          = Auth::user()->username;
            $transaksi->updated_by          = Auth::user()->username;
            
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan1.']);
            }

            //insert detail pemeriksaan
            foreach($request->items as $dt) {
                // dd($dt);
                //pemeriksaan stock bhp apakah tersedia atau tidak
                if($dt['is_bhp']) {
                    $itemBalanced = DepoProduk::where('is_aktif',1)->where('client_id',$this->clientId)
                        ->where('produk_id',$dt['item_id'])->where('depo_id',$dt['depo_id'])->first();

                    // $itemBalanced = DepoProduk::where('is_aktif',1)->where('client_id',$this->clientId)
                    //     ->where('produk_id',$dt['produk_id'])->where('depo_id',$dt['depo_id'])->first();

                    if(!$itemBalanced) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.','data'=>$dt]);
                    }
                    if($itemBalanced->jml_total < $dt['jumlah']) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Jumlah persediaan item '.$dt['item_nama'].' di depo '.$dt['depo_nama'].' tidak mencukupi. Jumlah tersedia hanya '.$itemBalanced->jml_total]);
                    }
                }
                
                /*** insert ke table bhp detail */
                $detailId = $dt['detail_id'];
                $detail = PemeriksaanDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->first();

                    // $detail = PemeriksaanDetail::where('client_id',$this->clientId)
                    // ->where('detail_id',$detailId)
                    // ->where('item_id',$dt['produk_id'])
                    // ->where('trx_id',$trxId)
                    // ->where('is_aktif',1)
                    // ->first();

                if(!$detail) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $detail = new PemeriksaanDetail();
                    $detail->detail_id     = $detailId;
                    $detail->trx_id        = $trxId;
                    $detail->reg_id        = $regId;
                    $detail->reff_trx_id   = $reffTrxId;
                    
                    //$detail->item_id       = $dt['produk_id'];
                    $detail->item_id       = $dt['item_id'];
                    $detail->client_id     = $this->clientId;
                    $detail->created_by    = Auth::user()->username;
                }

                $detail->item_nama     = $dt['item_nama'];
                //$detail->item_nama     = $dt['produk_nama'];
                
                if($unit) {
                    $detail->unit_id       = $request->unit_id;
                    $detail->unit_nama     = $unit->unit_nama;
                }
                else {
                    $detail->unit_id       = '';
                    $detail->unit_nama     = '';
                }
                
                $detail->depo_id       = $dt['depo_id'];
                $detail->depo_nama     = $dt['depo_nama'];

                $detail->dokter_id     = $dt['dokter_id'];
                $detail->dokter_nama   = $dt['dokter_nama'];
                
                $detail->jenis         = $dt['jenis'];
                
                $detail->satuan        = $dt['satuan'];
                $detail->jumlah        = $dt['jumlah'];
                $detail->jml_formula   = $dt['jml_formula'];
                
                $detail->satuan        = $dt['satuan'];                
                $detail->nilai         = $dt['nilai'];
                $detail->diskon_persen = $dt['diskon_persen'];
                $detail->diskon        = $dt['diskon'];
                $detail->harga_diskon  = $dt['harga_diskon'];
                $detail->subtotal      = $dt['subtotal'];

                $detail->tindakan_id   = $dt['item_id'];
                $detail->tindakan_nama = $dt['item_nama'];
                // $detail->tindakan_id   = $dt['tindakan_id'];
                // $detail->tindakan_nama = $dt['tindakan_nama'];

                $detail->is_realisasi  = false;
                $detail->bhp_tindakan  = $dt['bhp_tindakan'];
                $detail->is_bhp        = $dt['is_bhp'];
                $detail->is_aktif      = $dt['is_aktif'];
                $detail->updated_by    = Auth::user()->username;
                
                $success = $detail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail pemeriksaan.'];
                }

                //detail transaksi
                $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)->first();
                
                // $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                //     ->where('detail_id',$detailId)->where('item_id',$dt['produk_id'])
                //     ->where('trx_id',$trxId)->first();

                if(!$trxDetail) {
                    $trxDetail = new TransaksiDetail();
                    $trxDetail->detail_id           = $detailId;
                    $trxDetail->reg_id              = $regId;
                    $trxDetail->trx_id              = $trxId;
                    $trxDetail->jns_transaksi       = $dt['jenis'];
                    $trxDetail->client_id           = $this->clientId;
                    $trxDetail->created_by          = Auth::user()->username;
                    $trxDetail->item_id             = $dt['item_id'];
                    //$trxDetail->item_id             = $dt['produk_id'];
                }
                $trxDetail->kelas_harga_id      = $request->kelas_harga_id;
                $trxDetail->buku_harga_id       = $penjamin->buku_harga_id;
                $trxDetail->penjamin_id         = $penjamin->penjamin_id;
                $trxDetail->item_nama           = $dt['item_nama'];
                $trxDetail->jumlah              = $dt['jumlah'];
                $trxDetail->satuan              = $dt['satuan'];
                
                $trxDetail->nilai               = $dt['nilai'];
                $trxDetail->diskon_persen       = $dt['diskon_persen'];
                $trxDetail->diskon              = $dt['diskon'];
                $trxDetail->harga_diskon        = $dt['harga_diskon'];
                $trxDetail->subtotal            = $dt['subtotal'];
                $trxDetail->dokter_id           = $dt['dokter_id'];
                $trxDetail->dokter_pengirim_id  = '';
                $trxDetail->is_hitung_adm       = false;
                $trxDetail->is_aktif            = $dt['is_aktif'];
                $trxDetail->updated_by          = Auth::user()->username;

                $success = $trxDetail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP'];
                }

                foreach($dt['komponen'] as $komp) {
                    $detailKomp = new TransaksiDetailKomp();
                    $detailKomp->komp_detail_id = Uuid::uuid4()->getHex();
                    $detailKomp->detail_id = $detailId;
                    $detailKomp->reg_id = $regId;
                    $detailKomp->trx_id = $trxId;
                    $detailKomp->harga_id = $komp['harga_id'];

                    $detailKomp->is_realisasi = false;
                    $detailKomp->is_bayar = false;
                    $detailKomp->komp_harga_id = $komp['komp_harga_id'];
                    $detailKomp->komp_harga = $komp['komp_harga'];
                    $detailKomp->jumlah = $komp['jumlah'];
                    $detailKomp->nilai = $komp['nilai'];
                    $detailKomp->diskon = $komp['diskon'];
                    $detailKomp->nilai_diskon = $komp['nilai_diskon'];
                    $detailKomp->subtotal = $komp['subtotal'];

                    $detailKomp->dokter_id = $dt['dokter_id'];
                    $detailKomp->dokter_nama = $dt['dokter_nama'];
                    $detailKomp->deskripsi = '';

                    $detailKomp->is_aktif = $dt['is_aktif'];
                    $detailKomp->client_id = $this->clientId;
                    $detailKomp->created_by = Auth::user()->username;
                    $detailKomp->updated_by = Auth::user()->username;
                    
                    $success = $detailKomp->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data detail komponen gagal disimpan.']);
                    }   
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataPemeriksaan($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data bhp berhasil disimpan', 'data' => $data['data']]);

        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function updatePemeriksaan(Request $request) {
        try {
            $pemeriksaan = Pemeriksaan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->trx_id)->first();
            
            if(!$pemeriksaan) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($pemeriksaan->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $transaksi = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->trx_id)->first();
            
            if(!$transaksi) { return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']); }
            if($transaksi->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data transaksi sudah dikonfimasi.']); }

            $regId = $pemeriksaan->reg_id;
            $trxId = $pemeriksaan->trx_id;
            $reffTrxId = $pemeriksaan->reff_trx_id;

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();

            DB::connection('dbclient')->beginTransaction();
        
            $pemeriksaan->updated_by          = Auth::user()->username;
            $pemeriksaan->penjamin_id         = $penjamin->penjamin_id;
            $pemeriksaan->penjamin_nama       = $penjamin->penjamin_nama;            
            $pemeriksaan->total               = $request->total;
            if($unit) {
                $pemeriksaan->unit_id         = $unit->unit_id;
                $pemeriksaan->unit_nama       = $unit->unit_nama;
            }
            else {
                $pemeriksaan->unit_id         = '';
                $pemeriksaan->unit_nama       = '';
            }
            $pemeriksaan->dokter_id           = $request->dokter_id;
            $pemeriksaan->dokter_nama         = $dokter->dokter_nama;
            
            $pemeriksaan->pasien_id           = $request->pasien_id;
            $pemeriksaan->no_rm               = $pasien->no_rm;
            $pemeriksaan->nama_pasien         = $pasien->nama_pasien;
        
            $success = $pemeriksaan->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data pemeriksaan gagal diupdate.']);
            }

            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->total               = $request->total;
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data transaksi gagal diupdate.']);
            }

            //insert detail pemeriksaan
            foreach($request->items as $dt) {
                //pemeriksaan stock bhp apakah tersedia atau tidak
                if($dt['is_bhp']) {
                    $itemBalanced = DepoProduk::where('is_aktif',1)->where('client_id',$this->clientId)
                        ->where('produk_id',$dt['item_id'])->where('depo_id',$dt['depo_id'])->first();

                    if(!$itemBalanced) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
                    }
                    if($itemBalanced->jml_total < $dt['jumlah']) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Jumlah persediaan item '.$dt['item_nama'].' di depo '.$dt['depo_nama'].' tidak mencukupi. Jumlah tersedia hanya '.$itemBalanced->jml_total]);
                    }
                }
                
                /*** insert ke table bhp detail */
                $detailId = $dt['detail_id'];
                $detail = PemeriksaanDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$detail) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $detail = new PemeriksaanDetail();
                    $detail->detail_id     = $detailId;
                    $detail->trx_id        = $trxId;
                    $detail->reg_id        = $regId;
                    $detail->reff_trx_id   = $reffTrxId;
                    
                    $detail->item_id       = $dt['item_id'];
                    $detail->client_id     = $this->clientId;
                    $detail->created_by    = Auth::user()->username;
                }

                $detail->item_nama     = $dt['item_nama'];
                
                if($unit) {
                    $detail->unit_id       = $request->unit_id;
                    $detail->unit_nama     = $unit->unit_nama;
                }
                else {
                    $detail->unit_id       = '';
                    $detail->unit_nama     = '';
                }
                
                $detail->depo_id       = $dt['depo_id'];
                $detail->depo_nama     = $dt['depo_nama'];

                $detail->dokter_id     = $dt['dokter_id'];
                $detail->dokter_nama   = $dt['dokter_nama'];
                
                $detail->jenis         = $dt['jenis'];
                
                $detail->satuan        = $dt['satuan'];
                $detail->jumlah        = $dt['jumlah'];
                $detail->jml_formula   = $dt['jml_formula'];
                
                $detail->satuan        = $dt['satuan'];                
                $detail->nilai         = $dt['nilai'];
                $detail->diskon_persen = $dt['diskon_persen'];
                $detail->diskon        = $dt['diskon'];
                $detail->harga_diskon  = $dt['harga_diskon'];
                $detail->subtotal      = $dt['subtotal'];

                $detail->tindakan_id   = $dt['item_id'];
                $detail->tindakan_nama = $dt['item_nama'];

                $detail->is_realisasi  = false;
                $detail->bhp_tindakan  = $dt['bhp_tindakan'];
                $detail->is_bhp        = $dt['is_bhp'];
                $detail->is_aktif      = $dt['is_aktif'];
                $detail->updated_by    = Auth::user()->username;
                
                $success = $detail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail pemeriksaan.'];
                }

                //detail transaksi
                $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)->first();

                if(!$trxDetail) {
                    //$detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $trxDetail = new TransaksiDetail();
                    $trxDetail->detail_id           = $detailId;
                    $trxDetail->reg_id              = $regId;
                    $trxDetail->trx_id              = $trxId;
                    //$trxDetail->reff_trx_id         = $request->reff_trx_id;
                    $trxDetail->jns_transaksi       = $dt['jenis'];
                    $trxDetail->client_id           = $this->clientId;
                    $trxDetail->created_by          = Auth::user()->username;
                    $trxDetail->item_id             = $dt['item_id'];
                }
                $trxDetail->kelas_harga_id      = $transaksi->kelas_harga_id;
                $trxDetail->buku_harga_id       = $penjamin->buku_harga_id;
                $trxDetail->penjamin_id         = $penjamin->penjamin_id;
                $trxDetail->item_nama           = $dt['item_nama'];
                $trxDetail->jumlah              = $dt['jumlah'];
                $trxDetail->satuan              = $dt['satuan'];
                
                $trxDetail->nilai               = $dt['nilai'];
                $trxDetail->diskon_persen       = $dt['diskon_persen'];
                $trxDetail->diskon              = $dt['diskon'];
                $trxDetail->harga_diskon        = $dt['harga_diskon'];
                $trxDetail->subtotal            = $dt['subtotal'];
                $trxDetail->dokter_id           = $request->dokter_id;
                $trxDetail->dokter_pengirim_id  = '';
                $trxDetail->is_hitung_adm       = false;
                $trxDetail->is_aktif            = $dt['is_aktif'];
                $trxDetail->updated_by          = Auth::user()->username;

                $success = $trxDetail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP'];
                }

                foreach($dt['komponen'] as $komp) {
                    $detailKomp = TransaksiDetailKomp::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('komp_detail_id',$komp['komp_detail_id'])
                        ->where('trx_id',$trxId)->where('detail_id',$detailId)->first();
                    
                    if(!$detailKomp){
                        $detailKomp = new TransaksiDetailKomp();
                        $detailKomp->komp_detail_id = Uuid::uuid4()->getHex();
                        $detailKomp->detail_id = $detailId;
                        $detailKomp->reg_id = $regId;
                        $detailKomp->trx_id = $trxId;
                        $detailKomp->client_id = $this->clientId;
                        $detailKomp->created_by = Auth::user()->username;
                    }

                    $detailKomp->harga_id = $komp['harga_id'];

                    $detailKomp->is_realisasi = false;
                    $detailKomp->is_bayar = false;
                    $detailKomp->komp_harga_id = $komp['komp_harga_id'];
                    $detailKomp->komp_harga = $komp['komp_harga'];
                    $detailKomp->jumlah = $komp['jumlah'];
                    $detailKomp->nilai = $komp['nilai'];
                    $detailKomp->diskon = $komp['diskon'];
                    $detailKomp->nilai_diskon = $komp['nilai_diskon'];
                    $detailKomp->subtotal = $komp['subtotal'];

                    $detailKomp->dokter_id = $dt['dokter_id'];
                    $detailKomp->dokter_nama = $dt['dokter_nama'];

                    $detailKomp->deskripsi = '';
                    $detailKomp->is_aktif = $dt['is_aktif'];
                    $detailKomp->updated_by = Auth::user()->username;
                    
                    $success = $detailKomp->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data detail komponen gagal disimpan.']);
                    }   
                }
            }
            DB::connection('dbclient')->commit();

            $data = $this->getDataPemeriksaan($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data bhp berhasil disimpan', 'data' => $data['data']]);
            //return response()->json(['success' => false, 'message' => 'Data bhp berhasil disimpan']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }

    public function deletePemeriksaan(Request $request,$trxId) {
        try {
            $trx = Pemeriksaan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$trxId)
                ->first();
            
            if(!$trx) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trx->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $trxDetail = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $transaksi = Transaksi::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $transDetail = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $transDetailKomp = TransaksiDetailKomp::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
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
                $success = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal dihapus.']);
                }
            }

            if($transaksi) {
                $success = Transaksi::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi gagal dihapus.']);
                }
            }
            if($transDetail) {
                $success = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi detail gagal dihapus.']);
                }
            }

            if($transDetailKomp) {
                $success = TransaksiDetailKomp::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data komponen transaksi detail gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam menghapus data. Error: '.$e->getMessage()];
        }
    }

    public function confirmPemeriksaan(Request $request,$trxId) {
        try {
            $trx = Pemeriksaan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$trxId)
                ->first();
            
            if(!$trx) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            if($trx->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); }

            $transaksi = Transaksi::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$transaksi) { return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']); }
            if($transaksi->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data transaksi sudah dikonfimasi.']); }
    
            $trxDetail = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $trxBhp = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->where('is_bhp',1)->get();
            
            
            $transDetail = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $transDetailKomp = TransaksiDetailKomp::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $trx->status = 'SELESAI';
            $trx->updated_by = Auth::user()->username;
            $trx->is_realisasi = true;
            $success = $trx->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data gagal dikonfirmasi.']);
            }

            if($trxDetail) {
                $success = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>true, 'status'=>'SELESAI','updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal dikonfirmasi.']);
                }
            }

            if($transaksi) {
                $success = Transaksi::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>true,'status'=>'SELESAI', 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi gagal dikonfirmasi.']);
                }
            }
            if($transDetail) {
                $success = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>true,'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi detail gagal dikonfirmasi.']);
                }
            }

            if($transDetailKomp) {
                $success = TransaksiDetailKomp::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>true, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data komponen transaksi detail gagal dikonfirmasi.']);
                }
            }

            //pengurangan BHP di depo 
            foreach($trxBhp as $bhp) {
                $itemBalanced = DepoProduk::where('is_aktif',1)->where('client_id',$this->clientId)
                        ->where('produk_id',$bhp['item_id'])->where('depo_id',$bhp['depo_id'])->first();

                if(!$itemBalanced) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
                }
                if($itemBalanced->jml_total < $bhp['jumlah']) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Jumlah persediaan item '.$bhp['item_nama'].' di depo '.$bhp['depo_nama'].' tidak mencukupi. Jumlah tersedia hanya '.$itemBalanced->jml_total]);
                }

                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$bhp['item_id'])->where('is_aktif',1)->first();
                $bhp['produk_nama'] = $produk->produk_nama;
                $bhp['jenis_produk'] = $produk->jenis_produk;
                
                $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('produk_id',$bhp['item_id'])
                    ->where('depo_id',$bhp['depo_id'])
                    ->where('is_aktif',1)->first();
                
                if(!$depoProduk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /**
                 * periksa apakah penyesuaian menyebabkan nilai persediaan menjadi minus
                 */
                if($bhp['jumlah'] > $depoProduk->jml_total) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo akan menjadi kurang dari nol jika proses dilanjutkan.']);
                }

                $bhp['jml_total'] = $depoProduk->jml_total;

                /**
                 * update data persediaan
                 */
                $success = $depoProduk->update([
                        'updated_by' => Auth::user()->username,
                        'jml_keluar' => DB::raw('jml_keluar + '.$bhp['jumlah']),
                        'jml_total' => DB::raw('jml_total - '.$bhp['jumlah']),
                    ]);
                    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /*** buat kartu stock */
                $kartuStock = new KartuStock();
                $kartuStock->stock_log_id     = $this->clientId."-".Uuid::uuid4()->getHex();
                $kartuStock->reg_id           = $trx->reg_id;
                $kartuStock->trx_id           = $trx->trx_id;
                $kartuStock->sub_trx_id       = $trx->reff_trx_id;
                $kartuStock->detail_id        = $bhp['detail_id'];
                $kartuStock->produk_id        = $bhp["item_id"];
                $kartuStock->tgl_log          = date('Y-m-d H:i:s');
                $kartuStock->tgl_transaksi    = date('Y-m-d H:i:s');
                $kartuStock->jns_transaksi    = "BHP";
                $kartuStock->aksi             = "SIMPAN";
                // Sementara Jenis Produk menggunakan MEDIS
                $kartuStock->jns_produk       = $produk->jenis_produk;
                $kartuStock->produk_nama      = $produk->produk_nama;
                $kartuStock->unit_id          = $bhp["unit_id"];
                $kartuStock->depo_id          = $bhp["depo_id"];
                $kartuStock->jml_awal         = 0;    
                $kartuStock->jml_masuk        = 0;
                $kartuStock->jml_keluar       = $bhp['jumlah'];
                $kartuStock->jml_penyesuaian  = 0;
                $kartuStock->jml_akhir        = ($bhp['jml_total'] - $bhp["jumlah"])*1;
                $kartuStock->catatan          = 'Pengeluaran stok BHP tindakan pemeriksaan';
                $kartuStock->keterangan       = "Pengeluaran stok depo ".$bhp['depo_nama'];
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
            $data = $this->getDataPemeriksaan($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil dikonfirmasi', 'data' => $data['data']]);
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengkonfirmasi data. Error: '.$e->getMessage()];
        }
    }

    public function unconfirmPemeriksaan(Request $request,$trxId) {
        try {
            $trx = Pemeriksaan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$trxId)
                ->first();
            
            if(!$trx) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            //if(!$trx->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data sudah berubah status.']); }

            $transaksi = Transaksi::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$transaksi) { return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']); }
            //if(!$transaksi->is_realisasi) { return response()->json(['success' => false, 'message' => 'Data transaksi sudah berubah status.']); }
    
            $trxDetail = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('is_realisasi',1)->where('client_id',$this->clientId)->first();

            //if(!$trxDetail) { return response()->json(['success' => false, 'message' => 'Data detail transaksi tidak ditemukan.']); }
            
            $trxBhp = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->where('is_bhp',1)
                ->where('is_realisasi',1)->get();
            
            
            $transDetail = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $transDetailKomp = TransaksiDetailKomp::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            

            DB::connection('dbclient')->beginTransaction();
            $trx->status = 'DRAFT';
            $trx->updated_by = Auth::user()->username;
            $trx->is_realisasi = false;
            $success = $trx->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data gagal dikonfirmasi.']);
            }

            if($trxDetail) {
                $success = PemeriksaanDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>false,'status'=>'DRAFT', 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal dikonfirmasi.']);
                }
            }

            if($transaksi) {
                $success = Transaksi::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>false,'status'=>'DRAFT', 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi gagal dikonfirmasi.']);
                }
            }
            if($transDetail) {
                $success = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi detail gagal dikonfirmasi.']);
                }
            }

            if($transDetailKomp) {
                $success = TransaksiDetailKomp::where('trx_id',$trxId)->where('reg_id',$trx->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s') ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data komponen transaksi detail gagal dikonfirmasi.']);
                }
            }

            //pengurangan BHP di depo 
            foreach($trxBhp as $bhp) {
                $itemBalanced = DepoProduk::where('is_aktif',1)->where('client_id',$this->clientId)
                        ->where('produk_id',$bhp['item_id'])->where('depo_id',$bhp['depo_id'])->first();

                if(!$itemBalanced) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
                }
                // if($itemBalanced->jml_total < $bhp['jumlah']) {
                //     DB::connection('dbclient')->rollBack();
                //     return response()->json(['success' => false, 'message' => 'Jumlah persediaan item '.$bhp['item_nama'].' di depo '.$bhp['depo_nama'].' tidak mencukupi. Jumlah tersedia hanya '.$itemBalanced->jml_total]);
                // }

                $produk = Produk::where('client_id',$this->clientId)->where('produk_id',$bhp['item_id'])->where('is_aktif',1)->first();
                $bhp['produk_nama'] = $produk->produk_nama;
                $bhp['jenis_produk'] = $produk->jenis_produk;
                
                $depoProduk = DepoProduk::where('client_id',$this->clientId)->where('produk_id',$bhp['item_id'])
                    ->where('depo_id',$bhp['depo_id'])
                    ->where('is_aktif',1)->first();
                
                if(!$depoProduk) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /**
                 * periksa apakah penyesuaian menyebabkan nilai persediaan menjadi minus
                 */
                // if($bhp['jumlah'] > $depoProduk->jml_total) {
                //     DB::connection('dbclient')->rollBack();
                //     return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo akan menjadi kurang dari nol jika proses dilanjutkan.']);
                // }

                $bhp['jml_total'] = $depoProduk->jml_total;

                /**
                 * update data persediaan
                 */
                $success = $depoProduk->update([
                        'updated_by' => Auth::user()->username,
                        'jml_masuk' => DB::raw('jml_masuk + '.$bhp['jumlah']),
                        'jml_total' => DB::raw('jml_total + '.$bhp['jumlah']),
                    ]);
                    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data item penyesuaian di depo tidak ditemukan.']);
                }

                /*** buat kartu stock */
                $kartuStock = new KartuStock();
                $kartuStock->stock_log_id     = $this->clientId."-".Uuid::uuid4()->getHex();
                $kartuStock->reg_id           = $trx->reg_id;
                $kartuStock->trx_id           = $trx->trx_id;
                $kartuStock->sub_trx_id       = $trx->reff_trx_id;
                $kartuStock->detail_id        = $bhp['detail_id'];
                $kartuStock->produk_id        = $bhp["item_id"];
                $kartuStock->tgl_log          = date('Y-m-d H:i:s');
                $kartuStock->tgl_transaksi    = date('Y-m-d H:i:s');
                $kartuStock->jns_transaksi    = "BHP";
                $kartuStock->aksi             = "PEMBATALAN";
                // Sementara Jenis Produk menggunakan MEDIS
                $kartuStock->jns_produk       = $produk->jenis_produk;
                $kartuStock->produk_nama      = $produk->produk_nama;
                $kartuStock->unit_id          = $bhp["unit_id"];
                $kartuStock->depo_id          = $bhp["depo_id"];
                $kartuStock->jml_awal         = 0;    
                $kartuStock->jml_masuk        = $bhp['jumlah'];
                $kartuStock->jml_keluar       = 0;
                $kartuStock->jml_penyesuaian  = 0;
                $kartuStock->jml_akhir        = ($bhp['jml_total'] + $bhp["jumlah"])*1;
                $kartuStock->catatan          = 'Pengembalian stok BHP tindakan pemeriksaan';
                $kartuStock->keterangan       = "Pengembalian stok depo ".$bhp['depo_nama'];
                $kartuStock->client_id        = $this->clientId;
                $kartuStock->created_by       = Auth::user()->username;
                $kartuStock->updated_by       = Auth::user()->username;
                
                $success = $kartuStock->save();
    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data pengembalian item di depo tidak dapat disimpan.']);
                } 
            }

            //DB::connection('dbclient')->commit();
            DB::connection('dbclient')->commit();
            $data = $this->getDataPemeriksaan($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil dibatalkan konfirmasi', 'data' => $data['data']]);

            //return response()->json(['success' => true, 'message' => 'Data berhasil diubah.']);
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengkonfirmasi data. Error: '.$e->getMessage()];
        }
    }
}