<?php

namespace Modules\Laboratorium\Http\Controllers;

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
use Modules\Pendaftaran\Entities\RegPenjamin;
use Modules\Pendaftaran\Entities\SEP;
use Modules\Pendaftaran\Entities\RawatInap;
use Modules\Pendaftaran\Entities\PemakaianBed;
use Modules\Pendaftaran\Entities\DpjpLog;
use Modules\Pendaftaran\Entities\PenjaminLog;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\BedInap;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\Harga;
use Modules\MasterData\Entities\HargaDetail;

use Modules\Laboratorium\Entities\TrxLab;
use Modules\Laboratorium\Entities\TrxLabHasil;

use Modules\Pendaftaran\Entities\Pelayanan;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use RegistrasiHelper;


class LabRegistrasiController extends Controller
{
    public $clientId;
    //public $regHelper;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
        //$this->regHelper = new RegistrasiHelper();
    }

    /**
     * Display a listing of the resource.
     */
    public function lists(Request $request)
    {
        try {
            $per_page = 20;
            $aktif = 'true';
            $keyword = '%%';
            $status = '%%';
            $unitID = '%%';
            
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = TrxLab::where('client_id',$this->clientId)->count(); }
            }

            if($request->has('status')) {
                $status = '%'.$request->input('status').'%';
            }

            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            if ($request->has('unit_id')) {
                $unitID = '%'.$request->input('unit_id').'%';
            }

            $list = TrxLab::where([
                    ['client_id',$this->clientId],
                    ['is_aktif','ILIKE',$aktif],
                    ['status','ILIKE',$status],
                    ['unit_id','ILIKE',$unitID]
                ])->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword);
                })->orderBy('created_at','ASC')->paginate($per_page);
            
            foreach($list->items() as $dt) {
                $dt['transaksi'] = Transaksi::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt->trx_id],
                    ['reg_id',$dt->reg_id],
                ])->first();
                      
                $dt['detail'] = TransaksiDetail::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt->trx_id],
                    ['reg_id',$dt->reg_id],
                ])->get(); 
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        /**
         * penyimpanan data registrasi lab        
         * - Pendaftaran
         * - Lab
         * - Detail Lab
         * - transaksi 
         * - Detail transaksi
         */
        try {
            $regId = null;
            $reffTrxId = $request->reff_trx_id;
            $isRujukanInt = false; 
            $trxId = RegistrasiHelper::instance()->LabTransactionId($this->clientId); 
            //perhatikan bagian ini karena akan menjadi krusial
            if($reffTrxId) {
                $pelayanan = Transaksi::where('trx_id',$reffTrxId)->where('client_id',$this->clientId)
                    ->where('is_aktif',1)->first();

                if(!$pelayanan) {
                    return response()->json(['success' => false, 'message' => 'Data referensi tidak ditemukan.']);
                }
                else { $regId = $pelayanan->reg_id; $isRujukanInt = true; }
            }

            else { $regId =  $trxId;  $reffTrxId = $trxId;}
            
            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //check apakah ruang yang dipilih masih aktif dan ada
            $ruang = RuangPelayanan::where('client_id', $this->clientId)
                ->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)
                ->where('ruang_id', $request->ruang_id)
                ->first();
            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            }

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

            $trxId = $this->getTransactionId();   
            if(!$isRujukanInt) {
                $regId = $trxId; 
                $isRujukanInt = false;
            }         
            // input tb_registrasi
            $noAntrian = $this->getNoAntrian($request->tgl_periksa);
            DB::connection('dbclient')->beginTransaction();
            /**tambahkan pendaftaran bila lab adalah transaksi langsung */
            if(!$isRujukanInt) {
                $dataReg = new Pendaftaran();
                $dataReg->reg_id = $regId;
                $dataReg->tgl_registrasi = date('Y-m-d H:i:s');
                $dataReg->tgl_periksa = $request->tgl_periksa;
                $dataReg->jam_periksa = $request->jam_periksa;
    
                $dataReg->jns_registrasi = $request->jns_registrasi;
                $dataReg->cara_registrasi = $request->cara_registrasi;
                $dataReg->kode_booking = '-';
                $dataReg->no_antrian = $noAntrian;
                
                $dataReg->jadwal_id = null;
                $dataReg->dokter_id = $request->dokter_id;
                $dataReg->unit_id = $request->unit_id;
                $dataReg->ruang_id = $request->ruang_id;
                $dataReg->bed_id = '-';
                            
                $dataReg->asal_pasien = $request->asal_pasien;
                $dataReg->ket_asal_pasien = $request->ket_asal_pasien;
    
                $dataReg->pasien_id = $request->pasien_id;
                $dataReg->jns_penjamin = $request->jns_penjamin;
                $dataReg->penjamin_id = $request->penjamin_id;
                
                $dataReg->nama_penanggung = $request->nama_penanggung;
                $dataReg->hub_penanggung = $request->hub_penanggung;
                $dataReg->is_bpjs = $request->is_bpjs;
                $dataReg->is_pasien_baru = false;//$request->is_pasien_baru;
                $dataReg->status_reg = 'DAFTAR';
                $dataReg->is_aktif = true;
    
                $dataReg->client_id = $this->clientId;
                $dataReg->created_by = Auth::user()->username;
                $dataReg->updated_by = Auth::user()->username;
    
                $success = $dataReg->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data registrasi']);
                }
    
                // input tb_registrasi_pasien
                $pasienAlamat = PasienAlamat::where('pasien_id',$pasien->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                /** hitung usia **/
                $tgl_lahir = $pasien->tgl_lahir;
                $tgl_lahir = explode("-", $tgl_lahir);
                $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
                    ? ((date("Y") - $tgl_lahir[0]) - 1)
                    : (date("Y") - $tgl_lahir[0]));
                
                $regPasien = new RegPasien();
                $regPasien->reg_id = $regId;
                $regPasien->is_pasien_luar = $pasien->is_pasien_luar;
                $regPasien->pasien_id = $pasien->pasien_id;
                $regPasien->no_rm = $pasien->no_rm;
                $regPasien->nama_pasien = $pasien->nama_lengkap;
                $regPasien->tempat_lahir = $pasien->tempat_lahir;
                $regPasien->tgl_lahir = $pasien->tgl_lahir;
                $regPasien->usia = $usia;
                $regPasien->jns_kelamin = $pasien->jns_kelamin;
    
                $regPasien->propinsi_id = null;
                $regPasien->kota_id = null;
                $regPasien->kecamatan_id = null;
                $regPasien->kelurahan_id = null;
                $regPasien->kodepos = null;
    
                if($pasienAlamat) {
                    $regPasien->propinsi_id = $pasienAlamat->propinsi_id;
                    $regPasien->kota_id = $pasienAlamat->kota_id;
                    $regPasien->kecamatan_id = $pasienAlamat->kecamatan_id;
                    $regPasien->kelurahan_id = $pasienAlamat->kelurahan_id;
                    $regPasien->kodepos = $pasienAlamat->kodepos;
                }
    
                $regPasien->is_hamil = $request->is_hamil;
                $regPasien->is_pasien_baru = $pasien->is_pasien_baru;
                $regPasien->is_aktif = true;
                $regPasien->client_id = $this->clientId;
                $regPasien->created_by = Auth::user()->username;
                $regPasien->updated_by = Auth::user()->username;
                
                $success = $regPasien->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pasien luar gagal disimpan.']);
                } 
    
            }

            /*** simpan data transaksi lab*/
            $lab                    = new TrxLab();
            $lab->trx_id            = $trxId;
            $lab->reg_id            = $regId;
            $lab->reff_trx_id       = $reffTrxId;
            $lab->no_antrian        = $noAntrian;
            
            $lab->is_rujukan_int    = $isRujukanInt;
            $lab->tgl_transaksi     = date('Y-m-d H:i:s');
            $lab->tgl_periksa       = $request->tgl_periksa;
            $lab->jam_periksa       = $request->jam_periksa;            
            $lab->tgl_diproses      = null;
            $lab->tgl_selesai       = null;
            $lab->tgl_diserahkan    = null;

            //data pasien
            $lab->pasien_id         = $pasien->pasien_id;
            $lab->nama_pasien       = $pasien->nama_lengkap;
            $lab->no_rm             = $pasien->no_rm;
            $lab->tgl_lahir         = $request->tgl_lahir;
            $lab->tempat_lahir      = $request->tempat_lahir;            
            $lab->jns_kelamin       = $request->jns_kelamin;            
            $lab->nik               = $request->nik;            
            
            //dokter lab dan pengirim
            $lab->dokter_id         = $request->dokter_id;
            $lab->dokter_nama       = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $lab->dokter_pengirim_id = $request->dokter_pengirim_id;
            $lab->dokter_pengirim   = $request->dokter_pengirim;
            
            //data unit lab            
            $lab->unit_id           = $request->unit_id;
            $lab->unit_nama         = $unit->unit_nama;
            $lab->ruang_id          = $request->ruang_id;
            $lab->ruang_nama        = $ruang->ruang_nama;

            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $lab->unit_asal_id      = $request->unit_asal_id;
            $lab->unit_asal         = $request->unit_asal;
            $lab->ruang_asal_id     = $request->ruang_asal_id;
            $lab->ruang_asal        = $request->ruang_asal;
            $lab->bed_asal_id       = $request->bed_asal_id;
            $lab->no_bed_asal       = $request->no_bed_asal;

            $lab->asal_pasien       = $request->asal_pasien;
            $lab->penjamin_id       = $request->penjamin_id;
            $lab->penjamin_nama     = $penjamin->penjamin_nama;
            $lab->no_kepesertaan    = $request->no_kepesertaan;
            $lab->jns_penjamin      = $penjamin->jns_penjamin;

            $lab->buku_harga        = $penjamin->buku_harga;
            $lab->buku_harga_id     = $penjamin->buku_harga_id;
            $lab->kelas_harga       = $request->kelas_harga;
            $lab->kelas_harga_id    = $request->kelas_harga_id;
            $lab->kelas_penjamin_id = $request->kelas_harga_id;
            
            $lab->media_hasil       = $request->media_hasil;
            $lab->normal_group      = $request->normal_group;
            $lab->diagnosa          = $request->diagnosa;
            $lab->jenis_sampel      = $request->jenis_sample;
            $lab->no_sampel         = $request->no_sample;
            $lab->diserahkan_oleh   = null;
            $lab->penerima_hasil    = null;
            $lab->hub_penerima      = null;
            $lab->jns_registrasi    = $request->jns_registrasi;
            $lab->cara_registrasi   = $request->cara_registrasi;

            $lab->status            = 'DAFTAR';
            $lab->is_aktif          = true;
            $lab->is_realisasi      = false;
            
            $lab->client_id         = $this->clientId;
            $lab->created_by        = Auth::user()->username;
            $lab->updated_by        = Auth::user()->username;
            
            $success = $lab->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal disimpan.']);
            } 
            /**
             * simpan transaksi detail
             */
            $total = 0;
            foreach($request->detail as $dtl) {                
                //check data tindakan
                $labItem = Tindakan::where('tindakan_id',$dtl['item_id'])->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();

                if(!$labItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pemeriksaan lab tidak ditemukan.']);
                }

                $hargaItem = Harga::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('is_approve',true)->where('buku_harga_id',$penjamin->buku_harga_id)
                    ->where('tindakan_id',$dtl['item_id'])->where('kelas_id',$request->kelas_harga_id)->first();

                if(!$hargaItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Harga pemeriksaan lab tidak ditemukan.',]);
                }

                //check apakah item ada yang sama
                $detail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$regId)
                    ->where('trx_id',$trxId)->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();

                /**
                 * create transaksi detail
                 */
                $detailId = $trxId.Uuid::uuid4()->getHex();
                $detail = new TransaksiDetail();
                $detail->detail_id = $detailId;
                $detail->reg_id = $regId;
                $detail->trx_id = $trxId;
                $detail->item_id = $labItem->tindakan_id;
                $detail->item_nama = $labItem->tindakan_nama;
                $detail->satuan = 'X';
                
                $detail->jumlah = $dtl['jumlah'];
                $detail->nilai = $dtl['nilai'];
                $detail->diskon_persen = $dtl['diskon_persen'];
                $detail->diskon = $dtl['diskon'];
                $detail->harga_diskon = $dtl['harga_diskon'];
                $detail->subtotal = $dtl['subtotal'];
                                
                $detail->jns_transaksi = 'LAB';
                $detail->kelas_harga_id = $request->kelas_harga_id;
                $detail->buku_harga_id = $penjamin->buku_harga_id;
                $detail->penjamin_id = $request->penjamin_id;
                
                $detail->dokter_id = $request->dokter_id;
                if($request->dokter_pengirim_id) {
                    $detail->dokter_pengirim_id = $request->dokter_pengirim_id;
                }
                else {
                    $detail->dokter_pengirim_id = $request->dokter_id;
                }
                
                $detail->is_hitung_adm = $labItem->is_hitung_admin;
                $detail->group_tagihan = $labItem->group_tagihan_id;
                $detail->group_eklaim = $labItem->group_eklaim_id;
                
                $detail->rl_code = $labItem->rl_code;
                $detail->is_aktif = true;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data item pemeriksaan lab gagal disimpan.']);
                }

                /**
                 * simpan komponen
                 */
                foreach($dtl['komponen'] as $komp) {
                    $detailKomp = new TransaksiDetailKomp();
                    $detailKomp->komp_detail_id = Uuid::uuid4()->getHex();
                    $detailKomp->detail_id = $detailId;
                    $detailKomp->reg_id = $regId;
                    $detailKomp->trx_id = $trxId;
                    $detailKomp->harga_id = $komp['harga_id'];
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
                    $detailKomp->is_realisasi = false;
                    $detailKomp->is_bayar = false;
                    $detailKomp->is_aktif = true;
                    $detailKomp->is_diskon = $komp['is_diskon'];
                    $detailKomp->is_ubah_manual = $komp['is_ubah_manual'];

                    $detailKomp->client_id = $this->clientId;
                    $detailKomp->created_by = Auth::user()->username;
                    $detailKomp->updated_by = Auth::user()->username;
                    
                    $success = $detailKomp->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Data detail transaksi lab gagal disimpan.']);
                    }   
                }
            }
            /**
             * create data transaksi
             */
            $transaksi                      = new Transaksi();
            $transaksi->trx_id              = $trxId;
            $transaksi->reg_id              = $regId;
            $transaksi->reff_trx_id         = $reffTrxId;
            $transaksi->status              = 'DAFTAR';
            $transaksi->is_realisasi        = false;
            $transaksi->is_bayar            = false;
            $transaksi->is_aktif            = true;
            $transaksi->client_id           = $this->clientId;
            $transaksi->created_by          = Auth::user()->username;
            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->jns_transaksi       = 'LABORATORIUM';
        
            //$transaksi->is_sub_trx          = $isRujukanInt;
            $transaksi->tgl_transaksi       = $request->tgl_periksa .':'. $request->jam_periksa;
            $transaksi->no_antrian          = $noAntrian;
            $transaksi->no_transaksi        = 'TRX/'.date('Ymd').'/'.$noAntrian;
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
            $transaksi->ruang_id            = $request->ruang_id;
            $transaksi->ruang_nama          = $ruang->ruang_nama;
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = $dokter->dokter_pengirim;
            
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

            $data = TrxLab::where('is_aktif',1)->where('client_id',$this->clientId)->where('trx_id',$trxId)->first();

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                ])->first();
                  
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$trxId],
                    ['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;

            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan registrasi. Error : ' . $e->getMessage()]);
        }
    }

    public function getNoAntrian($tglPeriksa)
    {
        try {
            $id = 'LAB001';
            $maxNo = TrxLab::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('tgl_periksa', $tglPeriksa)->max('no_antrian');

            if (!$maxNo) { $id = 'LAB001'; } 
            else {
                $maxNo = str_replace('LAB', '', $maxNo);
                $count = $maxNo + 1;
                if ($count < 10) { $id = 'LAB00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'LAB0' . $count; } 
                else { $id = 'LAB' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return Uuid::uuid4()->getHex();
        }
    }

    public function update(Request $request) {
        /**
         *update  data registrasi lab
         * JIKA : pendaftaran langsung maka dibuat 3 data
         * - Pendaftaran
         * - Lab
         * - Detail Lab
         * Jika referensi dari poli / rawat inap :
         * - Lab 
         * - Lab Detail
         */
        try {
            $regId      = $request->reg_id;
            $trxId      = $request->trx_id;
            $reffTrxId   = $request->reff_trx_id;
            $isRujukanInt = $request->is_rujukan_int; //perhatikan bagian ini karena akan menjadi krusial
            /**cek data pendaftaran lab exist atau tidak */
            $data = TrxLab::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('status','DAFTAR')->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi lab tidak ditemukan / sudah berubah status.']);
            }
            
            /**
             * check apakah transaksi exist atau tidak 
             * JIKA tidak nanti akan dibuat. 
             * JIKA exist akan diupdate
             * */
            $transaksi = Transaksi::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->first();          


            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //check apakah ruang yang dipilih masih aktif dan ada
            $ruang = RuangPelayanan::where('client_id', $this->clientId)
                ->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)
                ->where('ruang_id', $request->ruang_id)
                ->first();
            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            }
            
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
            
            /*** 
             * update data transaksi lab
             * */
            $data->no_antrian           = $noAntrian;
            $data->is_rujukan_int       = $isRujukanInt;
            $data->tgl_periksa          = $request->tgl_periksa;
            $data->jam_periksa          = $request->jam_periksa;            
            $data->tgl_diproses         = null;
            $data->tgl_selesai          = null;
            $data->tgl_diserahkan       = null;
            //data pasien
            $data->pasien_id            = $pasien->pasien_id;
            $data->nama_pasien          = $pasien->nama_lengkap;
            $data->no_rm                = $pasien->no_rm;
            $data->tgl_lahir            = $request->tgl_lahir;
            $data->tempat_lahir         = $request->tempat_lahir;
            $data->jns_kelamin          = $request->jns_kelamin;            
            $data->nik                  = $request->nik;
            //dokter lab dan pengirim
            $data->dokter_id            = $request->dokter_id;
            $data->dokter_nama          = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $data->dokter_pengirim_id   = $request->dokter_pengirim_id;
            $data->dokter_pengirim      = $request->dokter_pengirim;            
            //data unit lab            
            $data->unit_id              = $request->unit_id;
            $data->unit_nama            = $unit->unit_nama;
            $data->ruang_id             = $request->ruang_id;
            $data->ruang_nama           = $ruang->ruang_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $data->unit_asal_id         = $request->unit_asal_id;
            $data->unit_asal            = $request->unit_asal;
            $data->ruang_asal_id        = $request->ruang_asal_id;
            $data->ruang_asal           = $request->ruang_asal;
            $data->bed_asal_id          = $request->bed_asal_id;
            $data->no_bed_asal          = $request->no_bed_asal;
            $data->asal_pasien          = $request->asal_pasien;
            
            //$data->ket_asal_pasien = $request->ket_asal_pasien;
            $data->reff_trx_id          = $request->reff_trx_id;
            $data->penjamin_id          = $request->penjamin_id;
            $data->penjamin_nama        = $penjamin->penjamin_nama;
            $data->no_kepesertaan       = $request->no_kepesertaan;
            $data->jns_penjamin         = $penjamin->jns_penjamin;

            $data->buku_harga_id        = $penjamin->buku_harga_id;
            $data->buku_harga           = $penjamin->buku_harga;
            $data->kelas_harga_id       = $request->kelas_harga_id;
            $data->kelas_harga          = $request->kelas_harga;
            
            $data->media_hasil          = $request->media_hasil;
            $data->normal_group         = $request->normal_group;
            $data->diagnosa             = $request->diagnosa;
            $data->jenis_sampel         = $request->jenis_sample;
            $data->no_sampel            = $request->no_sample;
            $data->diserahkan_oleh      = null;
            $data->penerima_hasil       = null;
            $data->hub_penerima         = null;
            $data->jns_registrasi       = $request->jns_registrasi;
            $data->cara_registrasi      = $request->cara_registrasi;
            $data->is_realisasi         = false;
            $data->updated_by           = Auth::user()->username;            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal diubah.']);
            } 

            /**
             * simpan transaksi detail
             */
            $total = 0;
            foreach($request->detail as $dtl) {                
                //check data tindakan
                $labItem = Tindakan::where('tindakan_id',$dtl['item_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->first();

                if(!$labItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pemeriksaan lab tidak ditemukan.']);
                }

                $hargaItem = Harga::where([
                        ['client_id',$this->clientId],
                        ['is_aktif',1],
                        ['is_approve',1],
                        ['buku_harga_id',$penjamin->buku_harga_id],
                        ['tindakan_id',$dtl['item_id']],
                        ['kelas_id',$request->kelas_harga_id],
                    ])->select('nilai')->first();
                
                //check apakah item ada yang sama
                $detail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$regId)
                    ->where('trx_id',$trxId)->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();
                

                if(!$detail) {
                    $detailId = $this->getDetailId();

                    $detail = new TransaksiDetail();
                    $detail->detail_id = $detailId;
                    $detail->reg_id = $regId;
                    $detail->trx_id = $trxId;
                    $detail->item_id = $labItem->tindakan_id;
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }
                else {
                    $detailId = $detail->detail_id;
                }

                $detail->item_nama = $labItem->tindakan_nama;
                $detail->satuan = 'X';
                
                $detail->jumlah = $dtl['jumlah'];
                $detail->nilai = $dtl['nilai'];
                $detail->diskon_persen = $dtl['diskon_persen'];
                $detail->diskon = $dtl['diskon'];
                $detail->harga_diskon = $dtl['harga_diskon'];
                $detail->subtotal = $dtl['subtotal'];
                
                
                $detail->jns_transaksi = 'LAB';
                $detail->kelas_harga_id = $request->kelas_harga_id;
                $detail->buku_harga_id = $penjamin->buku_harga_id;
                $detail->penjamin_id = $request->penjamin_id;
                
                $detail->dokter_id = $request->dokter_id;
                $detail->dokter_pengirim_id = $request->dokter_pengirim_id;

                $detail->is_hitung_adm = $labItem->is_hitung_admin;
                $detail->group_tagihan = $labItem->group_tagihan_id;
                $detail->group_eklaim = $labItem->group_eklaim_id;
                
                $detail->rl_code = $labItem->rl_code;
                $detail->is_aktif = $dtl['is_aktif'];
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data item pemeriksaan lab gagal disimpan.']);
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
                        return response()->json(['success' => false, 'message' => 'Data detail transaksi lab gagal disimpan.']);
                    }   
                }
            }

            /**
             * update atau create data transaksi
             */
            if(!$transaksi) {
                $transaksi                  = new Transaksi();
                $transaksi->reg_id          = $regId;
                $transaksi->trx_id          = $trxId;
                $transaksi->reff_trx_id     = $request->reff_trx_id;
                $transaksi->status          = 'DAFTAR';
                $transaksi->is_realisasi    = false;
                $transaksi->is_bayar        = false;
                $transaksi->is_aktif        = true;
                $transaksi->client_id       = $this->clientId;
                $transaksi->created_by      = Auth::user()->username;
                $transaksi->updated_by      = Auth::user()->username;
                $transaksi->jns_transaksi   = 'LAB';
            }

            //$transaksi->is_sub_trx          = $isRujukanInt;
            $transaksi->tgl_transaksi       = $data->tgl_periksa .':'. $data->jam_periksa;
            $transaksi->no_antrian          = $noAntrian;
            $transaksi->no_transaksi        = 'TRX/'.date('Ymd').'/'.$noAntrian;
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
            $transaksi->ruang_id            = $request->ruang_id;
            $transaksi->ruang_nama          = $ruang->ruang_nama;
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;

            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = $request->dokter_pengirim; 
            $transaksi->unit_pengirim_id    = $request->unit_pengirim_id;
            $transaksi->unit_pengirim       = $request->unit_pengirim;
            
            $transaksi->pasien_id           = $request->pasien_id;
            $transaksi->no_rm               = $pasien->no_rm;
            $transaksi->nama_pasien         = $request->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            DB::connection('dbclient')->commit();

            $data = TrxLab::where([['is_aktif',1],['client_id',$this->clientId],['reg_id',$regId],['trx_id',$trxId]])->first();
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
            
            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil diubah', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan ubah registrasi. Error : ' . $e->getMessage()]);
        }
    }


    public function getDetailId()
    {
        try {
            $id = $this->clientId.'-'.'DTL'.'-000001';
            $maxId = TransaksiDetail::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.'DTL'.'-%')->max('detail_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.'DTL'.'-000001';
            } else {
                $maxId = str_replace($this->clientId.'-'.'DTL'.'-', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.'DTL'.'-00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'DTL'.'-0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'DTL'.'-000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'DTL'.'-00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.'DTL'.'-0' . $count; } 
                else { $id = $this->clientId.'-'.'DTL'.'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . date('Ymd') . '-' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $TrxId)
    {
        try {
            $data = TrxLab::where([['client_id',$this->clientId],['trx_id',$TrxId]])->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi registrasi lab tidak ditemukan.']);
            } 

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id]])->first();
                  
            $details = TransaksiDetail::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id]])->get(); 
            
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
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * konfirmasi data lab sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
    public function confirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
                        
            $data = TrxLab::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi lab tidak ditemukan / sudah berubah status.']);
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
                return response()->json(['success' => false, 'message' => 'Data transaksi lab tidak ditemukan / sudah berubah status.']);
            }

            /**check detail transaksi */
            $detail = TransaksiDetail::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data detail transaksi lab tidak ditemukan / sudah berubah status.']);
            }

            /**
             * check pendaftaran exist / tidak
             */
            $regLab = Pendaftaran::where('reg_id',$trxId)->where('is_aktif',1)->first();


            DB::connection('dbclient')->beginTransaction();
            /**update data trx lab */
            $success = TrxLab::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->update(['status' => 'PERIKSA', 'updated_by' => Auth::user()->username, 'is_realisasi'=>true]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal dikonfirmasi.']);
            } 
            
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
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal diupdate.']);
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
                return response()->json(['success' => false, 'message' => 'Data transaksi detail lab gagal diupdate.']);
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
                return response()->json(['success' => false, 'message' => 'Data transaksi detail lab gagal diupdate.']);
            }

            /**update data trx lab */
            if($regLab) {
                $success = Pendaftaran::where('reg_id',$trxId)
                    ->where('pasien_id',$request->pasien_id)
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->update(['status_reg' => 'PERIKSA', 'updated_by' => Auth::user()->username]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data ppendaftaran lab gagal dikonfirmasi.']);
                }
            }
            
            /**
             * create jurnal from komponen
             */

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi transaksi lab berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengkonfirmasi transaksi lab. Error : ' . $e->getMessage()]);
        }
    }
    
    /**batalkan registrasi lab */
    public function delete(Request $request, $TrxId) {
        try {
            $data = TrxLab::where('trx_id',$TrxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi lab tidak ditemukan.']);
            }
            if($data->status !== 'DAFTAR' || $data->is_realisasi == true) {
                return response()->json(['success' => false, 'message' => 'Data transaksi lab sudah berubah status.']);
            }

            /**check data transaksi */
            $transaksi = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)->first();
                
            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi lab sudah berubah status.']);
            }

            $regLab = Pendaftaran::where('reg_id',$data->trx_id)->where('is_aktif',1)->first();

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

            $success = TrxLab::where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data rawat inap gagal dihapus.']);
            }

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
                return response()->json(['success' => false, 'message' => 'data transaksi lab gagal dihapus.']);
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
                    return response()->json(['success' => false, 'message' => 'data detail transaksi lab gagal dihapus.']);
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
                    return response()->json(['success' => false, 'message' => 'data detail komponen transaksi lab gagal dihapus.']);
                }
            }

            /**update transaksi detail komp */
            if($regLab) {
                $success = Pendaftaran::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->trx_id)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status_reg'=>'BATAL']);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data pendaftaran lab gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'data transaksi Lab berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus data transaksi lab. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * konfirmasi data lab sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
    public function deleteConfirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
                        
            $data = TrxLab::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','PERIKSA')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi lab tidak ditemukan / sudah berubah status.']);
            }

            $regLab = Pendaftaran::where('reg_id',$trxId)->where('is_aktif',1)->first();

            // if(!$request->is_rujukan_int) {                
            //     $reg = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)->where('pasien_id',$request->pasien_id)
            //         ->first();
            //     if(!$reg) {
            //         return response()->json(['success' => false, 'message' => 'Data referensi pelayanan tidak ditemukan.']);
            //     }
            // }

            /**check transaksi */
            $transaksi = Transaksi::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',true)
                ->where('is_bayar',false)
                ->first();

            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi lab tidak ditemukan / sudah berubah status.']);
            }

            /**check detail transaksi */
            $detail = TransaksiDetail::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data detail transaksi lab tidak ditemukan / sudah berubah status.']);
            }


            DB::connection('dbclient')->beginTransaction();
            /**update data rawat inap */
            $success = TrxLab::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','PERIKSA')
                ->update(['status' => 'DAFTAR', 'updated_by' => Auth::user()->username, 'is_realisasi'=>false]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal dibatalkan konfirmasi.']);
            } 
            
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

            if($regLab) {
                $success = Pendaftaran::where('reg_id',$trxId)
                    ->where('pasien_id',$request->pasien_id)
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->update(['updated_by'=>Auth::user()->username, 'status_reg' => 'DAFTAR']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal diupdate.']);
                }
            }
            
            /**
             * hapus jurnal from komponen
             */

            DB::connection('dbclient')->commit();

            $data = TrxLab::where([['is_aktif',1],['client_id',$this->clientId],['reg_id',$regId],['trx_id',$trxId]])->first();
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
            return response()->json(['success' => true, 'message' => 'Konfirmasi transaksi lab berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus konfirmasi transaksi lab. Error : ' . $e->getMessage()]);
        }
    }

    public function getTransactionId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-LAB00001';
            $maxId = TrxLab::withTrashed()->where('client_id', $this->clientId)->where('trx_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-LAB%')->max('trx_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ym').'-LAB00001';
            } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-LAB', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-LAB0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-LAB000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-LAB00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-LAB0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-LAB' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }
}
