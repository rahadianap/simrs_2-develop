<?php

namespace Modules\Radiologi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;

use Modules\Radiologi\Entities\TrxRad;
use Modules\Radiologi\Entities\TrxRadHasil;

use Modules\Radiologi\Entities\RadiologiDetail;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\Pendaftaran\Entities\RegPasien;
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
use Modules\Pendaftaran\Entities\Pendaftaran;

use RegistrasiHelper;

class RadiologiController extends Controller
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
                    $rowNumber = TrxRad::count();
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

            $tgl_pencarian_awal = Carbon\Carbon::now();
            if($request->has('tgl_pencarian_awal')) {
                $tgl_pencarian_awal = $request->input('tgl_pencarian_awal');
            }    

            $tgl_pencarian_akhir = Carbon\Carbon::now();
            if($request->has('tgl_pencarian_akhir')) {
                $tgl_pencarian_akhir = $request->input('tgl_pencarian_akhir');
            } 
            $lists = TrxRad::where([
                ['client_id',$this->clientId],
                ['is_aktif','ILIKE',$aktif],
                ['status','ILIKE',$status],
                ['unit_id','ILIKE',$unitID]
            ])->where(function($q) use ($keyword) {
                $q->where('no_rm','ILIKE',$keyword)
                ->orWhere('nama_pasien','ILIKE',$keyword);
            })->orderBy('created_at','ASC')->paginate($rowNumber);
            
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
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data radiologi', 'error' => $e->getMessage()]);
        }
    }

    public function listTindakanRad(request $request, $reg_id)
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

            $data = RadiologiDetail::where('client_id',$this->clientId)
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
            $data = TrxRad::where('client_id', $this->clientId)
                ->where('trx_id', $trxId)
                ->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi tidak ditemukan.']);
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
             * penyimpanan data regitrasi patologi berdasarkan radologi
             * penyimpanan data registrasi rad
             * JIKA : pendaftaran langsung maka dibuat
             * - Pendaftaran (registrasi)
             * - Radiologi
             * - Transaksi
             * - Transaksi Detail
             * Jika referensi dari poli / rawat inap :
             * - Radiologi
             * - Transaksi
             * - Transaksi Detail
             */
 
            $regId = null;
            $reffTrxId = $request->reff_trx_id;
            //return response()->json(['success' => false, 'message' => 'is rujukan internal: '.$request->is_rujukan_int]);

            $isRujukanInt = false; 
            $trxId = RegistrasiHelper::instance()->RadTransactionId($this->clientId); 
            
            //perhatikan bagian ini karena akan menjadi krusial
            if($reffTrxId) {
                $pelayanan = Transaksi::where('trx_id',$reffTrxId)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
                if(!$pelayanan) { return response()->json(['success' => false, 'message' => 'Data referensi tidak ditemukan.']); }
                else { $regId = $pelayanan->reg_id; $isRujukanInt = true; }
            }
            else { $regId =  $trxId; $reffTrxId = $trxId; }

            //$trxId = $request->trx_id;
            //$isRujukanInt = $request->is_rujukan_int; //perhatikan bagian ini karena akan menjadi krusial

            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //check apakah ruang yang dipilih masih aktif dan ada
            $ruang = RuangPelayanan::where('client_id', $this->clientId)
                ->where('is_aktif',1)->where('unit_id', $request->unit_id)
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
            
            // $trxId = $this->getTransactionId();
            // if(!$isRujukanInt) {
            //     $regId = $trxId;
            //     $isRujukanInt = false;
            // }
            // input tb_registrasi
            $noAntrian = $this->getNoAntrian($request->tgl_periksa);
            DB::connection('dbclient')->beginTransaction();
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
             /*** simpan data transaksi radiologi*/
            $radiology = new TrxRad();
            $radiology->reg_id          = $regId;
            $radiology->trx_id          = $trxId;
            $radiology->reff_trx_id     = $reffTrxId;
            // $radiology->sub_trx_id      = $reffTrxId;
            $radiology->no_antrian      = $noAntrian;
            $radiology->is_rujukan_int  = $isRujukanInt;
            //data pasien
            $radiology->pasien_id       = $pasien->pasien_id;
            $radiology->nama_pasien     = $pasien->nama_lengkap;
            $radiology->no_rm           = $pasien->no_rm;
            $radiology->tgl_lahir       = $request->tgl_lahir;
            $radiology->tempat_lahir    = $request->tempat_lahir;            
            $radiology->jns_kelamin     = $request->jns_kelamin;            
            $radiology->nik             = $request->nik;            

            //dokter radiologi
            $radiology->dokter_id       = $request->dokter_id;
            $radiology->dokter_nama     = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $radiology->dokter_pengirim_id = $request->dokter_pengirim_id;
            $radiology->dokter_pengirim = $request->dokter_pengirim;

            //data unit rad            
            $radiology->unit_id         = $request->unit_id;
            $radiology->unit_nama       = $unit->unit_nama;
            $radiology->ruang_id        = $request->ruang_id;
            $radiology->ruang_nama      = $ruang->ruang_nama;

            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $radiology->unit_asal_id    = $request->unit_asal_id;
            $radiology->unit_asal       = $request->unit_asal;
            $radiology->ruang_asal_id   = $request->ruang_asal_id;
            $radiology->ruang_asal      = $request->ruang_asal;
            $radiology->bed_asal_id     = $request->bed_asal_id;
            $radiology->no_bed_asal     = $request->no_bed_asal;

            $radiology->asal_pasien     = $request->asal_pasien;
            //$radiology->reff_trx_id     = $reffTrxId;

            $radiology->penjamin_id     = $request->penjamin_id;
            $radiology->penjamin_nama   = $penjamin->penjamin_nama;
            $radiology->no_kepesertaan  = $request->no_kepesertaan;
            $radiology->jns_penjamin    = $penjamin->jns_penjamin;
            $radiology->buku_harga_id   = $penjamin->buku_harga_id;
            $radiology->buku_harga      = $penjamin->buku_harga;

            $radiology->kelas_harga_id  = $request->kelas_harga_id;
            $radiology->kelas_harga     = $request->kelas_harga;
            $radiology->kelas_penjamin_id  = $request->kelas_harga_id;
            
            $radiology->media_hasil     = $request->media_hasil;

            $radiology->tgl_permintaan  = $request->tgl_transaksi.' '.$request->jam_transaksi;
            $radiology->tgl_periksa     = $request->tgl_periksa;
            $radiology->jam_periksa     = $request->jam_periksa;
            $radiology->diagnosa        = $request->diagnosa;            
            $radiology->ket_klinis      = $request->ket_klinis;
            $radiology->is_cito         = $request->is_cito;
            $radiology->jenis_cito      = $request->jenis_cito; 
            $radiology->is_multi_hasil  = $request->is_multi_hasil;
            $radiology->diserahkan_oleh = $request->diserahkan_oleh;            
            $radiology->penerima_hasil  = $request->penerima_hasil;            
            $radiology->hub_penerima    = $request->hub_penerima;

            $radiology->status          = 'DAFTAR';
            $radiology->is_aktif        = true;

            $radiology->client_id       = $this->clientId;
            $radiology->created_by      = Auth::user()->username;
            $radiology->updated_by      = Auth::user()->username;

            $success = $radiology->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data radiologi']);
            }

            /**
             * simpan transaksi detail
             */
            $total = 0;
            foreach($request->detail as $dtl) {                
                //check data tindakan
                $radItem = Tindakan::where('tindakan_id',$dtl['item_id'])->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();

                if(!$radItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pemeriksaan radiologi tidak ditemukan.']);
                }

                $hargaItem = Harga::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('is_approve',true)->where('buku_harga_id',$penjamin->buku_harga_id)
                    ->where('tindakan_id',$dtl['item_id'])->where('kelas_id',$request->kelas_harga_id)->first();

                if(!$hargaItem) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Harga pemeriksaan radiologi tidak ditemukan.',]);
                }

                //check apakah item ada yang sama
                $detail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$regId)->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();

                /**
                 * create transaksi detail
                 */
                $detailId = $this->getRadDetailId();
                $detail = new TransaksiDetail();
                $detail->detail_id = $detailId;
                $detail->reg_id = $regId;
                $detail->trx_id = $trxId;
                $detail->item_id = $radItem->tindakan_id;
                $detail->item_nama = $radItem->tindakan_nama;
                $detail->satuan = 'X';
                
                $detail->jumlah = $dtl['jumlah'];
                $detail->nilai = $dtl['nilai'];
                $detail->diskon_persen = $dtl['diskon_persen'];
                $detail->diskon = $dtl['diskon'];
                $detail->harga_diskon = $dtl['harga_diskon'];
                $detail->subtotal = $dtl['subtotal'];
                                
                $detail->jns_transaksi = 'RADIOLOGI';
                $detail->kelas_harga_id = $request->kelas_harga_id;
                $detail->buku_harga_id = $penjamin->buku_harga_id;
                $detail->penjamin_id = $request->penjamin_id;
                
                $detail->dokter_id = $request->dokter_id;
                $detail->dokter_pengirim_id = $request->dokter_pengirim_id;

                $detail->is_hitung_adm = $radItem->is_hitung_admin;
                $detail->group_tagihan = $radItem->group_tagihan_id;
                $detail->group_eklaim = $radItem->group_eklaim_id;
                
                $detail->rl_code = $radItem->rl_code;
                $detail->is_aktif = true;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
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
                        return response()->json(['success' => false, 'message' => 'Data detail transaksi radiologi gagal disimpan.']);
                    }   
                }
            }
            /**
             * create data transaksi
             */
            $transaksi = new Transaksi();
            $transaksi->reg_id = $regId;
            $transaksi->trx_id = $trxId;
            $transaksi->reff_trx_id = $reffTrxId;
            // $transaksi->sub_trx_id = $reffTrxId;
            $transaksi->status = 'DAFTAR';
            $transaksi->is_realisasi = false;
            $transaksi->is_bayar = false;
            $transaksi->is_aktif = true;
            $transaksi->client_id = $this->clientId;
            $transaksi->created_by = Auth::user()->username;
            $transaksi->updated_by = Auth::user()->username;
            $transaksi->jns_transaksi = 'RADIOLOGI';
        
            //$transaksi->is_sub_trx = $isRujukanInt;
            $transaksi->tgl_transaksi = $request->tgl_periksa .':'. $request->jam_periksa;
            $transaksi->no_antrian = $noAntrian;
            $transaksi->no_transaksi = 'TRX/'.date('Ymd').'/'.$noAntrian;
            $transaksi->kelas_id = $request->kelas_harga_id;
            $transaksi->kelas_harga_id = $request->kelas_harga_id;

            $transaksi->kelas_penjamin_id = $request->kelas_penjamin_id;
            $transaksi->penjamin_id = $request->penjamin_id;
            $transaksi->penjamin_nama = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id = $penjamin->buku_harga_id;
            $transaksi->buku_harga = $penjamin->buku_harga;
            $transaksi->total = $request->total;
            
            $transaksi->unit_id = $request->unit_id;
            $transaksi->unit_nama = $unit->unit_nama;
            $transaksi->ruang_id = $request->ruang_id;
            $transaksi->ruang_nama = $ruang->ruang_nama;
            $transaksi->dokter_id = $request->dokter_id;
            $transaksi->dokter_nama = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim = $dokter->dokter_pengirim;
            
            $transaksi->unit_pengirim_id = $request->unit_pengirim_id;
            $transaksi->unit_pengirim = $dokter->unit_pengirim;
            
            $transaksi->pasien_id = $request->pasien_id;
            $transaksi->no_rm = $pasien->no_rm;
            $transaksi->nama_pasien = $request->nama_pasien;
            // $transaksi->is_sub_trx = false;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            DB::connection('dbclient')->commit();

            $data = TrxRad::where([
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
            return response()->json(['success' => true, 'message' => 'Data radiologi berhasil disimpan', 'data' => $data]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan penambahan data radiologi. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            
            $regId = $request->reg_id;
            $trxId = $request->trx_id;
            $isRujukanInt = $request->is_rujukan_int; //perhatikan bagian ini karena akan menjadi krusial

            /**cek data pendaftaran radiologi exist atau tidak */
            $data = TrxRad::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->where('pasien_id',$request->pasien_id)
                ->where('status','DAFTAR')->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi rad tidak ditemukan / sudah berubah status.']);
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
            //             'tgl_periksa' => $request->tgl_periksa,    
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
             * update data transaksi rad
             * */
            $data->no_antrian       = $noAntrian;
            $data->is_rujukan_int   = $isRujukanInt;
            $data->tgl_periksa      = $request->tgl_periksa;
            $data->jam_periksa      = $request->jam_periksa;            
            $data->diagnosa         = $request->diagnosa;            
            $data->ket_klinis       = $request->ket_klinis;
            $data->is_cito          = $request->is_cito;
            $data->jenis_cito       = $request->jenis_cito; 
            $data->is_multi_hasil   = $request->is_multi_hasil;
            $data->diserahkan_oleh  = $request->diserahkan_oleh;            
            $data->penerima_hasil   = $request->penerima_hasil;            
            $data->hub_penerima     = $request->hub_penerima;

            //data pasien
            $data->pasien_id        = $pasien->pasien_id;
            $data->nama_pasien      = $pasien->nama_lengkap;
            $data->no_rm            = $pasien->no_rm;
            $data->tgl_lahir        = $request->tgl_lahir;
            $data->tempat_lahir     = $request->tempat_lahir;
            $data->jns_kelamin      = $request->jns_kelamin;            
            $data->nik              = $request->nik;
            //dokter rad dan pengirim
            $data->dokter_id        = $request->dokter_id;
            $data->dokter_nama      = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $data->dokter_pengirim_id = $request->dokter_pengirim_id;
            $data->dokter_pengirim  = $request->dokter_pengirim;         
            
            //diisi dokter ekspertise
            $data->expertise_id     = $request->expertise_id;
            $data->expertise_nama   = $request->expertise_nama;            
            
            //data unit rad            
            $data->unit_id          = $request->unit_id;
            $data->unit_nama        = $unit->unit_nama;
            $data->ruang_id         = $request->ruang_id;
            $data->ruang_nama       = $ruang->ruang_nama;
            
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $data->unit_asal_id     = $request->unit_asal_id;
            $data->unit_asal        = $request->unit_asal;
            $data->ruang_asal_id    = $request->ruang_asal_id;
            $data->ruang_asal       = $request->ruang_asal;
            $data->bed_asal_id      = $request->bed_asal_id;
            $data->no_bed_asal      = $request->no_bed_asal;
            $data->asal_pasien      = $request->asal_pasien;

            //$data->ket_asal_pasien = $request->ket_asal_pasien;
            $data->reff_trx_id      = $request->reff_trx_id;
            $data->penjamin_id      = $request->penjamin_id;
            $data->penjamin_nama    = $penjamin->penjamin_nama;
            $data->no_kepesertaan   = $request->no_kepesertaan;
            $data->jns_penjamin     = $penjamin->jns_penjamin;
            
            $data->buku_harga_id    = $penjamin->buku_harga_id;
            $data->buku_harga       = $penjamin->buku_harga;
            $data->kelas_harga_id   = $request->kelas_harga_id;
            $data->kelas_harga      = $request->kelas_harga;
            $data->kelas_penjamin_id = $request->kelas_harga_id;

            $data->media_hasil      = $request->media_hasil;
            $data->jns_registrasi   = $request->jns_registrasi;
            $data->cara_registrasi  = $request->cara_registrasi;
            $data->is_realisasi     = false;
            $data->updated_by       = Auth::user()->username;            
            $success                = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi gagal diubah.']);
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
                        ['buku_harga_id',$penjamin->buku_harga_id],
                        ['tindakan_id',$dtl['item_id']],
                        ['kelas_id',$request->kelas_harga_id],
                    ])->select('nilai')->first();

                //check apakah item ada yang sama
                $detail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$regId)->where('trx_id',$trxId)->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();

                if(!$detail) {
                    $detailId = $this->getRadDetailId();
                    $detail = new TransaksiDetail();
                    $detail->detail_id = $detailId;
                    $detail->reg_id = $regId;
                    $detail->trx_id = $trxId;
                    $detail->item_id = $radItem->tindakan_id;
                    $detail->client_id = $this->clientId;
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
                
                
                $detail->jns_transaksi = 'RADIOLOGI';
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
                $transaksi->jns_transaksi = 'RADIOLOGI';
            }

            //$transaksi->is_sub_trx = $isRujukanInt;
            $transaksi->tgl_transaksi = $data->tgl_periksa .':'. $data->jam_periksa;
            $transaksi->no_antrian = $noAntrian;
            $transaksi->no_transaksi = 'TRX/'.date('Ymd').'/'.$noAntrian;
            $transaksi->kelas_id = $request->kelas_harga_id;
            $transaksi->kelas_harga_id = $request->kelas_harga_id;

            $transaksi->kelas_penjamin_id = $request->kelas_penjamin_id;
            $transaksi->penjamin_id = $request->penjamin_id;
            $transaksi->penjamin_nama = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id = $penjamin->buku_harga_id;
            $transaksi->buku_harga = $penjamin->buku_harga;
            $transaksi->total = $request->total;
            
            $transaksi->unit_id = $request->unit_id;
            $transaksi->unit_nama = $unit->unit_nama;
            $transaksi->ruang_id = $request->ruang_id;
            $transaksi->ruang_nama = $ruang->ruang_nama;
            $transaksi->dokter_id = $request->dokter_id;
            $transaksi->dokter_nama = $dokter->dokter_nama;

            $transaksi->dokter_pengirim_id = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim = $request->dokter_pengirim; 
            $transaksi->unit_pengirim_id = $request->unit_pengirim_id;
            $transaksi->unit_pengirim = $request->unit_pengirim;
            
            $transaksi->pasien_id = $request->pasien_id;
            $transaksi->no_rm = $pasien->no_rm;
            $transaksi->nama_pasien = $request->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }
            DB::connection('dbclient')->commit();

            $data = TrxRad::where([
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
            $data = TrxRad::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$TrxId)->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data radiologi. Error : ' . $e->getMessage()]);
            }
            //ambil data detail transaksi
            $data['transaksi'] = TransaksiDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)
                ->first();

            //ambil data detail transaksi
            $details = TransaksiDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)
                ->get();

            $results = []; $i = 0;

            foreach($details as $dt) {
                if($data->is_multi_hasil) {
                    $hasil = TrxRadHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)
                        ->where('tindakan_id',$dt['item_id'])->first();
                    
                    if(!$hasil) {
                        $hasil['detail_id'] = null;
                        $hasil['reg_id'] = $dt['reg_id'];
                        $hasil['trx_id'] = $dt['trx_id'];
                        $hasil['tindakan_id'] = $dt['item_id'];
                        $hasil['tindakan_nama'] = $dt['item_nama'];
                        $hasil['jenis_foto'] = null;
                        $hasil['no_foto'] = null;
                        $hasil['uraian_hasil'] = null;
                        $hasil['kesan'] = null;
                        $hasil['catatan'] = null;
                        $hasil['tgl_hasil'] = $data->tgl_hasil;
                        $hasil['dokter_id'] = $data->dokter_id;
                        $hasil['dokter_nama'] = $data->dokter_nama;
                        $hasil['expertise_id'] = $data->expertise_id;
                        $hasil['expertise_nama'] = $data->expertise_nama;
                        $hasil['status'] = null;
                        $hasil['is_aktif'] = true;
                    }
                    
                    $results[$i] = $hasil;
                    $i++;
                }

                else {
                    $hasil = TrxRadHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)->first();

                    $results[$i] = $hasil;
                }
            }
            
            $data['detail'] = $details;
            $data['hasil'] = $results;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data radiologi. Error : ' . $e->getMessage()]);
        }
    }

    public function saveHasilRad(Request $request)
    {
        try {
            $regId =  $request->reg_id;
            $trxId =  $request->trx_id;
            
            // RADIOLOGI
            $radiology = TrxRad::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('reg_id', $request->reg_id)->where('trx_id', $request->trx_id)
                        ->first();

            if(!$radiology) {
                return response()->json(['success' => false,'message' => 'data transaksi radiologi tidak ditemukan.','error'=>'radiologi dengan id tersebut tidak ditemukan.']);
            }
            
            DB::connection('dbclient')->beginTransaction();  

            
            $radiology->tgl_periksa     = $request->tgl_periksa;
            $radiology->jam_periksa     = $request->jam_periksa;            
            $radiology->tgl_hasil       = $request->tgl_hasil;
            $radiology->tgl_diserahkan  = $request->tgl_diserahkan;            
            $radiology->diserahkan_oleh = Auth::user()->username;
            $radiology->penerima_hasil  = $request->penerima_hasil;            
            $radiology->hub_penerima    = $request->hub_penerima; 
            $radiology->is_multi_hasil  = $request->is_multi_hasil; 
            $radiology->media_hasil     = $request->media_hasil;            
            $radiology->dokter_id       = $request->dokter_id;
            $radiology->dokter_nama     = $request->dokter_nama;            
            $radiology->expertise_id    = $request->expertise_id;
            $radiology->expertise_nama  = $request->expertise_nama;            
            $radiology->updated_by      = Auth::user()->username;

            $success = $radiology->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data radiologi']);
            }

            foreach($request->hasil as $res) {
                // HASIL RADIOLOGI SINGLE
                $radHasil = TrxRadHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('reg_id', $request->reg_id)->where('trx_id', $request->trx_id)
                    ->where('tindakan_id', $res['tindakan_id'])
                    ->first();
                
                if(!$radHasil) {
                    // CREATE HASIL RADIOLOGI
                    $createHasil = new TrxRadHasil();
                    $createHasil->detail_id       = $this->getHasilRadId();
                    $createHasil->reg_id          = $request->reg_id;
                    $createHasil->trx_id          = $request->trx_id;
                    $createHasil->tindakan_id     = $res['tindakan_id'];
                    $createHasil->tindakan_nama   = $res['tindakan_nama'];
                    $createHasil->jenis_foto      = $res['jenis_foto'];            
                    $createHasil->no_foto         = $res['no_foto'];            
                    $createHasil->uraian_hasil    = $res['uraian_hasil'];
                    $createHasil->kesan           = $res['kesan'];
                    $createHasil->catatan         = $res['catatan'];
                    $createHasil->tgl_hasil       = $request->tgl_hasil;
                    $createHasil->dokter_id       = $request->dokter_id;
                    $createHasil->dokter_nama     = $request->dokter_nama;
                    $createHasil->expertise_id    = $res['expertise_id'];
                    $createHasil->expertise_nama  = $res['expertise_nama'];
                    $createHasil->status          = "CREATED";
                            
                    $createHasil->is_aktif        = True;
                    $createHasil->client_id       = $this->clientId;
                    $createHasil->created_by      = Auth::user()->username;
                    $createHasil->updated_by      = Auth::user()->username;

                    $success = $createHasil->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data hasil radiologi']);
                    }
                } 
                else {
                    // UPDATE HASIL RADIOLOGI
                    $radHasil->jenis_foto         = $res['jenis_foto'];            
                    $radHasil->no_foto            = $res['no_foto'];            
                    $radHasil->uraian_hasil       = $res['uraian_hasil'];
                    $radHasil->kesan              = $res['kesan'];
                    $radHasil->catatan            = $res['catatan'];
                    $radHasil->tgl_hasil          = $request->tgl_hasil;
                    $radHasil->dokter_id          = $request->dokter_id;
                    $radHasil->dokter_nama        = $request->dokter_nama;
                    $radHasil->expertise_id       = $res['expertise_id'];
                    $radHasil->expertise_nama     = $res['expertise_nama'];
                    $radHasil->status             = "CREATED";
                            
                    $radHasil->is_aktif           = True;
                    $radHasil->client_id          = $this->clientId;
                    $radHasil->updated_by         = Auth::user()->username;

                    $success = $radHasil->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam update data hasil radiologi']);
                    }
                }

            }

            DB::connection('dbclient')->commit();

            $data = TrxRad::where([
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

            $data = TrxRad::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi radiologi tidak ditemukan.']);
            }
            if($data->status !== 'DAFTAR' || $data->is_realisasi == true) {
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi sudah berubah status.']);
            }

            $regRad = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)->where('reg_id',$trxId)->first();

            /**check data transaksi */
            $transaksi = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)->first();
                
            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi sudah berubah status.']);
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

            $success = TrxRad::where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data radiologi gagal dihapus.']);
            }

            if($regRad) {
                $success = Pendaftaran::where('reg_id', $trxId)
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status_reg' =>'BATAL']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data registrasi (pendaftaran) radiologi gagal dihapus.']);
                }
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
                return response()->json(['success' => false, 'message' => 'data transaksi radiologi gagal dihapus.']);
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
                    return response()->json(['success' => false, 'message' => 'data detail transaksi radiologi gagal dihapus.']);
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
                    return response()->json(['success' => false, 'message' => 'data detail komponen transaksi radiologi gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();

            return response()->json(['success' => true, 'message' => 'Data radiologi berhasil dihapus']);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus data radiologi. Error : ' . $e->getMessage()]);
        }
    }

    public function getRadId()
    {
        try {
            $id = $this->clientId.'-'.'RAD'.'-000001';
            $maxId = Radiologi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('rad_id', 'LIKE', $this->clientId.'-'.'RAD'.'-%')
                ->max('rad_id');

            if (!$maxId) { $id = $this->clientId.'-'.'RAD'.'-000001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.'RAD'.'-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.'RAD'.'-00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'RAD'.'-0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'RAD'.'-000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'RAD'.'-00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.'RAD'.'-0' . $count; } 
                else { $id = $this->clientId.'-'.'RAD'.'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

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
            $maxId = TrxRad::withTrashed()->where('client_id', $this->clientId)->where('trx_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-RAD%')->max('trx_id');
            if (!$maxId) { $id = $this->clientId.'-'.date('Ym').'-RAD00001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-RAD', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-RAD0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-RAD000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-RAD00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-RAD0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-RAD' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return $this->clientId . date('Ym') . '-RAD' . Uuid::uuid4()->getHex();
        }
    }

    public function getNoAntrian($tglPeriksa)
    {
        try {
            $id = 'RAD001';
            $maxNo = TrxRad::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('tgl_periksa', $tglPeriksa)->max('no_antrian');

            if (!$maxNo) { $id = 'RAD001'; } 
            else {
                $maxNo = str_replace('RAD', '', $maxNo);
                $count = $maxNo + 1;
                if ($count < 10) { $id = 'RAD00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'RAD0' . $count; } 
                else { $id = 'RAD' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function getRadDetailId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-RDTL000001';
            $maxId = TransaksiDetail::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.date('Ym').'-RDTL%')->max('detail_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ym').'-RDTL000001';
            } else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-RDTL', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-RDTL00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-RDTL0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-RDTL000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.date('Ym').'-RDTL00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.date('Ym').'-RDTL0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-RDTL' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getHasilRadId()
    {
        try {
            $id = $this->clientId.'-'.'HSL'.'-000001';
            $maxId = TrxRadHasil::withTrashed()
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
     * konfirmasi data radiologi sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
    public function confirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
                        
            $data = TrxRad::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi Radiologi tidak ditemukan / sudah berubah status.']);
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
                return response()->json(['success' => false, 'message' => 'Data transaksi Radiologi tidak ditemukan / sudah berubah status.']);
            }

            /**check detail transaksi */
            $detail = TransaksiDetail::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data detail transaksi Radiologi tidak ditemukan / sudah berubah status.']);
            }

            $regRad = Pendaftaran::where('reg_id',$trxId)->where('client_id',$this->clientId)->where('is_aktif',1)->first();

            DB::connection('dbclient')->beginTransaction();
            /**update data trx radiologi */
            $success = TrxRad::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->update(['status' => 'PERIKSA', 'updated_by' => Auth::user()->username, 'is_realisasi'=>true]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi gagal dikonfirmasi.']);
            } 
            
            if($regRad){
                /**update data pendaftaran */
                $success = Pendaftaran::where('reg_id',$trxId)
                    ->where('pasien_id',$data->pasien_id) 
                    ->where('client_id',$this->clientId)->where('is_aktif',1)
                    ->update(['status_reg' =>'PERIKSA', 'updated_by' =>Auth::user()->username]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data registrasi radiologi gagal diupdate.']);
                }
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
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi gagal diupdate.']);
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
                return response()->json(['success' => false, 'message' => 'Data transaksi detail radiologi gagal diupdate.']);
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
                return response()->json(['success' => false, 'message' => 'Data transaksi detail radiologi gagal diupdate.']);
            }
            
            /**
             * create jurnal from komponen
             */

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi transaksi radiologi berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengkonfirmasi transaksi radiologi. Error : ' . $e->getMessage()]);
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
                        
            $data = TrxRad::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('status','PERIKSA')->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi radiologi tidak ditemukan / sudah berubah status.']);
            }

            $regRad = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('reg_id',$trxId)->where('pasien_id',$data->pasien_id)
                    ->first();

            // if(!$regRad) {                
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
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi tidak ditemukan / sudah berubah status.']);
            }

            /**check detail transaksi */
            $detail = TransaksiDetail::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data detail transaksi radiologi tidak ditemukan / sudah berubah status.']);
            }


            DB::connection('dbclient')->beginTransaction();
            /**update data radiologi */
            $success = TrxRad::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)->where('status','PERIKSA')
                ->update(['status' => 'DAFTAR', 'updated_by' => Auth::user()->username, 'is_realisasi'=>false]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi lab gagal dibatalkan konfirmasi.']);
            } 
            
            if($regRad){
                /**update data pendaftaran */
                $success = Pendaftaran::where('reg_id',$trxId)
                    ->where('pasien_id',$data->pasien_id) 
                    ->where('client_id',$this->clientId)->where('is_aktif',1)
                    ->update(['status_reg' =>'DAFTAR', 'updated_by' =>Auth::user()->username]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data registrasi lab gagal dibatalkan konfirmasi.']);
                }
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
            
            /**
             * hapus jurnal from komponen
             */

            DB::connection('dbclient')->commit();

            $data = TrxRad::where([['is_aktif',1],['client_id',$this->clientId],['reg_id',$regId],['trx_id',$trxId],])->first();
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
            return response()->json(['success' => true, 'message' => 'Konfirmasi transaksi radiologi berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus konfirmasi transaksi radiologi. Error : ' . $e->getMessage()]);
        }
    }

}
