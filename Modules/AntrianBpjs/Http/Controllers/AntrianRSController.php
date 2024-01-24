<?php

namespace Modules\AntrianBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AntrianBpjs\Entities\Client;
use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\PasienAlergi;
use Modules\MasterData\Entities\PasienKeluarga;
use Modules\MasterData\Entities\PasienDetail;

use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Kecamatan;

use Modules\MasterData\Entities\Bridging;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;

use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterJadwal;

use Modules\Pendaftaran\Entities\Pendaftaran;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Modules\AntrianBpjs\Entities\JknRegistrasi;

use Modules\AntrianBpjs\Entities\Antrian;
use RegistrasiHelper;


class AntrianRSController extends Controller
{
    public $clientId;
    public $username;
    public $token;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-username')) {
            $meta['code'] = 201;
            $meta['message'] = 'Unauthorized';
            return response()->json(['metadata' => $meta],201);
        }

        if(!$request->hasHeader('X-token')) {
            $meta['code'] = 201;
            $meta['message'] = 'Unauthorized';
            return response()->json(['metadata' => $meta],201);
        }

        $username = $request->header('X-username');
        $token = $request->header('X-token');

        $data = Client::where('jkn_username',$username)->where('jkn_token',$token)->where('is_aktif',1)->first();
        if(!$data) {
            $meta['code'] = 201;
            $meta['message'] = 'Unauthorized';
            return response()->json(['metadata' => $meta],201);
        } 

        $this->clientId = $data->client_id;
        $this->username = $username;
        $this->token = $token; 
    }

    public function StatusAntrean(Request $request) {
        try {
            $kodePoliBpjs = $request->kodepoli;
            $kodeDokterBpjs = $request->kodedokter;
            $tglPeriksa = $request->tanggalperiksa;
            $jamPraktek = $request->jampraktek;
            
            $response = [
                'namapoli' => 'ANAK',
                'namadokter' => 'Dr. Hendra',
                'totalantrean' => 25,
                'sisaantrean' => 4,
                'antreanpanggil' => 'A-21',
                'sisakuotajkn' => 2,
                'kuotajkn' => 30,
                'sisakuotanonjkn' => 2,
                'kuotanonjkn' => 30,
                'keterangan' => '',            
            ];

            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan status antrian';
            return response()->json(['metadata' => $meta]);
        }
    }

    public function AmbilAntrean(Request $request) {
        try {
            $nomorKartu = $request->nomorkartu;
            $nik = $request->nik;
            $noHp = $request->nohp;
            $kodePoli = $request->kodepoli;
            $noRm = $request->norm;
            $tanggalPeriksa = $request->tanggalperiksa;
            $kodeDokter = $request->kodedokter;
            $jamPraktek = $request->jampraktek;
            $jenisKunjungan = $request->jeniskunjungan;
            $nomorReferensi = $request->nomorreferensi;
            
            //{1 (Rujukan FKTP), 2 (Rujukan Internal), 3 (Kontrol), 4 (Rujukan Antar RS)}
            if($jenisKunjungan == '1') { $namaKunjungan = 'Rujukan FKTP'; }
            else if($jenisKunjungan == '2') { $namaKunjungan = 'Rujukan Internal'; }
            else if($jenisKunjungan == '3') { $namaKunjungan = 'Kontrol'; }
            else if($jenisKunjungan == '4') { $namaKunjungan = 'Rujukan Antar RS'; }
            else { $namaKunjungan = '-'; }

            /**
             * check pasien by nik dan no kepesertaan ada atau tidak
             */
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('nik',$nik)
                ->where('no_kepesertaan',$nomorKartu)
                ->first();

            if(!$pasien) { return response()->json(['metadata' => ["message" =>'Pasien Baru', "code" => 202]]); }

            /**check poli*/
            $poliBridging = Bridging::where('client_id',$this->clientId)->where("is_aktif",1)
                ->where('bridging_group','JKN')->where("bridging_resource_id",$kodePoli)->first();

            if(!$poliBridging) { return response()->json(['metadata' => ["message" =>'Poli tidak diketahui', "code" => 201]]); } 

            $poli = UnitPelayanan::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('unit_id',$poliBridging->local_resource_id)->first();

            if(!$poli) { return response()->json(['metadata' => ["message" =>'Mapping poli tidak diketahui', "code" => 201]]); } 

            /**check dokter*/
            $dokterBridging = Bridging::where('client_id',$this->clientId)->where("is_aktif",1)
                ->where('bridging_group','JKN')->where("bridging_resource_id",$kodeDokter)->first();

            if(!$dokterBridging) {
                return response()->json(['metadata' => ["message" =>'Dokter tidak diketahui', "code" => 201]]);
            } 

            $dokter = Dokter::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('dokter_id',$dokterBridging->local_resource_id)->first();

            if(!$dokter) {
                return response()->json(['metadata' => ["message" =>'Mapping dokter tidak diketahui', "code" => 201]]);
            } 

            /**ambil data hari dari tanggal periksa*/
            $today = Carbon::now();
            $timestamp = strtotime($tanggalPeriksa);
            $day = date('w',$timestamp);
            $diffDate = strtotime($tanggalPeriksa) - strtotime($today);
            $diff = abs(round($diffDate/ 86400)) + 1;
            $jamOps = explode("-",$jamPraktek);

            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('hari',$day)->where('mulai',$jamOps[0])
                ->where('unit_id',$poli->unit_id)
                ->where('dokter_id',$dokter->dokter_id)->first();

            if(!$jadwal) {
                return response()->json(['metadata' => ["message" =>'Jadwal dokter tidak diketahui', "code" => 201]]);
            }   
            
            $prefixNo = DokterUnit::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$dokter->dokter_id)
                ->where('unit_id',$poli->unit_id)
                ->select('prefix_no_antrian')
                ->first();

            if(!$prefixNo) {
                return response()->json(['metadata' => ["message" =>'Ada kesalahan mengambil data prefix', "code" => 201]]);
            }

            $kuotaTerpakai = Pendaftaran::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('tgl_periksa',$tanggalPeriksa)->where('jadwal_id',$jadwal->jadwal_id)
                ->where('dokter_id',$dokter->dokter_id)->where('unit_id',$poli->unit_id)
                ->count();
            
            $kuotaSisa = $jadwal->kuota - $kuotaTerpakai;
            if($kuotaSisa <= 0) {
                return response()->json(['metadata' => ["message" =>'Kuota Habis', "code" => 201]]);
            }

            $kodeBooking = RegistrasiHelper::instance()->BookingCode($this->clientId);
            $kodeReg = RegistrasiHelper::instance()->RegistrasiId($this->clientId);
            $noAntrian = RegistrasiHelper::instance()->NoAntrian($this->clientId, $prefixNo->prefix_no_antrian,$tanggalPeriksa,$jadwal->jadwal_id);

            $time = strtotime($jadwal->mulai);
            $minute = $jadwal->interval_periksa;
            $inter = date('H:i', $time + ($minute * $noAntrian['angka']) - $minute); 
            $startTime = date('H:i', $time + (($minute * 60 * $noAntrian['angka'])) - ($minute * 60));

            $metadata = ['message'=>'OK', 'code'=> 200 ];            
            $response = [
                'nomorantrean' => $noAntrian['id'],
                'angkaantrean' => $noAntrian['angka'],
                'kodebooking' => $kodeBooking,
                'norm' => $pasien->no_rm,
                'namapoli' => $poliBridging->bridging_resource_name,
                'namadokter' => $dokterBridging->bridging_resource_name,
                'estimasidilayani' => strtotime($tanggalPeriksa.' '.$startTime),             
                'sisakuotajkn' => $kuotaSisa,
                'kuotajkn' => $jadwal->kuota,                
                'sisakuotanonjkn' => $kuotaSisa,
                'kuotanonjkn' => $jadwal->kuota,
                'keterangan' => 'Peserta harap datang 60 menit lebih awal guna pencatatan administrasi',  
            ];

            $modeReg = 'BOOKING';
            
            /**simpan data JKN data*/
            DB::connection('dbclient')->beginTransaction();
            
            /**simpan data registrasi*/
            $dataReg = new Pendaftaran();
            $dataReg->reg_id            = $kodeReg;
            $dataReg->tgl_registrasi    = date('Y-m-d H:i:s');
            
            $dataReg->jns_registrasi    = $poli->jenis_registrasi;
            $dataReg->cara_registrasi   = 'DATANG SENDIRI';
            
            $dataReg->tgl_periksa       = $tanggalPeriksa;
            $dataReg->jam_periksa       = $startTime;
            $dataReg->mode_reg          = $modeReg;
            $dataReg->kode_booking      = $kodeBooking;
            
            $dataReg->jadwal_id         = $jadwal->jadwal_id;
            $dataReg->dokter_id         = $dokter->dokter_id;
            $dataReg->unit_id           = $poli->unit_id;
            $dataReg->ruang_id          = '';
            $dataReg->bed_id            = '';
            $dataReg->dokter_nama       = $dokter->dokter_nama;
            $dataReg->unit_nama         = $poli->unit_nama;

            $dataReg->no_antrian        = $noAntrian['id'];
            $dataReg->no_urut           = $noAntrian['angka'];
            
            if($modeReg == 'WALK IN') {
                $dataReg->tgl_checkin       = date('Y-m-d H:i:s');
                $dataReg->no_pendaftaran    = $noPendaftaran['id'];
            }
            else {
                $dataReg->tgl_checkin       = null;
                $dataReg->no_pendaftaran    = null;
            }            
            
            $dataReg->asal_pasien       = strtoupper($namaKunjungan);
            $dataReg->ket_asal_pasien   = strtoupper($namaKunjungan);
            
            $dataReg->pasien_id         = $pasien->pasien_id;
            $dataReg->nama_pasien       = $pasien->nama_pasien;
            $dataReg->jns_kelamin       = $pasien->jns_kelamin;
            $dataReg->nik               = $pasien->nik;
            $dataReg->no_kk             = $pasien->no_kk;
            $dataReg->no_rm             = $pasien->no_rm;
            $dataReg->tgl_lahir         = $pasien->tgl_lahir;
            $dataReg->tempat_lahir      = $pasien->tempat_lahir;
            
            $dataReg->jns_penjamin      = 'BPJS';
            $dataReg->penjamin_id       = 'CL2022-0002.INS-00002';
            $dataReg->penjamin_nama     = 'BPJS';
            $dataReg->no_rujukan        = $nomorReferensi;
            
            $dataReg->is_bpjs           = true;
                
            $dataReg->nama_penanggung   = strtoupper($pasien->nama_pasien);
            $dataReg->hub_penanggung    = 'DIRI SENDIRI';

            $dataReg->is_pasien_luar    = false;
            $dataReg->is_pasien_baru    = $pasien->is_pasien_baru;
            $dataReg->status_reg        = 'BOOKING';
            $dataReg->is_aktif          = true;

            $dataReg->client_id         = $this->clientId;
            $dataReg->created_by        = 'JKN';
            $dataReg->updated_by        = 'JKN';

            $success = $dataReg->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['metadata' => ["message" =>'Ada kesalahan menyimpan data registrasi', "code" => 201]]);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan antrian. '. $e->getMessage();
            return response()->json(['metadata' => $meta]);
        }
    }

    public function SisaAntrean(Request $request) {
        try {
            $kodeBooking = $request->kodebooking;

            $jknData = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kode_booking',$kodeBooking)
                ->first();
            
            if(!$jknData) {
                return response()->json(['metadata' => ["message" =>'data registrasi jkn tidak ditemukan.', "code" => 201]]);
            }

            /**check poli*/
            $poliBridging = Bridging::where('client_id',$this->clientId)->where("is_aktif",1)
                ->where('bridging_group','JKN')
                ->where("local_resource_id",$jknData->unit_id)
                ->first();

            if(!$poliBridging) { return response()->json(['metadata' => ["message" =>'Poli tidak diketahui', "code" => 201]]); } 

            /**check dokter*/
            $dokterBridging = Bridging::where('client_id',$this->clientId)->where("is_aktif",1)
                ->where('bridging_group','JKN')
                ->where("local_resource_id",$jknData->dokter_id)
                ->first();

            if(!$dokterBridging) {
                return response()->json(['metadata' => ["message" =>'Dokter tidak diketahui', "code" => 201]]);
            } 

            $jadwal = DokterJadwal::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('jadwal_id',$jknData->jadwal_id)->first();

            $antrianDiperiksa = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('tgl_periksa',$jknData->tgl_periksa)
                ->where('jadwal_id',$jknData->jadwal_id)
                ->where('no_urut','<=',$jknData->no_urut)
                ->where('status_reg','DAFTAR')
                ->orderBy('no_antrian','ASC')->get();
            
            $noAntrianDiperiksa = '00';
            if($antrianDiperiksa->count() >= 1) { 
                $noAntrianDiperiksa = $antrianDiperiksa[0]->no_antrian;
            }

            $sisaAntrian = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('tgl_periksa',$jknData->tgl_periksa)
                ->where('jadwal_id',$jknData->jadwal_id)
                ->where('no_urut','<',$jknData->no_urut)
                ->where('status_reg','DAFTAR')
                ->count();

            $waktuTunggu = $sisaAntrian * $jadwal->interval_periksa * 60;
            $response = [
                "nomorantrean" => $jknData->no_antrian,
                "namapoli" => $poliBridging->bridging_resource_name,
                "namadokter" => $dokterBridging->bridging_resource_name,
                "sisaantrean" => $sisaAntrian,
                "antreanpanggil" => $noAntrianDiperiksa,
                "waktutunggu" => $waktuTunggu,
                "keterangan" => "selalu periksa status antrian anda",
            ];

            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan sisa antrian :'.$e->getMessage();
            return response()->json(['metadata' => $meta]);
        }
    }

    public function BatalAntrean(Request $request) {
        try {
            $kodeBooking = $request->kodebooking;
            $keterangan = $request->keterangan;

            $registrasi = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kode_booking',$kodeBooking)->first();

            if(!$registrasi) {
                return response()->json(['metadata' => ["message" =>'data registrasi tidak ditemukan.', "code" => 201]]);
            }

            DB::connection('dbclient')->beginTransaction();
            $success = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kode_booking',$kodeBooking)
                ->update([ 'is_aktif'=>false, 'status_reg'=>'BATAL', 'keterangan_batal'=>$keterangan ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['metadata' => ["message" =>'data registrasi tidak berhasil dibatalkan.', "code" => 201]]);
            }
            DB::connection('dbclient')->commit();
            return response()->json(['metadata' => ['message'=>'OK', 'code'=> 200 ]]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pembatalan antrian :'.$e->getMessage();
            return response()->json(['metadata' => $meta]);
        }
    }

    public function CheckInAntrean(Request $request) {
        try {
            $kodeBooking = $request->kodebooking;
            $waktu = $request->waktu;

            $registrasi = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kode_booking',$kodeBooking)->first();

            if(!$registrasi) {
                return response()->json(['metadata' => ["message" =>'data registrasi tidak ditemukan.', "code" => 201]]);
            }

            $success = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kode_booking',$kodeBooking)->where('reg_id',$registrasi->reg_id)
                ->update(['is_checkin'=>true, 'tgl_checkin'=>date('Y-m-d H:i:s')]);
            
            if(!$success) {
                return response()->json(['metadata' => ["message" =>'data registrasi tidak berhasil diupdate.', "code" => 201]]);
            }

            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam proses check In antrian';
            return response()->json(['metadata' => $meta]);
        }
    }

    public function InfoPasienBaru(Request $request) {
        try {            
            $nomorKartu = $request->nomorkartu;
            $nik = $request->nik;
            $nomorKk = $request->nomorkk;
            $nama = $request->nama;
            $jenisKelamin = $request->jeniskelamin;
            $tanggalLahir = $request->tanggallahir;
            $noHp = $request->nohp;
            $alamatPasien = $request->alamat;
            $kodeProp = $request->kodeprop;
            $namaProp = $request->namaprop;
            $kodeDati2 = $request->kodedati2;
            $namaDati2 = $request->namadati2;
            $kodeKec = $request->kodekec;
            $namaKec = $request->namakec;
            $kodeKel = $request->kodekel;
            $namaKel = $request->namakel;
            $rt = $request->rt;
            $rw = $request->rw;

            /**check apakah pasien sudah ada sebelumnya */
            $pasienExist = Pasien::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('nik',$nik)->first();

            if($pasienExist) {
                return response()->json(['metadata' => ["message" =>'NIK sudah terpakai', "code" => 201]]);
            }

            /**Check propinsi */
            $propBridging = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('bridging_resource_id',$kodeProp)->first();

            //return response()->json(['metadata' => ["message" =>'Propinsi tidak diketahui', "code" => $propBridging]]);
            if(!$propBridging) {
                return response()->json(['metadata' => ["message" =>'Propinsi tidak diketahui', "code" => 201]]);
            } 

            $propinsi = Propinsi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('propinsi_id',$propBridging->local_resource_id)->first();

            if(!$propinsi) {
                return response()->json(['metadata' => ["message" =>'Mapping propinsi tidak diketahui', "code" => 201]]);
            } 

            /**Check kabupaten(kota) */
            $kabBridging = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('bridging_resource_id',$kodeDati2)->first();

            if(!$kabBridging) {
                return response()->json(['metadata' => ["message" =>'Kabupaten tidak diketahui', "code" => 201]]);
            } 

            $kabupaten = Kota::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kota_id',$kabBridging->local_resource_id)->first();

            if(!$propinsi) {
                return response()->json(['metadata' => ["message" =>'Mapping kabupaten tidak diketahui', "code" => 201]]);
            } 

            /**Check kecamatan */
            $kecBridging = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('bridging_resource_id',$kodeKec)->first();

            if(!$kecBridging) {
                return response()->json(['metadata' => ["message" =>'Kecamatan tidak diketahui', "code" => 201]]);
            } 

            $kecamatan = Kecamatan::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('kecamatan_id',$kecBridging->local_resource_id)->first();

            if(!$propinsi) {
                return response()->json(['metadata' => ["message" =>'Mapping kecamatan tidak diketahui', "code" => 201]]);
            } 

            $id = RegistrasiHelper::instance()->PasienId($this->clientId);
            $no_rm = RegistrasiHelper::instance()->NoRM($this->clientId, false);

            DB::connection('dbclient')->beginTransaction();
            $data = new Pasien();
            $data->pasien_id = $id;
            $data->client_id = $this->clientId;
            $data->no_rm = $no_rm;
            $data->is_pasien_luar = false;
            $data->nama_pasien = $nama;
            $data->salut = '';
            $data->nama_lengkap = strtoupper($nama);
            $data->nik = $nik;
            $data->no_kk = $nomorKk;
            $data->jns_kelamin = $jenisKelamin;
            $data->tgl_lahir = $tanggalLahir;
            $data->tempat_lahir = '';
            
            $data->jns_penjamin = "BPJS";
            $data->penjamin_id = 'CL2022-0002.INS-00002';
            $data->penjamin_nama = 'BADAN PENYELENGGARA JAMINAN SOSIAL';            
            $data->no_kepesertaan = $nomorKartu;
            $data->tgl_daftar = date('Y-m-d H:i:s');
            $data->is_aktif = 1;
            $data->created_by = 'JKN ANTRIAN';
            $data->created_at = date('Y-m-d H:i:s');

            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['metadata' => ["message" =>'Gagal menyimpan data pasien', "code" => 201]]);
            }

            $alamat = new PasienAlamat(); 
            $alamat->pasien_id = $id;       
            $alamat->client_id = $this->clientId;
                 
            $alamat->alamat = $alamatPasien;
            $alamat->rt = $rt;
            $alamat->rw = $rw;
            $alamat->propinsi_id = $propinsi->propinsi_id;
            $alamat->propinsi = $propinsi->propinsi;
            $alamat->kota_id = $kabupaten->kota_id;
            $alamat->kota = $kabupaten->kota;
            $alamat->kecamatan_id = $kecamatan->kecamatan_id;
            $alamat->kecamatan = $kecamatan->kecamatan;
            $alamat->kelurahan_id = $kodeKel;
            $alamat->kelurahan = $namaKel;

            $alamat->is_ktp_sama_dgn_tinggal = true;
            
            $alamat->alamat_tinggal = $alamatPasien;
            $alamat->rt_tinggal = $rt;
            $alamat->rw_tinggal = $rw;
            $alamat->propinsi_tinggal_id = $propinsi->propinsi_id;
            $alamat->propinsi_tinggal = $propinsi->propinsi;
            $alamat->kota_tinggal_id = $kabupaten->kota_id;
            $alamat->kota_tinggal = $kabupaten->kota;
            $alamat->kecamatan_tinggal_id = $kecamatan->kecamatan_id;
            $alamat->kecamatan_tinggal = $kecamatan->kecamatan;
            $alamat->kelurahan_tinggal_id = $kodeKel;
            $alamat->kelurahan_tinggal = $namaKel;
            //$alamat->kodepos_tinggal = $request->alamat['kodepos'];
            $alamat->is_aktif = 1;
            $alamat->created_by = 'JKN ANTRIAN';
            $alamat->updated_by = 'JKN ANTRIAN';

            $success = $alamat->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['metadata' => ["message" =>'Gagal menyimpan data alamat pasien', "code" => 201]]);
            }

            DB::connection('dbclient')->commit();

            $metadata = ['message'=>'Harap datang ke admisi untuk melengkapi data rekam medis', 'code'=> 200 ];
            return response()->json(['response'=>['norm'=>$no_rm], 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pembuatan pasien baru : '.$e->getMessage();
            return response()->json(['metadata' => $meta]);
        }
    }

    public function JadwalOperasiRS(Request $request) {
        try {
            $tanggalAwal = $request->tanggalawal;
            $tanggalAkhir = $request->tanggalakhir;
            
            $list[0] = [
                "kodebooking" => "123456ZXC",
                "tanggaloperasi" => "2019-12-11",
                "jenistindakan" => "operasi gigi",
                "kodepoli" => "001",
                "namapoli" => "Poli Bedah Mulut",
                "terlaksana" => 1,
                "nopeserta" => "0000000924782",
                "lastupdate" => 1577417743000 
            ]; 

            $list[1] = [
                "kodebooking"=> "67890QWE",
                "tanggaloperasi"=> "2019-12-11",
                "jenistindakan"=> "operasi mulut",
                "kodepoli"=> "001",
                "namapoli"=> "Poli Bedah Mulut",
                "terlaksana"=> 0,
                "nopeserta"=> "",
                "lastupdate"=> 1577417743000
            ]; 

            $response = ['list' => $list];

            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan jadwal operasi RS';
            return response()->json(['metadata' => $meta]);
        }
    }

    public function JadwalOperasiPasien(Request $request) {
        try {
            $noPeserta = $request->nopeserta;

            $list[0] = [
                "kodebooking" => "123456ZXC",
                "tanggaloperasi" => "2019-12-11",
                "jenistindakan" => "operasi gigi",
                "kodepoli" => "001",
                "namapoli" => "Poli Bedah Mulut",
                "terlaksana" => 0 
            ];

            $response = ['list' => $list];
            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan jadwal operasi pasien';
            return response()->json(['metadata' => $meta]);
        }
    }

    public function AmbilAntreanFarmasi(Request $request) {
        try {
            $kodeBooking = $request->kodebooking;

            $response = [
                "jenisresep" => "Racikan/Non Racikan",
                "nomorantrean" => 1,
                "keterangan" => ""
            ];
            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan antrean farmasi';
            return response()->json(['metadata' => $meta]);
        }
    }

    public function StatusAntreanFarmasi(Request $request) {
        try {
            $kodeBooking = $request->kodebooking;

            $response = [
                "jenisresep" => "Racikan/Non Racikan",
                "totalantrean" => 10,
                "sisaantrean" => 8,
                "antreanpanggil" => 2,
                "keterangan" => ""
            ];

            $metadata = ['message'=>'OK', 'code'=> 200 ];
            return response()->json(['response'=>$response, 'metadata' => $metadata]);
        }
        catch (\Exception $e) {
            $meta['code'] = 201;
            $meta['message'] = 'ada kesalahan dalam pengambilan status antrian farmasi';
            return response()->json(['metadata' => $meta]);
        }
    }

}
