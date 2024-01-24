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

class RujukanController extends Controller
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

    public function dataRujukanByNomorPcare(Request $request, $noRujukan) {
        try {
            $result = $this->httpGetRequest('Rujukan/'.$noRujukan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function dataRujukanByNomorRS(Request $request, $noRujukan) {
        try {
            $result = $this->httpGetRequest('Rujukan/RS/'.$noRujukan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function dataRujukanByNokaPcare(Request $request, $noKartu) {
        try {
            $result = $this->httpGetRequest('Rujukan/Peserta/'.$noKartu);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function dataRujukanByNokaRS(Request $request, $noKartu) {
        try {
            $result = $this->httpGetRequest('Rujukan/RS/Peserta/'.$noKartu);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function listRujukanByNokaPcare(Request $request, $noKartu) {
        try {
            $result = $this->httpGetRequest('Rujukan/List/Peserta/'.$noKartu);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function listRujukanByNokaRS(Request $request, $noKartu) {
        try {
            $result = $this->httpGetRequest('Rujukan/RS/List/Peserta/'.$noKartu);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function insertRujukanRS(Request $request) {
        try {
            /**
             * format data :
             * {
             *      "request": {
             *          "t_rujukan": {
             *                   "noSep": "0301R0010321V000003",
             *                   "tglRujukan": "2021-03-18",
             *                   "tglRencanaKunjungan":"2021-03-19",
             *                   "ppkDirujuk": "03010402",
             *                   "jnsPelayanan": "1",
             *                   "catatan": "test",
             *                   "diagRujukan": "A15",
             *                   "tipeRujukan": "2", "{0->Penuh, 1->Partial, 2->balik PRB}",
             *                   "poliRujukan": "", "{kosong untuk tipe rujukan 2, harus diisi jika 0 atau 1}",
             *                   "user": "Coba Ws"
             *          }
             *      }
             *  }
             */
            
            $data = [
                "request" => [
                    "t_rujukan" => [
                        "noSep" => $request->no_sep,
                        "tglRujukan" => $request->tgl_rujukan,
                        "tglRencanaKunjungan" => $request->tgl_rencana_kunjungan,
                        "ppkDirujuk" => $request->ppk_dirujuk,
                        "jnsPelayanan" => $request->jns_pelayanan,
                        "catatan" => $request->catatan,
                        "diagRujukan" => $request->diag_rujukan,
                        "tipeRujukan" => $request->tipe_rujukan,
                        "poliRujukan" => $request->poli_rujukan,
                        "user" => Auth::user()->username
                    ]
                ]
            ];


            $result = $this->httpPostRequest('Rujukan/2.0/insert',$data);
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
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function updateRujukanRS(Request $request) {
        try {
            /**
             * format data :
             * {
             *      "request": {
             *          "t_rujukan": {
             *                   "noRujukan": "0301R0010321V000003",
             *                   "tglRujukan": "2021-03-18",
             *                   "tglRencanaKunjungan":"2021-03-19",
             *                   "ppkDirujuk": "03010402",
             *                   "jnsPelayanan": "1",
             *                   "catatan": "test",
             *                   "diagRujukan": "A15",
             *                   "tipeRujukan": "2", "{0->Penuh, 1->Partial, 2->balik PRB}",
             *                   "poliRujukan": "", "{kosong untuk tipe rujukan 2, harus diisi jika 0 atau 1}",
             *                   "user": "Coba Ws"
             *          }
             *      }
             *  }
             */
            
            $data = [
                "request" => [
                    "t_rujukan" => [
                        "noRujukan" => $request->no_rujukan,
                        "tglRujukan" => $request->tgl_rujukan,
                        "tglRencanaKunjungan" => $request->tgl_rencana_kunjungan,
                        "ppkDirujuk" => $request->ppk_dirujuk,
                        "jnsPelayanan" => $request->jns_pelayanan,
                        "catatan" => $request->catatan,
                        "diagRujukan" => $request->diag_rujukan,
                        "tipeRujukan" => $request->tipe_rujukan,
                        "poliRujukan" => $request->poli_rujukan,
                        "user" => Auth::user()->username
                    ]
                ]
            ];


            $result = $this->httpPutRequest('Rujukan/2.0/Update',$data);
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
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function deleteRujukanRS(Request $request) {
        try {
            /**
             * format data :
             * {
             *      "request": {
             *          "t_rujukan": {
             *                   "noRujukan": "0301R0010321V000003",
             *                   "tglRujukan": "2021-03-18",
             *                   "tglRencanaKunjungan":"2021-03-19",
             *                   "ppkDirujuk": "03010402",
             *                   "jnsPelayanan": "1",
             *                   "catatan": "test",
             *                   "diagRujukan": "A15",
             *                   "tipeRujukan": "2", "{0->Penuh, 1->Partial, 2->balik PRB}",
             *                   "poliRujukan": "", "{kosong untuk tipe rujukan 2, harus diisi jika 0 atau 1}",
             *                   "user": "Coba Ws"
             *          }
             *      }
             *  }
             */
            
            $data = [
                "method" => 'DELETE',
                "request" => [
                    "t_rujukan" => [
                        "noRujukan" => $request->no_rujukan,                        
                        "user" => Auth::user()->username
                    ]
                ]
            ];


            $result = $this->httpDeleteRequest('Rujukan/delete',$data);
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
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data. error: '.$e->getMessage()]);
        }
    }

    public function listSpesialistikRujukanRS(Request $request) {
        try {
            $ppkTujuan = $request->ppk;
            $tglRujukan = $request->tanggal;
            
            $result = $this->httpGetRequest('Rujukan/ListSpesialistik/PPKRujukan/'.$ppkRujukan.'/TglRujukan/'.$tglRujukan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function listSaranaRS(Request $request, $kodePpkRujukan) {
        try {
            $result = $this->httpGetRequest('Rujukan/ListSarana/PPKRujukan/'.$kodePpkRujukan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function listRujukanKeluarRS(Request $request) {
        try {
            $tglMulai = $request->tgl_mulai;
            $tglAkhir = $request->tgl_akhir;
            
            $result = $this->httpGetRequest('Rujukan/Keluar/List/tglMulai/'.$tglMulai.'/tglAkhir/'.$tglAkhir);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function insertRujukanKhususRS(Request $request) {
        try {
            /**
            * format data :
            *  {
            *       "noRujukan": "0301U0331019P003283",
            *       "diagnosa": [
            *               {"kode": "P;N18"},  //{"kode": "{primer/sekunder};{kodediagnosa}"}
            *               {"kode": "S;N18.1"}
            *       ],
            *       "procedure":  [
            *               {"kode": "39.95"} //{"kode": "{kodeprocedure}"}
            *       ],
            *       "user": "Coba Ws"
            *   }             
            */
            
            $data = [
                "request" => [
                    "t_rujukan" => [
                        "noRujukan" => $request->no_rujukan,
                        "diagnosa" => [
                            "kode" => $request->jenis_diag.';'.$request->kode_diag, //nantinya kemungkinan data berupa array
                        ],
                        "procedure" => [
                            ["kode" => $request->$request->kode_procedure,] //nantinya kemungkinan data berupa array
                        ],
                        "user" => Auth::user()->username
                    ]
                ]
            ];


            $result = $this->httpPostRequest('Rujukan/Khusus/Insert',$data);
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
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function deleteRujukanKhususRS(Request $request) {
        try {
            /**
             * format data :
             * {
             *      "request": {
             *          "t_rujukan": {
             *                   "idRujukan": "98865",
             *                   "noRujukan": "0301U0331019P003283",
             *                   "user": "Coba Ws"
             *          }
             *      }
             *  }
             */
            
            $data = [
                "method" => 'DELETE',
                "request" => [
                    "t_rujukan" => [
                        "idRujukan" => $request->id_rujukan,                        
                        "noRujukan" => $request->no_rujukan,                        
                        "user" => Auth::user()->username
                    ]
                ]
            ];


            $result = $this->httpDeleteRequest('Rujukan/Khusus/delete',$data);
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
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data. error: '.$e->getMessage()]);
        }
    }

    public function listRujukanKhususRS(Request $request) {
        try {
            $bulan = $request->bulan; //berisi angka 1 - 12
            $tahun = $request->tahun;
            
            $result = $this->httpGetRequest('Rujukan/Khusus/List/Bulan/'.$bulan.'/Tahun/'.$tahun);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function listRujukanKeluarByNoRujukanRS(Request $request, $noRujukan) {
        try {
            $result = $this->httpGetRequest('Rujukan/Keluar/'.$noRujukan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->rujukan]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    public function dataJumlahSepRujukanRS(Request $request) {
        try {
            /**
             *  Parameter 1: Jenis Rujukan 1 -> fktp, 2 -> fkrtl
             *  Parameter 2: No Rujukan
             */
            $jenisRujukan = $request->jenis_rujukan;
            $noRujukan = $request->no_rujukan;
            
            $result = $this->httpGetRequest('Rujukan/JumlahSEP/'.$jenisRujukan.'/'.$noRujukan);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->jumlahSEP]);
            }
            else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data tidak ditemukan. ('.$result->metaData->code.')'.$result->metaData->message
                ]);
            }
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

}
