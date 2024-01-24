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

class MonitoringBpjsController extends Controller
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

    /**
     * Display a listing of the resource dokter.
     */
    public function dataKunjungan(Request $request, $param, $param2){
        try {
            $result = $this->httpGetRequest('Monitoring/Kunjungan/Tanggal/'.$param.'/JnsPelayanan/'.$param2);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->sep]);
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
     * Display a listing of the resource monitoring klaim
     */
    public function dataKlaim(Request $request, $tglKunjungan, $jnsPelayanan, $status){
        try {
            $result = $this->httpGetRequest('Monitoring/Klaim/Tanggal/'.$tglKunjungan.'/JnsPelayanan/'.$jnsPelayanan.'/Status/'.$status);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->klaim]);
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
     * Display a listing of the resource monitoring pelayanan
     */
    public function dataPelayanan(Request $request, $noka, $tglMulai, $tglSelesai){
        try {
            $result = $this->httpGetRequest('Monitoring/HistoriPelayanan/NoKartu/'.$noka.'/tglMulai/'.$tglMulai.'/tglAkhir/'.$tglSelesai);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->histori]);
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
     * Display a listing of the resource monitoring jasa raharja
     */
    public function dataJasaRaharja(Request $request, $jenisPelayanan, $tglMulai, $tglSelesai){
        try {
            $result = $this->httpGetRequest('Monitoring/JasaRaharja/JnsPelayanan/'.$jenisPelayanan.'/tglMulai/'.$tglMulai.'/tglAkhir/'.$tglSelesai);
            if($result->metaData->code == '200') {
                return response()->json(['success' => true, 'message' => 'OK', 'data'=>$result->response->sep]);
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
