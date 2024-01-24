<?php

namespace Modules\PatologiAnatomi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;

use Modules\Pendaftaran\Entities\RegPasien;
use Modules\Pendaftaran\Entities\Pendaftaran;

use Modules\PatologiAnatomi\Entities\TrxPA;
use Modules\PatologiAnatomi\Entities\TrxPAHasil;
use Modules\PatologiAnatomi\Entities\PatologiDetail;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\Harga;
use Modules\MasterData\Entities\HargaDetail;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use RegistrasiHelper;

class PatologiAnatomiController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(request $request)
    {
         try {           
            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $status = '%%';
            if($request->has('status')) {
                if ($status == 'queue') { $status = '%ANTRI%'; }
                else if ($status == 'check') { $status = '%PERIKSA%'; }
                else if ($status == 'results') { $status = '%HASIL%'; }
                else if ($status == 'done') { $status = '%SELESAI%'; }
                else if ($status == 'cancelled') { $status = '%BATAL%'; }
                else { $status = '%%'; }
            }

            $rowNumber = 20;
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = TrxPAHasil::count();
                }
            }
            
            $kolom = "";
            if($request->has('kolom')) {
                $kolom = "rad." . $request->input('kolom');
            }

            $unitID = "%%";
            if ($request->has('unit_id')) {
                $unitID = '%'.$request->input('unit_id').'%';
            }

            // $tgl_pencarian_awal = Carbon\Carbon::now();
            // if($request->has('tgl_pencarian_awal')) {
            //     $tgl_pencarian_awal = $request->input('tgl_pencarian_awal');
            // }    

            // $tgl_pencarian_akhir = Carbon\Carbon::now();
            // if($request->has('tgl_pencarian_akhir')) {
            //     $tgl_pencarian_akhir = $request->input('tgl_pencarian_akhir');
            // } 
            
            $lists = TrxPA::leftJoin('tb_pasien', 'tb_trx_pa.pasien_id', '=', 'tb_pasien.pasien_id')
            ->where([
                ['tb_trx_pa.client_id',$this->clientId],
                ['tb_trx_pa.is_aktif','ILIKE',$aktif],
                ['tb_trx_pa.status','ILIKE',$status],
                //['tb_trx_pa.nama_pasien','ILIKE',$keyword]
            ])
            ->where(function($q) use ($keyword) {
                $q->where('tb_pasien.no_rm','ILIKE',$keyword)
                ->orWhere('tb_trx_pa.nama_pasien','ILIKE',$keyword);
            })
            ->select('tb_trx_pa.*','tb_pasien.no_rm')
            ->orderBy('tb_trx_pa.created_at','ASC')->paginate($rowNumber);
            
            
            foreach($lists as $dt) {
                $dt['transaksi'] = Transaksi::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt->trx_id],
                    ['reg_id',$dt->reg_id],
                ])->first();
                      
                $dt['detail'] = TransaksiDetail::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt['trx_id']],
                    ['reg_id',$dt['reg_id']],
                ])->get(); 
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data patologi anatomi', 'error' => $e->getMessage()]);
        }
    }

    public function listTindakanPA(request $request, $reg_id)
    {
        try {
            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $data = PatologiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$reg_id)
                    ->where('is_aktif',1)
                    ->where('tindakan_nama','ILIKE',$keyword)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->paginate(20);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $trxId)
    {
        try {
            $data = TrxPA::where('client_id', $this->clientId)->where('trx_id', $trxId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi patologi tidak ditemukan.']);
            }

            $pasien = Pasien::where('client_id', $this->clientId)->where('is_aktif',1)->where('pasien_id',$data->pasien_id)->first();
            if($pasien) {
                $data['jns_kelamin'] = $pasien->jns_kelamin;
                $data['no_rm'] = $pasien->no_rm;
                $data['tempat_lahir'] = $pasien->tempat_lahir;
                $data['tgl_lahir'] = $pasien->tgl_lahir;
                $data['nik'] = $pasien->nik;
            }

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id],
                ])->first();
                
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id],
                ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id],['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            /**
             * penyimpanan data registrasi pa
             * JIKA : pendaftaran langsung maka dibuat
             * - Pendaftaran (registrasi)
             * - Patologi Anatomi
             * - Transaksi
             * - Transaksi Detail
             * Jika referensi dari poli / rawat inap :
             * - Patologi Anatomi
             * - Transaksi
             * - Transaksi Detail
             */
 
            $regId          = null;
            $isRujukanInt   = false; 
            $trxId          = RegistrasiHelper::instance()->PaTransactionId($this->clientId); 
            
            //perhatikan bagian ini karena akan menjadi krusial
            $reffTrxId      =  $request->reff_trx_id;
            if($reffTrxId) {
                $pelayanan = Transaksi::where('trx_id',$reffTrxId)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
                if(!$pelayanan) { return response()->json(['success' => false, 'message' => 'Data referensi tidak ditemukan.']); }
                else { $regId = $pelayanan->reg_id; $isRujukanInt = true; }
            } else { 
                $reffTrxId = $trxId;
                $regId =  $trxId; 
            } 

            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //$trxId = $request->trx_id;
            //$isRujukanInt = $request->is_rujukan_int; //perhatikan bagian ini karena akan menjadi krusial

            //check apakah ruang yang dipilih masih aktif dan ada
            // $ruang = RuangPelayanan::where('client_id', $this->clientId)
            //     ->where('is_aktif',1)->where('unit_id', $request->unit_id)
            //     ->where('ruang_id', $request->ruang_id)
            //     ->first();
            // if(!$ruang) {
            //     return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            // }

            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }

            //check penjamin apakah aktif / tidak
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            if(!$penjamin){
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }

            //check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            // $noAntrian = $this->getNoAntrian($request->tgl_diperiksa);
            
            DB::connection('dbclient')->beginTransaction();
          
             /*** simpan data transaksi patologi anatomi*/
            $patologi = new TrxPA();
            $patologi->trx_id               = $trxId;
            $patologi->reg_id               = $regId;
            $patologi->reff_trx_id          = $reffTrxId;
            
            $patologi->tgl_permintaan       = $request->tgl_permintaan;
            $patologi->tgl_dibutuhkan       = $request->tgl_dibutuhkan;
            //$patologi->tgl_diperiksa        = $request->tgl_diperiksa. ' ' .$request->jam_periksa;
            $patologi->tgl_periksa        = $request->tgl_periksa;
            $patologi->jam_periksa        = $request->jam_periksa;
            
            $patologi->tgl_haid_terakhir    = $request->tgl_haid_terakhir;

            $patologi->is_cito              = $request->is_cito;
            $patologi->jenis_cito           = $request->jenis_cito;
            $patologi->cara_pengambilan     = $request->cara_pengambilan;

            $patologi->lokasi_jaringan      = $request->lokasi_jaringan;
            $patologi->media_hasil          = "";
            $patologi->diag_klinis          = $request->diag_klinis;
            $patologi->no_spesimen          = $request->no_spesimen;

            $patologi->pasien_id            = $request->pasien_id;
            $patologi->nama_pasien          = $request->nama_pasien;

            $patologi->dokter_id            = $request->dokter_id;
            $patologi->dokter_nama          = $request->dokter_nama;
            $patologi->buku_harga_id        = $request->buku_harga_id;
            $patologi->buku_harga           = $request->buku_harga;

            $patologi->kelas_harga_id        = $request->kelas_harga_id;
            $patologi->kelas_harga           = $request->kelas_harga;

            $patologi->unit_id              = $request->unit_id;
            $patologi->unit_nama            = $request->unit_nama;

            $patologi->penjamin_id          = $penjamin->penjamin_id;
            $patologi->penjamin_nama        = $penjamin->penjamin_nama;

            $patologi->asal_pasien          = $request->asal_pasien;

            $patologi->status               = 'DAFTAR';
            $patologi->is_aktif             = true;
            $patologi->is_realisasi         = false;
            $patologi->is_bayar             = true;

            $patologi->client_id            = $this->clientId;
            $patologi->created_by           = Auth::user()->username;
            $patologi->updated_by           = Auth::user()->username;

            $success = $patologi->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data patologi anatomi']);
            }

            /**
             * simpan transaksi detail
             */
            $total = 0;
            foreach($request->detail as $dtl) {                
                //check data tindakan
                $paItem = Tindakan::where('tindakan_id',$dtl['item_id'])->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();

                if(!$paItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pemeriksaan patologi anatomi tidak ditemukan.']);
                }

                $hargaItem = Harga::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('is_approve',true)->where('buku_harga_id',$penjamin->buku_harga_id)
                    ->where('tindakan_id',$dtl['item_id'])->where('kelas_id',$request->kelas_harga_id)->first();

                if(!$hargaItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Harga pemeriksaan patologi anatomi tidak ditemukan.',]);
                }

                // //check apakah item ada yang sama
                // $detail = TransaksiDetail::where('client_id',$this->clientId)
                //     ->where('reg_id',$regId)->where('trx_id',$trxId)
                //     ->where('is_aktif',1)
                //     ->where('item_id',$dtl['item_id'])->first();

                /**
                 * create transaksi detail
                 */
                $detailId                   = $this->getPaDetailId();
                $detail                     = new TransaksiDetail();
                $detail->detail_id          = $detailId;
                $detail->reg_id             = $regId;
                $detail->trx_id             = $trxId;
                $detail->item_id            = $paItem->tindakan_id;
                $detail->item_nama          = $paItem->tindakan_nama;
                $detail->satuan             = 'X';
                
                $detail->jumlah             = $dtl['jumlah'];
                $detail->nilai              = $dtl['nilai'];
                $detail->diskon_persen      = $dtl['diskon_persen'];
                $detail->diskon             = $dtl['diskon'];
                $detail->harga_diskon       = $dtl['harga_diskon'];
                $detail->subtotal           = $dtl['subtotal'];
                                
                $detail->jns_transaksi      = 'PATOLOGI ANATOMI';
                $detail->kelas_harga_id     = $request->kelas_harga_id;
                $detail->buku_harga_id      = $penjamin->buku_harga_id;
                $detail->penjamin_id        = $request->penjamin_id;
                
                $detail->dokter_id          = $request->dokter_id;
                $detail->dokter_pengirim_id = $request->dokter_id;

                $detail->is_hitung_adm      = $paItem->is_hitung_admin;
                $detail->group_tagihan      = $paItem->group_tagihan_id;
                $detail->group_eklaim       = $paItem->group_eklaim_id;
                
                $detail->rl_code            = $paItem->rl_code;
                $detail->is_aktif           = true;
                $detail->client_id          = $this->clientId;
                $detail->created_by         = Auth::user()->username;
                $detail->updated_by         = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data item pemeriksaan patologi anatomi gagal disimpan.']);
                }

                /**
                 * simpan komponen
                 */
                foreach($dtl['komponen'] as $komp) {
                    $detailKomp                     = new TransaksiDetailKomp();
                    $detailKomp->komp_detail_id     = Uuid::uuid4()->getHex();
                    $detailKomp->detail_id          = $detailId;
                    $detailKomp->reg_id             = $regId;
                    $detailKomp->trx_id             = $trxId;
                    $detailKomp->harga_id           = $komp['harga_id'];
                    $detailKomp->komp_harga_id      = $komp['komp_harga_id'];
                    $detailKomp->komp_harga         = $komp['komp_harga'];
                    $detailKomp->jumlah             = $komp['jumlah'];
                    $detailKomp->nilai              = $komp['nilai'];
                    $detailKomp->diskon             = $komp['diskon'];
                    $detailKomp->nilai_diskon       = $komp['nilai_diskon'];
                    $detailKomp->subtotal           = $komp['subtotal'];
                    $detailKomp->dokter_id          = $request->dokter_id;
                    $detailKomp->dokter_nama        = $dokter->dokter_nama;
                    $detailKomp->deskripsi          = '';
                    $detailKomp->is_realisasi       = false;
                    $detailKomp->is_bayar           = false;
                    $detailKomp->is_aktif           = true;
                    $detailKomp->is_diskon          = $komp['is_diskon'];
                    $detailKomp->is_ubah_manual     = $komp['is_ubah_manual'];

                    $detailKomp->client_id          = $this->clientId;
                    $detailKomp->created_by         = Auth::user()->username;
                    $detailKomp->updated_by         = Auth::user()->username;
                    
                    $success = $detailKomp->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data detail transaksi radiologi gagal disimpan.']);
                    }   
                }
            }
            /**
             * create data transaksi
             */
            $transaksi                      = new Transaksi();
            $transaksi->reg_id              = $regId;
            $transaksi->trx_id              = $trxId;
            $transaksi->reff_trx_id         = $reffTrxId;
            $transaksi->status              = 'DAFTAR';
            $transaksi->is_realisasi        = false;
            $transaksi->is_bayar            = false;
            $transaksi->is_aktif            = true;
            $transaksi->client_id           = $this->clientId;
            $transaksi->created_by          = Auth::user()->username;
            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->jns_transaksi       = 'PATOLOGI_ANATOMI';
        
            //$transaksi->is_sub_trx            = $isRujukanInt;
            $transaksi->tgl_transaksi       = $request->tgl_periksa .' '. $request->jam_periksa;
            // $transaksi->no_antrian          = $noAntrian;
            $transaksi->no_antrian          = null;
            // $transaksi->no_transaksi        = 'TRX/'.date('Ymd').'/'.$noAntrian;
            $transaksi->no_transaksi        = null;
            $transaksi->kelas_id            = $request->kelas_harga_id;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;

            $transaksi->kelas_penjamin_id   = $request->kelas_penjamin_id;
            $transaksi->penjamin_id         = $request->penjamin_id;
            $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksi->buku_harga          = $penjamin->buku_harga;
            $transaksi->total               = $request->total;
            
            $transaksi->unit_id             = $request->unit_id;
            $transaksi->unit_nama           = $unit->unit_nama;
            $transaksi->ruang_id            = "";
            $transaksi->ruang_nama          = "";
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id  = null;
            $transaksi->dokter_pengirim     = null;
            
            $transaksi->unit_pengirim_id    = $request->unit_pengirim_id;
            $transaksi->unit_pengirim       = $dokter->unit_pengirim;
            
            $transaksi->pasien_id           = $request->pasien_id;
            $transaksi->no_rm               = $pasien->no_rm;
            $transaksi->nama_pasien         = $request->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            DB::connection('dbclient')->commit();

            $data = TrxPA::where([
                ['is_aktif',1],['client_id',$this->clientId],
                ['trx_id',$trxId],
            ])->first();

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],
                ])->first();
                
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],
                ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],
                    ['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;            
            return response()->json(['success' => true, 'message' => 'Data patologi anatomi berhasil disimpan', 'data' => $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan penambahan data patologi anatomi. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $regId = $request->reg_id;
            $trxId = $request->trx_id;
            $isRujukanInt = $request->is_rujukan_int; //perhatikan bagian ini karena akan menjadi krusial

            /**cek data pendaftaran radiologi exist atau tidak */
            $data = TrxPA::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->where('pasien_id',$request->pasien_id)
                ->where('status','DAFTAR')->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi pa tidak ditemukan / sudah berubah status.']);
            }
            
            /**
             * check apakah transaksi exist atau tidak 
             * JIKA tidak nanti akan dibuat. 
             * JIKA exist akan diupdate
             * */
            $transaksi = Transaksi::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('trx_id',$trxId)
                ->first();          


            //check apakah unit yang dipilih masih aktif dan ada
            // $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            // if(!$unit) {
            //     return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            // }

            //check apakah ruang yang dipilih masih aktif dan ada
            // $ruang = RuangPelayanan::where('client_id', $this->clientId)
            //     ->where('is_aktif',1)
            //     ->where('unit_id', $request->unit_id)
            //     ->where('ruang_id', $request->ruang_id)
            //     ->first();
            // if(!$ruang) {
            //     return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            // }

            /**bila rujukan internal check registrasi perujuk 
             * karena untuk rujukan internal data registrasi akan 
             * dipakai bersama
            */
            // if($isRujukanInt) {                
            //     $reg = Pendaftaran::where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)
            //         ->where('pasien_id',$request->pasien_id)
            //         ->first();

            //     if(!$reg) {
            //         return response()->json(['success' => false, 'message' => 'Data referensi pelayanan tidak ditemukan.']);
            //     }
            // }
            
            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }

            /**
             * check penjamin apakah aktif / tidak
             * untuk mengambil buku harga dari penjamin
             * */
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            if(!$penjamin){
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }

            /**
             * check apakah pasien exist
             **/
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            // jika no antrian masih kosong maka dibuat baru
            $noAntrian = $data->no_antrian;
            if($data->tgl_periksa !== $request->tgl_periksa || $noAntrian ==  null) {
                $noAntrian = $this->getNoAntrian($request->tgl_periksa);
            }
            
            DB::connection('dbclient')->beginTransaction();
            // if(!$isRujukanInt) {
            //     $success = Pendaftaran::where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)
            //         ->where('pasien_id',$request->pasien_id)
            //         ->update([
            //             'tgl_diperiksa' => $request->tgl_diperiksa,    
            //             'jns_registrasi' => $request->jns_registrasi,
            //             'cara_registrasi' => $request->cara_registrasi,
            //             'kode_booking' => '-',
            //             'no_antrian' => $noAntrian,                        
            //             'jadwal_id' => null,
            //             'dokter_id' => $request->dokter_id,
            //             'unit_id' => $request->unit_id,
            //             'ruang_id' => $request->ruang_id,
            //             'bed_id' => '-',                                    
            //             'asal_pasien' => $request->asal_pasien,
            //             'ket_asal_pasien' => $request->ket_asal_pasien,            
            //             'pasien_id' => $request->pasien_id,
            //             'jns_penjamin' => $request->jns_penjamin,
            //             'penjamin_id' => $request->penjamin_id,                        
            //             'nama_penanggung' => $request->nama_penanggung,
            //             'hub_penanggung' => $request->hub_penanggung,
            //             'is_bpjs' => $penjamin->is_bpjs,
            //             'is_pasien_baru' => $request->is_pasien_baru,
            //             'updated_by' => Auth::user()->username,            
            //         ]);
                    
            //     if (!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data registrasi']);
            //     }
    
            //     // input tb_registrasi_pasien
            //     $pasienAlamat = PasienAlamat::where('pasien_id',$pasien->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            //     /** hitung usia **/
            //     $tgl_lahir = $pasien->tgl_lahir;
            //     $tgl_lahir = explode("-", $tgl_lahir);
            //     $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
            //         ? ((date("Y") - $tgl_lahir[0]) - 1)
            //         : (date("Y") - $tgl_lahir[0]));
                
            //     $regPasien = RegPasien::where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)
            //         ->where('pasien_id',$request->pasien_id)->first();
                
            //     if($regPasien) {                    
            //         $regPasien->is_pasien_luar = $pasien->is_pasien_luar;
            //         $regPasien->no_rm = $pasien->no_rm;
            //         $regPasien->nama_pasien = $pasien->nama_lengkap;
            //         $regPasien->tempat_lahir = $pasien->tempat_lahir;
            //         $regPasien->tgl_lahir = $pasien->tgl_lahir;
            //         $regPasien->usia = $usia;
            //         $regPasien->jns_kelamin = $pasien->jns_kelamin;
        
            //         $regPasien->propinsi_id = null;
            //         $regPasien->kota_id = null;
            //         $regPasien->kecamatan_id = null;
            //         $regPasien->kelurahan_id = null;
            //         $regPasien->kodepos = null;
        
            //         if($pasienAlamat) {
            //             $regPasien->propinsi_id = $pasienAlamat->propinsi_id;
            //             $regPasien->kota_id = $pasienAlamat->kota_id;
            //             $regPasien->kecamatan_id = $pasienAlamat->kecamatan_id;
            //             $regPasien->kelurahan_id = $pasienAlamat->kelurahan_id;
            //             $regPasien->kodepos = $pasienAlamat->kodepos;
            //         }
        
            //         $regPasien->is_hamil = $request->is_hamil;
            //         $regPasien->is_pasien_baru = $pasien->is_pasien_baru;
            //         $regPasien->is_aktif = true;
            //         $regPasien->client_id = $this->clientId;
            //         $regPasien->created_by = Auth::user()->username;
            //         $regPasien->updated_by = Auth::user()->username;
                    
            //         $success = $regPasien->save();
            //         if(!$success) {
            //             DB::connection('dbclient')->rollBack();
            //             return response()->json(['success' => false, 'message' => 'Data pasien luar gagal disimpan.']);
            //         }
            //     }
            // }
            
            /*** 
             * update data transaksi pa
             * */
            // $data->no_antrian           = $noAntrian;
            $data->tgl_permintaan    = $request->tgl_permintaan;
            $data->tgl_dibutuhkan    = $request->tgl_dibutuhkan;
            $data->tgl_periksa       = $request->tgl_periksa;
            $data->jam_periksa       = $request->jam_periksa;
            $data->tgl_haid_terakhir = $request->tgl_haid_terakhir;
            $data->asal_pasien       = $request->asal_pasien;
            $data->is_cito           = $request->is_cito;
            $data->jenis_cito        = $request->jenis_cito;
            
            $data->lokasi_jaringan      = $request->lokasi_jaringan;
            $data->cara_pengambilan     = $request->cara_pengambilan;
            $data->diag_klinis          = $request->diag_klinis;
            $data->no_spesimen          = $request->no_spesimen;

            $data->kelas_harga_id       = $request->kelas_harga_id;
            $data->kelas_harga          = $request->kelas_harga;

            // $data->diserahkan_oleh      = $request->diserahkan_oleh;
            // $data->penerima_hasil       = $request->penerima_hasil;

            // $data->diagnosa             = $request->diagnosa;            
            // $data->ket_klinis           = $request->ket_klinis;
            // $data->is_cito              = $request->is_cito;
            // $data->jenis_cito           = $request->jenis_cito; 
            // $data->is_multi_hasil       = $request->is_multi_hasil;
            // $data->diserahkan_oleh      = $request->diserahkan_oleh;            
            // $data->penerima_hasil       = $request->penerima_hasil;            
            // $data->hub_penerima         = $request->hub_penerima;

            // //data pasien
            // $data->pasien_id            = $pasien->pasien_id;
            // $data->nama_pasien          = $pasien->nama_lengkap;
            // $data->no_rm                = $pasien->no_rm;
            // $data->tgl_lahir            = $request->tgl_lahir;
            // $data->tempat_lahir         = $request->tempat_lahir;
            // $data->jns_kelamin          = $request->jns_kelamin;            
            // $data->nik                  = $request->nik;
            //dokter rad dan pengirim
            // $data->dokter_id            = $request->dokter_id;
            // $data->dokter_nama          = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            // $data->dokter_pengirim_id   = $request->dokter_pengirim_id;
            // $data->dokter_pengirim      = $request->dokter_pengirim;            
            
            //diisi dokter ekspertise
            // $data->expertise_id     = $request->expertise_id;
            // $data->expertise_nama   = $request->expertise_nama;            
            
            //data unit rad            
            // $data->unit_id          = $request->unit_id;
            // $data->unit_nama        = $unit->unit_nama;
            // $data->ruang_id         = $request->ruang_id;
            // $data->ruang_nama       = $ruang->ruang_nama;
            
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            // $data->unit_asal_id     = $request->unit_asal_id;
            // $data->unit_asal        = $request->unit_asal;
            // $data->ruang_asal_id    = $request->ruang_asal_id;
            // $data->ruang_asal       = $request->ruang_asal;
            // $data->bed_asal_id      = $request->bed_asal_id;
            // $data->no_bed_asal      = $request->no_bed_asal;
            // $data->asal_pasien      = $request->asal_pasien;

            //$data->ket_asal_pasien = $request->ket_asal_pasien;
            $data->reff_trx_id      = $request->reff_trx_id;
            $data->pasien_id        = $request->pasien_id;
            $data->nama_pasien      = $request->nama_pasien;

            $data->dokter_id        = $request->dokter_id;
            $data->dokter_nama      = $request->dokter_nama;
            $data->buku_harga_id    = $request->buku_harga_id;
            $data->buku_harga       = $request->buku_harga;
            $data->kelas_harga_id   = $request->kelas_harga_id;
            $data->kelas_harga      = $request->kelas_harga;

            $data->unit_id          = $request->unit_id;
            $data->unit_nama        = $request->unit_nama;
            $data->dokter_id        = $request->dokter_id;
            $data->dokter_nama      = $dokter->dokter_nama;

            $data->penjamin_id      = $penjamin->penjamin_id;
            $data->penjamin_nama    = $penjamin->penjamin_nama;

            $data->updated_by       = Auth::user()->username;            
            $success                = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi patologi anatomi gagal diubah.']);
            } 

            /**
             * simpan transaksi detail
             */
            $total = 0;
            foreach($request->detail as $dtl) {                
                //check data tindakan
                $radItem = Tindakan::where('tindakan_id',$dtl['item_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->first();

                if(!$radItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pemeriksaan radiologi tidak ditemukan.']);
                }

                $hargaItem = Harga::where([
                        ['client_id',$this->clientId],
                        ['is_aktif',1],
                        ['is_approve',1],
                        ['buku_harga_id',$request->buku_harga_id],
                        ['tindakan_id',$dtl['item_id']],
                        ['kelas_id',$request->kelas_harga_id],
                    ])->select('nilai')->first();

                //check apakah item ada yang sama
                $detail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$regId)->where('trx_id',$trxId)->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();

                if(!$detail) {
                    $detailId = $this->getPaDetailId();
                    $detail             = new TransaksiDetail();
                    $detail->detail_id  = $detailId;
                    $detail->reg_id     = $regId;
                    $detail->trx_id     = $trxId;
                    $detail->item_id    = $radItem->tindakan_id;
                    $detail->client_id  = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }
                else { $detailId = $detail->detail_id; }
                $detail->item_nama = $radItem->tindakan_nama;

                $detail->satuan = 'X';
                
                $detail->jumlah = $dtl['jumlah'];
                $detail->nilai = $dtl['nilai'];
                $detail->diskon_persen = $dtl['diskon_persen'];
                $detail->diskon = $dtl['diskon'];
                $detail->harga_diskon = $dtl['harga_diskon'];
                $detail->subtotal = $dtl['subtotal'];
                
                $detail->jns_transaksi = 'PATOLOGI ANATOMI';
                $detail->kelas_harga_id = $request->kelas_harga_id;
                $detail->buku_harga_id = $request->buku_harga_id;
                $detail->penjamin_id = $request->penjamin_id;
                
                $detail->dokter_id = $request->dokter_id;
                $detail->dokter_pengirim_id = '';//$request->dokter_pengirim_id;

                $detail->is_hitung_adm = $radItem->is_hitung_admin;
                $detail->group_tagihan = $radItem->group_tagihan_id;
                $detail->group_eklaim = $radItem->group_eklaim_id;
                
                $detail->rl_code = $radItem->rl_code;
                $detail->is_aktif = $dtl['is_aktif'];
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data item pemeriksaan radiologi gagal disimpan.']);
                }

                /**
                 * simpan komponen
                 */
                foreach($dtl['komponen'] as $komp) {
                    $detailKomp = TransaksiDetailKomp::where([
                        ['is_aktif',true],
                        ['client_id',$this->clientId],
                        ['harga_id',$komp['harga_id']],
                        ['komp_detail_id',$komp['komp_detail_id']],
                        ['detail_id',$komp['detail_id']],
                        ['reg_id',$regId],
                        ['trx_id',$trxId],                  
                    ])->first();
                    
                    if(!$detailKomp) {
                        $detailKomp = new TransaksiDetailKomp();
                        $detailKomp->komp_detail_id = Uuid::uuid4()->getHex();
                        $detailKomp->detail_id = $detailId;
                        $detailKomp->reg_id = $regId;
                        $detailKomp->trx_id = $trxId;
                        $detailKomp->harga_id = $komp['harga_id'];
                        $detailKomp->client_id = $this->clientId;
                        $detailKomp->created_by = Auth::user()->username;
                        $detailKomp->is_realisasi = false;
                        $detailKomp->is_bayar = false;
                    }
                    $detailKomp->komp_harga_id = $komp['komp_harga_id'];
                    $detailKomp->komp_harga = $komp['komp_harga'];
                    $detailKomp->jumlah = $komp['jumlah'];
                    $detailKomp->nilai = $komp['nilai'];
                    $detailKomp->diskon = $komp['diskon'];
                    $detailKomp->nilai_diskon = $komp['nilai_diskon'];
                    $detailKomp->subtotal = $komp['subtotal'];
                    $detailKomp->dokter_id = $request->dokter_id;
                    $detailKomp->dokter_nama = $dokter->dokter_nama;
                    $detailKomp->deskripsi = '';
                    $detailKomp->is_aktif = $dtl['is_aktif'];
                    $detailKomp->updated_by = Auth::user()->username;
                    
                    $success = $detailKomp->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data detail transaksi radiologi gagal disimpan.']);
                    }   
                }
            }

            /**
             * update atau create data transaksi
             */
            if(!$transaksi) {
                $transaksi = new Transaksi();
                $transaksi->reg_id = $regId;
                $transaksi->trx_id = $trxId;
                $transaksi->status = 'DAFTAR';
                $transaksi->is_realisasi = false;
                $transaksi->is_bayar = false;
                $transaksi->is_aktif = true;
                $transaksi->client_id = $this->clientId;
                $transaksi->created_by = Auth::user()->username;
                $transaksi->updated_by = Auth::user()->username;
                $transaksi->jns_transaksi = 'PATOLOGI ANATOMI';
            }

            //$transaksi->is_sub_trx = $isRujukanInt;
            $transaksi->tgl_transaksi = $request->tgl_periksa .' '. $request->jam_periksa;
            $transaksi->no_antrian = $noAntrian;
            $transaksi->no_transaksi = 'TRX/'.date('Ymd').'/'.$noAntrian;
            $transaksi->kelas_id            = $request->kelas_harga_id;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;
            // $transaksi->kelas_id = $request->kelas_harga_id;
            // $transaksi->kelas_harga_id = $request->kelas_harga_id;

            //$transaksi->kelas_penjamin_id = $request->kelas_penjamin_id;
            $transaksi->penjamin_id = $request->penjamin_id;
            $transaksi->penjamin_nama = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id = $request->buku_harga_id;
            $transaksi->buku_harga = $request->buku_harga;
            $transaksi->total = $request->total;
            
            // $transaksi->unit_id = $request->unit_id;
            // $transaksi->unit_nama = $unit->unit_nama;
            // $transaksi->ruang_id = $request->ruang_id;
            // $transaksi->ruang_nama = $ruang->ruang_nama;
            $transaksi->dokter_id = $request->dokter_id;
            $transaksi->dokter_nama = $request->dokter_nama;

            // $transaksi->dokter_pengirim_id = $request->dokter_pengirim_id;
            // $transaksi->dokter_pengirim = $request->dokter_pengirim; 
            // $transaksi->unit_pengirim_id = $request->unit_pengirim_id;
            // $transaksi->unit_pengirim = $request->unit_pengirim;
            
            $transaksi->pasien_id = $request->pasien_id;
            // $transaksi->no_rm = $pasien->no_rm;
            $transaksi->nama_pasien = $request->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }
            DB::connection('dbclient')->commit();

            $data = TrxPA::where([
                ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId]
            ])->first();

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],['reg_id',$regId],
                ])->first();
                
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId], ['reg_id',$regId],
                ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],['reg_id',$regId],['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;

            return response()->json(['success' => true, 'message' => 'Data radiologi berhasil diubah', 'data' => $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan penambahan data radiologi. Error : ' . $e->getMessage()]);
        }
    }

    public function resultData(Request $request, $TrxId)
    {
        try {
            $data = TrxPA::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$TrxId)->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data patologi anatomi. Error : ' . $e->getMessage()]);
            }
            //ambil data detail transaksi
            $transaksi = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)
                ->first();
            $data['transaksi'] = $transaksi;
            $data['penjamin_id'] = null;
            $data['penjamin_nama'] = null;
            $data['jns_kelamin'] = null;
            $data['tempat_lahir'] = null;
            $data['tgl_lahir'] = null;
            $data['no_rm'] = null;
            
        
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$transaksi->penjamin_id)->first();
            if($penjamin) {
                $data['penjamin_id'] = $transaksi->penjamin_id;
                $data['penjamin_nama'] = $penjamin->penjamin_nama;
            }

            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$data->pasien_id)->first();
            if($pasien) {
                $data['jns_kelamin'] = $pasien->jns_kelamin;
                $data['tempat_lahir'] = $pasien->tempat_lahir;
                $data['tgl_lahir'] = $pasien->tgl_lahir;
                $data['no_rm'] = $pasien->no_rm;
            }
        
            //ambil data detail transaksi
            $details = TransaksiDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)
                ->get();

            $results = []; $i = 0;
           
            foreach($details as $dt) {
                $hasil = TrxPAHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)
                    ->where('tindakan_id',$dt['item_id'])->first();
                
                if(!$hasil) {
                    $hasil['detail_id'] = null;
                    $hasil['reg_id'] = $dt['reg_id'];
                    $hasil['trx_id'] = $dt['trx_id'];
                    $hasil['reff_trx_id'] = $dt['reff_trx_id'];
                    $hasil['tindakan_id'] = $dt['item_id'];
                    $hasil['tindakan_nama'] = $dt['item_nama'];
                    $hasil['hasil'] = null;
                    $hasil['catatan'] = null;
                    $hasil['status'] = null;
                    $hasil['is_aktif'] = true;
                    $hasil['dokter_id'] = $data->dokter_id;
                    $hasil['dokter_nama'] = $data->dokter_nama;                    
                    
                }
                
                $results[$i] = $hasil;
                $i++;
            }
            $data['detail'] = $details;
            $data['hasil'] = $results;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data patologi. Error : ' . $e->getMessage()]);
        }
    }

    public function saveHasilRad(Request $request)
    {
        try {
            $regId =  $request->reg_id;
            $trxId =  $request->trx_id;
            
            // PATOLOGI ANATOMI
            $patologi = TrxPA::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('reg_id', $request->reg_id)->where('trx_id', $request->trx_id)
                        ->first();

            if(!$patologi) {
                return response()->json(['success' => false,'message' => 'data transaksi patologi tidak ditemukan.','error'=>'patologi dengan id tersebut tidak ditemukan.']);
            }
            
            DB::connection('dbclient')->beginTransaction();  

            
            $patologi->tgl_periksa     = $request->tgl_periksa;
            $patologi->jam_periksa     = $request->jam_periksa;
            $patologi->tgl_selesai     = $request->tgl_selesai;
            $patologi->tgl_diserahkan  = $request->tgl_diserahkan;            
            $patologi->diserahkan_oleh = Auth::user()->username;
            $patologi->hub_penerima    = $request->hub_penerima;            
            $patologi->penerima_hasil  = $request->penerima_hasil;            
            $patologi->media_hasil     = $request->media_hasil;            
            $patologi->updated_by      = Auth::user()->username;

            $success = $patologi->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data radiologi']);
            }

            foreach($request->hasil as $res) {
                // HASIL RADIOLOGI SINGLE
                $radHasil = TrxPAHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('reg_id', $request->reg_id)->where('trx_id', $request->trx_id)
                    ->where('tindakan_id', $res['tindakan_id'])
                    ->first();

                //return $radHasil;

                if(!$radHasil) {
                    // CREATE HASIL RADIOLOGI
                    $createHasil = new TrxPAHasil();
                    $createHasil->detail_id       = $this->getHasilPaId();
                    $createHasil->reg_id          = $request->reg_id;
                    $createHasil->trx_id          = $request->trx_id;
                    $createHasil->reff_trx_id     = $request->reff_trx_id;
                    $createHasil->tindakan_id     = $res['tindakan_id'];
                    $createHasil->tindakan_nama   = $res['tindakan_nama'];
                    $createHasil->hasil           = $res['hasil'];
                    $createHasil->catatan         = $res['catatan'];
                    $createHasil->tgl_hasil       = $request->tgl_hasil;
                    
                    $createHasil->dokter_id       = $res['dokter_id'];            
                    $createHasil->dokter_nama     = $res['dokter_nama'];            
                            
                    $createHasil->is_aktif        = True;
                    $createHasil->client_id       = $this->clientId;
                    $createHasil->created_by      = Auth::user()->username;
                    $createHasil->updated_by      = Auth::user()->username;

                    $success = $createHasil->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data hasil patologi']);
                    }
                } 
                else {
                    // UPDATE HASIL RADIOLOGI
                    $radHasil->hasil           = $res['hasil'];
                    $radHasil->catatan         = $res['catatan'];
                    $radHasil->tgl_hasil       = $request->tgl_hasil;
                    $radHasil->dokter_id       = $res['dokter_id'];            
                    $radHasil->dokter_nama     = $res['dokter_nama'];    
                            
                    $radHasil->is_aktif        = True;
                    $radHasil->client_id       = $this->clientId;
                    $radHasil->updated_by      = Auth::user()->username;

                    $success = $radHasil->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam update data hasil patologi']);
                    }
                }

            }
            DB::connection('dbclient')->commit();
            $data = TrxPA::where([
                ['is_aktif',1],['client_id',$this->clientId],
                ['trx_id',$trxId],['reg_id',$regId],
            ])->first();

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],['reg_id',$regId]
                ])->first();
                
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],['reg_id',$regId],
                ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;

            return response()->json(['success' => true, 'message' => 'Data radiologi berhasil diubah', 'data' => $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan pngubahan hasil data radiologi. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $trxId)
    {
        try {
            // DB::connection('dbclient')->beginTransaction();

            // // RADIOLOGI
            // $radiology = Radiologi::where('client_id',$this->clientId)
            //             ->where('is_aktif',1)
            //             ->where('reg_id',  $reg_id)
            //             ->first();
            // if(!$radiology) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data radiologi','error'=>'radiologi dengan id tersebut tidak ditemukan.']);
            // }
            
            // $radiology->status      = 'BATAL';
            // $radiology->is_aktif    = 'false';
            // $radiology->updated_by  = Auth::user()->username;

            // $success = $radiology->save();
            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data radiologi']);
            // }

            // // RADIOLOGI DETAIL
            // $radDetail = RadiologiDetail::where('client_id',$this->clientId)
            //             ->where('is_aktif',1)
            //             ->where('reg_id',  $reg_id)
            //             ->first();
            // if(!$radDetail) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data detail radiologi','error'=>'radiologi dengan id tersebut tidak ditemukan.']);
            // }
            
            // $radDetail->is_aktif   = 'false';
            // $radDetail->updated_by = Auth::user()->username;

            // $success = $radDetail->save();
            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data radiologi']);
            // }

            // // TRANSAKSI
            // $dataTrx = Transaksi::where('is_aktif', 1)->where('client_id', $this->clientId)->where('reg_id', $reg_id)->first();
            // if (!$dataTrx) {
            //     return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']);
            // }

            // $dataTrx->status     = 'BATAL';
            // $dataTrx->is_aktif   = 'false';
            // $dataTrx->updated_by = Auth::user()->username;

            // $success = $dataTrx->save();
            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data transaksi']);
            // }

            // // TRANSAKSI DETAIL
            // $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
            //             ->where('is_aktif',1)
            //             ->where('reg_id',  $reg_id)
            //             ->first();
                        
            // if(!$trxDetail) {
            //     return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data detail transaksi','error'=>'transaksi dengan id tersebut tidak ditemukan.']);
            // }
            
            // $trxDetail->is_aktif    = 'false';
            // $trxDetail->updated_by  = Auth::user()->username;

            // $success = $trxDetail->save();
            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data transaksi']);
            // }

            // // REGISTRASI
            // $dataReg = Pendaftaran::where('is_aktif', 1)->where('client_id', $this->clientId)->where('reg_id', $reg_id)->first();
            // if (!$dataReg) {
            //     return response()->json(['success' => false, 'message' => 'Data registrasi tidak ditemukan.']);
            // }
        
            // $dataReg->status_reg = 'DAFTAR';
            // $dataReg->updated_by = Auth::user()->username;

            // $success = $dataReg->save();
            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data registrasi']);
            // }

            $data = TrxPA::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi patologi anatomi tidak ditemukan.']);
            }
            // if($data->status !== 'DAFTAR' || $data->is_realisasi == true) {
            if($data->status !== 'DAFTAR' || $data->is_realisasi == true) {
                return response()->json(['success' => false, 'message' => 'Data transaksi patologi anatomi sudah berubah status.']);
            }

            /**check data transaksi */
            $transaksi = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)->first();
                
            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi patologi anatomi sudah berubah status.']);
            }

            $detail = TransaksiDetail::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();
                
            $komponen = TransaksiDetailKomp::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();
            
            DB::connection('dbclient')->beginTransaction();

            $success = TrxPA::where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data patologi anatomi gagal dihapus.']);
            }

            // if(!$data->is_rujukan_int) {
            //     $success = Pendaftaran::where('reg_id',$data->reg_id)
            //         ->where('pasien_id',$data->pasien_id)
            //         ->where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status_reg' =>'BATAL']);

            //     if(!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'data registrasi (pendaftaran) radiologi gagal dihapus.']);
            //     }
            // }

            /**update transaksi */
            $success = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data transaksi patologi anatomi gagal dihapus.']);
            }

            /**update transaksi detail */
            if($detail) {
                $success = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data detail transaksi patologi anatomi gagal dihapus.']);
                }
            }
            
            /**update transaksi detail komp */
            if($komponen) {
                $success = TransaksiDetailKomp::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->where('is_realisasi',false)
                    ->where('is_bayar',false)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data detail komponen transaksi patologi anatomi gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();

            return response()->json(['success' => true, 'message' => 'Data patologi anatomi berhasil dihapus']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus data patologi anatomi. Error : ' . $e->getMessage()]);
        }
    }

    // public function getPAId()
    // {
    //     try {
    //         $id = $this->clientId.'-'.'RAD'.'-000001';
    //         $maxId = Patologi::withTrashed()
    //             ->where('client_id', $this->clientId)
    //             ->where('rad_id', 'LIKE', $this->clientId.'-'.'RAD'.'-%')
    //             ->max('rad_id');

    //         if (!$maxId) { $id = $this->clientId.'-'.'RAD'.'-000001'; } 
    //         else {
    //             $maxId = str_replace($this->clientId.'-'.'RAD'.'-', '', $maxId);
    //             $count = $maxId + 1;
    //             if ($count < 10) { $id = $this->clientId.'-'.'RAD'.'-00000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'RAD'.'-0000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'RAD'.'-000' . $count; } 
    //             elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'RAD'.'-00' . $count; } 
    //             elseif ($count >= 10000) { $id = $this->clientId.'-'.'RAD'.'-0' . $count; } 
    //             else { $id = $this->clientId.'-'.'RAD'.'-' . $count; }
    //         }
    //         return $id;
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    // public function getRegId()
    // {
    //     try {
    //         $id = $this->clientId.'-'.date('Ymd').'-REG00001';
    //         $maxId = Pendaftaran::withTrashed()->where('client_id', $this->clientId)->where('reg_id', 'ILIKE', $this->clientId.'-'.date('Ymd').'-REG%')->max('reg_id');
    //         if (!$maxId) {
    //             $id = $this->clientId.'-'.date('Ymd').'-REG00001';
    //         } 
    //         else {
    //             $maxId = str_replace($this->clientId.'-'.date('Ymd').'-REG', '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-REG0000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-REG000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-REG00' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-REG0' . $count; } 
    //             else { $id = $this->clientId.'-'.date('Ymd').'-REG' . $count; }
    //         }
    //         return $id;
    //     } 
    //     catch (\Exception $e) {
    //         return $this->clientId . date('Ymd') . '-REG' . Uuid::uuid4()->getHex();
    //     }
    // }

    public function getTransactionId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-RAD00001';
            $maxId = TrxPA::withTrashed()->where('client_id', $this->clientId)->where('trx_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-PA%')->max('trx_id');
            if (!$maxId) { $id = $this->clientId.'-'.date('Ym').'-PA00001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-PA', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-PA0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-PA000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-PA00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-PA0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-PA' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return $this->clientId . date('Ym') . '-PA' . Uuid::uuid4()->getHex();
        }
    }

    public function getNoAntrian($tglPeriksa)
    {
        try {
            $id = 'PA001';
            
            $maxNo = TrxPA::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('tgl_diperiksa', $tglPeriksa)->max('no_antrian');
                
            if (!$maxNo) { $id = 'PA001'; } 
            else {
                $maxNo = str_replace('PA', '', $maxNo);
                $count = $maxNo + 1;
                if ($count < 10) { $id = 'PA00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'PA0' . $count; } 
                else { $id = 'PA' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function getPaDetailId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-PADTL000001';
            $maxId = TransaksiDetail::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.date('Ym').'-PADTL%')->max('detail_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ym').'-PADTL000001';
            } else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-PADTL', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-PADTL00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-PADTL0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-PADTL000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.date('Ym').'-PADTL00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.date('Ym').'-PADTL0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-PADTL' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getHasilPaId()
    {
        try {
            $id = $this->clientId.'-'.'HSL'.'-000001';
            $maxId = TrxPAHasil::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.'HSL'.'-%')->max('detail_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.'HSL'.'-000001';
            } else {
                $maxId = str_replace($this->clientId.'-'.'HSL'.'-', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.'HSL'.'-00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'HSL'.'-0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'HSL'.'-000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'HSL'.'-00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.'HSL'.'-0' . $count; } 
                else { $id = $this->clientId.'-'.'HSL'.'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * konfirmasi data patologi sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
    public function confirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
                        
            $data = TrxPA::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                // ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi Patologi Anatomi tidak ditemukan / sudah berubah status.']);
            }

            /**check transaksi */
            $transaksi = Transaksi::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)
                ->first();

            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi Patologi Anatomi tidak ditemukan / sudah berubah status.']);
            }

            /**check detail transaksi */
            $detail = TransaksiDetail::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data detail transaksi Patologi Anatomi tidak ditemukan / sudah berubah status.']);
            }


            DB::connection('dbclient')->beginTransaction();
            /**update data trx radiologi */
            $success = TrxPA::where('trx_id',$trxId)->where('reg_id',$regId)
                // ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->update(['status' => 'PERIKSA', 'updated_by' => Auth::user()->username, 'is_realisasi'=>true]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi gagal dikonfirmasi.']);
            } 
            
            // if(!$request->is_rujukan_int){
            //     /**update data pendaftaran */
            //     $success = Pendaftaran::where('reg_id',$regId)
            //         ->where('pasien_id',$data->pasien_id) 
            //         ->where('client_id',$this->clientId)->where('is_aktif',1)
            //         ->update(['status_reg' =>'PERIKSA', 'updated_by' =>Auth::user()->username]);

            //     if(!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'Data registrasi radiologi gagal diupdate.']);
            //     }
            // }             
            /**
             * update data transaksi 
             */
            $success = Transaksi::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)
                ->update(['is_realisasi'=>true, 'updated_by'=>Auth::user()->username, 'status' => 'PERIKSA']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi Patologi Anatomi gagal diupdate.']);
            }

            /**
             * update detail transaksi 
             */
            $success = TransaksiDetail::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_realisasi'=>true, 'updated_by'=>Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi detail Patologi Anatomi gagal diupdate.']);
            }

            /**
             * update detail komponen harga transaksi
             */
            $success = TransaksiDetailKomp::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)
                ->update(['is_realisasi'=>true, 'updated_by'=>Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi detail Patologi Anatomi gagal diupdate.']);
            }
            
            /**
             * create jurnal from komponen
             */

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi transaksi patologi berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengkonfirmasi transaksi patologi. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * konfirmasi data radiologi sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
    public function deleteConfirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
                        
            $data = TrxPA::where('trx_id',$trxId)->where('reg_id',$regId)
                // ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('status','PERIKSA')->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi Patologi Anatomi tidak ditemukan / sudah berubah status.']);
            }

            // if(!$request->is_rujukan_int) {                
            //     $reg = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)->where('pasien_id',$request->pasien_id)
            //         ->first();
            //     if(!$reg) {
            //         return response()->json(['success' => false, 'message' => 'Data referensi pelayanan tidak ditemukan.']);
            //     }
            // }

            /**check transaksi */
            $transaksi = Transaksi::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('is_realisasi',true)->where('is_bayar',false)->first();

            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi Patologi Anatomi tidak ditemukan / sudah berubah status.']);
            }

            /**check detail transaksi */
            $detail = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data detail transaksi Patologi Anatomi tidak ditemukan / sudah berubah status.']);
            }


            DB::connection('dbclient')->beginTransaction();
            /**update data radiologi */
            $success = TrxPA::where('trx_id',$trxId)->where('reg_id',$regId)
                // ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)->where('status','PERIKSA')
                ->update(['status' => 'DAFTAR', 'updated_by' => Auth::user()->username, 'is_realisasi'=>false]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal dibatalkan konfirmasi.']);
            } 
            
            // if(!$request->is_rujukan_int){
            //     /**update data pendaftaran */
            //     $success = Pendaftaran::where('reg_id',$regId)
            //         ->where('pasien_id',$data->pasien_id) 
            //         ->where('client_id',$this->clientId)->where('is_aktif',1)
            //         ->update(['status_reg' =>'DAFTAR', 'updated_by' =>Auth::user()->username]);

            //     if(!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'Data registrasi lab gagal dibatalkan konfirmasi.']);
            //     }
            // }             
            /**
             * update data transaksi 
             */
            $success = Transaksi::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',true)
                ->where('is_bayar',false)
                ->update(['is_realisasi'=>false, 'updated_by'=>Auth::user()->username, 'status' => 'DAFTAR']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal diupdate.']);
            }

            /**
             * update detail transaksi 
             */
            $success = TransaksiDetail::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_realisasi'=>false, 'updated_by'=>Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi detail lab gagal diupdate.']);
            }

            /**
             * update detail komponen harga transaksi
             */
            $success = TransaksiDetailKomp::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',true)
                ->where('is_bayar',false)
                ->update(['is_realisasi'=>false, 'updated_by'=>Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi detail lab gagal diupdate.']);
            }
            
            /**
             * hapus jurnal from komponen
             */

            DB::connection('dbclient')->commit();

            $data = TrxPA::where([['is_aktif',1],['client_id',$this->clientId],['reg_id',$regId],['trx_id',$trxId],])->first();
            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],
                ])->first();
                  
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],
                ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['reg_id',$regId],['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;
            return response()->json(['success' => true, 'message' => 'Konfirmasi transaksi Patologi Anatomi berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus konfirmasi transaksi Patologi Anatomi. Error : ' . $e->getMessage()]);
        }
    }
}
