<?php

namespace Modules\BridgingBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ManajemenUser\Entities\Client as ClientWs;

use BpjsHelper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LZCompressor\LZString;

class SuratKontrolController extends Controller
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
        
        if(!$data) { return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']); }   
        
        $this->credential = [
            'cons_id'       => $data->bpjs_cons_id,
            'secret_key'    => $data->bpjs_secret,
            'user_key'      => $data->bpjs_user_key 
        ];
    }

    public function httpGetRequest($url){
        try {
            $result = BpjsHelper::BpjsHttpGet('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credential);
            return $result;
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function httpPostRequest($url, $data){
        try {
            $result = BpjsHelper::BpjsHttpPost('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credential, $data);
            return $result; 
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function httpDeleteRequest($url, $data){
        try {
            $result = BpjsHelper::BpjsHttpDelete('https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/'.$url, $this->credential, $data);
            return $result; 
        }           
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data', 'error' => $e->getMessage()]);
        }
    }

    public function cariNomorSuratKontrol(Request $request, $noSuratKontrol) {
        try {
            $result = $this->httpGetRequest('RencanaKontrol/noSuratKontrol/'.$noSuratKontrol);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data SEP. error: '.$e->getMessage()]);
        }
    }

    public function insertRencanaKontrol(Request $request) {
        try {
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses simpan data. Data pasien tidak ditemukan.']);
            }

            /**
             * contoh insert :
             *  {
             *      "request": {
             *          "noSEP":"0301R0111018V000006",
             *          "kodeDokter":"12345",
             *          "poliKontrol":"INT",
             *          "tglRencanaKontrol":"2021-03-20",
             *          "user":"ws"
             *      }
             *  }
             * 
             */

            /**
             * Format Data :
             *  {
             *      "request": {
             *          "noSEP":"{nomor SEP}",
             *          "kodeDokter":"{kode dokter}",
             *          "poliKontrol":"{kode poli}",
             *          "tglRencanaKontrol":"{Rawat Jalan: diisi tanggal rencana kontrol, format: yyyy-MM-dd. Rawat Inap: diisi tanggal SPRI, format: yyyy-MM-dd}",
             *          "user":"{user pembuat rencana kontrol}"
             *      }
             *  }
             * 
             **/
            
                        
            //coding    
            $data = [
                'request' => [
                    'noSEP' => $request->no_sep,
                    'kodeDokter' => $request->dokter_id,
                    'poliKontrol' => $request->poli_id,
                    'tglRencanaKontrol' => $request->tgl_rencana_kontrol,
                    'user' => Auth::user()->username,
                ]
            ];
            
            $result = $this->httpPostRequest('RencanaKontrol/insert', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data Surat Kontrol. error: '.$e->getMessage()]);
        }
    }

    public function updateRencanaKontrol(Request $request) {
        try {
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses simpan data. Data pasien tidak ditemukan.']);
            }

            /**
             * contoh insert :
             *  {
             *      "request": {
             *          "noSEP":"0301R0111018V000006",
             *          "kodeDokter":"12345",
             *          "poliKontrol":"INT",
             *          "tglRencanaKontrol":"2021-03-20",
             *          "user":"ws"
             *      }
             *  }
             * 
             */

            /**
             * Format Data :
             *  {
             *      "request": {
             *          "noSEP":"{nomor SEP}",
             *          "kodeDokter":"{kode dokter}",
             *          "poliKontrol":"{kode poli}",
             *          "tglRencanaKontrol":"{Rawat Jalan: diisi tanggal rencana kontrol, format: yyyy-MM-dd. Rawat Inap: diisi tanggal SPRI, format: yyyy-MM-dd}",
             *          "user":"{user pembuat rencana kontrol}"
             *      }
             *  }
             * 
             **/
            
                        
            //coding    
            $data = [
                'request' => [
                    'noSuratKontrol' => $request->no_surat_kontrol,
                    'noSEP' => $request->no_sep,
                    'kodeDokter' => $request->dokter_id,
                    'poliKontrol' => $request->poli_id,
                    'tglRencanaKontrol' => $request->tgl_rencana_kontrol,
                    'user' => Auth::user()->username,
                ]
            ];
            
            $result = $this->httpPutRequest('RencanaKontrol/Update', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data Surat Kontrol. error: '.$e->getMessage()]);
        }
    }

    public function deleteRencanaKontrol(Request $request, $noSuratKontrol) {
        try {
            $data = [
                'request' => [
                    't_suratkontrol' => [
                        'noSuratKontrol' => $noSuratKontrol,
                        'user' => Auth::user()->username,
                    ]
                ]
            ];
            $result = $this->httpDeleteRequest('RencanaKontrol/Delete', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ada kesalahan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data. error: '.$e->getMessage()]);
        }
    }

    public function insertSpri(Request $request) {
        try {
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses simpan data. Data pasien tidak ditemukan.']);
            }

            /**
             * contoh insert :
             *  {
             *      "request":
             *          {
             *              "noKartu":"0001116500714",
             *              "kodeDokter":"31537",
             *              "poliKontrol":"BED",
             *              "tglRencanaKontrol":"2021-04-13",
             *              "user":"sss"
             *          }
             *  }
             * 
             */

            /**
             * Format Data :
             *  {
             *      "request":
             *          {
             *              "noKartu":"{nomor Kartu}",
             *              "kodeDokter":"{kode dokter}",
             *              "poliKontrol":"{poli kontrol}",
             *              "tglRencanaKontrol":"{tgl rencana kontrol, format:yyyy-MM-dd}",
             *              "user":"{user pembuat spri}"
             *          }
             *  }
             * 
             **/
            
                        
            //coding    
            $data = [
                'request' => [
                    'noKartu' => $request->no_kartu,
                    'kodeDokter' => $request->dokter_id,
                    'poliKontrol' => $request->poli_id,
                    'tglRencanaKontrol' => $request->tgl_rencana_kontrol,
                    'user' => Auth::user()->username,
                ]
            ];
            
            $result = $this->httpPostRequest('RencanaKontrol/InsertSPRI', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data SPRI. error: '.$e->getMessage()]);
        }
    }

    public function updateSpri(Request $request) {
        try {
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses simpan data. Data pasien tidak ditemukan.']);
            }

            /**
             * contoh insert :
             *  {
             *      "request": {
             *          "noSPRI":"0301R0111018V000006",
             *          "kodeDokter":"12345",
             *          "poliKontrol":"INT",
             *          "tglRencanaKontrol":"2021-03-20",
             *          "user":"ws"
             *      }
             *  }
             * 
             */

            /**
             * Format Data :
             *  {
             *      "request": {
             *          "noSPRI":"{nomor SEP}",
             *          "kodeDokter":"{kode dokter}",
             *          "poliKontrol":"{kode poli}",
             *          "tglRencanaKontrol":"{Rawat Jalan: diisi tanggal rencana kontrol, format: yyyy-MM-dd. Rawat Inap: diisi tanggal SPRI, format: yyyy-MM-dd}",
             *          "user":"{user pembuat rencana kontrol}"
             *      }
             *  }
             * 
             **/
            
                        
            //coding    
            $data = [
                'request' => [
                    'noSPRI' => $request->no_spri,
                    'noSEP' => $request->no_sep,
                    'kodeDokter' => $request->dokter_id,
                    'poliKontrol' => $request->poli_id,
                    'tglRencanaKontrol' => $request->tgl_rencana_kontrol,
                    'user' => Auth::user()->username,
                ]
            ];
            
            $result = $this->httpPutRequest('RencanaKontrol/UpdateSPRI', $data);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data SPRI. error: '.$e->getMessage()]);
        }
    }
}
