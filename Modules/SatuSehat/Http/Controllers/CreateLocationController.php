<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\RuangPelayanan;
use Modules\ManajemenUser\Entities\Client;
use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kelurahan;
use Modules\MasterData\Entities\Kota;
use Modules\SatuSehat\Entities\Bridging;

use DB;

class CreateLocationController extends Controller
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
        try{
            $rowNumber = 10;
            $keyword = '%%';
            if($request->has('per_page')) {
                $rowNumber = $request->get('per_page');
                if($rowNumber == 'ALL') { $rowNumber = Bridging::where('client_id',$this->clientId)->count(); }
            }
            if($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            $data = Bridging::where('client_id',$this->clientId)->where('is_aktif',true)->where('resource', 'Location')
                ->where(function($q) use ($keyword){
                    $q->where('bridging_id','ILIKE',$keyword)
                    ->orWhere('bridging_group','ILIKE',$keyword);
                })->paginate($rowNumber);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan. error: '.$e->getMessage()]);
        }
    }

    public function getUrl()
    {
        $baseUrl = 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1';
        return $baseUrl;
    }

    public function createToken()
    {
        $response = Http::asForm()->post('https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials', [
            'client_id' => 'KQc4OiEXQ8XPP1UeMkxLnyZnAzooGLi6pyqR17ZPDG6hmlVt',
            'client_secret' => 'FHxvSs1IIGghiiuj641P9QCIAkQN0F4yVtD2Um89rqfMQpjm6GtZuQseAyVc7c51',
        ]);
        $result = json_decode($response->getBody(), true);
        $value = $result['access_token'];
        return (string)$value;
    }

    public function createLocation(Request $request)
    {
        $dataCentral = Client::where('client_id',$this->clientId)->first();

        $dataClient = Propinsi::select('tb_propinsi.kode_kemendagri AS kode_propinsi','tb_kota.kode_kemendagri AS kode_kota','tb_kecamatan.kode_kemendagri AS kode_kecamatan','tb_kelurahan.kode_kemendagri AS kode_kelurahan')
        ->leftJoin('tb_kota', 'tb_propinsi.propinsi_id', '=', 'tb_kota.propinsi_id')
        ->leftJoin('tb_kecamatan', 'tb_propinsi.propinsi_id', '=', 'tb_kecamatan.propinsi_id')
        ->leftJoin('tb_kelurahan', 'tb_propinsi.propinsi_id', '=', 'tb_kelurahan.propinsi_id')
        ->where('tb_propinsi.client_id',$this->clientId)
        ->where('tb_propinsi.propinsi',$dataCentral->propinsi)
        ->where('tb_kota.kota',$dataCentral->kota)
        ->where('tb_kecamatan.kecamatan',$dataCentral->kecamatan)
        ->where('tb_kelurahan.kelurahan',$dataCentral->kelurahan)
        ->first();

        $dataRuang = RuangPelayanan::where('client_id',$this->clientId)->where('ruang_id', $request->ruang_id)->first();

        if(!$dataCentral)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $body = [
            "resourceType" => "Location",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/1000001",
                    "value" => $request->ruang_id
                ]
            ],
            "status" => "active",
            "name" => $request->ruang_nama,
            "description" => $dataRuang->deskripsi,
            "mode" => "instance",
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => $dataCentral->no_telepon,
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" => $dataCentral->email,
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    $dataRuang->lokasi.', '.$dataCentral->alamat
                ],
                "city" => $dataCentral->kota,
                "postalCode" => $dataCentral->kodepos,
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" => $dataClient->kode_propinsi
                            ],
                            [
                                "url" => "city",
                                "valueCode" => $dataClient->kode_kota
                            ],
                            [
                                "url" => "district",
                                "valueCode" => $dataClient->kode_kecamatan
                            ],
                            [
                                "url" => "village",
                                "valueCode" => $dataClient->kode_kelurahan
                            ]
                        ]
                    ]
                ]
            ],
            "physicalType" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "ro",
                        "display" => "Room"
                    ]
                ]
            ],
            "position" => [
                "longitude" => -6.23115426275766,
                "latitude" => 106.83239885393944,
                "altitude" => 0
            ],
            "managingOrganization" => [
                "reference" => "Organization/10083042",
            ]
        ];
        
        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Location', $body);

        $value = json_decode($result->getBody(), true);

        DB::connection('dbclient')->beginTransaction();

        $data = new Bridging();

        $data->bridging_id = $this->getBridgingId();
        $data->bridging_group = 'SATUSEHAT';
        $data->local_resource_id = $request->ruang_id;
        $data->local_resource_name = $request->ruang_nama;
        $data->bridging_resource_id = $value['id'];
        $data->bridging_resource_name = $value['name'];
        $data->resource = 'Location';
        $data->is_aktif = 1;
        $data->client_id = $this->clientId;
        // $data->created_by = Auth::user()->username;
        // $data->updated_by = Auth::user()->username;
        $data->created_by = 'adminis';
        $data->updated_by = 'adminis';
        $success = $data->save();
    
        if(!$success) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false,'message' => 'ada kesalahan dalam mapping data ruang.']);
        }
        DB::connection('dbclient')->commit();
        return response()->json(['success' => true,'message' => 'Pelayanan berhasil dimapping.','data'=>$data]);
    }

    public function searchID($id)
    {   
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Organization/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function getBridgingId()
    {
        try {
            $id = 'BRDG'.date('Y').'-0001';
            $maxId = Bridging::withTrashed()->where('bridging_id','LIKE','BRDG'.date('Y').'%')->max('bridging_id');
            if (!$maxId) { $id = 'BRDG'.date('Y').'-0001'; }
            else {
                $maxId = str_replace('BRDG'.date('Y').'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = 'BRDG'.date('Y').'-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'BRDG'.date('Y').'-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = 'BRDG'.date('Y').'-0'.$count; } 
                else { $id = 'BRDG'.date('Y').'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'BRDG'.date('Y').'-'.Uuid::uuid4()->getHex();
        }
    }
}