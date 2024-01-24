<?php

namespace Modules\AntrianBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\DokterJadwal;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\BedInap;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\PasienAlergi;
use Modules\MasterData\Entities\PasienKeluarga;
use Modules\MasterData\Entities\PasienDetail;

use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kelurahan;
use Modules\MasterData\Entities\Penjamin;

use Modules\AntrianBpjs\Entities\Registrasi;
use Modules\AntrianBpjs\Entities\RegistrasiPasien;

use RegistrasiHelper;
use Ramsey\Uuid\Uuid;
use DB;

use Modules\MasterData\Entities\Bridging;

class DummyKioskController extends Controller
{
    public $clientId;
    public $credentialJkn;
    public $credentialBpjs;
    public $RegHelper;


    public function __construct(Request $request)
    {
        if (!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'client tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
        $RegHelper = new RegistrasiHelper();
    }

    /**
     * get list doctor by date
     */
    public function GetListPoli(Request $request, $tglPeriksa = null)
    {
        try {
            $today = Carbon::now();
            if ($tglPeriksa) {
                if (strtoupper($tglPeriksa) !== 'UNDEFINED') {
                    $today = $tglPeriksa;
                }
            }

            $timestamp = strtotime($today);
            $day = date('w', $timestamp);

            $jadwal = DokterJadwal::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('hari', $day)->select('unit_id')->get();

            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Tidak ada jadwal dokter hari ini']);
            }

            $list = UnitPelayanan::where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->where('is_unit_kiosk', 1)
                ->whereIn('unit_id', $jadwal)
                ->select('unit_id', 'unit_nama')
                ->get();

            if (!$list) {
                return response()->json(['success' => false, 'message' => 'Jadwal Unit Pelayanan (poli) tidak ditemukan']);
            }

            foreach ($list as $item) {
                $jadwal = DokterJadwal::leftJoin('tb_dokter', 'tb_dokter_jadwal.dokter_id', '=', 'tb_dokter.dokter_id')
                    ->where('tb_dokter_jadwal.unit_id', $item['unit_id'])
                    ->where('tb_dokter_jadwal.is_aktif', 1)
                    ->where('tb_dokter.is_aktif', 1)
                    ->where('tb_dokter_jadwal.hari', $day)
                    ->where('tb_dokter_jadwal.client_id', $this->clientId)
                    ->select(
                        'tb_dokter_jadwal.jadwal_id',
                        'tb_dokter_jadwal.hari',
                        'tb_dokter_jadwal.nama_hari',
                        'tb_dokter_jadwal.mulai',
                        'tb_dokter_jadwal.selesai',
                        'tb_dokter.dokter_nama',
                        'tb_dokter.dokter_id',
                        'tb_dokter.url_avatar',
                    )->get();

                // if($jadwal) {
                //     foreach($jadwal as $jd) { $jd['unit_nama'] = $item['unit_nama']; }
                // }                            
                $item['jadwal'] = $jadwal;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function GetListPoliByIdAndDate(Request $request, $poliId,$tglPeriksa) {
        try {
            $today = Carbon::now();
            if ($tglPeriksa) {
                if (strtoupper($tglPeriksa) !== 'UNDEFINED') {
                    $today = $tglPeriksa;
                }
            }

            $timestamp = strtotime($today);
            $day = date('w', $timestamp);

            $jadwal = DokterJadwal::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('hari', $day)
                ->select('unit_id')
                ->where('unit_id',$poliId)
                ->first();

            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Tidak ada jadwal dokter di poli tersebut di tanggal '.$tglPeriksa]);
            }

            $list = UnitPelayanan::where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->where('is_unit_kiosk', 1)
                ->where('unit_id', $poliId)
                ->select('unit_id', 'unit_nama')
                ->get();

            if (!$list) {
                return response()->json(['success' => false, 'message' => 'Jadwal Unit Pelayanan (poli) tidak ditemukan']);
            }

            foreach ($list as $item) {
                $jadwal = DokterJadwal::leftJoin('tb_dokter', 'tb_dokter_jadwal.dokter_id', '=', 'tb_dokter.dokter_id')
                    ->where('tb_dokter_jadwal.unit_id', $item['unit_id'])
                    ->where('tb_dokter_jadwal.is_aktif', 1)
                    ->where('tb_dokter.is_aktif', 1)
                    ->where('tb_dokter_jadwal.hari', $day)
                    ->where('tb_dokter_jadwal.client_id', $this->clientId)
                    ->select(
                        'tb_dokter_jadwal.jadwal_id',
                        'tb_dokter_jadwal.hari',
                        'tb_dokter_jadwal.nama_hari',
                        'tb_dokter_jadwal.mulai',
                        'tb_dokter_jadwal.selesai',
                        'tb_dokter.dokter_nama',
                        'tb_dokter.dokter_id',
                        'tb_dokter.url_avatar',
                    )->get();       
                $item['jadwal'] = $jadwal;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * get Dokter Data
     */
    public function dataDokter(Request $request, $dokterId, $unitId)
    {
        try {
            $data = Dokter::where('client_id', $this->clientId)
                ->where('dokter_id', $dokterId)
                ->select('dokter_id', 'dokter_nama', 'pend_akhir', 'biografi')
                ->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam pengambilan data', 'error' => 'data tidak ditemukan.']);
            }

            $data['jadwal'] = DokterJadwal::select('jadwal_id', 'hari', 'nama_hari', 'kuota', DB::raw("CONCAT(mulai,' : ',selesai) as jam_praktik"))
                ->where('client_id', $this->clientId)
                ->where('dokter_id', $dokterId)
                ->where('unit_id', $unitId)
                ->where('is_aktif', 1)->orderBy('hari', 'ASC')->get();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam pengambilan data.', 'error' => $e->getMessage()]);
        }
    }

    public function dataPasien(Request $request, $typeId = 'rm', $pasienId)
    {
        try {
            $fieldId = 'no_rm';
            if ($typeId == 'rm') {
                $fieldId = 'no_rm';
            } else if ($typeId == 'nik') {
                $fieldId = 'nik';
            } else {
                return response()->json(['success' => false, 'message'  => 'Data pasien tidak ditemukan']);
            }

            $data = Pasien::where('client_id', $this->clientId)
                ->where($fieldId, $pasienId)
                ->where('is_aktif', 1)
                ->select('pasien_id', 'no_rm', 'nama_pasien', 'salut', 'nama_lengkap', 'jns_kelamin', 'tempat_lahir', 'tgl_lahir')
                ->first();

            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Data pasien tidak ditemukan']);
            }

            // $data['detail'] = PasienDetail::where('client_id', $this->clientId)->where('pasien_id',$pasienId)->first();

            $data['alamat'] = PasienAlamat::where('client_id', $this->clientId)
                ->where('pasien_id', $data->pasien_id)
                ->select('alamat', 'rt', 'rw', 'propinsi', 'kota', 'kecamatan', 'kelurahan', 'kodepos')
                ->first();

            // $data['keluarga'] = PasienKeluarga::where('client_id', $this->clientId)->where('pasien_id',$pasienId)->first();
            // $data['alergi'] = [];
            // $alergiPasien = PasienAlergi::where('client_id', $this->clientId)->where('pasien_id',$pasienId)->where('is_aktif',1)->get();
            // if($alergiPasien) { $data['alergi'] = $alergiPasien; }

            return response()->json(['success' => true, 'message'  => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan data Pasien' . '. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataBooking(request $request, $bookingId)
    {
        try {
            $data = Registrasi::where('client_id', $this->clientId)
                ->where('is_aktif', 1)
                ->where('kode_booking', $bookingId)
                ->select('reg_id', 'tgl_registrasi', 'tgl_periksa', 'jam_periksa', 'kode_booking', 'no_antrian', 'dokter_nama', 'unit_nama', 'nama_pasien', 'jns_kelamin', 'no_rm', 'tgl_lahir', 'tempat_lahir', 'jadwal_id', 'nik')
                ->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $jadwal = DokterJadwal::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('jadwal_id', $data->jadwal_id)
                ->select('nama_hari', 'hari', 'mulai', 'selesai')
                ->first();

            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'jadwal tidak ditemukan.']);
            }
            $data['jadwal'] = $jadwal;
            return response()->json(['success' => true, 'message' => 'OK.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function verifikasiBooking(request $request)
    {
        try {
            $bookingId = $request->reg_id;
            $data = Registrasi::where('client_id', $this->clientId)->where('is_aktif', 1)->where('reg_id', $bookingId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            if ($data->is_checkin == true) {
                return response()->json(['success' => false, 'message' => 'anda sudah melakukan Check In pendaftaran.']);
            }

            $tgl1 = strtotime(date('Y-m-d'));
            $tgl2 = strtotime($data->tgl_periksa);
            $jarak = $tgl2 - $tgl1;
            $selisihHari = $jarak / 60 / 60 / 24;
            if ($selisihHari > 0) {
                return response()->json(['success' => false, 'message' => 'tanggal periksa anda bukan hari ini.']);
            }

            $noPendaftaran = RegistrasiHelper::instance()->NoPendaftaran($this->clientId, $data->tgl_periksa);

            /**if booking is from JKN need to update to bpjs data*/

            /**update antrian*/
            $data->tgl_checkin      = date('Y-m-d H:i:s');
            $data->is_checkin       = true;
            $data->no_pendaftaran   = $noPendaftaran['id'];
            $data->status_reg       = 'DAFTAR';
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'verifikasi tidak berhasil dilakukan.']);
            }

            return response()->json(['success' => true, 'message' => 'OK.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses memverifikasi data', 'error' => $e->getMessage()]);
        }
    }

    public function saveBooking(request $request)
    {
        try {
            //return response()->json(['success' => false, 'message' => 'buat booking baru' ]);
            /**periksa jadwal*/
            $jadwal = DokterJadwal::where('is_aktif', 1)->where('client_id', $this->clientId)
                ->where('jadwal_id', $request->jadwal_id)
                ->first();

            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.']);
            }

            $dokter = Dokter::where('is_aktif', 1)->where('client_id', $this->clientId)
                ->where('dokter_id', $jadwal->dokter_id)
                ->first();

            if (!$dokter) {
                return response()->json(['success' => false, 'message' => 'Dokter tidak ditemukan.']);
            }

            $poli = UnitPelayanan::where('is_aktif', 1)->where('client_id', $this->clientId)
                ->where('unit_id', $jadwal->unit_id)
                ->first();

            if (!$poli) {
                return response()->json(['success' => false, 'message' => 'Poli tidak ditemukan.']);
            }
            
            $ruangId = '';
            $kelasid = null;
            $ruang = RuangPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$jadwal->unit_id)->orderBy('is_utama','DESC')->first();
            if($ruang) {
                $ruangId = $ruang->ruang_id;
                $kelasId = $ruang->kelas_kamar;
            }


            if (!$request->is_pasien_baru) {
                $pasien = Pasien::where('is_aktif', 1)->where('client_id', $this->clientId)
                    ->where('pasien_id', $request->pasien_id)
                    ->first();

                if (!$pasien) {
                    return response()->json(['success' => false, 'message' => 'Data rekam medis tidak ditemukan.']);
                }

                $alamat = PasienAlamat::where('is_aktif', 1)->where('client_id', $this->clientId)
                    ->where('pasien_id', $request->pasien_id)->first();

                $penjamin = Penjamin::where('is_aktif', 1)->where('client_id', $this->clientId)
                    ->where('penjamin_id', $pasien->penjamin_id)
                    ->first();
            }

            $prefixNo = DokterUnit::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('dokter_id', $jadwal->dokter_id)->where('unit_id', $jadwal->unit_id)
                ->select('prefix_no_antrian')
                ->first();

            if (!$prefixNo) {
                return response()->json(['success' => false, 'message' => 'Jadwal gagal dibuat (0).']);
            }

            $kuotaTerpakai = Registrasi::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('tgl_periksa', $request->tgl_periksa)
                ->where('jadwal_id', $request->jadwal_id)
                ->where('dokter_id', $jadwal->dokter_id)
                ->where('unit_id', $jadwal->unit_id)
                ->count();

            $kuotaSisa = $jadwal->kuota - $kuotaTerpakai;
            if ($kuotaSisa <= 0) {
                return response()->json(['success' => false, 'message' => 'Kuota pelayanan sudah habis.']);
            }

            $modeReg = RegistrasiHelper::instance()->ModeReg($request->tgl_periksa);
            $kodeReg = RegistrasiHelper::instance()->RegistrasiId($this->clientId);
            $kodeBooking = RegistrasiHelper::instance()->BookingCode($this->clientId);
            $noAntrian = RegistrasiHelper::instance()->NoAntrian($this->clientId, $prefixNo->prefix_no_antrian, $request->tgl_periksa, $jadwal->jadwal_id);
            $noPendaftaran = RegistrasiHelper::instance()->NoPendaftaran($this->clientId, $request->tgl_periksa);

            $time = strtotime($jadwal->mulai);
            $minute = $jadwal->interval_periksa;
            $inter = date('H:i', $time + ($minute * $noAntrian['angka']) - $minute);
            $startTime = date('H:i', $time + (($minute * 60 * $noAntrian['angka'])) - ($minute * 60));

            /**simpan data registrasi*/
            DB::connection('dbclient')->beginTransaction();
            $dataReg = new Registrasi();
            $dataReg->reg_id            = $kodeReg;
            $dataReg->tgl_registrasi    = date('Y-m-d H:i:s');

            $dataReg->jns_registrasi    = $poli->jenis_registrasi;
            $dataReg->cara_registrasi   = 'DATANG SENDIRI';

            $dataReg->tgl_periksa       = $request->tgl_periksa;
            $dataReg->jam_periksa       = $startTime;
            $dataReg->mode_reg          = $modeReg;
            $dataReg->kode_booking      = $kodeBooking;

            $dataReg->no_antrian        = $noAntrian['id'];
            $dataReg->no_urut           = $noAntrian['angka'];

            $dataReg->jadwal_id         = $jadwal->jadwal_id;
            $dataReg->dokter_id         = $dokter->dokter_id;
            $dataReg->unit_id           = $poli->unit_id;
            $dataReg->ruang_id          = $ruangId;
            $dataReg->bed_id            = '';
            $dataReg->kelas_id          = $kelasId;
            $dataReg->dokter_nama       = $dokter->dokter_nama;
            $dataReg->unit_nama         = $poli->unit_nama;

            if ($modeReg == 'WALKIN') {
                $dataReg->tgl_checkin       = date('Y-m-d H:i:s');
                $dataReg->no_pendaftaran    = $noPendaftaran['id'];
            } else {
                $dataReg->tgl_checkin       = null;
                $dataReg->no_pendaftaran    = null;
            }

            $dataReg->asal_pasien       = 'DATANG SENDIRI';
            $dataReg->ket_asal_pasien   = '';
            if (!$request->is_pasien_baru) {
                $dataReg->pasien_id         = $pasien->pasien_id;
                $dataReg->nama_pasien       = $pasien->nama_pasien;
                $dataReg->jns_kelamin       = $pasien->jns_kelamin;
                $dataReg->nik               = $pasien->nik;
                $dataReg->no_kk             = $pasien->no_kk;
                $dataReg->no_rm             = $pasien->no_rm;
                $dataReg->tgl_lahir         = $pasien->tgl_lahir;
                $dataReg->tempat_lahir      = $pasien->tempat_lahir;

                $dataReg->jns_penjamin      = $pasien->jns_penjamin;
                $dataReg->penjamin_id       = $penjamin->penjamin_id;
                $dataReg->penjamin_nama     = $penjamin->penjamin_nama;
                $dataReg->is_bpjs           = $penjamin->is_bpjs;
                $dataReg->nama_penanggung   = strtoupper($pasien->nama_pasien);
            } else {
                $dataReg->pasien_id         = '';
                $dataReg->nama_pasien       = '';
                $dataReg->jns_kelamin       = '';
                $dataReg->nik               = '';
                $dataReg->no_kk             = '';
                $dataReg->no_rm             = '';
                $dataReg->tgl_lahir         = date('Y-m-d');
                $dataReg->tempat_lahir      = '';

                $dataReg->jns_penjamin      = '';
                $dataReg->penjamin_id       = '';
                $dataReg->penjamin_nama     = '';
                $dataReg->is_bpjs           = false;
                $dataReg->nama_penanggung   = '';
            }
            // $dataReg->pasien_id         = $pasien->pasien_id;
            // $dataReg->nama_pasien       = $pasien->nama_pasien;
            // $dataReg->jns_kelamin       = $pasien->jns_kelamin;
            // $dataReg->nik               = $pasien->nik;
            // $dataReg->no_kk             = $pasien->no_kk;
            // $dataReg->no_rm             = $pasien->no_rm;
            // $dataReg->tgl_lahir         = $pasien->tgl_lahir;
            // $dataReg->tempat_lahir      = $pasien->tempat_lahir;

            // $dataReg->jns_penjamin      = $pasien->jns_penjamin;
            // $dataReg->penjamin_id       = $penjamin->penjamin_id;
            // $dataReg->penjamin_nama     = $penjamin->penjamin_nama;
            // $dataReg->is_bpjs           = $penjamin->is_bpjs;

            // $dataReg->nama_penanggung   = strtoupper($pasien->nama_pasien);
            // $dataReg->hub_penanggung    = 'DIRI SENDIRI';

            // $dataReg->is_pasien_luar    = false;
            // $dataReg->is_pasien_baru    = $pasien->is_pasien_baru;

            $dataReg->is_pasien_luar    = false;
            $dataReg->hub_penanggung    = 'DIRI SENDIRI';
            $dataReg->is_pasien_baru    = $request->is_pasien_baru;

            $dataReg->status_reg        = 'DAFTAR';
            $dataReg->is_aktif          = true;

            $dataReg->client_id         = $this->clientId;
            $dataReg->created_by        = 'KIOSK';
            $dataReg->updated_by        = 'KIOSK';

            $success = $dataReg->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menyimpan data antrian(2).']);
            }

            if (!$request->is_pasien_baru) {
                /** hitung usia **/
                $tgl_lahir = $pasien->tgl_lahir;
                $tgl_lahir = explode("-", $tgl_lahir);
                $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
                    ? ((date("Y") - $tgl_lahir[0]) - 1)
                    : (date("Y") - $tgl_lahir[0]));

                /**insert di registrasi pasien */
                $regPasien = new RegistrasiPasien();
                $regPasien->reg_id          = $kodeReg;
                $regPasien->is_pasien_luar  = false;
                $regPasien->pasien_id       = $pasien->pasien_id;
                $regPasien->no_rm           = $pasien->no_rm;
                $regPasien->nama_pasien     = $pasien->nama_pasien;
                $regPasien->tempat_lahir    = $pasien->tempat_lahir;
                $regPasien->tgl_lahir       = $pasien->tgl_lahir;
                $regPasien->usia            = $usia;
                $regPasien->jns_kelamin     = $pasien->jns_kelamin;
                $regPasien->is_hamil        = $pasien->is_hamil;
                $regPasien->is_pasien_baru  = $request->is_pasien_baru;
                if ($alamat) {
                    $regPasien->propinsi_id     = $alamat->propinsi_tinggal_id;
                    $regPasien->kota_id         = $alamat->kota_tinggal_id;
                    $regPasien->kecamatan_id    = $alamat->kecamatan_tinggal_id;
                    $regPasien->kelurahan_id    = $alamat->kelurahan_tinggal_id;
                    $regPasien->kodepos         = $alamat->kodepos_tinggal;;
                }
                $regPasien->is_aktif        = true;
                $regPasien->client_id       = $this->clientId;
                $regPasien->created_by      = 'KIOSK';
                $regPasien->updated_by      = 'KIOSK';

                $success = $regPasien->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
                }
            }


            /**
             * insert to tb_antrian
             */

            DB::connection('dbclient')->commit();

            $data = Registrasi::where('client_id', $this->clientId)
                ->where('reg_id', $kodeReg)
                ->select(
                    'reg_id',
                    'tgl_registrasi',
                    'mode_reg',
                    'tgl_periksa',
                    'jam_periksa',
                    'kode_booking',
                    'no_antrian',
                    'no_pendaftaran',
                    'dokter_nama',
                    'unit_nama',
                    'pasien_id',
                    'nama_pasien',
                    'jns_kelamin',
                    'no_rm',
                    'tgl_lahir',
                    'tempat_lahir',
                    'kelas_id',
                )
                ->first();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * dummy data rujukan berdasar no peserta
     */
    public function dataRujukanByNoPeserta(request $request, $noPeserta)
    {
        try {
            //awal adalah pencarion langsung ke server BPJS
            //jika tidak ada dikembalikan error
            //jika ada dilanjut dengan pencarian ke master pasien internal

            $data = Pasien::where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->where('no_kepesertaan', $noPeserta)
                ->select('pasien_id', 'no_rm', 'nama_pasien', 'salut', 'nama_lengkap', 'tgl_lahir', 'tempat_lahir', 'jns_kelamin', 'nik', 'no_kepesertaan')
                ->first();

            if (!$data) {
                //dikembalikan dengan data respons dari BPJS jika data pasien tidak ada
                $data['nik'] = '000001';
                $data['nama_pasien'] = 'John Doe';
                $data['no_rm'] = null;
                $data['pasien_id'] = null;
                $data['salut'] = null;
                $data['nama_lengkap'] = 'John Doe';
                $data['tgl_lahir'] = '1978-06-06';
                $data['tempat_lahir'] = null;
                $data['jns_kelamin'] = "L";
                $data['nik'] = "337205460678001";
                $data['no_kepesertaan'] = "98462730462";
                $data['is_pasien_baru'] = true;
                //return response()->json(['success' => false, 'message' => 'data pasien / data surat kontrol tidak ditemukan']);
            }
            else {
                $data['is_pasien_baru'] = false;
            }
            
            //$data['rujukan'] = $this->createDummyRujukanMulti(null);
            $rujukan = $this->createDummyRujukanMulti(null);

            $rujukanData = [];
            $i = 0;
            foreach($rujukan as $ruj) {
                $kodePoliRujukan = $ruj['poliRujukan']['kode'];
                $dataBridging = Bridging::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('bridging_resource_id',$kodePoliRujukan)
                    ->where('bridging_group','BPJS')
                    ->select('local_resource_id','local_resource_name')
                    ->first();
                
                if($dataBridging) {
                    $ruj['unitRujukan'] = [
                        'unit_id' => $dataBridging->local_resource_id,
                        'unit_nama' => $dataBridging->local_resource_name,
                    ];
                }
                else {
                    $ruj['unitRujukan'] = [
                        'unit_id' => null,
                        'unit_nama' => null,
                    ];
                }
                $rujukanData[$i] = $ruj;
                $i++;
            }
            
            $data['rujukan'] = $rujukanData;

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * dummy data rujukan berdasar nik     
     **/
    public function dataRujukanByNik(request $request, $nik)
    {
        try {
            $data = Pasien::where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->where('nik', $nik)
                ->select('pasien_id', 'no_rm', 'nama_pasien', 'salut', 'nama_lengkap', 'tgl_lahir', 'tempat_lahir', 'jns_kelamin', 'nik', 'no_kepesertaan')
                ->first();

            if (!$data) {
                //jika data tidak ada melakukan pemanggilan ke server BPJS untuk akses endpoint pencarian Peserta berdassar NIK.
                $data['nik'] = '000001';
                $data['nama_pasien'] = 'John Doe';
                $data['no_rm'] = null;
                $data['pasien_id'] = null;
                $data['salut'] = null;
                $data['nama_lengkap'] = 'John Doe';
                $data['tgl_lahir'] = '1978-06-06';
                $data['tempat_lahir'] = null;
                $data['jns_kelamin'] = "L";
                $data['nik'] = "337205460678001";
                $data['no_kepesertaan'] = "98462730462";
                $data['is_pasien_baru'] = true;
                //return response()->json(['success' => false, 'message' => 'data pasien / data surat kontrol tidak ditemukan']);
            }
            else {
                $data['is_pasien_baru'] = false;
            }
            
            //data balikan akan diambil no kepesertaan untuk dicari data rujukannya.

            $rujukan = $this->createDummyRujukanMulti($data);
            $rujukanData = [];
            $i = 0;
            foreach($rujukan as $ruj) {
                $kodePoliRujukan = $ruj['poliRujukan']['kode'];
                $dataBridging = Bridging::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('bridging_resource_id',$kodePoliRujukan)
                    ->where('bridging_group','BPJS')
                    ->select('local_resource_id','local_resource_name')
                    ->first();
                
                if($dataBridging) {
                    $ruj['unitRujukan'] = [
                        'unit_id' => $dataBridging->local_resource_id,
                        'unit_nama' => $dataBridging->local_resource_name,
                    ];
                }
                else {
                    $ruj['unitRujukan'] = [
                        'unit_id' => null,
                        'unit_nama' => null,
                    ];
                }
                $rujukanData[$i] = $ruj;
                $i++;
            }
            
            $data['rujukan'] = $rujukanData;

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }


    /**
     * dummy data rujukan berdasar no rujukan
     */
    public function dataRujukanByNoRujukan(request $request, $noRujukan)
    {
        try {
            $list = $this->listRujukan();
            $nik = null;
            foreach ($list as $l) {
                if ($l['noRujukan'] == $noRujukan) {
                    $nik = $l['nik'];
                }
            }

            $data = Pasien::where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->where('nik', $nik)
                ->select('pasien_id', 'no_rm', 'nama_pasien', 'salut', 'nama_lengkap', 'tgl_lahir', 'tempat_lahir', 'jns_kelamin', 'nik', 'no_kepesertaan')
                ->first();

            if (!$data) {
                $data['no_rm'] = null;
                $data['pasien_id'] = null;
                $data['salut'] = null;
                $data['nama_lengkap'] = 'John Doe';
                $data['tgl_lahir'] = '1978-06-06';
                $data['tempat_lahir'] = null;
                $data['jns_kelamin'] = "L";
                $data['nik'] = "337205460678001";
                $data['no_kepesertaan'] = "98462730462";
                $data['is_pasien_baru'] = true;
                //return response()->json(['success' => false, 'message' => 'data pasien / data surat kontrol tidak ditemukan']);
            }
            else {
                $data['is_pasien_baru'] = false;
            }
            
            $rujukan = $this->createDummyRujukanTunggal($data);
            
            $rujukanData = [];
            $i = 0;
            foreach($rujukan as $ruj) {
                $kodePoliRujukan = $ruj['poliRujukan']['kode'];
                $dataBridging = Bridging::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('bridging_resource_id',$kodePoliRujukan)
                    ->where('bridging_group','BPJS')
                    ->select('local_resource_id','local_resource_name')
                    ->first();
                
                if($dataBridging) {
                    $ruj['unitRujukan'] = [
                        'unit_id' => $dataBridging->local_resource_id,
                        'unit_nama' => $dataBridging->local_resource_name,
                    ];
                }
                else {
                    $ruj['unitRujukan'] = [
                        'unit_id' => null,
                        'unit_nama' => null,
                    ];
                }
                $rujukanData[$i] = $ruj;
                $i++;
            }
            
            $data['rujukan'] = $rujukanData;

            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * dummy data rujukan berdasar no rujukan
     */
    public function dataRujukanByNoKontrol(request $request, $noKontrol)
    {
        try {
            $list = $this->listRujukan();
            $nik = null;
            foreach ($list as $l) {
                if ($l['noRujukan'] == $noKontrol) {
                    $nik = $l['nik'];
                }
            }

            $data = Pasien::where('is_aktif', 1)
                ->where('client_id', $this->clientId)
                ->where('nik', $nik)
                ->select('pasien_id', 'no_rm', 'nama_pasien', 'salut', 'nama_lengkap', 'tgl_lahir', 'tempat_lahir', 'jns_kelamin', 'nik', 'no_kepesertaan')
                ->first();


            if (!$data) {
                $data['no_rm'] = null;
                $data['pasien_id'] = null;
                $data['salut'] = null;
                $data['nama_lengkap'] = 'John Doe';
                $data['tgl_lahir'] = '1978-06-06';
                $data['tempat_lahir'] = null;
                $data['jns_kelamin'] = "L";
                $data['nik'] = "337205460678001";
                $data['no_kepesertaan'] = "98462730462";
                $data['is_pasien_baru'] = true;
                //return response()->json(['success' => false, 'message' => 'data pasien / data surat kontrol tidak ditemukan']);
            }
            else {
                $data['is_pasien_baru'] = false;
            }

            $rujukan = $this->createDummyRujukanTunggal($data);
            $rujukanData = [];
            $i = 0;
            foreach($rujukan as $ruj) {
                $kodePoliRujukan = $ruj['poliRujukan']['kode'];
                $dataBridging = Bridging::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('bridging_resource_id',$kodePoliRujukan)
                    ->where('bridging_group','BPJS')
                    ->select('local_resource_id','local_resource_name')
                    ->first();
                
                if($dataBridging) {
                    $ruj['unitRujukan'] = [
                        'unit_id' => $dataBridging->local_resource_id,
                        'unit_nama' => $dataBridging->local_resource_name,
                    ];
                }
                else {
                    $ruj['unitRujukan'] = [
                        'unit_id' => null,
                        'unit_nama' => null,
                    ];
                }
                $rujukanData[$i] = $ruj;
                $i++;
            }
            
            $data['rujukan'] = $rujukanData;
            

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }


    public function createDummyRujukanTunggal($pasien)
    {
        try {
            $data = [
                [
                    "diagnosa" => [
                        "kode" => "N40",
                        "nama" => "Hyperplasia of prostate"
                    ],
                    "keluhan" => "kencing tidak puas",
                    "noKunjungan" => "030107010217Y001465",
                    "pelayanan" => [
                        "kode" => "2",
                        "nama" => "Rawat Jalan"
                    ],
                    "poliRujukan" => [
                        "kode" => "INT",
                        "nama" => "Poli Penyakit Dalam"
                    ],
                    "provPerujuk" => [
                        "kode" => "0304R005",
                        "nama" => "RSI IBNU SINA"
                    ],
                ]
            ];
            return $data;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function createDummyKontrolTunggal($pasien)
    {
        try {
            $data = [
                [
                    "diagnosa" => [
                        "kode" => "N40",
                        "nama" => "Hyperplasia of prostate"
                    ],
                    "keluhan" => "kencing tidak puas",
                    "noKunjungan" => "030107010217Y001465",
                    "pelayanan" => [
                        "kode" => "2",
                        "nama" => "Rawat Jalan"
                    ],
                    "poliRujukan" => [
                        "kode" => "INT",
                        "nama" => "Poli Penyakit Dalam"
                    ],
                    "provPerujuk" => [
                        "kode" => "0304R005",
                        "nama" => "RSI IBNU SINA"
                    ],
                ]
            ];
            return $data;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function createDummyRujukanMulti($pasien)
    {
        try {
            $data = [
                [
                    "diagnosa" => [
                        "kode" => "N40",
                        "nama" => "Hyperplasia of prostate"
                    ],
                    "keluhan" => "kencing tidak puas",
                    "noKunjungan" => "030107010217Y001465",
                    "pelayanan" => [
                        "kode" => "2",
                        "nama" => "Rawat Jalan"
                    ],
                    "poliRujukan" => [
                        "kode" => "INT",
                        "nama" => "Poli Penyakit Dalam"
                    ],
                    "provPerujuk" => [
                        "kode" => "0304R005",
                        "nama" => "RSI IBNU SINA"
                    ],
                ],
                [
                    "diagnosa" => [
                        "kode" => "N41",
                        "nama" => "Hyperplasia"
                    ],
                    "keluhan" => "rasa sakit saat buang air kecil",
                    "noKunjungan" => "030107010217Y008888",
                    "pelayanan" => [
                        "kode" => "2",
                        "nama" => "Rawat Jalan"
                    ],
                    "poliRujukan" => [
                        "kode" => "ANA",
                        "nama" => "Poli Anak"
                    ],
                    "provPerujuk" => [
                        "kode" => "0304R005",
                        "nama" => "RSI IBNU SINA"
                    ],
                ]
            ];
            return $data;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function listRujukan()
    {
        try {
            $data[0] = ["noRujukan" => "030107010217Y001465", "noPeserta" => "74384463682", "nik" => "337202020571000"];
            $data[1] = ["noRujukan" => "030107010217Y146501", "noPeserta" => "92328328372", "nik" => "337202101169003"];
            $data[2] = ["noRujukan" => "030107010217Y220012", "noPeserta" => "98462730462", "nik" => "337205460678001"];

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
}
