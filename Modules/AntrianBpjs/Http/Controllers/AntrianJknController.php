<?php

namespace Modules\AntrianBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Ramsey\Uuid\Uuid;
use Modules\ManajemenUser\Entities\Client as ClientWs;
use BpjsHelper;


use Modules\MasterData\Entities\Bridging;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;

use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterJadwal;
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;
// use LZCompressor\LZString;

class AntrianJknController extends Controller
{    
    public $clientId;
    public $credential;
    
    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
        
        /**get data bpjs credential */
        $data = ClientWs::where('client_id',$this->clientId)->where('is_aktif',1)
            ->select('client_id','client_nama','bpjs_cons_id','bpjs_secret','bpjs_user_key','is_bpjs_aktif')
            ->first();
        
        if(!$data) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }   

        $this->credential = [
            'cons_id'       => '29393',
            'secret_key'    => '9wS6BB628F',
            'user_key'      => '6ab9aecf4e8ca01b8a94699f07c36bb9',
        ];
    }

    public function httpGetRequest($url){
        try {            
            $result = BpjsHelper::JknHttpGet('https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev/'.$url, $this->credential);
            return $result;
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function httpPostRequest($url, $data){
        try {            
            $result = BpjsHelper::JknHttpGet('https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev/'.$url, $this->credential, $data);
            return $result;
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengirim data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of poli.
     */
    public function RefPoli(Request $request){
        try {
            $result = $this->httpGetRequest('ref/poli');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=> $result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * Display a listing of dokter.
     */
    public function RefDokter(Request $request){
        try {
            $result = $this->httpGetRequest('ref/dokter');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * Display a listing of dokter.
     */
    public function RefJadwalDokter(Request $request){
        try {
            $kodePoli = $request->unit_id;
            $kodePoliBpjs = Bridging::where('is_aktif',1)->where('bridging_group','JKN')->where("local_resource_id",$kodePoli)->first();
            if(!$kodePoliBpjs) {
                return response()->json(['success' => false, 'message' => 'Data poli / mapping poli tidak ditemukan.']);
            }
            $tanggalPeriksa = $request->tanggal_periksa;
            $result = $this->httpGetRequest('jadwaldokter/kodepoli/'.$kodePoliBpjs.'/tanggal/'.$tanggalPeriksa);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * Display a listing of dokter by date and poli.
     */
    public function RefPoliFingerPrint(Request $request){
        try {
            $result = $this->httpGetRequest('ref/poli/fp');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * Display a listing of dokter by date and poli.
     */
    public function RefPasienFingerPrint(Request $request){
        try {
            $jenisId = $request->jenis_id;
            $idPeserta = $request->no_kepesertaan;
            
            $result = $this->httpGetRequest('ref/pasien/fp/identitas'.$jenisId.'/noidentitas/'.$idPeserta);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }
    /**
     * Update jadwal dokter
     */
    public function UpdateJadwalDokter(Request $request){
        try {
            /**
             * hari 
             * 1 (senin)
             * 2 (selasa)
             * 3 (rabu)
             * 4 (kamis)
             * 5 (jumat)
             * 6 (sabtu)
             * 7 (minggu)
             * 8 (hari libur nasional)}
             **/

            /**
             * format :
             * {
             *      "kodepoli": "ANA",
             *      "kodesubspesialis": "ANA",
             *      "kodedokter": 12346,
             *      "jadwal": [
             *          {
             *              "hari": "1",
             *              "buka": "08:00",
             *              "tutup": "10:00"
             *          },
             *          {
             *              "hari": "2",
             *              "buka": "15:00",
             *              "tutup": "17:00"
             *          }
             *      ]
             *  }
             */

            $kodePoli = $request->unit_id;
            $kodeDokter = $request->dokter_id;
            $hari = $request->hari;
            $mulai = $request->jam_mulai;
            $selesai = $request->jam_selesai;
            $kodePoliBpjs = Bridging::where('is_aktif',1)->where('bridging_group','JKN')->where("local_resource_id",$kodePoli)->first();
            if(!$kodePoliBpjs) {
                return response()->json(['success' => false, 'message' => 'Data poli / mapping poli tidak ditemukan.']);
            }

            $kodeDokterBpjs = Bridging::where('is_aktif',1)->where('bridging_group','JKN')->where("local_resource_id",$kodeDokter)->first();
            if(!$kodeDokterBpjs) {
                return response()->json(['success' => false, 'message' => 'Data dokter / mapping dokter tidak ditemukan.']);
            }

            $data = [
                "kodepoli" => $kodePoliBpjs->bridging_resource_id,
                "kodesubspesialis" => $kodePoliBpjs->bridging_sub_resource_id,
                "kodedokter" => $kodeDokterBpjs->bridging_resource_id,
                "jadwal" => [
                    [
                        "hari" => $hari,
                        "buka" => $mulai,
                        "tutup" => $selesai
                    ]
                ]
            ];
            
            $result = $this->httpPostRequest('jadwaldokter/updatejadwaldokter', $data);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * tambah antrean
     */
    public function TambahAntrean(Request $request){
        try {
            $no_rujukan = $request->no_rujukan;
            $pasien_id = $request->pasien_id;
            $jenis_rujukan = $request->jenis_rujukan;
            
            $tanggal_periksa = $request->tanggal_periksa;
            $jadwal_id = $request->jadwal_id;
            $dokter_id = $request->dokter_id;
            $unit_id = $request->unit_id;

            $pasien_data = $request->pasien;
            $rujukan_data = $request->rujukan;     
            
            $pasien = Pasien::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('pasien_id',$pasien_id)
                ->first();
            
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan']);
            }

            $jadwal = DokterJadwal::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('jadwal_id',$jadwal_id)
                    ->first();
            
            if(!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Jadwal dokter tidak ditemukan']);
            }

            $dokter = Dokter::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('dokter_id',$jadwal_id)
                ->first();

            if(!$dokter) {
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan']);
            }

            $dokterBridging = Bridging::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('local_resource_id',$dokter_id)
                ->first();
            
            if(!$dokterBridging) {
                return response()->json(['success' => false, 'message' => 'Mapping data dokter tidak ditemukan']);
            }

            $poli = UnitPelayanan::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('unit_id',$unit_id)
                ->first();

            if(!$poli) {
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan']);
            }

            $poliBridging = Bridging::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('local_resource_id',$unit_id)
                ->first();
            
            if(!$poliBridging) {
                return response()->json(['success' => false, 'message' => 'Mapping data poli tidak ditemukan']);
            }



            // $postJson = [
            //     "kodebooking" => "16032021A001",
            //     "jenispasien"  => "JKN",
            //     "nomorkartu" => "00012345678",
            //     "nik" => "3212345678987654",
            //     "nohp" => "085635228888",
            //     "kodepoli" => "ANA",
            //     "namapoli" => "Anak",
            //     "pasienbaru" => 0,
            //     "norm" => "123345",
            //     "tanggalperiksa" => "2021-01-28",
            //     "kodedokter" => 12345,
            //     "namadokter" => "Dr. Hendra",
            //     "jampraktek" => "08 =>00-16 =>00",
            //     "jeniskunjungan" => 1,
            //     "nomorreferensi" => "0001R0040116A000001",
            //     "nomorantrean" => "A-12",
            //     "angkaantrean" => 12,
            //     "estimasidilayani" => 1615869169000,
            //     "sisakuotajkn" => 5,
            //     "kuotajkn" => 30,
            //     "sisakuotanonjkn" => 5,
            //     "kuotanonjkn" => 30,
            //     "keterangan" => "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
            // ];
           
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * tambah antrean farmasi
     */
    public function tambahAntreanFarmasi(Request $request){
        try {
            
            /**
             * format :
             *  {
             *      "kodebooking": "16032021A001",
             *      "jenisresep": "racikan" ---> (racikan / non racikan),
             *      "nomorantrean": 1,
             *      "keterangan": ""
             *  }
             */

            $kodeBooking = $request->kode_booking;
            $jenisResep = $request->jenis_resep;
            $nomorAntrean = $request->no_antrean;
            $keterangan = $request->keterangan;

            $data = [
                "kodebooking" => $kodeBooking,
                "jenisresep" => $jenisResep,
                "nomorantrean" => $nomorAntrean,
                "keterangan" => $keterangan,
            ];
            
            $result = $this->httpPostRequest('antrean/farmasi/add', $data);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengirim data. error :'. $e->getMessage()]);
        }
    }

    /**
     * update waktu antrean
     */
    public function updateWaktuAntrean(Request $request){
        try {
            
            /**
             * format :
             *{
             *  "kodebooking": "16032021A001",
             *  "taskid": 1,
             *  "waktu": 1616559330000,
             *  "jenisresep": "Tidak ada/Racikan/Non racikan" ---> khusus yang sudah implementasi antrean farmasi
             *}
             */
            /**
             * "taskid": {
             *      1 (mulai waktu tunggu admisi), 
             *      2 (akhir waktu tunggu admisi/mulai waktu layan admisi), 
             *      3 (akhir waktu layan admisi/mulai waktu tunggu poli), 
             *      4 (akhir waktu tunggu poli/mulai waktu layan poli),  
             *      5 (akhir waktu layan poli/mulai waktu tunggu farmasi), 
             *      6 (akhir waktu tunggu farmasi/mulai waktu layan farmasi membuat obat), 
             *      7 (akhir waktu obat selesai dibuat),
             *      99 (tidak hadir/batal)
             *  },
             *  "waktu": {waktu dalam timestamp milisecond}
             */


            $kodeBooking = $request->kode_booking;
            $taskId = $request->task_id;
            $waktu = $request->waktu;
            $jenisResep = $request->jenis_resep;

            $data = [
                "kodebooking" => $kodeBooking,
                "jenisresep" => $jenisResep,
                "waktu" => $waktu,
                "taskid" => $taskId,
            ];
            
            $result = $this->httpPostRequest('antrean/updatewaktu', $data);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengirim data. error :'. $e->getMessage()]);
        }
    }

    /**
     * update waktu antrean
     */
    public function batalAntrean(Request $request){
        try {
            
            /**
             * format :
             *{
             *  "kodebooking": "16032021A001",
             *  "keterangan": "Terjadi perubahan jadwal dokter, silahkan daftar kembali"
             *}
             */
            $kodeBooking = $request->kode_booking;
            $keterangan = $request->keterangan;

            $data = [
                "kodebooking" => $kodeBooking,
                "keterangan" => $keterangan,
            ];
            
            $result = $this->httpPostRequest('antrean/batal', $data);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengirim data. error :'. $e->getMessage()]);
        }
    }

    
    
    public function getRegId($clientId)
    {
        try {
            $id = $this->clientId.'-'.date('Ymd').'-00001';
            $maxId = Pendaftaran::withTrashed()->where('client_id', $this->clientId)
                ->where('reg_id', 'LIKE', $this->clientId.'-'.date('Ymd').'-%')->max('reg_id');

            if (!$maxId) { $id = $this->clientId.'-'.date('Ymd').'-00001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . date('Ymd') . '-' . Uuid::uuid4()->getHex();
        }
    }

    public function getBookingCode()
    {
        try {
            $id = date('ymd').'00001';
            $maxId = Pendaftaran::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('kode_booking', 'LIKE', date('ymd').'%')->max('kode_booking');
            
            if (!$maxId) { $id = date('ymd').'00001'; } 
            else {
                $maxId = str_replace( date('ymd'), '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = date('ymd').'0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = date('ymd').'000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = date('ymd').'00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = date('ymd').'0' . $count; } 
                else { $id = date('ymd'). $count; }
            }
            return $id;
        } 
        catch (\Exception $e) { return null; }
    }

    public function getAntrianNo($prefixNo, $tglPeriksa, $jadwalId)
    {
        try {
            $id = $prefixNo.'001';
            $angka = null;
            $maxId = Pendaftaran::where('client_id', $this->clientId)->withTrashed()
                ->where('no_antrian','ILIKE',$prefixNo.'%')->where('tgl_periksa',$tglPeriksa)
                ->where('jadwal_id',$jadwalId)->max('no_antrian');
            
            if (!$maxId) { $id = $prefixNo.'001'; $angka = 1;} 
            else {
                $maxId = str_replace( $prefixNo, '', $maxId);
                $count = $maxId + 1;
                $angka = $count;
                if ($count < 10) { $id = $prefixNo.'00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $prefixNo.'0' . $count; } 
                else { $id = $prefixNo . $count; } 
            }

            $data['id'] = $id;
            $data['angka'] = $angka;
            
            return $data;
        } 
        catch (\Exception $e) { return null; }
    }

}
