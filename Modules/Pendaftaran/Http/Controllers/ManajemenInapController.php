<?php

namespace Modules\Pendaftaran\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

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

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use RegistrasiHelper;

class ManajemenInapController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
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
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $query = RawatInap::query();
            $query = $query->where('client_id',$this->clientId);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_masuk_start') && $request->has('tgl_masuk_end')) {
                $dtStart = $request->input('tgl_masuk_start').' 00:00:00';
                $dtEnd = $request->input('tgl_masuk_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_masuk', [$dtStart, $dtEnd]);
            }
            
            if ($request->has('unit')) {
                $unit = $request->input('unit');
                $query = $query->where(function($q) use ($unit) {
                        $q->where('unit_id','ILIKE',$unit)
                        ->orWhere('unit_nama','ILIKE',$unit);
                    });
            }

            if ($request->has('dokter')) {
                $dokter = $request->input('dokter');
                $query = $query->where(function($q) use ($dokter) {
                        $q->where('dokter_id','ILIKE',$dokter)
                        ->orWhere('dokter_nama','ILIKE',$dokter);
                    });
            }

            if ($request->has('status')) {
                $query = $query->where('status', 'ILIKE', '%' .$request->input('status'). '%');
            }

            // $list = RawatInap::where('client_id',$this->clientId)
            //     ->where('is_aktif','ILIKE',$aktif)
            //     ->where('status','ILIKE',$status)
            //     ->where('unit_id','ILIKE',$unitID)
            //     ->where(function($q) use ($keyword) {
            //         $q->where('no_rm','ILIKE',$keyword)
            //         ->orWhere('nama_pasien','ILIKE',$keyword);
            //     })->orderBy('created_at','DESC')->paginate($per_page);

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword)
                    ->orWhere('trx_id','ILIKE',$keyword);
                })->orderBy('created_at','DESC')
                ->paginate($per_page);

            foreach($list->items() as $dt) {
                $dt['beds'] = PemakaianBed::where('trx_id',$dt->trx_id)
                    ->where('reg_id',$dt->reg_id)
                    ->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->orderBy('tgl_masuk','ASC')
                    ->orderBy('jam_masuk','ASC')
                    ->get(); 

                $dt['transactions'] = Transaksi::where('reg_id',$dt->reg_id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->orderBy('created_at','DESC')
                    ->get();
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        /**
         * penyimpanan data registrasi rawat inap
         * saat penyimpanan status bed belum diupdate (belum dibooking).
         * hanya pembuatan untuk data registrasi dan rawat inap.
         */
        try {
            $regId = null;
            $reffTrxId = $request->reff_trx_id;
            $isRujukanInt = false; 
            $trxId = RegistrasiHelper::instance()->InapTransactionId($this->clientId); 
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
                ->where('is_aktif',1)->where('unit_id', $request->unit_id)
                ->where('ruang_id', $request->ruang_id)->first();
            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            }

            //check apakah bed yang dipilih masih aktif dan tersedia
            $bed = BedInap::where('client_id', $this->clientId)->where('is_aktif',1)->where('bed_id', $request->bed_id)->where('is_tersedia',true)->first();
            if(!$bed) {
                return response()->json(['success' => false, 'message' => 'Bed inap tidak tersedia.']);
            }
            
            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }

            //check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            //check apakah pasien exist
            $penjamin = Penjamin::where('penjamin_id',$request->penjamin_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }
            
            $regId = $this->getRegId();
            $id = $this->getTransactionId();
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir); 
            // input tb_registrasi
            DB::connection('dbclient')->beginTransaction();

            $dataReg = new Pendaftaran();
            $dataReg->reg_id = $regId;
            $dataReg->tgl_registrasi = date('Y-m-d H:i:s');
            $dataReg->tgl_periksa = $request->tgl_periksa;
            $dataReg->jam_periksa = "00:00:00";

            $dataReg->jns_registrasi = $request->jns_registrasi;
            $dataReg->cara_registrasi = $request->cara_registrasi;
            $dataReg->kode_booking = '-';
            $dataReg->no_antrian = '-';
            
            $dataReg->jadwal_id = null;
            $dataReg->dokter_id = $dokter->dokter_id;
            $dataReg->dokter_nama = $dokter->dokter_nama;
            $dataReg->unit_id = $request->unit_id;
            $dataReg->unit_nama = $request->unit_nama;
            $dataReg->ruang_id = $request->ruang_id;
            $dataReg->ruang_nama = $request->ruang_nama;
            $dataReg->bed_id = $request->bed_id;            
            $dataReg->asal_pasien = $request->asal_pasien;
            $dataReg->ket_asal_pasien = $request->ket_asal_pasien;

            $dataReg->pasien_id = $request->pasien_id;
            $dataReg->no_rm = $pasien->no_rm;
            $dataReg->nama_pasien = $pasien->nama_lengkap;
            $dataReg->jns_kelamin = $pasien->jns_kelamin;
            $dataReg->nik = $pasien->nik;
            $dataReg->no_kk = $pasien->no_kk;
            $dataReg->tgl_lahir = $pasien->tgl_lahir;
            $dataReg->tempat_lahir = $pasien->tempat_lahir;
            $dataReg->usia = $usia;

            $dataReg->jns_penjamin = $request->jns_penjamin;
            $dataReg->penjamin_id = $request->penjamin_id;
            $dataReg->penjamin_nama = $penjamin->penjamin_nama;
            $dataReg->nama_penanggung = $request->nama_penanggung;
            $dataReg->hub_penanggung = $request->hub_penanggung;
            $dataReg->is_bpjs = $request->is_bpjs;
            $dataReg->is_pasien_baru = "false";
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
            $regPasien->is_hamil = false;
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

            $regPasien->is_hamil = $pasien->is_hamil;
            $regPasien->is_pasien_baru = "false";
            $regPasien->is_aktif = true;
            $regPasien->client_id = $this->clientId;
            $regPasien->created_by = Auth::user()->username;
            $regPasien->updated_by = Auth::user()->username;
            
            $success = $regPasien->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data pasien luar gagal disimpan.']);
            } 

            /**
             * simpan data transaksi inap
             */

            $rawatInap = new RawatInap();
            $rawatInap->reg_id = $regId;
            $rawatInap->trx_id = $trxId;
            $rawatInap->reff_trx_id = $reffTrxId;
            
            $rawatInap->tgl_transaksi = date('Y-m-d H:i:s');

            $rawatInap->jns_registrasi = $request->jns_registrasi;
            $rawatInap->cara_registrasi = $request->cara_registrasi;

            $rawatInap->asal_pasien = $request->asal_pasien;
            $rawatInap->ket_asal_pasien = $request->ket_asal_pasien;
            
            $rawatInap->unit_id = $request->unit_id;
            $rawatInap->unit_nama = $request->unit_nama;
            $rawatInap->ruang_id = $request->ruang_id;
            $rawatInap->ruang_nama = $request->ruang_nama;
            $rawatInap->bed_id = $request->bed_id;
            $rawatInap->no_bed = $request->no_bed;

            $rawatInap->dokter_id = $dokter->dokter_id;
            $rawatInap->dokter_nama = $dokter->dokter_nama;
            $rawatInap->dokter_pengirim_id = $request->dokter_pengirim_id;
            $rawatInap->dokter_pengirim = $request->dokter_pengirim;
            
            $rawatInap->tgl_masuk = $request->tgl_masuk;
            $rawatInap->jam_masuk = $request->jam_masuk;

            $rawatInap->hub_penanggung = $request->hub_penanggung;
            $rawatInap->nama_penanggung = $request->nama_penanggung;
            
            $rawatInap->penjamin_id = $request->penjamin_id;
            $rawatInap->penjamin_nama = $request->penjamin_nama;
            $rawatInap->jns_penjamin = $request->jns_penjamin;
            $rawatInap->no_kepesertaan = $request->no_kepesertaan;
            
            $rawatInap->is_bpjs = $request->is_bpjs;

            $rawatInap->kelas_harga_id = $request->kelas_harga_id;
            $rawatInap->kelas_harga = $request->kelas_harga;
            $rawatInap->kelas_penjamin_id = $request->kelas_penjamin_id;
            $rawatInap->buku_harga_id = $penjamin->buku_harga_id;
            $rawatInap->buku_harga = $penjamin->buku_harga;
            
            $rawatInap->plafon = $request->plafon;
            
            $rawatInap->pasien_id = $pasien->pasien_id;
            $rawatInap->no_rm = $pasien->no_rm;
            $rawatInap->nama_pasien = $pasien->nama_lengkap;
            $rawatInap->usia = $usia;
            $rawatInap->jns_kelamin = $pasien->jns_kelamin;

            $rawatInap->diag_awal = $pasien->diag_awal;

            $rawatInap->status = 'DAFTAR';
            $rawatInap->is_pulang = false;
            $rawatInap->is_aktif = true;
            $rawatInap->is_konfirmasi = false;
            
            $rawatInap->client_id = $this->clientId;
            $rawatInap->created_by = Auth::user()->username;
            $rawatInap->updated_by = Auth::user()->username;
            
            $success = $rawatInap->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi inap gagal disimpan.']);
            } 

            /**create transaction */
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
            $transaksi->jns_transaksi       = 'RAWAT_INAP';
        
            //$transaksi->is_sub_trx          = $isRujukanInt;
            $transaksi->tgl_transaksi       = $request->tgl_periksa .':'. $request->jam_periksa;
            $transaksi->no_antrian          = '';//$noAntrian;
            $transaksi->no_transaksi        = '';//'TRX/'.date('Ymd').'/'.$noAntrian;
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
            $transaksi->nama_pasien         = $pasien->nama_lengkap;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            DB::connection('dbclient')->commit();

            $data = RawatInap::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $pasien = Pasien::where('pasien_id',$data->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $pasien['alamat'] = PasienAlamat::where('pasien_id',$data->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $data['pasien'] = $pasien;

            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan registrasi. Error : ' . $e->getMessage()]);
        }
    }

    public function getRegId()
    {
        try {
            $id = $this->clientId.'-'.date('Ymd').'-REG00001';
            $maxId = Pendaftaran::withTrashed()->where('client_id', $this->clientId)->where('reg_id', 'ILIKE', $this->clientId.'-'.date('Ymd').'-REG%')->max('reg_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ymd').'-REG00001';
            } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'-REG', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-REG0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-REG000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-REG00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-REG0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'-REG' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return $this->clientId . date('Ymd') . '-REG' . Uuid::uuid4()->getHex();
        }
    }

    public function getTransactionId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-RI00001';
            $maxId = RawatInap::withTrashed()->where('client_id', $this->clientId)->where('trx_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-RI%')->max('trx_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ym').'-RI00001';
            } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-RI', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-RI0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-RI000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-RI00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-RI0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-RI' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return $this->clientId . date('Ym') . '-RI' . Uuid::uuid4()->getHex();
        }
    }

    public function getSubTransactionId($trxId,$prefix)
    {
        try {
            $id = $this->clientId.'-'.$prefix.date('Ym').'-00001';
            $maxId = Transaksi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('sub_trx_id', 'ILIKE', $this->clientId.'-'.$prefix.date('Ym').'-%')->max('sub_trx_id');

            if (!$maxId) {
                $id = $this->clientId.'-'.$prefix.date('Ym').'-00001';
            } 
            else {
                $maxId = str_replace($this->clientId.'-'.$prefix.date('Ym').'-', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.$prefix.date('Ym').'-0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.$prefix.date('Ym').'-000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.$prefix.date('Ym').'-00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.$prefix.date('Ym').'-0' . $count; } 
                else { $id = $this->clientId.'-'.$prefix.date('Ym').'-' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return $this->clientId.'-'.$prefix.date('Ym').'-' . Uuid::uuid4()->getHex();
        }
    }

    public function update(Request $request) {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;

            $data = RawatInap::where('reg_id',$regId)->where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->where('status','DAFTAR')->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi inap tidak ditemukan / sudah berubah status.']);
            }

            $transaksi = Transaksi::where('reg_id',$regId)->where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->where('status','DAFTAR')->first();
            
            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }

            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //check apakah ruang yang dipilih masih aktif dan ada
            $ruang = RuangPelayanan::where('client_id', $this->clientId)
                ->where('is_aktif',1)->where('unit_id', $request->unit_id)
                ->where('ruang_id', $request->ruang_id)->first();
            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            }

            //check apakah bed yang dipilih masih aktif dan tersedia
            $bed = BedInap::where('client_id', $this->clientId)->where('is_aktif',1)->where('bed_id', $request->bed_id)->where('is_tersedia',true)->first();
            if(!$bed) {
                return response()->json(['success' => false, 'message' => 'Bed inap tidak tersedia.']);
            }

            //check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            //check apakah pasien exist
            $penjamin = Penjamin::where('penjamin_id',$request->penjamin_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }

            // //check data reg
            // $dataReg = Pendaftaran::where('reg_id',$regId)->where('pasien_id',$data->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            // if(!$dataReg) {
            //     return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            // }

            $regPasien = regPasien::where('reg_id',$regId)->where('pasien_id',$data->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir); 

            // input tb_registrasi
            DB::connection('dbclient')->beginTransaction();

            // $dataReg->tgl_periksa = $request->tgl_periksa;

            // $dataReg->jns_registrasi = $request->jns_registrasi;
            // $dataReg->cara_registrasi = $request->cara_registrasi;
            // $dataReg->dokter_id = $request->dokter_id;
            // $dataReg->asal_pasien = $request->asal_pasien;
            // $dataReg->ket_asal_pasien = $request->ket_asal_pasien;

            // $dataReg->jns_penjamin = $request->jns_penjamin;
            // $dataReg->penjamin_id = $request->penjamin_id;

            // $dataReg->unit_id = $request->unit_id;
            // $dataReg->ruang_id = $request->ruang_id;
            // $dataReg->bed_id = $request->bed_id;
            
            // $dataReg->nama_penanggung = $request->nama_penanggung;
            // $dataReg->hub_penanggung = $request->hub_penanggung;
            // $dataReg->is_bpjs = $request->is_bpjs;
            // $dataReg->status_reg = 'DAFTAR';
            // $dataReg->updated_by = Auth::user()->username;
            // $success = $dataReg->save();
            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data registrasi']);
            // }

            // // input tb_registrasi_pasien
            // $pasienAlamat = PasienAlamat::where('pasien_id',$pasien->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            // /** hitung usia **/
            // $tgl_lahir = $pasien->tgl_lahir;
            // $tgl_lahir = explode("-", $tgl_lahir);
            // $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
            //     ? ((date("Y") - $tgl_lahir[0]) - 1)
            //     : (date("Y") - $tgl_lahir[0]));
            
            // if(!$regPasien) {
            //     $regPasien = new RegPasien();
            //     $regPasien->reg_id = $regId;
            //     $regPasien->is_aktif = true;
            //     $regPasien->client_id = $this->clientId;
            //     $regPasien->created_by = Auth::user()->username;
            // }
            
            // $regPasien->is_pasien_luar = $pasien->is_pasien_luar;
            // $regPasien->pasien_id = $pasien->pasien_id;
            // $regPasien->no_rm = $pasien->no_rm;
            // $regPasien->nama_pasien = $pasien->nama_lengkap;
            // $regPasien->tempat_lahir = $pasien->tempat_lahir;
            // $regPasien->tgl_lahir = $pasien->tgl_lahir;
            // $regPasien->usia = $usia;
            // $regPasien->jns_kelamin = $pasien->jns_kelamin;

            // $regPasien->propinsi_id = null;
            // $regPasien->kota_id = null;
            // $regPasien->kecamatan_id = null;
            // $regPasien->kelurahan_id = null;
            // $regPasien->kodepos = null;

            // if($pasienAlamat) {
            //     $regPasien->propinsi_id = $pasienAlamat->propinsi_id;
            //     $regPasien->kota_id = $pasienAlamat->kota_id;
            //     $regPasien->kecamatan_id = $pasienAlamat->kecamatan_id;
            //     $regPasien->kelurahan_id = $pasienAlamat->kelurahan_id;
            //     $regPasien->kodepos = $pasienAlamat->kodepos;
            // }

            // $regPasien->is_hamil = $request->is_hamil;
            // $regPasien->is_pasien_baru = $pasien->is_pasien_baru;
            // $regPasien->updated_by = Auth::user()->username;            
            // $success = $regPasien->save();
            // if(!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
            // } 

            /**
             * ubah data transaksi inap
             */

            $data->unit_id          = $request->unit_id;
            $data->unit_nama        = $request->unit_nama;
            $data->ruang_id         = $request->ruang_id;
            $data->ruang_nama       = $request->ruang_nama;
            $data->bed_id           = $request->bed_id;
            $data->no_bed           = $request->no_bed;

            $data->cara_registrasi  = $request->cara_registrasi;
            $data->asal_pasien      = $request->asal_pasien;
            $data->ket_asal_pasien  = $request->ket_asal_pasien;

            $data->dokter_id        = $dokter->dokter_id;
            $data->dokter_nama      = $dokter->dokter_nama;
            $data->dokter_pengirim_id = $request->dokter_pengirim_id;
            $data->dokter_pengirim  = $request->dokter_pengirim;
            
            $data->tgl_masuk        = $request->tgl_masuk;
            $data->jam_masuk        = $request->jam_masuk;

            $data->hub_penanggung   = $request->hub_penanggung;
            $data->nama_penanggung  = $request->nama_penanggung;
            
            $data->penjamin_id      = $request->penjamin_id;
            $data->penjamin_nama    = $request->penjamin_nama;
            $data->jns_penjamin     = $request->jns_penjamin;
            $data->no_kepesertaan   = $request->no_kepesertaan;
            
            $data->is_bpjs          = $request->is_bpjs;
            $data->kelas_harga_id   = $request->kelas_harga_id;
            $data->kelas_harga      = $request->kelas_harga;
            $data->kelas_penjamin_id = $request->kelas_penjamin_id;            
            $data->plafon           = $request->plafon;
            
            $data->pasien_id        = $pasien->pasien_id;
            $data->no_rm            = $pasien->no_rm;
            $data->nama_pasien      = $pasien->nama_lengkap;
            $data->usia             = $usia;
            $data->jns_kelamin      = $pasien->jns_kelamin;

            $data->diag_awal        = $pasien->diag_awal;
            $data->status           = 'DAFTAR';
            $data->updated_by       = Auth::user()->username;            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi inap gagal diubah.']);
            } 

            /**
             * create or update data transaksi
             */
            if(!$transaksi) {
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
                $transaksi->jns_transaksi       = 'RAWAT_INAP';
                $transaksi->tgl_transaksi       = $request->tgl_periksa .':'. $request->jam_periksa;
                $transaksi->no_antrian          = '';//$noAntrian;
                $transaksi->no_transaksi        = '';//'TRX/'.date('Ymd').'/'.$noAntrian;
            }
            
            $transaksi->updated_by          = Auth::user()->username;
            //$transaksi->is_sub_trx          = $isRujukanInt;
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
            $transaksi->nama_pasien         = $pasien->nama_lengkap;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }
            
            DB::connection('dbclient')->commit();

            $data = RawatInap::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $pasien = Pasien::where('pasien_id',$data->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $pasien['alamat'] = PasienAlamat::where('pasien_id',$data->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $data['pasien'] = $pasien;
            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil diubah', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengubah registrasi. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $trxId)
    {
        try {
            $data = RawatInap::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap tidak ditemukan.']);
            }

            $pasien = Pasien::where('pasien_id',$data->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $pasien['alamat'] = PasienAlamat::where('pasien_id',$data->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $data['pasien'] = $pasien;
            $data['beds'] = PemakaianBed::where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)
                ->orderBy('tgl_masuk','ASC')
                ->orderBy('jam_masuk','ASC')
                ->get(); 
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * konfirmasi data rawat inap sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
    public function confirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
            
            $data = RawatInap::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi inap tidak ditemukan / sudah berubah status.']);
            }

            // //check data reg
            // $dataReg = Pendaftaran::where('reg_id',$regId)
            //     ->where('pasien_id',$data->pasien_id) 
            //     ->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            // if(!$dataReg) {
            //     return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            // }

            /**
             * check apakah bed inap masih tersedia
             */
            $bed = BedInap::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('bed_id', $data->bed_id)
                ->where('is_tersedia',true)->first();
            if(!$bed) {
                return response()->json(['success' => false, 'message' => 'Bed inap (sudah) tidak tersedia.']);
            }

            /** ambil data penjamin */
            $penjamin = Penjamin::where('penjamin_id',$data->penjamin_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data Penjamin tidak ditemukan.']);
            }

            /** ambil data ruang */
            $ruang = RuangPelayanan::where('ruang_id',$data->ruang_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)->first();

            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data Ruang tidak ditemukan.']);
            }            

            DB::connection('dbclient')->beginTransaction();
            /**update data rawat inap */
            $success = RawatInap::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->update(['status' => 'RAWAT', 'updated_by' => Auth::user()->username, 'is_konfirmasi'=>true]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data rawat inap gagal diupdate.']);
            } 
            
            // /**update data pendaftaran */
            // $success = Pendaftaran::where('reg_id',$regId)
            //     ->where('pasien_id',$data->pasien_id) 
            //     ->where('client_id',$this->clientId)->where('is_aktif',1)
            //     ->update(['status_reg' =>'RAWAT', 'updated_by' =>Auth::user()->username]);

            // if(!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'Data registrasi rawat inap gagal diupdate.']);
            // } 

            /**
             * update status bed inap
             */
            BedInap::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('bed_id', $data->bed_id)
                ->where('is_tersedia',true)
                ->update([
                    'is_tersedia' => false,
                    'status' => 'TERPAKAI',
                    'reg_id' => $regId, 
                    'trx_id' => $trxId, 
                    'pasien_id' => $data->pasien_id,
                    'nama_pasien' => $data->nama_pasien,
                    'no_rm' => $data->no_rm,
                    'penjamin_id' => $data->penjamin_id,
                    'penjamin_nama' => $data->penjamin_nama,                    
                    'tgl_masuk' => $request->tgl_masuk,
                    'jam_masuk' => $request->jam_masuk,
                    'updated_by' => Auth::user()->username
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data bed inap gagal diupdate.']);
            } 

            /**buat log pemakaian bed */
            $bedTujuanLog = new PemakaianBed();
            $bedTujuanLog->pemakaian_bed_id = $this->clientId.'-'.date('Ymd').'-'.Uuid::uuid4()->getHex();
            $bedTujuanLog->reg_id = $data->reg_id;
            $bedTujuanLog->trx_id = $data->trx_id;
            $bedTujuanLog->unit_id = $data->unit_id;
            $bedTujuanLog->unit_nama = $data->unit_nama;
            $bedTujuanLog->ruang_id = $data->ruang_id;
            $bedTujuanLog->ruang_nama = $data->ruang_nama;
            $bedTujuanLog->bed_id = $data->bed_id;
            $bedTujuanLog->no_bed = $data->no_bed;
            
            $bedTujuanLog->jam_masuk = $request->jam_masuk;
            $bedTujuanLog->tgl_masuk = $request->tgl_masuk;
            
            $bedTujuanLog->pasien_id = $data->pasien_id;
            $bedTujuanLog->nama_pasien = $data->nama_pasien;
            $bedTujuanLog->no_rm = $data->no_rm;
            $bedTujuanLog->kelas_harga = $data->kelas_harga;

            $bedTujuanLog->penjamin_id = $penjamin->penjamin_id;
            $bedTujuanLog->penjamin_nama = $penjamin->penjamin_nama;
        
            $bedTujuanLog->buku_harga_id = $penjamin->buku_harga_id;
            $bedTujuanLog->buku_nama = $penjamin->buku_harga;
            $bedTujuanLog->kelas_harga = $data->kelas_harga;
            $bedTujuanLog->harga_id = $ruang->harga_id;
            $bedTujuanLog->harga_nama = null;  
        
            $bedTujuanLog->jml_hari = 1;
            $bedTujuanLog->is_manual = false;
            $bedTujuanLog->is_keluar = false;
            $bedTujuanLog->is_pulang = false;
            $bedTujuanLog->status = 'RAWAT';
            
            $bedTujuanLog->client_id = $this->clientId;
            $bedTujuanLog->created_by = Auth::user()->username;
            $bedTujuanLog->updated_by = Auth::user()->username;
            $bedTujuanLog->is_aktif = true;

            $success = $bedTujuanLog->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data pemakaian bed baru gagal disimpan.']);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi rawat inap berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengkonfirmasi rawat inap. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * Update dokter dpjp.
     */
    public function gantiDokterDPJP(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $data = RawatInap::where('trx_id',$trxId)
                ->where('dokter_id',$request->dokter_asal_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap tidak ditemukan.']);
            }

            if($data->status == 'BOOKING' || $data->status == 'DAFTAR' || $data->status == 'PULANG' || $data->status == 'BATAL' ) {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap sudah berubah status. Jika konfirmasi belum dilakukan. Anda dapat mengubah dokter DPJP dari menu ubah data.']);
            }
 
            /**check dokter */
            $dokter = Dokter::where('dokter_id',$request->dokter_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)
                ->first();

            if(!$dokter) {
                return response()->json(['success' => false, 'message' => 'Data dokter DPJP tidak ditemukan.']);
            }            
            
            DB::connection('dbclient')->beginTransaction();

            $success = RawatInap::where('trx_id',$trxId)
                ->where('dokter_id',$request->dokter_asal_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'dokter_id' => $request->dokter_id,
                    'dokter_nama' => $dokter->dokter_nama,
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Penggantian dokter DPJP gagal disimpan(1).']);
            } 
            /**
             * buat data DPJP log
             */

            $dpjp = new DpjpLog();

            $dpjp->reg_id = $data->reg_id;
            $dpjp->trx_id = $trxId;
            $dpjp->dokter_log_id = $this->clientId.date('Ym').'-'.Uuid::uuid4()->getHex();
            $dpjp->tgl_ubah = date('Y-m-d');
            //$dpjp->jam_ubah = date('H:i:s');
            
            $dpjp->dokter_id = $request->dokter_id;
            $dpjp->dokter_nama = $request->dokter_nama;
            $dpjp->smf_id = $request->smf_id;
            $dpjp->smf = $request->smf;
            
            $dpjp->dokter_asal_id = $data->dokter_id;
            $dpjp->dokter_asal = $data->dokter_nama;
            $dpjp->smf_asal_id = $data->smf_id;
            $dpjp->smf_asal = $data->smf;
            
            $dpjp->catatan = $data->catatan;
            $dpjp->trx_status = 'RAWAT';
            
            $dpjp->is_aktif = true;
            $dpjp->is_dpjp = true;
            $dpjp->client_id = $this->clientId;
            $dpjp->created_by = Auth::user()->username;
            $dpjp->updated_by = Auth::user()->username;
            $success = $dpjp->save();
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Log dokter DPJP gagal disimpan(2).']);
            } 

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Dokter DPJP berhasil diubah', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data. Error : ' . $e->getMessage()]);
        }
    }
    /**
     * Update ruang.
     */
    public function gantiRuangInap(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $data = RawatInap::where('trx_id',$trxId)
                ->where('unit_id',$request->unit_asal_id)
                ->where('ruang_id',$request->ruang_asal_id)
                ->where('bed_id',$request->bed_asal_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data rawat inap tidak ditemukan.']);
            }

            if($data->status == 'BOOKING' || $data->status == 'DAFTAR' || $data->status == 'PULANG' || $data->status == 'BATAL' ) {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap sudah berubah status. Jika konfirmasi belum dilakukan. Anda dapat mengubah dokter DPJP dari menu ubah data.']);
            }

            /**check bed tujuan */
            $bed = BedInap::where('bed_id',$request->bed_id)
                ->where('ruang_id',$request->ruang_id)
                ->where('is_tersedia',true)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$bed) {
                return response()->json(['success' => false, 'message' => 'Data Ruang inap tidak ditemukan / tidak tersedia.']);
            }

            /**check bed asal */
            $bedAsal = BedInap::where('bed_id',$request->bed_asal_id)
                ->where('ruang_id',$request->ruang_asal_id)
                ->where('is_tersedia',false)
                ->where('pasien_id',$data->pasien_id)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$bedAsal) {
                return response()->json(['success' => false, 'message' => 'Data Ruang inap asal pasien tidak ditemukan.']);
            }
            
            /** ambil data penjamin */
            $penjamin = Penjamin::where('penjamin_id',$data->penjamin_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data Penjamin tidak ditemukan.']);
            }

            /** ambil data ruang */
            $ruang = RuangPelayanan::where('ruang_id',$data->ruang_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)->first();

            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data Ruang tidak ditemukan.']);
            }            

            DB::connection('dbclient')->beginTransaction();

            /**update informasi rawat inap */
            $success = RawatInap::where('trx_id',$trxId)
                ->where('unit_id',$request->unit_asal_id)
                ->where('ruang_id',$request->ruang_asal_id)
                ->where('bed_id',$request->bed_asal_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'unit_id' => $request->unit_id,
                    'ruang_id' => $request->ruang_id,
                    'bed_id' => $request->bed_id,
                    'unit_nama' => $request->unit_nama,
                    'ruang_nama' => $request->ruang_nama,
                    'kelas_harga' => $request->kelas_harga,
                    'no_bed' => $request->no_bed,
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Penggantian ruang gagal disimpan(1).']);
            }
            
            /**update informasi bed asal*/
            $success = BedInap::where('bed_id',$request->bed_asal_id)
                ->where('ruang_id',$request->ruang_asal_id)
                ->where('is_tersedia',false)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'is_tersedia' => true, 
                    'pasien_id' => null, 
                    'reg_id' => null, 
                    'trx_id' => null, 
                    'tgl_masuk' => null, 
                    'jam_masuk' => null, 
                    'status' => 'TERSEDIA',
                    'updated_by' => Auth::user()->username
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Update data bed asal pasien gagal.']);
            }

            /**update informasi bed tujuan*/
            $success = BedInap::where('bed_id',$request->bed_id)
                ->where('ruang_id',$request->ruang_id)
                ->where('is_tersedia',true)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'is_tersedia'=>false, 
                    'pasien_id'=>$data->pasien_id, 
                    'reg_id'=>$data->reg_id, 
                    'trx_id'=>$data->trx_id, 
                    'tgl_masuk' =>$request->tgl_masuk, 
                    'jam_masuk' =>$request->jam_masuk, 
                    'status' => 'RAWAT',
                    'updated_by' => Auth::user()->username,
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Update data bed tujuan pasien gagal.']);
            }

            $pemakaianBedSblm = PemakaianBed::where('is_keluar',0)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('pasien_id',$data->pasien_id)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();
            
            /**update pemakaian bed sebelumnya*/        
            if($pemakaianBedSblm){
                $success = PemakaianBed::where('is_keluar',0)
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('pasien_id',$data->pasien_id)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->update([
                        'is_keluar' => true,
                        'tgl_keluar' => $request->tgl_masuk,
                        'jam_keluar' => $request->jam_masuk,
                        'updated_by' => Auth::user()->username
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'update pemakaian bed gagal disimpan.']);
                }
            }    
                    
            /**buat data pemakaian bed */     
            /**create pemakaian bed tujuan*/
            $bedTujuanLog = new PemakaianBed();
            $bedTujuanLog->pemakaian_bed_id = $this->clientId.'-'.date('Ymd').'-'.Uuid::uuid4()->getHex();
            $bedTujuanLog->reg_id = $data->reg_id;
            $bedTujuanLog->trx_id = $data->trx_id;
            
            $bedTujuanLog->unit_id = $request->unit_id;
            $bedTujuanLog->unit_nama = $request->unit_nama;
            $bedTujuanLog->ruang_id = $request->ruang_id;
            $bedTujuanLog->ruang_nama = $request->ruang_nama;
            $bedTujuanLog->bed_id = $request->bed_id;
            $bedTujuanLog->no_bed = $request->no_bed;
            
            $bedTujuanLog->unit_asal_id = $request->unit_asal_id;
            $bedTujuanLog->unit_asal = $request->unit_asal;
            $bedTujuanLog->ruang_asal_id = $request->ruang_asal_id;
            $bedTujuanLog->ruang_asal = $request->ruang_asal;
            $bedTujuanLog->bed_asal_id = $request->bed_asal_id;
            $bedTujuanLog->no_bed_asal = $request->no_bed_asal;
            
            $bedTujuanLog->jam_masuk = $request->jam_masuk;
            $bedTujuanLog->tgl_masuk = $request->tgl_masuk;
            
            $bedTujuanLog->pasien_id = $request->pasien_id;
            $bedTujuanLog->nama_pasien = $request->nama_pasien;
            $bedTujuanLog->no_rm = $request->no_rm;
            $bedTujuanLog->kelas_harga = $request->kelas_harga;

            $bedTujuanLog->penjamin_id = $penjamin->penjamin_id;
            $bedTujuanLog->penjamin_nama = $penjamin->penjamin_id;
        
            $bedTujuanLog->buku_harga_id = $penjamin->buku_harga_id;
            $bedTujuanLog->buku_nama = $penjamin->buku_harga;
            $bedTujuanLog->kelas_harga = $request->kelas_harga;
            $bedTujuanLog->harga_id = $request->harga_id;
            $bedTujuanLog->harga_nama = null;
        
            $bedTujuanLog->jml_hari = 1;
            $bedTujuanLog->is_manual = false;
            $bedTujuanLog->is_keluar = false;
            $bedTujuanLog->is_pulang = false;
            $bedTujuanLog->status = 'RAWAT';
            
            $bedTujuanLog->client_id = $this->clientId;
            $bedTujuanLog->created_by = Auth::user()->username;
            $bedTujuanLog->updated_by = Auth::user()->username;
            $bedTujuanLog->is_aktif = true;

            $success = $bedTujuanLog->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data pemakaian bed baru gagal disimpan.']);
            }

            /**
             * Mengambil harga kamar berdasarkan buku harga, kelas, dan tindakan (harga)
             */
            $hargaInap = DB::connection('dbclient')->table('tb_pemakaian_bed as tpb')
            ->join('tb_transaksi as tt', 'tpb.reg_id','=','tt.reg_id')
            ->join('tb_harga as th', function($join){
                $join->on('tpb.buku_harga_id','=','th.buku_harga_id');
                $join->on('tt.kelas_harga_id','=','th.kelas_id');
                $join->on('tpb.harga_id','=','th.tindakan_id');
            })
            ->join('tb_tindakan as td', 'th.tindakan_id','=','td.tindakan_id')
            ->where('tpb.reg_id',$data->reg_id)
            ->where('tpb.client_id',$this->clientId)
            ->select('tpb.reg_id', 
                'tpb.harga_id', 
                'tpb.jml_hari', 
                'tpb.client_id', 
                'th.nilai',
                'tt.kelas_harga_id',
                'tpb.buku_harga_id',
                'tt.penjamin_id',
                'tt.dokter_id',
                'tt.dokter_pengirim_id',
                'td.is_hitung_admin'
            )
            ->first();
            
            if(!$hargaInap){
                return response()->json(['success' => false, 'message' => 'Pencarian harga bed tidak ditemukan.']);
            }

            /** Hitung harga sewa kamar */
            $calculateHargaInap = $durationInDays * $hargaInap->nilai;

            /** Create Transaksi Detail */
            $trxDetail = new TransaksiDetail();
            $trxDetail->reg_id = $data->reg_id;
            $trxDetail->trx_id = $data->trxId;
            $trxDetail->detail_id = $detailId = $trxId.Uuid::uuid4()->getHex();;
            
            $trxDetail->item_id = $pemakaianBed->pemakaian_bed_id;
            $trxDetail->item_nama = "Pemakaian Kamar " . $pemakaianBed->no_bed;
            $trxDetail->satuan = 'hari';
            
            $trxDetail->jumlah = $durationInDays;
            $trxDetail->nilai = $hargaInap->nilai;
            $trxDetail->diskon_persen = 0;
            $trxDetail->diskon = 0;
            $trxDetail->harga_diskon = 0;
            $trxDetail->subtotal = $calculateHargaInap;
                            
            $trxDetail->jns_transaksi = 'RAWAT INAP';
            $trxDetail->kelas_harga_id = $hargaInap->kelas_harga_id;
            $trxDetail->buku_harga_id = $hargaInap->buku_harga_id;
            $trxDetail->penjamin_id = $hargaInap->penjamin_id;
            $trxDetail->dokter_id = $hargaInap->dokter_id;
            if(!$hargaInap->dokter_pengirim_id){
                $trxDetail->dokter_pengirim_id = "-";
            } else {
                $trxDetail->dokter_pengirim_id = $hargaInap->dokter_pengirim_id;
            }
            $trxDetail->is_hitung_adm = $hargaInap->is_hitung_admin;
            
            $trxDetail->is_aktif = true;
            $trxDetail->client_id = $this->clientId;
            $trxDetail->created_by = Auth::user()->username;
            $trxDetail->updated_by = Auth::user()->username;

            $success = $trxDetail->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data pemakaian bed baru gagal disimpan.']);
            }

            /**
             * Update total transaksi
            */
            $success = Transaksi::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('trx_id',$trxId)->where('reg_id',$data->reg_id)
                ->update([
                    'is_realisasi' => true,
                    'total' => $calculateHargaInap,
                    'updated_by' => Auth::user()->username
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi inap gagal diupdate.']);
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi rawat inap berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengubah data ruang inap. Error : ' . $e->getMessage()]);
        }
    }
    /**
     * Pasien Pulang.
     */
    public function pasienPulang(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
            /** check data apakah exist */
            $data = RawatInap::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('unit_id',$request->unit_id)
                ->where('ruang_id',$request->ruang_id)
                ->where('bed_id',$request->bed_id)
                ->where('status','RAWAT')
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data rawat inap tidak ditemukan / sudah berubah .']);
            }

            /**check reg */
            $reg = Pendaftaran::where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',true)
                ->where('client_id',$this->clientId)
                ->first();

            // if(!$reg) {
            //     return response()->json(['success' => false, 'message' => 'Data Registrasi tidak ditemukan / tidak tersedia.']);
            // }

            /**check bed asal */
            $bed = BedInap::where('bed_id',$request->bed_id)
                ->where('ruang_id',$request->ruang_id)
                ->where('is_tersedia',false)
                ->where('pasien_id',$data->pasien_id)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$bed) {
                return response()->json(['success' => false, 'message' => 'Data Ruang inap pasien tidak ditemukan.']);
            }
            
            DB::connection('dbclient')->beginTransaction();

            /**update informasi rawat inap */
            $success = RawatInap::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('status','RAWAT')
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'status' => 'PULANG',
                    'tgl_pulang' => $request->tgl_pulang,
                    'jam_pulang' => $request->jam_pulang,
                    'status_pulang' => $request->status_pulang,
                    'cara_pulang' => $request->cara_pulang,
                    'catatan_pulang' => $request->catatan_pulang,
                    'is_pulang' => true,
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Informasi pulang gagal disimpan.']);
            }
            
            /**update informasi bed asal*/
            $success = BedInap::where('bed_id',$request->bed_id)
                ->where('ruang_id',$request->ruang_id)
                ->where('is_tersedia',false)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'is_tersedia' => true, 
                    'pasien_id' => null, 
                    'reg_id' => null, 
                    'trx_id' => null, 
                    'tgl_masuk' => null, 
                    'jam_masuk' => null, 
                    'status' => 'TERSEDIA',
                    'updated_by' => Auth::user()->username
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Update data bed asal pasien gagal.']);
            }

            if($reg){
                /**update informasi pendaftaran*/
                $success = Pendaftaran::where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',true)
                ->where('client_id',$this->clientId)
                ->update([
                    'status_reg' =>'PULANG', 
                    'tgl_pulang' => $request->tgl_pulang.' '.$request->jam_pulang,
                    'status_pulang' => $request->status_pulang,
                    'cara_pulang' => $request->cara_pulang,
                    'updated_by' => Auth::user()->username,
                ]);

                if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Update data registrasi pasien gagal.']);
                }
            }

            /**
             * Hitung jumlah hari keluar
             */
            $pemakaianBed = PemakaianBed::where('reg_id',$regId)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$pemakaianBed) {
                return response()->json(['success' => false, 'message' => 'Data pemakaian bed tidak ditemukan / sudah berubah .']);
            }

            // Assuming you have the check-in and check-out times as strings
            $checkInTime = $pemakaianBed->tgl_masuk . ' ' . $pemakaianBed->jam_masuk;
            $checkOutTime = $request->tgl_pulang  . ' ' . $request->jam_pulang; 

            // Convert check-in and check-out times to Carbon objects
            $checkIn = Carbon::parse($checkInTime);
            $checkOut = Carbon::parse($checkOutTime);

            $checkOutTimeOnly = Carbon::createFromFormat('H:i', $request->jam_pulang);
            $midDay = Carbon::today()->setTime(12, 0, 0); // Represents noon (12:00 PM)
            if ($checkOutTimeOnly->lt($midDay)) {
                $durationInDays = $checkIn->diffInDays($checkOut);
            } else {
                $durationInDays = $checkIn->diffInDays($checkOut->addDay());
            }

            /** update pemakaian bed */
            $success = PemakaianBed::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('bed_id',$request->bed_id)
                ->where('ruang_id',$request->ruang_id)
                ->where('unit_id',$request->unit_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('reg_id',$regId)
                ->where('trx_id',$trxId)
                ->update([
                    'jml_hari'  => $durationInDays,
                    'is_keluar' => true,
                    'is_pulang' => true,
                    'status' => 'PULANG',
                    'tgl_keluar' => $request->tgl_pulang,
                    'jam_keluar' => $request->jam_pulang,
                    'updated_by' => Auth::user()->username,    
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'update pemakaian bed gagal disimpan.']);
            }
            
            /**
             * Mengambil harga kamar berdasarkan buku harga, kelas, dan tindakan (harga)
             */
            $hargaInap = DB::connection('dbclient')->table('tb_pemakaian_bed as tpb')
            ->join('tb_transaksi as tt', 'tpb.reg_id','=','tt.reg_id')
            ->join('tb_harga as th', function($join){
                $join->on('tpb.buku_harga_id','=','th.buku_harga_id');
                $join->on('tt.kelas_harga_id','=','th.kelas_id');
                $join->on('tpb.harga_id','=','th.tindakan_id');
            })
            ->join('tb_tindakan as td', 'th.tindakan_id','=','td.tindakan_id')
            ->where('tpb.reg_id',$regId)
            ->where('tpb.client_id',$this->clientId)
            ->select('tpb.reg_id', 
                'tpb.harga_id', 
                'tpb.jml_hari', 
                'tpb.client_id', 
                'th.nilai',
                'tt.kelas_harga_id',
                'tpb.buku_harga_id',
                'tt.penjamin_id',
                'tt.dokter_id',
                'tt.dokter_pengirim_id',
                'td.is_hitung_admin'
            )
            ->first();
            
            if(!$hargaInap){
                return response()->json(['success' => false, 'message' => 'Pencarian harga bed tidak ditemukan.']);
            }

            /** Hitung harga sewa kamar */
            $calculateHargaInap = $durationInDays * $hargaInap->nilai;

            /** Create Transaksi Detail */
            $trxDetail = new TransaksiDetail();
            $trxDetail->reg_id = $regId;
            $trxDetail->trx_id = $trxId;
            $trxDetail->detail_id = $detailId = $trxId.Uuid::uuid4()->getHex();;
            
            $trxDetail->item_id = $pemakaianBed->pemakaian_bed_id;
            $trxDetail->item_nama = "Pemakaian Kamar " . $pemakaianBed->no_bed;
            $trxDetail->satuan = 'hari';
            
            $trxDetail->jumlah = $durationInDays;
            $trxDetail->nilai = $hargaInap->nilai;
            $trxDetail->diskon_persen = 0;
            $trxDetail->diskon = 0;
            $trxDetail->harga_diskon = 0;
            $trxDetail->subtotal = $calculateHargaInap;
                            
            $trxDetail->jns_transaksi = 'RAWAT INAP';
            $trxDetail->kelas_harga_id = $hargaInap->kelas_harga_id;
            $trxDetail->buku_harga_id = $hargaInap->buku_harga_id;
            $trxDetail->penjamin_id = $hargaInap->penjamin_id;
            $trxDetail->dokter_id = $hargaInap->dokter_id;
            if(!$hargaInap->dokter_pengirim_id){
                $trxDetail->dokter_pengirim_id = "-";
            } else {
                $trxDetail->dokter_pengirim_id = $hargaInap->dokter_pengirim_id;
            }
            $trxDetail->is_hitung_adm = $hargaInap->is_hitung_admin;

            $trxDetail->is_aktif = true;
            $trxDetail->client_id = $this->clientId;
            $trxDetail->created_by = Auth::user()->username;
            $trxDetail->updated_by = Auth::user()->username;

            $success = $trxDetail->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data pemakaian bed baru gagal disimpan.']);
            }

            /**
             * Update total transaksi
            */
            $success = Transaksi::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('trx_id',$trxId)->where('reg_id',$regId)
                ->update([
                    'is_realisasi' => true,
                    'total' => $calculateHargaInap,
                    'updated_by' => Auth::user()->username
                ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi inap gagal diupdate.']);
            } 
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Kepulangan rawat inap berhasil disimpan', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengubah data (kepulangan) rawat inap. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * Update penjamin.
     */
    public function gantiPenjamin(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
            /**check data exist /tidak */
            $data = RawatInap::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('penjamin_id',$request->penjamin_asal_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap tidak ditemukan.']);
            }

            if($data->status == 'BATAL' ) {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap sudah berubah status. ']);
            }
 
            /**check penjamin apakah masih aktif atau tidak */
            $penjamin = Penjamin::where('penjamin_id',$request->penjamin_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)
                ->first();

            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data penjamin (asuransi) tidak ditemukan.']);
            }            
            
            DB::connection('dbclient')->beginTransaction();
            //update data rawat inap
            $success = RawatInap::where('trx_id',$trxId)
                ->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)                
                ->where('penjamin_id',$request->penjamin_asal_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update([
                    'penjamin_id' => $request->penjamin_id,
                    'no_kepesertaan' => $request->no_kepesertaan,
                    'kelas_penjamin' => $request->kelas_penjamin,
                    'penjamin_nama' => $penjamin->penjamin_nama,
                    'jns_penjamin' => $penjamin->jns_penjamin,
                    'is_bpjs' => $penjamin->is_bpjs,
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Penggantian penjamin gagal disimpan(1).']);
            } 
            /**
             * buat data Penjamin log
             */
            $penjaminLog = new PenjaminLog();

            $penjaminLog->reg_id = $data->reg_id;
            $penjaminLog->trx_id = $trxId;
            $penjaminLog->penjamin_log_id = $this->clientId.date('Ym').'-'.Uuid::uuid4()->getHex();
            $penjaminLog->tgl_ubah = date('Y-m-d');

            $penjaminLog->penjamin_id = $request->penjamin_id;
            $penjaminLog->penjamin_nama = $penjamin->penjamin_nama;
            $penjaminLog->kelas = $request->kelas_penjamin;
            
            $penjaminLog->penjamin_asal_id = $data->penjamin_id;
            $penjaminLog->penjamin_asal = $data->penjamin_nama;
            $penjaminLog->kelas_asal = $data->kelas_penjamin;
            
            $penjaminLog->trx_status = $data->status;            
            $penjaminLog->is_aktif = true;
            $penjaminLog->client_id = $this->clientId;
            $penjaminLog->created_by = Auth::user()->username;
            $penjaminLog->updated_by = Auth::user()->username;
            $success = $penjaminLog->save();
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Log penjamin gagal dibuat.']);
            } 

            /**
             * masih perlu dilihat lagi, bagaimana dengan data rawat inap dengan penjamin sebelumnya.
             * harga bed, tindakan, dll yang terpengaruh dengan buku harga masing-masing penjamin.
             */

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Penjamin berhasil diubah', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengubah data. Error : ' . $e->getMessage()]);
        }
    }

    /**batalkan registrasi inap */
    public function delete(Request $request, $trxId) {
        try {
            $data = RawatInap::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data rawat inap tidak ditemukan.']);
            }

            if($data->status !== 'BOOKING' && $data->status !== 'DAFTAR') {
                return response()->json(['success' => false, 'message' => 'Data transaksi inap sudah berubah status. Proses tidak dapat dilanjutkan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $success = RawatInap::where('trx_id',$trxId)
                ->where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data rawat inap gagal dihapus.']);
            }

            $success = Pendaftaran::where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status_reg' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data registrasi rawat inap gagal dihapus.']);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'data rawat berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus data registrasi inap. Error : ' . $e->getMessage()]);
        }
    }

}
