<?php

namespace Modules\BridgingBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ManajemenUser\Entities\Client as ClientWs;

use BpjsHelper;

// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;
// use LZCompressor\LZString;

class ReferensiController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function refSpesialistik(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/spesialistik');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error :'. $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource propinsi.
     */
    public function refPropinsi(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/propinsi');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource kabupaten.
     */
    public function refKabupaten(Request $request,$propBpjsId){
        try {
            $result = $this->httpGetRequest('referensi/kabupaten/propinsi/'.$propBpjsId);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource kecamatan.
     */
    public function refKecamatan(Request $request, $kabBpjsId){
        try {
            $result = $this->httpGetRequest('referensi/kecamatan/kabupaten/'.$kabBpjsId);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource kecamatan.
     */
    public function refKelasRawat(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/kelasrawat');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource kecamatan.
     */
    public function refRuangRawat(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/ruangrawat');
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

    /**
     * Display a listing of the resource kecamatan.
     */
    public function refCaraKeluar(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/carakeluar');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource pasca pulang.
     */
    public function refPascaPulang(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/pascapulang');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource ICD diagnosa.
     */
    public function refDiagnosa(Request $request, $param){
        try {
            $result = $this->httpGetRequest('referensi/diagnosa/'.$param);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource ICD procedure.
     */
    public function refProcedure(Request $request, $param){
        try {
            $result = $this->httpGetRequest('referensi/procedure/'.$param);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource Diagnosa PRB.
     */
    public function refPrbDiagnosa(Request $request){
        try {
            $result = $this->httpGetRequest('referensi/diagnosaprb');
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource PRB Obat.
     */
    public function refPrbObat(Request $request, $param){
        try {
            $result = $this->httpGetRequest('referensi/obatprb/'.$param);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource Faskes.
     */
    public function refFaskes(Request $request, $param, $param2){
        try {
            $result = $this->httpGetRequest('referensi/faskes/'.$param.'/'.$param2);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource poli.
     */
    public function refPoli(Request $request, $param){
        try {
            $result = $this->httpGetRequest('referensi/poli/'.$param);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->poli]);
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

    /**
     * Display a listing of the resource dokter.
     */
    public function refDokter(Request $request, $param){
        try {
            $result = $this->httpGetRequest('referensi/dokter/'.$param);            
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

    /**
     * Display a listing of the resource dokter DPJP.
     */
    public function refDpjp(Request $request, $jnsPelayanan, $tglPelayanan, $kodeSpesialis){
        try {
            $result = $this->httpGetRequest('referensi/dokter/pelayanan/'.$jnsPelayanan.'/tglPelayanan/'.$tglPelayanan.'/Spesialis/'.$kodeSpesialis);
            return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->list]);
        }   
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data. error: '.$e->getMessage()]);
        }
    }
}
