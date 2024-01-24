<?php

namespace Modules\Pendaftaran\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

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
use Modules\MasterData\Entities\DokterJadwal;
use Modules\MasterData\Entities\Penjamin;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;
use Modules\Pendaftaran\Entities\RawatJalan;

use Modules\SatuSehat\Entities\BridgingLog;

use RegistrasiHelper;

class PendaftaranController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request)
    {
        try {
            $per_page = 20;
            $aktif = 'true';
            $keyword = '%%';
            
            $query = RawatJalan::query();
            $query = $query->where('client_id',$this->clientId);
            
            if ($request->has('tgl_periksa_start') && $request->has('tgl_periksa_end')) {
                $dtStart = $request->input('tgl_periksa_start').' 00:00:00';
                $dtEnd = $request->input('tgl_periksa_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_periksa', [$dtStart, $dtEnd]);
            }

            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
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

            if ($request->has('jns_penjamin')) {
                $query = $query->where('jns_penjamin', 'ILIKE', '%' .$request->input('jns_penjamin'). '%');
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword);
                })->select('reg_id','trx_id','reff_trx_id',
                    'pasien_id','no_rm','nama_pasien','usia','no_kepesertaan','jns_kelamin',
                    'no_antrian','tgl_periksa',
                    'dokter_nama','dokter_id',
                    'unit_nama','unit_id','ruang_nama','no_sep',
                    'jns_penjamin','penjamin_nama',
                    'status','is_aktif')
                ->orderBy('tgl_periksa','ASC')
                ->orderBy('no_antrian','ASC')
                ->paginate($per_page);            
            
            foreach($list->items() as $dt) {
                $dt['transaksi'] = Transaksi::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where(function($q) use ($dt) {
                        $q->where('reff_trx_id',$dt->trx_id)
                        ->orWhere('trx_id',$dt->trx_id);
                    })
                    ->orderBy('created_at','DESC')
                    ->get();
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {            
            $data = RawatJalan::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if (!$data) { 
                return response()->json(['success' => false, 'message' => 'Data registrasi rawat jalan tidak ditemukan.']); 
            }
            $pasien = Pasien::where('client_id',$this->clientId)->where('pasien_id',$data->pasien_id)->first();
            $pasien['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$data->pasien_id)->first();
            $data['pasien'] = $pasien;
            $data['transaksi'] = Transaksi::where('client_id',$this->clientId)
                ->where(function($q) use($id) {
                    $q->where('reff_trx_id',$id)
                    ->orWhere('trx_id',$id);
                })->where('is_aktif',1)->get();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran ds', 'error' => $e->getMessage()]);
        }
    }

    // Lists Registrasi pasien Penunjang berdasarkan jenis
    // public function listsRegJns(request $request, $jns)
    // {
    //     try {
            
    //         $data = Pendaftaran::leftjoin('tb_pasien AS tp', 'tb_registrasi.pasien_id', '=', 'tp.pasien_id')
    //             ->leftJoin('tb_unit AS tu', 'tb_registrasi.unit_id', '=', 'tu.unit_id')
    //             ->where('tb_registrasi.client_id', $this->clientId)
    //             ->where('tb_registrasi.jns_registrasi', $jns)
    //             ->orderBy('tb_registrasi.tgl_registrasi', 'DESC')->paginate(10);

    //         if (!$data) {
    //             return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
    //         }

    //         return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
    //     } 
    //     catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran rad', 'error' => $e->getMessage()]);
    //     }
    // }

    // public function getDataRegistrasi($id){
    //     try {
    //         $reg = Pendaftaran::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
    //         if(!$reg) { return null; }

    //         $reg['pasien'] = RegPasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
    //     } 
    //     catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan registrasi. Error : ' . $e->getMessage()]);
    //     }
    // }

    public function create(Request $request) {
        try {

            //return response()->json(['success' => false, 'message' => 'Disini oiii.']);
            /**
             * pembuatan data registrasi rawat jalan
             * yang harus diperhatikan :
             * tb_rawat_jalan (data harus ada)
             * tb_registrasi (data harus ada)
             * 
             */
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
            if(!$dokter){ return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']); }

            //check penjamin apakah aktif / tidak
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            if(!$penjamin){ return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']); }

            //check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {  return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);  }           
            $alamat = PasienAlamat::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            
            /**ambil prefix untuk no antrian */
            $prefixNo = DokterUnit::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)->where('unit_id',$request->unit_id)
                ->select('prefix_no_antrian')
                ->first();

            if(!$prefixNo) { return response()->json(['success' => false, 'message' => 'Jadwal gagal dibuat (0).' ]);  }           

            $jadwal = DokterJadwal::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('jadwal_id',$request->jadwal_id)
                ->first();

            if(!$jadwal) { return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.' ]); }
            
            // input tb_registrasi
            $regId = RegistrasiHelper::instance()->RegistrasiId($this->clientId); 
            $trxId = RegistrasiHelper::instance()->PoliTransactionId($this->clientId);
            $modeReg = RegistrasiHelper::instance()->ModeReg($request->tgl_periksa);
            $kodeBooking = RegistrasiHelper::instance()->BookingCode($this->clientId);
            $noAntrian = RegistrasiHelper::instance()->NoAntrian($this->clientId, $prefixNo->prefix_no_antrian, $request->tgl_periksa, $request->jadwal_id);
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir);        

            //return response()->json(['success' => false, 'message' => 'lalalala' ]);

            $reffTrxId = $request->reff_trx_id;
            $isRujukanInt = false;
            if($reffTrxId == null) { $reffTrxId = $trxId; }            
                        
            $noPendaftaran = null;
            if($modeReg == 'WALKIN') {
                $noPendaftaran = RegistrasiHelper::instance()->NoPendaftaran($this->clientId, $request->tgl_periksa);
            }

            /**hitung estimasi jam diperiksa */
            $time = strtotime($jadwal->mulai);
            $minute = $jadwal->interval_periksa;
            $inter = date('H:i', $time + ($minute * $noAntrian['angka']) - $minute); 
            $startTime = date('H:i', $time + (($minute * 60 * $noAntrian['angka'])) - ($minute * 60));
            
            DB::connection('dbclient')->beginTransaction();
            
            /**
             * create data transaksi
             */
            $transaksi                      = new Transaksi();
            $transaksi->reg_id              = $regId;
            $transaksi->trx_id              = $trxId;
            $transaksi->reff_trx_id         = $reffTrxId;
            // $transaksi->sub_trx_id          = $reffTrxId;
            $transaksi->status              = 'ANTRI';
            $transaksi->is_realisasi        = false;
            $transaksi->is_bayar            = false;
            $transaksi->is_aktif            = true;
            $transaksi->client_id           = $this->clientId;
            $transaksi->created_by          = Auth::user()->username;
            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->jns_transaksi       = 'RAWAT JALAN';
        
            //$transaksi->is_sub_trx          = false;
            $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
            $transaksi->no_antrian          = $noAntrian['id'];
            $transaksi->no_transaksi        = 'RJ/'.date('Ymd').'/'.$noAntrian['angka'];
            $transaksi->kelas_id            = $request->kelas_harga_id;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;
            $transaksi->kelas_penjamin_id   = $request->kelas_harga_id;
            $transaksi->penjamin_id         = $request->penjamin_id;
            $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksi->buku_harga          = $penjamin->buku_harga;
            $transaksi->total               = 0;
            
            $transaksi->unit_id             = $request->unit_id;
            $transaksi->unit_nama           = $unit->unit_nama;
            $transaksi->ruang_id            = $request->ruang_id;
            $transaksi->ruang_nama          = $ruang->ruang_nama;
            
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = '';
            
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            
            $transaksi->pasien_id           = $request->pasien_id;
            $transaksi->no_rm               = $pasien->no_rm;
            $transaksi->nama_pasien         = $pasien->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }
            
            /**
             * isi tb_registrasi
             */
            $dataReg                    = new Pendaftaran();
            $dataReg->reg_id            = $regId;
            $dataReg->tgl_registrasi    = date('Y-m-d H:i:s');
                
            $dataReg->jns_registrasi    = $unit->jenis_registrasi;
            $dataReg->cara_registrasi   = $request->cara_registrasi;
                
            $dataReg->tgl_periksa       = $request->tgl_periksa;
            $dataReg->jam_periksa       = $startTime;
            $dataReg->mode_reg          = $modeReg;
            $dataReg->kode_booking      = $kodeBooking;

            $dataReg->no_antrian        = $noAntrian['id'];
            $dataReg->no_urut           = $noAntrian['angka'];
            if($noPendaftaran) {
                $dataReg->no_pendaftaran    = $noPendaftaran['id'];
                $dataReg->tgl_checkin       = date('Y-m-d H:i:s');
                $dataReg->status_reg        = 'ANTRI';
            }
            else {
                $dataReg->status_reg        = 'BOOKING';
            }
            
            $dataReg->jadwal_id         = $jadwal->jadwal_id;
            $dataReg->dokter_id         = $dokter->dokter_id;
            $dataReg->unit_id           = $unit->unit_id;
            $dataReg->ruang_id          = $request->ruang_id;
            $dataReg->bed_id            = $ruang->ruang_nama;
            $dataReg->dokter_nama       = $dokter->dokter_nama;
            $dataReg->unit_nama         = $unit->unit_nama;
            
            $dataReg->asal_pasien       = $request->asal_pasien;
            $dataReg->ket_asal_pasien   = $request->ket_asal_pasien;
            
            $dataReg->pasien_id         = $pasien->pasien_id;
            $dataReg->nama_pasien       = $pasien->nama_pasien;
            $dataReg->jns_kelamin       = $pasien->jns_kelamin;
            $dataReg->nik               = $pasien->nik;
            $dataReg->no_kk             = $pasien->no_kk;
            $dataReg->no_rm             = $pasien->no_rm;
            $dataReg->tgl_lahir         = $pasien->tgl_lahir;
            $dataReg->tempat_lahir      = $pasien->tempat_lahir;
            $dataReg->usia              = $usia;
            
            $dataReg->jns_penjamin      = $pasien->jns_penjamin;
            $dataReg->penjamin_id       = $penjamin->penjamin_id;
            $dataReg->penjamin_nama     = $penjamin->penjamin_nama;
            $dataReg->is_bpjs           = $penjamin->is_bpjs;
                
            $dataReg->nama_penanggung   = strtoupper($request->nama_penanggung);
            $dataReg->hub_penanggung    = $request->hub_penanggung;

            $dataReg->is_pasien_luar    = $request->is_pasien_luar;
            $dataReg->is_pasien_baru    = $request->is_pasien_baru;
            $dataReg->is_aktif          = true;

            $dataReg->kelas_id          = $request->kelas_id;
            $dataReg->kelas_nama        = $request->kelas_nama;
            
            $dataReg->client_id         = $this->clientId;
            $dataReg->created_by        = Auth::user()->username;
            $dataReg->updated_by        = Auth::user()->username;

            $success = $dataReg->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menyimpan data antrian(2).' ]);
            }

            /**insert di registrasi pasien */
            $regPasien                  = new RegPasien();
            $regPasien->reg_id          = $regId;
            $regPasien->is_pasien_luar  = false;
            $regPasien->pasien_id       = $pasien->pasien_id;
            $regPasien->no_rm           = $pasien->no_rm;
            $regPasien->nama_pasien     = $pasien->nama_pasien;
            $regPasien->tempat_lahir    = $pasien->tempat_lahir;
            $regPasien->tgl_lahir       = $pasien->tgl_lahir;
            $regPasien->usia            = $usia;
            $regPasien->jns_kelamin     = $pasien->jns_kelamin;
            $regPasien->is_hamil        = $pasien->is_hamil;

            if($request->is_pasien_baru) { $regPasien->is_pasien_baru  = $request->is_pasien_baru; }
            else { $regPasien->is_pasien_baru  = false; }
            
            if($alamat){
                $regPasien->propinsi_id     = $alamat->propinsi_tinggal_id;
                $regPasien->kota_id         = $alamat->kota_tinggal_id;
                $regPasien->kecamatan_id    = $alamat->kecamatan_tinggal_id;
                $regPasien->kelurahan_id    = $alamat->kelurahan_tinggal_id;
                $regPasien->kodepos         = $alamat->kodepos_tinggal;;
            }
            $regPasien->is_aktif        = true;
            $regPasien->client_id       = $this->clientId;
            $regPasien->created_by      = Auth::user()->username;
            $regPasien->updated_by      = Auth::user()->username;

            $success = $regPasien->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
            }
           
            /**
             * insert ke tb rawat jalan
             */                
            $rawatJalan                      = new RawatJalan();
            $rawatJalan->reg_id              = $regId;
            $rawatJalan->trx_id              = $trxId;
            $rawatJalan->tgl_transaksi       = date('Y-m-d H:i:s');
            if($request->reff_trx_id) {
                $rawatJalan->reff_trx_id     = $request->reff_trx_id;
            }
            else {
                $rawatJalan->reff_trx_id     = $trxId;
            }
            $rawatJalan->cara_registrasi     = $request->cara_registrasi;
            $rawatJalan->asal_pasien         = $request->asal_pasien;
            $rawatJalan->ket_asal_pasien     = $request->ket_asal_pasien;
            
            $rawatJalan->jns_registrasi      = $unit->jns_registrasi;
            $rawatJalan->unit_id             = $request->unit_id;
            $rawatJalan->unit_nama           = $unit->unit_nama;
            $rawatJalan->ruang_id            = $request->ruang_id;
            $rawatJalan->ruang_nama          = $ruang->ruang_nama;

            $rawatJalan->jadwal_id           = $request->jadwal_id;
            $rawatJalan->dokter_id           = $dokter->dokter_id;
            $rawatJalan->dokter_nama         = $dokter->dokter_nama;            
            $rawatJalan->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $rawatJalan->dokter_pengirim     = $request->dokter_pengirim;
            
            $rawatJalan->tgl_periksa         = $request->tgl_periksa;
            $rawatJalan->jam_periksa         = $startTime;
            $rawatJalan->no_antrian          = $noAntrian['id'];

            $rawatJalan->hub_penanggung      = $request->hub_penanggung;
            $rawatJalan->nama_penanggung     = $request->nama_penanggung;
            
            $rawatJalan->penjamin_id         = $penjamin->penjamin_id;
            $rawatJalan->penjamin_nama       = $penjamin->penjamin_nama;
            $rawatJalan->jns_penjamin        = $penjamin->jns_penjamin;
            $rawatJalan->no_kepesertaan      = $request->no_kepesertaan;
            $rawatJalan->no_rujukan          = $request->no_rujukan;
            $rawatJalan->no_sep              = $request->no_sep;
            
            $rawatJalan->is_bpjs             = $penjamin->is_bpjs;

            $rawatJalan->kelas_harga_id      = $request->kelas_harga_id;
            $rawatJalan->kelas_harga         = $request->kelas_harga;
            $rawatJalan->buku_harga_id       = $penjamin->buku_harga_id;
            $rawatJalan->buku_harga          = $penjamin->buku_harga;
            $rawatJalan->kelas_penjamin_id   = $request->kelas_harga_id;
            
            $rawatJalan->pasien_id           = $pasien->pasien_id;
            $rawatJalan->no_rm               = $pasien->no_rm;
            $rawatJalan->nama_pasien         = $pasien->nama_lengkap;
            $rawatJalan->usia                = $usia;
            $rawatJalan->jns_kelamin         = $pasien->jns_kelamin;
            $rawatJalan->diag_awal           = $request->diag_awal;
            $rawatJalan->status              = 'ANTRI';
            $rawatJalan->is_aktif            = true;
            $rawatJalan->is_konfirmasi       = false;
            
            $rawatJalan->client_id           = $this->clientId;
            $rawatJalan->created_by          = Auth::user()->username;
            $rawatJalan->updated_by          = Auth::user()->username;
            
            $success = $rawatJalan->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data rawat jalan gagal disimpan.']);
            }

            if($pasien->is_satusehat == true || $request->is_satusehat == true){
                $this->createEncounter($request);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data rawat jalan berhasil disimpan.']);
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data registrasi. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->trx_id;
            $pasienId = $request->pasien_id;
            //check apakah data rawat jalan sudah ada
            $data = RawatJalan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('trx_id',$id)->first();

            if(!$data) { return response()->json(['success' => false, 'message' => 'Data registrasi rawat jalan tidak ditemukan.']); }

            if(!$data->is_pasien_baru) {
                $pasienId = $data->pasien_id;
            }

            $regId = $data->reg_id;
            $trxId = $id;

            if($data->status !== 'ANTRI') {
                return response()->json(['success' => false, 'message' => 'Data transaksi rawat jalan sudah berubah status. Data tidak dapat diubah.']);
            }

            $transaksi = Transaksi::where('trx_id',$id)->where('reg_id',$data->reg_id)->where('client_id',$this->clientId)->first();

            //check apakah data registrasi sudah ada
            $dataReg = Pendaftaran::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->first();                
            
            if(!$dataReg){ return response()->json(['success' => false, 'message' => 'Data registrasi tidak ditemukan.']); }

            /**periksa apakah data pasien ada */
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('pasien_id',$pasienId)->first();

            if(!$pasien) { return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']); }

            /**periksa apakah data penjamin ada */
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('penjamin_id',$request->penjamin_id)->first();

            if(!$penjamin) { return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']); }
            
            /**periksa apakah data unit pelayanan ada */
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)->first();

            if(!$unit) { return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']); }

            /** ambil data ruang */
            $ruang = RuangPelayanan::where('ruang_id',$request->ruang_id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)->first();

            if(!$ruang) { return response()->json(['success' => false, 'message' => 'Data Ruang tidak ditemukan.']); }     

            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)->first();

            if(!$dokter){ return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']); }
            
            //check apakah jadwal ada / tidak
            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)
                ->where('jadwal_id',$request->jadwal_id)
                ->first();

            if(!$jadwal){ return response()->json(['success' => false, 'message' => 'Data jadwal dokter tidak ditemukan.']); }
            
            //check kelengkapan data pasien di registrasi
            $regPasien = RegPasien::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('pasien_id',$data->pasien_id)
                ->where('reg_id',$data->reg_id)
                ->first();

            $alamat = PasienAlamat::where('client_id',$this->clientId)->where('pasien_id',$request->pasien_id)->first();
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir);

            DB::connection('dbclient')->beginTransaction();

            /**
             * update data transaksi
             */            
            if(!$transaksi) {
                $transaksi                      = new Transaksi();
                $transaksi->reg_id              = $regId;
                $transaksi->trx_id              = $trxId;
                $transaksi->reff_trx_id         = $request->reff_trx_id;
                $transaksi->status              = 'ANTRI';
                $transaksi->is_realisasi        = false;
                $transaksi->is_bayar            = false;
                $transaksi->is_aktif            = true;
                $transaksi->client_id           = $this->clientId;
                $transaksi->created_by          = Auth::user()->username;
                $transaksi->jns_transaksi       = 'RAWAT JALAN';
            
                //$transaksi->is_sub_trx          = false;
                $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
                $transaksi->no_antrian          = $data->no_antrian;//$noAntrian['id'];
                //$transaksi->no_transaksi        = 'RJ/'.date('Ymd').'/'.$noAntrian['angka'];
                
                $transaksi->penjamin_id         = $request->penjamin_id;
                $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
                $transaksi->total               = 0;
                
                $transaksi->unit_id             = $request->unit_id;
                $transaksi->unit_nama           = $unit->unit_nama;                
                $transaksi->dokter_id           = $request->dokter_id;
                $transaksi->dokter_nama         = $dokter->dokter_nama;
                $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
                $transaksi->dokter_pengirim     = '';
                
                $transaksi->unit_pengirim_id    = '';
                $transaksi->unit_pengirim       = '';
                
                $transaksi->pasien_id           = $request->pasien_id;
                $transaksi->no_rm               = $pasien->no_rm;
                $transaksi->nama_pasien         = $pasien->nama_pasien;
            }

            $transaksi->kelas_id            = $request->kelas_harga;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;
            $transaksi->kelas_penjamin_id   = $request->kelas_harga_id;
            $transaksi->penjamin_id         = $request->penjamin_id;
            $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksi->buku_harga          = $penjamin->buku_harga;
            
            $transaksi->ruang_id            = $request->ruang_id;
            $transaksi->ruang_nama          = $ruang->ruang_nama;
            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->updated_by          = Auth::user()->username;
            
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            $dataReg->cara_registrasi   = $request->cara_registrasi;            
            
            $dataReg->ruang_id          = $request->ruang_id;
            $dataReg->ruang_nama        = $ruang->ruang_nama;
                
            $dataReg->asal_pasien       = $request->asal_pasien;
            $dataReg->ket_asal_pasien   = $request->ket_asal_pasien;

            $dataReg->nama_pasien       = $pasien->nama_pasien;
            $dataReg->jns_kelamin       = $pasien->jns_kelamin;
            $dataReg->nik               = $pasien->nik;
            $dataReg->no_kk             = $pasien->no_kk;
            $dataReg->no_rm             = $pasien->no_rm;
            $dataReg->tgl_lahir         = $pasien->tgl_lahir;
            $dataReg->tempat_lahir      = $pasien->tempat_lahir;
            $dataReg->usia              = $usia;
            
            $dataReg->jns_penjamin      = $pasien->jns_penjamin;
            $dataReg->penjamin_id       = $penjamin->penjamin_id;
            $dataReg->penjamin_nama     = $penjamin->penjamin_nama;
            $dataReg->is_bpjs           = $penjamin->is_bpjs;

            $dataReg->nama_penanggung   = $request->nama_penanggung;
            $dataReg->hub_penanggung    = $request->hub_penanggung;
            $dataReg->status_reg        = 'ANTRI';
            $dataReg->updated_by        = Auth::user()->username;
            $success = $dataReg->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'Data registrasi gagal diupdate.']);
            }

            /**reg pasien */
            if(!$regPasien) {                
                $regPasien                  = new RegPasien();
                $regPasien->reg_id          = $id;
                $regPasien->is_aktif        = true;
                $regPasien->client_id       = $this->clientId;
                $regPasien->created_by      = Auth::user()->username;
            }
            $regPasien->is_pasien_luar  = $pasien->is_pasien_luar;
            $regPasien->pasien_id       = $pasien->pasien_id;
            $regPasien->no_rm           = $pasien->no_rm;
            $regPasien->nama_pasien     = $pasien->nama_pasien;
            $regPasien->tempat_lahir    = $pasien->tempat_lahir;
            $regPasien->tgl_lahir       = $pasien->tgl_lahir;
            $regPasien->usia            = $usia;
            $regPasien->jns_kelamin     = $pasien->jns_kelamin;
            $regPasien->propinsi_id     = $alamat->propinsi_tinggal_id;
            $regPasien->kota_id         = $alamat->kota_tinggal_id;
            $regPasien->kecamatan_id    = $alamat->kecamatan_tinggal_id;
            $regPasien->kelurahan_id    = $alamat->kelurahan_tinggal_id;
            $regPasien->kodepos         = $alamat->kodepos_tinggal;;
            $regPasien->is_hamil        = $pasien->is_hamil;
            if($request->is_pasien_baru) { $regPasien->is_pasien_baru  = $request->is_pasien_baru; }
            else { $regPasien->is_pasien_baru  = false; }
            $regPasien->updated_by      = Auth::user()->username;
            $success = $regPasien->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
            } 

            /**update rawat jalan*/
            
            $data->ruang_id         = $request->ruang_id;
            $data->ruang_nama       = $ruang->ruang_nama;
            $data->penjamin_id      = $penjamin->penjamin_id;
            $data->penjamin_nama    = $penjamin->penjamin_nama;
            $data->jns_penjamin     = $penjamin->jns_penjamin;
            $data->no_kepesertaan   = $request->no_kepesertaan;
            $data->no_rujukan       = $request->no_rujukan;
            $data->no_sep           = $request->no_sep;
            
            $data->is_bpjs          = $penjamin->is_bpjs;

            $data->kelas_harga_id   = $request->kelas_harga_id;
            $data->kelas_harga      = $request->kelas_harga;
            $data->buku_harga_id    = $penjamin->buku_harga_id;
            $data->buku_harga       = $penjamin->buku_harga;
            $data->kelas_penjamin_id = $request->kelas_harga_id;
            
            $data->pasien_id        = $pasien->pasien_id;
            $data->no_rm            = $pasien->no_rm;
            $data->nama_pasien      = $pasien->nama_lengkap;
            $data->usia             = $usia;
            $data->jns_kelamin      = $pasien->jns_kelamin;
                        
            $data->diag_awal        = $request->diag_awal;
            $data->status           = 'ANTRI';
            $data->updated_by       = Auth::user()->username;
            $success                = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data rawat jalan gagal disimpan.']);
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil diubah.']);
            
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data registrasi. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = RawatJalan::where('trx_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }

            if(!$data->status == 'ANTRI' ) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data sudah berubah status.']);
            }

            $dataReg = Pendaftaran::where('reg_id', $data->reg_id)->where('client_id', $this->clientId)->first();
            if (!$dataReg) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data registrasi tidak ditemukan.']);
            }

            $transaksi = Transaksi::where('reg_id', $data->reg_id)->where('trx_id', $data->trx_id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            $detailTrx = TransaksiDetail::where('reg_id', $data->reg_id)->where('trx_id', $data->trx_id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            $detailTrxKomp = TransaksiDetailKomp::where('reg_id', $data->reg_id)->where('trx_id', $data->trx_id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            

            DB::connection('dbclient')->beginTransaction();
            $dataReg->updated_by = Auth::user()->username;
            $dataReg->is_aktif = 0;
            $success = $dataReg->save();

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menonaktifkan data registrasi']);
            }

            $data->updated_by = Auth::user()->username;
            $data->is_aktif = 0;
            $data->status = 'BATAL';
            $success = $data->save();
            
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menonaktifkan data rawat jalan']);
            }

            if($transaksi) {
                $success = Transaksi::where('reg_id', $data->reg_id)
                    ->where('trx_id', $data->trx_id)
                    ->where('client_id', $this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username,'updated_at'=>date('Y-m-d H:i:s')]);

                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menonaktifkan data transaksi rawat jalan']);
                }
            }

            if($detailTrx) {
                $success = TransaksiDetail::where('reg_id', $data->reg_id)
                    ->where('trx_id', $data->trx_id)
                    ->where('client_id', $this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username,'updated_at'=>date('Y-m-d H:i:s')]);
                
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menonaktifkan transaksi detail rawat jalan']);
                }   
            }

            if($detailTrxKomp) {
                $success = TransaksiDetailKomp::where('reg_id', $data->reg_id)->where('trx_id', $data->trx_id)
                    ->where('client_id', $this->clientId)
                    ->where('is_aktif',1)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username,'updated_at'=>date('Y-m-d H:i:s')]);
            
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menonaktifkan transaksi detail komponen rawat jalan']);
                }  
            }
            DB::connection('dbclient')->commit();                
            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil dinonaktifkan', 'data' => $dataReg]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data registrasi gagal dinonaktifkan. Error :' . $e->getMessage()]);
        }
    }

    public function DokterJadwalByDay(Request $request) 
    {
        try {
            /**mengambil data poli dan jadwal dokter aktif pada tanggal periksa */
            $jenisReg = $request->input('jenis_reg');
            $today = Carbon::now();
            
            if ($request->has('tanggal')) {
                $tglPeriksa = $request->input('tanggal');
                if(strtoupper($tglPeriksa) !== 'UNDEFINED') {
                    $today = $tglPeriksa;
                }
            }
            
            $timestamp = strtotime($today);
            $day = date('w',$timestamp);
            /**ambil jadwal dokter di hari tersebut */
            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('hari',$day)->select('unit_id')->groupBy('unit_id')->get();           

            if(!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Tidak ada jadwal dokter hari ini']);
            }

            $list = UnitPelayanan::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('jenis_registrasi',$jenisReg)
                ->whereIn('unit_id',$jadwal)
                ->select('unit_id','unit_nama')
                ->get();
            
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Jadwal Unit Pelayanan (poli) tidak ditemukan']);
            }       

            foreach($list as $item) {
                /**tambahkan data ruang*/
                $item['ruang'] = RuangPelayanan::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('unit_id',$item['unit_id'])->get();
                
                $dokter = DokterJadwal::leftJoin('tb_dokter','tb_dokter_jadwal.dokter_id','=','tb_dokter.dokter_id')
                    ->where('tb_dokter_jadwal.unit_id',$item['unit_id'])
                    ->where('tb_dokter_jadwal.is_aktif',1)
                    ->where('tb_dokter.is_aktif',1)
                    ->where('tb_dokter_jadwal.hari',$day)
                    ->where('tb_dokter_jadwal.client_id',$this->clientId)
                    ->select(
                        'tb_dokter_jadwal.jadwal_id',
                        'tb_dokter_jadwal.unit_id',
                        'tb_dokter_jadwal.hari',
                        'tb_dokter_jadwal.mulai',
                        'tb_dokter_jadwal.selesai',
                        'tb_dokter_jadwal.nama_hari',
                        'tb_dokter.*'
                    )->get();

                if($dokter) { $item['dokter'] = $dokter; }
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        }
        
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    // public function getRegId($clientId)
    // {
    //     try {
    //         $id = $this->clientId.'-'.date('Ymd').'-00001';
    //         $maxId = Pendaftaran::withTrashed()
    //             ->where('client_id', $this->clientId)
    //             ->where('reg_id', 'LIKE', $this->clientId.'-'.date('Ymd').'-%')->max('reg_id');
    //         if (!$maxId) {
    //             $id = $this->clientId.'-'.date('Ymd').'-00001';
    //         } else {
    //             $maxId = str_replace($this->clientId.'-'.date('Ymd').'-', '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-0000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-00' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-0' . $count; } 
    //             else { $id = $this->clientId.'-'.date('Ymd').'-' . $count; }
    //         }
    //         return $id;
    //     } catch (\Exception $e) {
    //         return $this->clientId . date('Ymd') . '-' . Uuid::uuid4()->getHex();
    //     }
    // }

    // public function getBookingCode()
    // {
    //     try {
    //         $id = date('ymd').'00001';
    //         $maxId = Pendaftaran::where('client_id', $this->clientId)
    //             ->where('is_aktif',1)
    //             ->where('kode_booking', 'LIKE', date('ymd').'%')
    //             ->max('kode_booking');
            
    //         if (!$maxId) { $id = date('ymd').'00001'; } 
    //         else {
    //             $maxId = str_replace( date('ymd'), '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = date('Ymd').'0000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = date('Ymd').'000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = date('Ymd').'00' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = date('Ymd').'0' . $count; } 
    //             else { $id = date('Ymd'). $count; }
    //         }
    //         return $id;
    //     } 
    //     catch (\Exception $e) { return null; }
    // }

    // public function getAntrianNo($prefixNo, $tglPeriksa, $jadwalId)
    // {
    //     try {
    //         $id = $prefixNo.'001';
    //         $maxId = Pendaftaran::where('client_id', $this->clientId)
    //             ->withTrashed()
    //             ->where('no_antrian','ILIKE',$prefixNo.'%')
    //             ->where('tgl_periksa',$tglPeriksa)
    //             ->where('jadwal_id',$jadwalId)
    //             ->max('no_antrian');
            
    //         if (!$maxId) { $id = $prefixNo.'001'; } 
    //         else {
    //             $maxId = str_replace( $prefixNo, '', $maxId);
    //             $count = $maxId + 1;
    //             if ($count < 10) { $id = $prefixNo.'00' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $prefixNo.'0' . $count; } 
    //             else { $id = $prefixNo . $count; } 
    //         }
    //         return $id;
    //     } 
    //     catch (\Exception $e) { return null; }
    // }

    // public function createRegPasien($request, $clientId, $regId)
    // {
    //     try {
    //         $pasien = Pasien::where('pasien_id', $request->pasien_id)
    //             ->where('client_id', $this->clientId)
    //             ->where('is_aktif', 1)
    //             ->first();

    //         if (!$pasien) {
    //             return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data pasien']);
    //         }

    //         $alamat = PasienAlamat::where('pasien_id', $request->pasien_id)
    //         ->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
    //         if (!$alamat) {
    //             return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil alamat pasien']);
    //         }

    //         $tgl_lahir = $pasien[0]['tgl_lahir'];
    //         $tgl_lahir = explode("-", $tgl_lahir);
    //         $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
    //             ? ((date("Y") - $tgl_lahir[0]) - 1)
    //             : (date("Y") - $tgl_lahir[0]));

    //         $data                   = new RegPasien();
    //         $data->reg_id           = $regId;
    //         $data->is_pasien_luar   = $request->is_pasien_luar;
    //         $data->pasien_id        = $request->pasien_id;
    //         $data->no_rm            = $pasien[0]['no_rm'];
    //         $data->nama_pasien      = $pasien[0]['nama_pasien'];
    //         $data->tempat_lahir     = $pasien[0]['tempat_lahir'];
    //         $data->tgl_lahir        = $pasien[0]['tgl_lahir'];
    //         $data->usia             = $usia;
    //         $data->jns_kelamin      = $pasien[0]['jns_kelamin'];
    //         $data->propinsi_id      = $alamat[0]['propinsi_id'];
    //         $data->kota_id          = $alamat[0]['kabupaten_id'];
    //         $data->kecamatan_id     = $alamat[0]['kecamatan_id'];
    //         $data->kelurahan_id     = $alamat[0]['kelurahan_id'];
    //         $data->kodepos          = $alamat[0]['kodepos'];
    //         $data->is_hamil         = $pasien[0]['is_hamil'];
    //         $data->is_pasien_baru   = $request->is_pasien_baru;
    //         $data->client_id        = $this->clientId;
    //         $data->is_aktif         = true;
    //         $data->created_by       = Auth::user()->username;
            
    //         $success                = $data->save();
            
    //         if (!$success) { return false; }            
    //         return true;

    //     } 
    //     catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Ada kesalahan saat proses data registrasi pasien. Error : ' . $e->getMessage()]);
    //     }
    // }

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

    public function confirm(Request $request)
    {   
        try {
            $regId = $request->reg_id;
            $trxId = $request->trx_id;

            $data = RawatJalan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->first();                
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi Poli tidak ditemukan.']);
            }
            
            $reg = Pendaftaran::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$regId)->first();                
            if(!$reg) {
                return response()->json(['success' => false, 'message' => 'Data registrasi tidak ditemukan.']);
            }

            $transaksi = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->first();     
            
            
            DB::connection('dbclient')->beginTransaction();

            $data->status = 'PERIKSA';
            $data->tgl_periksa = date('Y-m-d');
            $data->jam_periksa = date('H:i:s');
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data registrasi poli gagal diupdate.']);
            }

            $reg->status_reg = 'PERIKSA';
            $reg->tgl_periksa = date('Y-m-d');
            $reg->jam_periksa = date('H:i:s');
            $reg->updated_by = Auth::user()->username;
            $success = $reg->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data registrasi gagal diupdate.']);
            }

            /**
             * insert/update transaksi
             */

            if(!$transaksi) {
                $transaksi                      = new Transaksi();
                $transaksi->reg_id              = $regId;
                $transaksi->trx_id              = $trxId;
                $transaksi->reff_trx_id         = $data->reff_trx_id;
                $transaksi->status              = 'ANTRI';
                $transaksi->is_realisasi        = false;
                $transaksi->is_bayar            = false;
                $transaksi->is_aktif            = true;
                $transaksi->client_id           = $this->clientId;
                $transaksi->created_by          = Auth::user()->username;
                $transaksi->updated_by          = Auth::user()->username;
                $transaksi->jns_transaksi       = 'RAWAT JALAN';
            }
            $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
            $transaksi->no_antrian          = $data->no_antrian;
            $transaksi->no_transaksi        = 'RJ/'.date('Ymd').'/'.$data->no_antrian;
            $transaksi->kelas_id            = $data->kelas_harga_id;
            $transaksi->kelas_harga_id      = $data->kelas_harga_id;

            $transaksi->kelas_penjamin_id   = $data->kelas_harga_id;
            $transaksi->penjamin_id         = $data->penjamin_id;
            $transaksi->penjamin_nama       = $data->penjamin_nama;
            $transaksi->buku_harga_id       = $data->buku_harga_id;
            $transaksi->buku_harga          = $data->buku_harga;
            $transaksi->total               = 0;
            
            $transaksi->unit_id             = $data->unit_id;
            $transaksi->unit_nama           = $data->unit_nama;
            $transaksi->ruang_id            = $data->ruang_id;
            $transaksi->ruang_nama          = $data->ruang_nama;
            
            $transaksi->dokter_id           = $data->dokter_id;
            $transaksi->dokter_nama         = $data->dokter_nama;
            $transaksi->dokter_pengirim_id  = $data->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = '';
            
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            
            $transaksi->pasien_id           = $data->pasien_id;
            $transaksi->no_rm               = $data->no_rm;
            $transaksi->nama_pasien         = $data->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            // input TRX Detail / Pemeriksaan
            if($request->tindakan){
                foreach($request->tindakan as $key => $value) {
                    $trxDetail                      = new TransaksiDetail();
                    $trxDetail->detail_id           = $this->getDetailId();
                    $trxDetail->reg_id              = $regId;
                    $trxDetail->trx_id              = $trxId;
                    $trxDetail->jns_transaksi       = 'TINDAKAN';
                    $trxDetail->kelas_harga_id      = $data->kelas_harga_id;
                    $trxDetail->buku_harga_id       = $data->buku_harga_id;
                    $trxDetail->penjamin_id         = $data->penjamin_id;
                    $trxDetail->item_id             = $value['tindakan_id'];
                    $trxDetail->item_nama           = $value['tindakan_nama'];
                    $trxDetail->jumlah              = $value['jumlah'];
                    $trxDetail->satuan              = $value['satuan'];
                    // NILAI TINDAKAN BELUM ADA
                    $trxDetail->nilai               = 0;
                    $trxDetail->diskon_persen       = 0;
                    $trxDetail->diskon              = 0;
                    $trxDetail->harga_diskon        = 0;
                    $trxDetail->subtotal            = 0;
                    $trxDetail->dokter_id           = $dokter->dokter_id;
                    $trxDetail->dokter_pengirim_id  = $dokter->dokter_id;
                    $trxDetail->is_hitung_adm       = 'False';
                    // $trxDetail->group_tagihan       = $request->group_tagihan;
                    // $trxDetail->group_eklaim        = $request->group_eklaim;
                    // $trxDetail->rl_code             = $request->rl_code;
                    
                    $trxDetail->is_aktif            = true;
                    $trxDetail->client_id           = $this->clientId;
                    $trxDetail->created_by          = Auth::user()->username;
                    $trxDetail->updated_by          = Auth::user()->username;

                    $success = $trxDetail->save();
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail pendaftaran']);
                    }
                }
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil dikonfirmasi']);
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat konfirmasi registrasi pasien. Error : ' . $e->getMessage()]);
        }

    }

    public function getBridgingLogId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-LOG00001';
            $maxId = BridgingLog::withTrashed()->where('client_id', $this->clientId)->where('bridging_log_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-LOG%')->max('bridging_log_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ym').'-LOG00001';
            } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-LOG', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-LOG0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-LOG000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-LOG00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-LOG0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-LOG' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    // public function getSubTransactionId($trxId,$prefix)
    // {
    //     try {
    //         $id = $this->clientId.'-'.$prefix.date('Ym').'-00001';
    //         $maxId = Transaksi::withTrashed()
    //             ->where('client_id', $this->clientId)
    //             ->where('sub_trx_id', 'ILIKE', $this->clientId.'-'.$prefix.date('Ym').'-%')->max('sub_trx_id');

    //         if (!$maxId) {
    //             $id = $this->clientId.'-'.$prefix.date('Ym').'-00001';
    //         } 
    //         else {
    //             $maxId = str_replace($this->clientId.'-'.$prefix.date('Ym').'-', '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = $this->clientId.'-'.$prefix.date('Ym').'-0000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.$prefix.date('Ym').'-000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.$prefix.date('Ym').'-00' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.$prefix.date('Ym').'-0' . $count; } 
    //             else { $id = $this->clientId.'-'.$prefix.date('Ym').'-' . $count; }
    //         }
    //         return $id;
    //     } 
    //     catch (\Exception $e) {
    //         return null;
    //     }
    // }

    // public function createData(Request $request) 
    //{
        //     try {
        //         // MAKE TRX ID
        //         $trx_id = '';
        //         if($request->trx_id == '' or $request->trx_id == null){
        //             $trx_id = $this->clientId . '-' . date('Ymd') . '-' . Uuid::uuid4()->getHex();
        //         } else {
        //             $trx_id = $request->trx_id;
        //         }
    
        //         // GET PENJAMIN
        //         if(!$request->penjamin_id) {
        //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data penjamin','error'=>'penjamin id tidak ditemukan.']);
        //         }
    
        //         $penjamin = DB::connection('dbclient')->table('tb_penjamin')->select('penjamin_nama','penjamin_id')
        //                     ->where('client_id',$this->clientId)
        //                     ->where('is_aktif',1)
        //                     ->where('penjamin_id',  $request->penjamin_id)->first();
                
        //         // GET PASIEN
        //         if(!$request->pasien_id) {
        //             return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data pasien','error'=>'pasien id tidak ditemukan.']);
        //         }
        //         $pasien = DB::connection('dbclient')->table('tb_pasien')
        //                     ->where('client_id',$this->clientId)
        //                     ->where('is_aktif',1)
        //                     ->where('pasien_id',  $request->pasien_id)->first();
             
        //         //check apakah unit yang dipilih masih aktif dan ada
        //         $unit = UnitPelayanan::where('client_id', $this->clientId)
        //             ->where('is_aktif',1)
        //             ->where('unit_id', $request->unit_id)
        //             ->first();
    
        //         if(!$unit) {
        //             return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
        //         }
                
        //         // check apakah dokter ada dan aktif
        //         $dokter = Dokter::where('client_id',$this->clientId)
        //             ->where('is_aktif',1)
        //             ->where('dokter_id',$request->dokter_id)
        //             ->first();
    
        //         if(!$dokter){
        //             return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
        //         }
    
        //         // check apakah referensi rawat jalan /  inap ada.
        //         if($request->asal_data_pasien == 'RAWAT_JALAN' || $request->asal_data_pasien == 'RAWAT_INAP') {
        //             $refPasien = Pendaftaran::where('client_id',$this->clientId)
        //                 ->where('is_aktif',1)
        //                 ->where('reg_id',$request->ref_reg_id)
        //                 ->first();
    
        //             if(!$refPasien){
        //                 return response()->json(['success' => false, 'message' => 'Referensi ID registrasi pasien tidak ditemukan.']);
        //             }
        //         }
    
        //         // check prefix antrian dokter
        //         $prefixNo = DokterUnit::where('client_id',$this->clientId)->where('is_aktif',1)
        //             // ->where('dokter_unit_id',$request->dokter_unit_id)
        //             ->where('dokter_id',$request->dokter_id)
        //             ->select('prefix_no_antrian')
        //             ->first();
        //         if(!$prefixNo) {
        //             return response()->json(['success' => false, 'message' => 'Data prefix no antrian dokter tidak ditemukan.']);
        //         }
    
        //         // check penjamin
        //         $penjamin = Penjamin::where('client_id', $this->clientId)
        //             ->where('is_aktif',1)
        //             ->where('penjamin_id', $request->penjamin_id)
        //             ->first();
    
        //         if(!$penjamin) {
        //             return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
        //         }
                
        //         $id = $this->getRegId($this->clientId);
        //         $bookingCode = $this->getBookingCode();
        //         $noAntrian = $this->getAntrianNo($prefixNo->prefix_no_antrian, $request->tgl_periksa, $request->jadwal_id);
    
        //         // input tb_registrasi
        //         DB::connection('dbclient')->beginTransaction();
        //         $dataReg = new Pendaftaran();
        //         $dataReg->reg_id = $id;
        //         $dataReg->tgl_registrasi    = date('Y-m-d H:i:s');
        //         $dataReg->tgl_periksa       = $request->tgl_periksa;
        //         $dataReg->jns_registrasi    = $request->jns_registrasi;
        //         $dataReg->cara_registrasi   = $request->cara_registrasi;
        //         $dataReg->kode_booking      = $bookingCode;
        //         $dataReg->no_antrian        = $noAntrian;
                
        //         $dataReg->jadwal_id         = $request->jadwal_id;
        //         $dataReg->dokter_id         = $request->dokter_id;
        //         $dataReg->unit_id           = $request->unit_id;
        //         $dataReg->ruang_id          = $request->ruang_id;
        //         $dataReg->bed_id            = $request->bed_id;
                
        //         $dataReg->asal_pasien       = $request->asal_pasien;
        //         $dataReg->ket_asal_pasien   = $request->ket_asal_pasien;
                
        //         $dataReg->pasien_id         = $request->pasien_id;
                
        //         $dataReg->jns_penjamin      = $request->jns_penjamin;
        //         $dataReg->penjamin_id       = $request->penjamin_id;
        //         $dataReg->nama_penanggung   = $request->nama_penanggung;
        //         $dataReg->hub_penanggung    = $request->hub_penanggung;
        //         $dataReg->is_bpjs           = $penjamin->is_bpjs;
        //         $dataReg->is_pasien_baru    = $request->is_pasien_baru;
        //         $dataReg->status_reg        = 'DAFTAR';
        //         $dataReg->is_aktif          = true;
    
        //         $dataReg->client_id         = $this->clientId;
        //         $dataReg->created_by        = Auth::user()->username;
        //         $dataReg->updated_by        = Auth::user()->username;
        //         $success = $dataReg->save();
        //         if (!$success) {
        //             DB::connection('dbclient')->rollBack();
        //             return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data registrasi']);
        //         }
    
        //         /** catat data pasien dan update **/
        //         if(!$request->is_pasien_luar) {
        //             $alamat = PasienAlamat::where('client_id',$this->clientId)->where('pasien_id',$request->pasien_id)->first();
                    
        //             /** hitung usia **/
        //             $tgl_lahir = $pasien->tgl_lahir;
        //             $tgl_lahir = explode("-", $tgl_lahir);
        //             $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
        //                 ? ((date("Y") - $tgl_lahir[0]) - 1)
        //                 : (date("Y") - $tgl_lahir[0]));
                    
        //             $regPasien = new RegPasien();
        //             $regPasien->reg_id = $id;
        //             $regPasien->is_pasien_luar = $pasien->is_pasien_luar;
        //             $regPasien->pasien_id = $pasien->pasien_id;
        //             $regPasien->no_rm = $pasien->no_rm;
        //             $regPasien->nama_pasien = $pasien->nama_pasien;
        //             $regPasien->tempat_lahir = $pasien->tempat_lahir;
        //             $regPasien->tgl_lahir = $pasien->tgl_lahir;
        //             $regPasien->usia = $usia;
        //             $regPasien->jns_kelamin = $pasien->jns_kelamin;
        //             $regPasien->propinsi_id = $alamat->propinsi_tinggal_id;
        //             $regPasien->kota_id = $alamat->kota_tinggal_id;
        //             $regPasien->kecamatan_id = $alamat->kecamatan_tinggal_id;
        //             $regPasien->kelurahan_id = $alamat->kelurahan_tinggal_id;
        //             $regPasien->kodepos = $alamat->kodepos_tinggal;;
        //             $regPasien->is_hamil = $pasien->is_hamil;
        //             $regPasien->is_pasien_baru = $request->is_pasien_baru;
        //             $regPasien->is_aktif = true;
        //             $regPasien->client_id = $this->clientId;
        //             $regPasien->created_by = Auth::user()->username;
        //             $regPasien->updated_by = Auth::user()->username;
    
        //             $success = $regPasien->save();
        //             if(!$success) {
        //                 DB::connection('dbclient')->rollBack();
        //                 return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
        //             }                      
        //         }
    
        //         else {
        //             $pasien = $request->pasien;
        //             /** hitung usia **/
        //             $tgl_lahir = $pasien['tgl_lahir'];
        //             $tgl_lahir = explode("-", $tgl_lahir);
        //             $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
        //                 ? ((date("Y") - $tgl_lahir[0]) - 1)
        //                 : (date("Y") - $tgl_lahir[0]));
                    
        //             $regPasien = new RegPasien();
        //             $regPasien->reg_id = $id;
        //             $regPasien->is_pasien_luar = true;
        //             $regPasien->pasien_id = 'PL'.date('YmdHis').Str::random(5);
        //             $regPasien->no_rm = 'PL'.date('YmdHis').Str::random(5);
        //             $regPasien->nama_pasien = $pasien['nama_pasien'];
        //             $regPasien->tempat_lahir = $pasien['tempat_lahir'];
        //             $regPasien->tgl_lahir = $pasien['tgl_lahir'];
        //             $regPasien->usia = $usia;
        //             $regPasien->jns_kelamin = $pasien['jns_kelamin'];
        //             $regPasien->propinsi_id = null;
        //             $regPasien->kota_id = null;
        //             $regPasien->kecamatan_id = null;
        //             $regPasien->kelurahan_id = null;
        //             $regPasien->kodepos = null;
        //             $regPasien->is_hamil = $pasien['is_hamil'];
        //             $regPasien->is_pasien_baru = $pasien['is_pasien_baru'];
        //             $regPasien->is_aktif = true;
        //             $regPasien->client_id = $this->clientId;
        //             $regPasien->created_by = Auth::user()->username;
        //             $regPasien->updated_by = Auth::user()->username;
                    
        //             $success = $regPasien->save();
        //             if(!$success) {
        //                 DB::connection('dbclient')->rollBack();
        //                 return response()->json(['success' => false, 'message' => 'Data pasien luar gagal disimpan.']);
        //             }                      
        //         }
    
        //         // input tb_sep
        //         // $sep = new SEP();
        //         // $sep->reg_id = $id;
        //         // $sep->no_sep = $request->no_sep;
        //         // $sep->tgl_sep = $request->tgl_sep;
        //         // $sep->pasien_id = $request->pasien_id;
        //         // $sep->no_rm = $request->no_rm;
        //         // $sep->no_kepesertaan = $request->no_kepesertaan;
        //         // $sep->diag_awal = $request->diag_awal;
        //         // $sep->catatan = $request->catatan;
        //         // $sep->sep_request = $request->sep_request;
        //         // $sep->sep_response = $request->sep_response;
        //         // $sep->is_aktif = $request->is_aktif;
        //         // $sep->client_id = $this->clientId;
        //         // $sep->created_by = Auth::user()->username;
    
        //         // $success = $sep->save();
        //         // if (!$success) {
        //         //     DB::connection('dbclient')->rollBack();
        //         //     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data sep pasien']);
        //         // }
                
        //         // TRANSAKSI
        //         $transaksi = new Transaksi();
        //         $transaksi->reg_id              = $id;
        //         $transaksi->trx_id              = $trx_id;
        //         $transaksi->sub_trx_id          = $trx_id;
        //         $transaksi->is_sub_trx          = 'false';
        //         $transaksi->jns_transaksi       = $request->jns_registrasi;
        //         $transaksi->tgl_transaksi       = $request->tgl_periksa;
        //         $transaksi->no_antrian          = "-";
        //         $transaksi->no_transaksi        = "-";
        //         $transaksi->kelas_id            = "-";
        //         $transaksi->kelas_harga_id      = "-";
        //         $transaksi->kelas_penjamin_id   = "-";
        //         $transaksi->penjamin_id         = $penjamin->penjamin_id;
        //         $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
        //         $transaksi->unit_id             = $unit->unit_id;
        //         $transaksi->unit_nama           = $unit->unit_nama;
        //         $transaksi->ruang_id            = "-";
        //         $transaksi->ruang_nama          = "-";
        //         $transaksi->dokter_id           = $dokter->dokter_id;
        //         $transaksi->dokter_nama         = $dokter->dokter_nama;
        //         $transaksi->dokter_pengirim_id  = "-";
        //         $transaksi->dokter_pengirim     = "-";
        //         $transaksi->unit_pengirim_id    = "-";
        //         $transaksi->unit_pengirim       = "-";
        //         $transaksi->pasien_id           = $pasien->pasien_id;
        //         $transaksi->no_rm               = $pasien->no_rm;
        //         $transaksi->nama_pasien         = $pasien->nama_pasien;
        //         $transaksi->is_realisasi        = "false";
        //         $transaksi->is_bayar            = "false";
    
        //         $transaksi->status              = 'DAFTAR';
        //         $transaksi->is_aktif            = 'True';
    
        //         $transaksi->client_id           = $this->clientId;
        //         $transaksi->created_by          = Auth::user()->username;
        //         $transaksi->updated_by          = Auth::user()->username;
    
        //         $success = $transaksi->save();
        //         if (!$success) {
        //             DB::connection('dbclient')->rollBack();
        //             return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data transaksi radiologi']);
        //         }
    
        //         // input TRX Detail / Pemeriksaan
        //         if($request->tindakan){
        //             foreach($request->tindakan as $key => $value){
        //                 $trxDetail = new TransaksiDetail();
        //                 $trxDetail->detail_id           = $this->getDetailId();
        //                 $trxDetail->reg_id              = $id;
        //                 $trxDetail->trx_id              = $trx_id;
        //                 $trxDetail->sub_trx_id          = $trx_id;
        //                 $trxDetail->jns_transaksi       = $request->jns_registrasi;
        //                 $trxDetail->kelas_harga_id      = "KLS-0001";
        //                 $trxDetail->buku_harga_id       = "BK-0001";
        //                 $trxDetail->penjamin_id         = $penjamin->penjamin_id;
        //                 $trxDetail->item_id             = $value['tindakan_id'];
        //                 $trxDetail->item_nama           = $value['tindakan_nama'];
        //                 $trxDetail->jumlah              = $value['jumlah'];
        //                 $trxDetail->satuan              = $value['satuan'];
        //                 // NILAI TINDAKAN BELUM ADA
        //                 $trxDetail->nilai               = 0;
        //                 $trxDetail->diskon_persen       = 0;
        //                 $trxDetail->diskon              = 0;
        //                 $trxDetail->harga_diskon        = 0;
        //                 $trxDetail->subtotal            = 0;
        //                 $trxDetail->dokter_id           = $dokter->dokter_id;
        //                 $trxDetail->dokter_pengirim_id  = $dokter->dokter_id;
        //                 $trxDetail->is_hitung_adm       = 'False';
        //                 // $trxDetail->group_tagihan       = $request->group_tagihan;
        //                 // $trxDetail->group_eklaim        = $request->group_eklaim;
        //                 // $trxDetail->rl_code             = $request->rl_code;
                        
        //                 $trxDetail->is_aktif            = 'True';
    
        //                 $trxDetail->client_id           = $this->clientId;
        //                 $trxDetail->created_by          = Auth::user()->username;
        //                 $trxDetail->updated_by          = Auth::user()->username;
    
        //                 $success = $trxDetail->save();
        //                 if (!$success) {
        //                     DB::connection('dbclient')->rollBack();
        //                     return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail pendaftaran']);
        //                 }
        //             }
        //         }
    
        //         DB::connection('dbclient')->commit();
                
        //         return response()->json(['success' => true, 'message' => 'Data registrasi berhasil disimpan', 'data' => $dataReg]);
        //     } catch (\Exception $e) {
        //         return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan registrasi. Error : ' . $e->getMessage()]);
        //     }
        //}

        // SATUSEHAT

        public function getUrl()
        {
            $baseUrl = 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1';
            return $baseUrl;
        }

        public function createToken()
        {
            $response = Http::asForm()->post('https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials', [
                // 'client_id' => 'KQc4OiEXQ8XPP1UeMkxLnyZnAzooGLi6pyqR17ZPDG6hmlVt',
                // 'client_secret' => 'FHxvSs1IIGghiiuj641P9QCIAkQN0F4yVtD2Um89rqfMQpjm6GtZuQseAyVc7c51',
                'client_id' => '75gPnX96H7gsAFm6dYCZRmbGV7RdAahtVAunKAZ6spdtzgIo',
                'client_secret' => '94AbrhUt5AcrKqGBFPqKKKGjI67mJLG3yGTa4NJIJLZYKEjeBVEjV4hIDZyy7lmR'
            ]);
            $result = json_decode($response->getBody(), true);
            $value = $result['access_token'];
            return (string)$value;
        }

        public function createEncounter(Request $request) 
        {
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            
            $ruang  = RuangPelayanan::leftJoin('tb_bridging', 'tb_ruang.ruang_id', '=', 'tb_bridging.local_resource_id')
            ->where('tb_bridging.resource', 'Location')
            ->where('tb_bridging.local_resource_id', $request->ruang_id)
            ->where('tb_ruang.client_id',$this->clientId)->first();
            
            $unit  = UnitPelayanan::leftJoin('tb_bridging', 'tb_unit.unit_id', '=', 'tb_bridging.local_resource_id')
            ->where('tb_bridging.resource', 'Organization')
            ->where('tb_bridging.local_resource_id', $request->unit_id)
            ->where('tb_unit.client_id',$this->clientId)->first();

            $current = Carbon::now();
            $dt = $current->format('c');

            $trxId = RegistrasiHelper::instance()->PoliTransactionId($this->clientId);

            $data = [
                "resourceType" => "Encounter",
                "identifier" => [
                    [
                        // 10083042 adalah ID IHS Rumah Sakit yang didaftarkan di DTO.
                        // INI NANTI DIGANTI/
                        "system" => "http://sys-ids.kemkes.go.id/encounter/9af73311-b6c9-4359-8b4e-a16f04eb58da", 
                        "value" => $pasien->pasien_id // bebas. bisa menggunakan id pasien simrs, id ihs, atau nomor rm.
                        // "value" => "100000030009"
                    ]
                ],
                "status" => "arrived", // Pasien melakukan kunjungan
                "class" => [
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                    "code" => "AMB",
                    "display" => "ambulatory"
                ],
                "subject" => [
                    // ID IHS Pasien berhubung tidak bisa mendaftar maka menggunakan data yang sudah ada
                    "reference" => "Patient/100000030009", 
                    "display" => $pasien->nama_pasien
                    // "display" => "Bambang Santoso"
                ],
                "participant" => [
                    [
                        "type" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                        "code" => "ATND",
                                        "display" => "attender"
                                    ]
                                ]
                            ]
                        ],
                        "individual" => [
                            // ID IHS Dokter / Tenaga Medis N10000002 berhubung tidak bisa daftar maka menggunakan data yang sudah ada
                            "reference" => "Practitioner/N10000002",
                            "display" => $dokter->dokter_nama
                            // "display" => "Voigt"
                        ]
                    ]
                ],
                "period" => [
                    "start" => "2022-09-14T07:00:00+07:00" // INI NANTI DIHAPUS YA, DIGANTI BAWAHNYA
                    // "start" => $dt
                ],
                "location" => [
                    [
                        "location" => [
                            // ID Ruang / Location yang sudah didaftarkan
                            "reference" => "Location/" . $ruang->bridging_resource_id,
                            // "reference" => "Location/ef011065-38c9-46f8-9c35-d1fe68966a3e",
                            "display" => $ruang->bridging_resource_name
                        ]
                    ]
                ],
                "statusHistory" => [
                    [
                        "status" => "arrived",
                        "period" => [
                            "start" => "2022-09-14T07:00:00+07:00" // INI NANTI DIHAPUS YA, DIGANTI BAWAHNYA
                            // "start" => $dt
                        ]
                    ]
                ],
                "serviceProvider" => [
                     // 10083042 adalah ID IHS Rumah Sakit yang didaftarkan di DTO.
                     //INI NANTI DIGANTI.
                    "reference" => "Organization/9af73311-b6c9-4359-8b4e-a16f04eb58da"
                ]
            ];

            $result = Http::withToken($this->createToken())->post($this->getUrl().'/Encounter', $data, 200, [], JSON_UNESCAPED_SLASHES);
    
            $value = json_decode($result->getBody(), true);
            // return json_encode($value);

            // INSERT INTO tb_bridging_log
            $log = new BridgingLog();
            $log->bridging_log_id   = $this->getBridgingLogId();
            $log->bridging_type     = 'SATUSEHAT';
            $log->bridging_resource = 'Encounter';
            $log->bridging_phase    = 'arrived';
            $log->state             = 'SUCCESS';
            $log->data              = json_encode($data);
            $log->result            = json_encode($value);
            $log->ss_id             = $value['id'];
            $log->trx_id            = $trxId;
            $log->is_aktif          = true;
            $log->client_id         = $this->clientId;
            $log->created_by        = Auth::user()->username;
            $log->updated_by        = Auth::user()->username;

            $success = $log->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data log gagal disimpan.']);
            }
        }
        
}
