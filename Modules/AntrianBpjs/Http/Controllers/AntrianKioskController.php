<?php

namespace Modules\AntrianBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Pendaftaran\Entities\Pendaftaran;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

use Modules\AntrianBpjs\Entities\Registrasi;
use Modules\AntrianBpjs\Entities\RegistrasiPasien;


use Modules\AntrianBpjs\Entities\JknRegistrasi;
use Modules\AntrianBpjs\Entities\Antrian;
use Modules\AntrianBpjs\Entities\AntrianKasir;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\PasienKeluarga;

use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kelurahan;
use Modules\MasterData\Entities\Penjamin;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\BedInap;

use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\DokterJadwal;

use Modules\MasterData\Entities\Bridging;

use Modules\ManajemenUser\Entities\Client as ClientWs;

use BpjsHelper;
use RegistrasiHelper;
use Ramsey\Uuid\Uuid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LZCompressor\LZString;

class AntrianKioskController extends Controller
{
    public $clientId;
    public $credentialJkn;
    public $credentialBpjs;
    public $RegHelper;

    public function __construct(Request $request) {
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');

        /**get data bpjs credential */
        $data = ClientWs::where('client_id',$this->clientId)
            ->where('is_aktif',1)
            ->select('client_id','client_nama','bpjs_cons_id','bpjs_secret','bpjs_user_key','is_bpjs_aktif')->first();
        
        if(!$data) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }   

        $this->credentialJkn = [
            'cons_id'       => '29393',
            'secret_key'    => '9wS6BB628F',
            'user_key'      => '6ab9aecf4e8ca01b8a94699f07c36bb9',
        ];

        $this->credentialBpjs = [
            'cons_id'       => $data->bpjs_cons_id,
            'secret_key'    => $data->bpjs_secret,
            'user_key'      => $data->bpjs_user_key 
        ];

        $RegHelper = new RegistrasiHelper();
    }

    public function httpGetRequestBpjs($url){
        try {
            $result = BpjsHelper::BpjsHttpGet('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credentialBpjs);            
            return $result;
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function httpGetRequestJkn($url){
        try {
            $result = BpjsHelper::JknHttpGet('https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev/'.$url, $this->credentialJkn);            
            return $result;
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    /**internal data from kiosk */
    public function GetAntreanData(Request $request, $kodeBooking) {
        try {
            $jknData = Pendaftaran::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('kode_booking',$kodeBooking)->first();
            
            if(!$jknData) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']);
            }

            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$jknData->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'data pasien tidak ditemukan']);
            }
            $jknData['pasien'] = $pasien;
            
            $noRujukan = $jknData->no_rujukan;
            $result = $this->httpGetRequestBpjs('Rujukan/RS/'.$noRujukan);
            
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
                $jknData['rujukan'] = $result->response->rujukan;
            }
            else { $jknData['rujukan'] = null; }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $jknData ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function GetPasienData(Request $request, $jenisId, $idpasien) {
        try {
            $pasien = null;
            if($jenisId == 'RM') {
                $pasien = Pasien::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('no_rm',$idpasien)
                    ->select('pasien_id','no_rm','nama_pasien','salut','nama_lengkap','tgl_lahir','tempat_lahir','nik','no_kk','jns_kelamin')
                    ->first();
            } 
            else if($jenisId == "NIK") {
                $pasien = Pasien::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('nik',$idpasien)
                    ->select('pasien_id','no_rm','nama_pasien','salut','nama_lengkap','tgl_lahir','tempat_lahir','nik','no_kk','jns_kelamin')
                    ->first();
            } 
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            
            $alamat = PasienAlamat::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('pasien_id',$pasien->pasien_id)->first();

            if($alamat) {
                $pasien['alamat_ktp'] = $alamat->alamat.' RT.'.$alamat->rt.'/ RW.'.$alamat->rw.' Kel.'.$alamat->kelurahan.' Kec.'.$alamat->kecamatan.' Kota/Kab.'.$alamat->kota.' Prop.'.$alamat->propinsi;
                $pasien['alamat_tinggal'] = $alamat->alamat_tinggal.' RT.'.$alamat->rt_tinggal.'/ RW.'.$alamat->rw_tinggal.' Kel.'.$alamat->kelurahan_tinggal.' Kec.'.$alamat->kecamatan_tinggal.' Kota/Kab.'.$alamat->kota_tinggal.' Prop.'.$alamat->propinsi_tinggal;
            }
            else {
                $pasien['alamat_ktp'] = null;
                $pasien['alamat_tinggal'] = null;
            }

            $detail = PasienDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('pasien_id',$pasien->pasien_id)->first();
            
            if($detail) {
                $pasien['no_telepon'] = $detail->no_telepon;
            }
            else {
                $pasien['no_telepon'] = null;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$pasien]);
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function GetListPoli(Request $request, $tglPeriksa = null) {
        try {
            $today = Carbon::now();
            if($tglPeriksa) {
                if(strtoupper($tglPeriksa) !== 'UNDEFINED') {
                    $today = $tglPeriksa;
                }                
            }

            $timestamp = strtotime($today);
            $day = date('w',$timestamp);

            // $diffDate = strtotime($tanggalPeriksa) - strtotime($today);
            // $diff = abs(round($diffDate/ 86400)) + 1;
            // $jamOps = explode("-",$jamPraktek);

            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('hari',$day)->select('unit_id')->get();           

            if(!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Tidak ada jadwal dokter hari ini']);
            }

            $list = UnitPelayanan::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_unit_kiosk',1)
                ->whereIn('unit_id',$jadwal)
                ->select('unit_id','unit_nama')
                ->get();
            
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Jadwal Unit Pelayanan (poli) tidak ditemukan']);
            }       

            foreach($list as $item) {
                $jadwal = DokterJadwal::leftJoin('tb_dokter','tb_dokter_jadwal.dokter_id','=','tb_dokter.dokter_id')
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

                if($jadwal) {
                    foreach($jadwal as $jd) { $jd['unit_nama'] = $item['unit_nama']; }
                }                            
                $item['jadwal'] = $jadwal;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        }
        
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    /**cara rujukan bpjs */
    public function GetRujukanBpjsByNomor(Request $request, $noRujukan) {
        try {
            $nik = null;
            $noKartu = null;
            $noRm = null;

            $result = $this->httpGetRequestBpjs('Rujukan/RS/'.$noRujukan);
            return response()->json(['success' => false, 'message' => $result ]);

            if($result->metaData->code == '200') {
                $rujukan = $result->response->rujukan;
                $jknData['rujukan'] = $rujukan;
                $nik = null;
                $noKartu = null;
                $noRm = null;
            }
            else { 
                return response()->json(['success' => false, 'message' => 'Data rujukan tidak ditemukan.' ]);
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $jknData ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }
    
    /**cari peserta bpjs */
    public function GetRujukanBpjsByNoka(Request $request, $noKartu) {
        try {            
            $nik = null;
            $noKartu = null;
            $noRm = null;

            $result = $this->httpGetRequestBpjs('Rujukan/RS/Peserta/'.$noKartu);
            //return response()->json(['success' => false, 'message' => json_decode($result) ]);

            if($result->metaData->code == '200') {
                $rujukan = $result->response->rujukan;
                $jknData['rujukan'] = $rujukan;
                $nik = null;
                $noKartu = null;
                $noRm = null;
            }
            else { 
                return response()->json(['success' => false, 'message' => 'Data rujukan tidak ditemukan.' ]);
             }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $jknData ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }
    
    /**ambil data jadwal per tanggal berikut jumlah pasien yang sudah daftar*/
    public function infoJadwal(Request $request, $jadwalId, $tglPeriksa) {
        try {
            $jadwal = DokterJadwal::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('jadwal_id',$jadwalId)->first();
            
            if(!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.' ]);
            }

            $jadwal['list_jadwal'] = DokterJadwal::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('unit_id',$jadwal->unit_id)->where('dokter_id',$jadwal->dokter_id)
                ->orderBy('hari','ASC')->get();

            $jadwal['jml_antrian'] = Pendaftaran::where('jadwal_id',$jadwalId)
                ->where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$jadwal->dokter_id)
                ->where('unit_id',$jadwal->unit_id)
                ->where('tgl_periksa',$tglPeriksa)->count();
            
            $jadwal['dokter'] = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$jadwal->dokter_id)
                ->select('dokter_id','dokter_nama','biografi','url_avatar','pend_akhir','tempat_lahir','tgl_lahir')
                ->first();
            
            $jadwal['unit'] = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('unit_id',$jadwal->unit_id)
                ->select('unit_id','inisial','unit_nama','icon_dir','icon_url','group_unit')
                ->first();
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $jadwal ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function saveBooking(request $request) {
        try {
            /**periksa jadwal*/
            $jadwal = DokterJadwal::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('jadwal_id',$request->jadwal_id)
                ->first();
            
            if(!$jadwal) { return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.' ]); }

            $dokter = Dokter::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('dokter_id',$jadwal->dokter_id)
                ->first();

            if(!$dokter) { return response()->json(['success' => false, 'message' => 'Dokter tidak ditemukan.' ]); }
            
            $poli = UnitPelayanan::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('unit_id',$jadwal->unit_id)
                ->first();

            if(!$poli) { return response()->json(['success' => false, 'message' => 'Poli tidak ditemukan.' ]); }

            $pasien = Pasien::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('pasien_id',$request->pasien_id)
                ->first();

            if(!$pasien) { return response()->json(['success' => false, 'message' => 'Data rekam medis tidak ditemukan.' ]); }

            $alamat = PasienAlamat::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('pasien_id',$request->pasien_id)->first();

            $prefixNo = DokterUnit::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)->where('unit_id',$request->unit_id)
                ->select('prefix_no_antrian')
                ->first();

            if(!$prefixNo) {
                return response()->json(['success' => false, 'message' => 'Jadwal gagal dibuat (0).' ]);
            }

            $penjamin = Penjamin::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('penjamin_id',$pasien->penjamin_id)
                ->first();

            $kuotaTerpakai = Registrasi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('tgl_periksa',$request->tgl_periksa)
                ->where('jadwal_id',$request->jadwal_id)
                ->where('dokter_id',$jadwal->dokter_id)
                ->where('unit_id',$jadwal->unit_id)
                ->count();
            
            $kuotaSisa = $jadwal->kuota - $kuotaTerpakai;
            if($kuotaSisa <= 0) {
                return response()->json(['success' => false, 'message' => 'Kuota pelayanan sudah habis.' ]);
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
            $dataReg->ruang_id          = '';
            $dataReg->bed_id            = '';
            $dataReg->dokter_nama       = $dokter->dokter_nama;
            $dataReg->unit_nama         = $poli->unit_nama;
            
            if($modeReg == 'WALK IN') {
                $dataReg->tgl_checkin       = date('Y-m-d H:i:s');
                $dataReg->no_pendaftaran    = $noPendaftaran['id'];
            }
            else {
                $dataReg->tgl_checkin       = null;
                $dataReg->no_pendaftaran    = null;
            }            
            
            $dataReg->asal_pasien       = 'DATANG SENDIRI';
            $dataReg->ket_asal_pasien   = '';
            
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
            $dataReg->hub_penanggung    = 'DIRI SENDIRI';

            $dataReg->is_pasien_luar    = false;
            $dataReg->is_pasien_baru    = $pasien->is_pasien_baru;
            $dataReg->status_reg        = 'DAFTAR';
            $dataReg->is_aktif          = true;

            $dataReg->client_id         = $this->clientId;
            $dataReg->created_by        = 'KIOSK';
            $dataReg->updated_by        = 'KIOSK';

            $success = $dataReg->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menyimpan data antrian(2).' ]);
            }

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
            if($alamat){
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
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
            }  
            
            /**
             * insert to tb_antrian
             */

            DB::connection('dbclient')->commit();
            
            $data = Registrasi::where('client_id',$this->clientId)->where('reg_id',$kodeReg)->first();
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function dataBooking(request $request,$bookingId) {
        try {            
            $data = Registrasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('kode_booking',$bookingId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);  
            }

            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('jadwal_id',$data->jadwal_id)
                    ->select('nama_hari','hari','mulai','selesai')
                    ->first();

            if(!$jadwal) {
                return response()->json(['success' => false, 'message' => 'jadwal tidak ditemukan.']);  
            }
            $data['jadwal'] = $jadwal;
            return response()->json(['success' => true, 'message' => 'OK.', 'data' => $data]);  
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function verifikasiBooking(request $request) {
        try {            
            $bookingId = $request->antrian_id;
            $data = Registrasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('kode_booking',$bookingId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);  
            }

            if($data->is_checkin == true) {
                return response()->json(['success' => false, 'message' => 'anda sudah melakukan Check In pendaftaran.']);  
            }

            $tgl1 = strtotime(date('Y-m-d'));
            $tgl2 = strtotime($data->tgl_periksa);
            $jarak = $tgl2 - $tgl1;
            $selisihHari = $jarak / 60 / 60 / 24;
            if($selisihHari > 0) { 
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
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'verifikasi tidak berhasil dilakukan.']);
            }
            
            return response()->json(['success' => true, 'message' => 'OK.', 'data' => $data]);  
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses memverifikasi data', 'error' => $e->getMessage()]);
        }
    }

    public function createAntrianKasir(request $request) {
        try {
            $noAntrian = '001';
            $angka = null;
            $maxId = AntrianKasir::where('client_id', $this->clientId)->withTrashed()
                ->where('tgl_antrian',date('Y-m-d'))
                ->where('jns_antrian','KASIR')
                ->max('no_antrian');
            
            if (!$maxId) { $noAntrian = '001'; $angka = 1;} 
            else {
                $count = $maxId + 1;
                $angka = $count;
                if ($count < 10) { $noAntrian = '00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $noAntrian = '0' . $count; } 
                else { $noAntrian = $count; } 
            }

            $id = $this->clientId . date('Ymd') . '-' . Uuid::uuid4()->getHex();
            
            $data =  new AntrianKasir();
            $data->client_id  = $this->clientId;
            $data->antrian_kasir_id  = $id;
            $data->reg_id  = null;
            $data->jns_antrian  = 'KASIR';
            $data->tgl_ambil = date('Y-m-d H:i:s');
            $data->tgl_antrian = date('Y-m-d');
            $data->no_antrian = $noAntrian;
            $data->angka_antrian = $angka;
            $data->is_dilayani = false;
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses data']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses data', 'error' => $e->getMessage()]);
        }
    }

    public function createPasienBaru(Request $request)
    {
        try {
            /** check client ID */            
            $id = RegistrasiHelper::instance()->PasienId($this->clientId);
            $no_rm = RegistrasiHelper::instance()->NoRM($this->clientId, false);
            
            $prop = null; $kota = null; $kec = null; $kel = null;
            $propTinggal = null; $kotaTinggal = null; $kecTinggal = null; $kelTinggal = null;
            
            $prop = Propinsi::where('propinsi_id',$request->propinsi_id)->select('propinsi')->first(); 
            if($prop) { $prop = $prop->propinsi; }
            $kota = Kota::where('kota_id',$request->kota_id)->select('kota')->first(); 
            if($kota) { $kota = $kota->kota; }
            $kec = Kecamatan::where('kecamatan_id',$request->kecamatan_id)->select('kecamatan')->first(); 
            if($kec) { $kec = $kec->kecamatan; }
            $kel = Kelurahan::where('kelurahan_id',$request->kelurahan_id)->select('kelurahan')->first(); 
            if($kel) { $kel = $kel->kelurahan; }
            
            $propTinggal = $prop; $kotaTinggal = $kota; $kecTinggal = $kec; $kelTinggal = $kel; 

            $asuransi = Penjamin::where('client_id',$this->clientId)
                ->where('penjamin_id',$request->penjamin_id)
                ->where('is_aktif',1)->first();

            if(!$asuransi) {
                return response()->json(['success' => false, 'message' => 'data penjamin / asuransi / instansi tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $data = new Pasien();
            /*** penyimpanan data utama pasien **/
            $data->pasien_id = $id;
            $data->no_rm = $no_rm;
            $data->is_pasien_luar = false;
            $data->nama_pasien = $request->nama_pasien;
            $data->salut = $request->salut;
            $data->nama_lengkap = strtoupper($request->salut).' '.strtoupper($request->nama_pasien);
            $data->nik = $request->nik;
            $data->no_kk = $request->no_kk;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->tgl_lahir = $request->tgl_lahir;
            $data->tempat_lahir = strtoupper($request->tempat_lahir);
            
            $data->jns_penjamin = $asuransi->jns_penjamin;
            $data->penjamin_id = $request->penjamin_id;
            $data->penjamin_nama = $asuransi->penjamin_nama;            
            $data->no_kepesertaan = $request->no_kepesertaan;
            
            $data->tgl_terakhir_periksa = null;
            $data->is_hamil = false;
            $data->is_meninggal = false;
            $data->tgl_meninggal = null;
            $data->penyebab_kematian = null;
            $data->tgl_daftar = date('Y-m-d H:i:s');
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = "KIOSK";
            $data->created_at = date('Y-m-d H:i:s');

            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data pasien.']);
            }

            /*** penyimpanan data detail pasien **/
            $detail = new PasienDetail();
            $detail->pasien_id = $id;
            $detail->is_aktif = true;
            $detail->client_id = $this->clientId;
            $detail->created_by = 'KIOSK';
            $detail->updated_by = 'KIOSK';
            
            $success = $detail->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data pasien.']);
            }

            /*** penyimpanan data keluarga pasien */
            $keluarga = new PasienKeluarga();
            $keluarga->pasien_id = $id;
            $keluarga->is_aktif = true;
            $keluarga->client_id = $this->clientId;
            $keluarga->created_by = 'KIOSK';
            $keluarga->updated_by = 'KIOSK';
            $success = $keluarga->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data pasien.']);
            }

            /*** penyimpanan data alamat pasien **/
            $alamat = new PasienAlamat();
            $alamat->pasien_id = $id;
            $alamat->alamat = $request->alamat;
            $alamat->rt = $request->rt;
            $alamat->rw = $request->rw;
            $alamat->propinsi_id = $request->propinsi_id;
            $alamat->propinsi = $prop;
            $alamat->kota_id = $request->kota_id;
            $alamat->kota = $kota;
            $alamat->kecamatan_id = $request->kecamatan_id;
            $alamat->kecamatan = $kec;
            $alamat->kelurahan_id = $request->kelurahan_id;
            $alamat->kelurahan = $kel;
            $alamat->kodepos = $request->kodepos;
            $alamat->is_ktp_sama_dgn_tinggal = true;
            
            $alamat->alamat_tinggal = $request->alamat;
            $alamat->rt_tinggal = $request->rt;
            $alamat->rw_tinggal = $request->rw;
            $alamat->propinsi_tinggal_id = $request->propinsi_id;
            $alamat->propinsi_tinggal = $prop;
            $alamat->kota_tinggal_id = $request->kota_id;
            $alamat->kota_tinggal = $kota;
            $alamat->kecamatan_tinggal_id = $request->kecamatan_id;
            $alamat->kecamatan_tinggal = $kec;
            $alamat->kelurahan_tinggal_id = $request->kelurahan_id;
            $alamat->kelurahan_tinggal = $kel;
            $alamat->kodepos_tinggal = $request->kodepos;
            
            $alamat->is_aktif = 1;
            $alamat->client_id = $this->clientId;
            $alamat->created_by = 'KIOSK';
            $alamat->created_at = date('Y-m-d H:i:s');

            $success = $alamat->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data alamat.']);
            }
            
            DB::connection('dbclient')->commit();

            $data = Pasien::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            
            return response()->json(['success' => true, 'message'  => 'Berhasil simpan data ' . $data->nama_pasien, 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menyimpan data Pasien. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

}
