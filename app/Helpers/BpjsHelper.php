<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use App\Models\ActivityLog;

//use Modules\ManajemenUser\Entities\Client as ClientWs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LZCompressor\LZString;
 
class BpjsHelper {    

    public static function bpjsSignature($consId, $secret, $userKey){
        date_default_timezone_set('UTC');
        $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $consId."&".$tStamp, $secret, true);
        $encodedSignature = base64_encode($signature);

        $respData = [
            'X-cons-id' => $consId,
            'X-timestamp' => $tStamp,
            'X-signature' => $encodedSignature,
            'user_key' => $userKey,
            'encrypt_key' => $consId.$secret.$tStamp,
        ];
        return $respData;
    }

    public static function stringDecrypt($key, $string) {
        $encrypt_method = 'AES-256-CBC';
        // hash
        $key_hash = hex2bin(hash('sha256', $key));
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
    
        return $output;
    }

    /*** untuk Vclaim BPJS */
    public static function decryptResponse($response, $key) {
        $result = json_decode($response);
		if ($result->metaData->code == "200" && is_string($result->response)) {
            return self::doMaping($result->metaData, $result->response, $key);
        }
        return json_encode($result);
    }

    public static function doMaping($metaData, $response, $key) {
        $data = [
            "metaData" => $metaData,
            "response" => json_decode(self::decompressed(self::stringDecrypt($key, $response)))
        ];
		return json_encode($data);
    }

    protected static function decompressed($dataString) {
        return LZString::decompressFromEncodedURIComponent($dataString);
    }

    /**
     * untuk JKN antrian mobile
     */
    public static function JKNDecryptResponse($response, $key) {
        $result = json_decode($response);
		if ($result->metadata->code == "1" && is_string($result->response)) {
            return self::doMaping($result->metadata, $result->response, $key);
        }
        return json_encode($result);
    }

    public static function BpjsHttpGet($endpoint, $credential) {
        /**
         * credential berisi :
         * cons_id => consId
         * secret_key => secret_key
         * user_key => user_key 
         */
        try {
            $signature = self::bpjsSignature($credential['cons_id'], $credential['secret_key'], $credential['user_key']);
            if(!$signature) { return 'data signature gagal dibuat'; }
            
            $client = new Client();
            $response = $client->get($endpoint, [
                'headers' => [
                    'X-cons-id' => $signature['X-cons-id'],
                    'X-timestamp' => $signature['X-timestamp'],
                    'X-signature' => $signature['X-signature'],
                    'user_key' => $signature['user_key'],
                ]
            ]);
            $result = json_decode(self::decryptResponse($response->getBody()->getContents(),$signature['encrypt_key']));
            return $result;
            //return $response;
        } 
        catch (RequestException $e) {
            $result = self::responseError($e); return $result;
        } 
    } 

    public static function JknHttpGet($endpoint, $credential) {
        /**
         * credential berisi :
         * cons_id => consId
         * secret_key => secret_key
         * user_key => user_key 
         */
        try {
            $signature = self::bpjsSignature($credential['cons_id'], $credential['secret_key'], $credential['user_key']);
            if(!$signature) { return null; }
            
            $client = new Client();
            $response = $client->get($endpoint, [
                'headers' => [
                    'X-cons-id' => $signature['X-cons-id'],
                    'X-timestamp' => $signature['X-timestamp'],
                    'X-signature' => $signature['X-signature'],
                    'user_key' => $signature['user_key'],
                ]
            ]);
            $result = json_decode(self::JKNDecryptResponse($response->getBody()->getContents(),$signature['encrypt_key']));
            return $result;
        } 
        catch (RequestException $e) {
            $result = self::responseError($e); return $result;
        } 
    } 


    public function BpjsHttpPost($endpoint, $credential, $data)
    {
        try {
            $signature = self::bpjsSignature($credential['cons_id'], $credential['secret_key'], $credential['user_key']);
            if(!$signature) { return null; }

            $header = [
                'X-cons-id' => $signature['X-cons-id'],
                'X-timestamp' => $signature['X-timestamp'],
                'X-signature' => $signature['X-signature'],
                'user_key' => $signature['user_key'],
            ];

            $response = $this->client->post($endpoint, ['headers' => $header, 'body' => $data]);
			$result = json_decode(self::decryptResponse($response->getBody()->getContents(),$signature['encrypt_key']));
            return $result;

            // $result = $response->getBody()->getContents();
            // return $result;
        } catch (RequestException $e) {
            $result = $this->responseError($e);
            return $result;
        } 
    }
    
    public function BpjsHttpDelete($endpoint, $credential, $data)
    {
        try {
            $signature = self::bpjsSignature($credential['cons_id'], $credential['secret_key'], $credential['user_key']);
            if(!$signature) { return null; }

            $header = [
                'X-cons-id' => $signature['X-cons-id'],
                'X-timestamp' => $signature['X-timestamp'],
                'X-signature' => $signature['X-signature'],
                'user_key' => $signature['user_key'],
            ];

            $response = $this->client->delete($endpoint, ['headers' => $header, 'body' => $data]);
			$result = json_decode(self::decryptResponse($response->getBody()->getContents(),$signature['encrypt_key']));
            return $result;

            // $result = $response->getBody()->getContents();
            // return $result;
        } catch (RequestException $e) {
            $result = $this->responseError($e);
            return $result;
        } 
    }



    public function jknHttpPost($endpoint, $credential, $data)
    {
        try {
            $signature = self::bpjsSignature($credential['cons_id'], $credential['secret_key'], $credential['user_key']);
            if(!$signature) { return null; }

            $header = [
                'X-cons-id' => $signature['X-cons-id'],
                'X-timestamp' => $signature['X-timestamp'],
                'X-signature' => $signature['X-signature'],
                'user_key' => $signature['user_key'],
            ];

            $response = $this->client->post($endpoint, ['headers' => $header, 'body' => $data]);
			$result = $response->getBody()->getContents();
            return $result;
        } catch (RequestException $e) {
            $result = $this->responseError($e);
            return $result;
        } 
    } 




    public function httpPost($endpoint, $header, $data)
    {
        try {
            $response = $this->client->post($endpoint, ['headers' => $header, 'body' => $data]);
			$result = $response->getBody()->getContents();
            return $result;
        } catch (RequestException $e) {
            $result = $this->responseError($e);
            return $result;
        } 
    } 
    public function httpPut($endpoint, $header, $data)
    {
         try {
            $response = $this->client->put($endpoint, ['headers' => $header, 'body' => $data]);
			$result = $response->getBody()->getContents();
            return $result;
        } catch (RequestException $e) {
            $result = $this->responseError($e);
            return $result;
        } 
    }
    public function httpDelete($endpoint, $header, $data)
    {
         try {
            $response = $this->client->delete($endpoint, ['headers' => $header, 'body' => $data]);
			$result = $response->getBody()->getContents();
            return $result;
        } catch (RequestException $e) {
            $result = $this->responseError($e);
            return $result;
        } 
    }
    protected static function responseError($error)
    {
        $result = [
            'metaData' => [
                'code' => $error->getCode(),
                'message' => $error->getMessage()
            ],
            'response' => null
        ];

        return json_encode($result);
    }
}
