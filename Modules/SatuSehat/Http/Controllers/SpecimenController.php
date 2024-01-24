<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

use Modules\ManajemenUser\Entities\Client;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Referensi;

class SpecimenController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function getUrl()
    {
        $baseUrl = 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1';
        return $baseUrl;
    }

    public function createToken()
    {
        $response = Http::asForm()->post('https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials', [
            'client_id' => 'uKCKCZuFkkudD1225L8RHFtlH5y6RHQYGDaRjxJJBnE14sk8',
            'client_secret' => 'LtQQVc7Cpp9iN1Rsz1cWz9YG60QV0VsaAALxOJFjHHjKfurflqhWHvdyq4bvc7XS',
        ]);
        $result = json_decode($response->getBody(), true);
        $value = $result['access_token'];
        return (string)$value;
    }

    public function lists()
    {
        //
    }

    public function createSpecimen()
    {   
        $client     = Client::where('client_id',$this->clientId)->first();
        $pasien     = Pasien::where('client_id',$this->clientId)->first();
        $dokter     = Dokter::where('client_id',$this->clientId)->first();
        $unit       = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang      = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();
        
        if(!$pasien || !$client)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $current = Carbon::now();
        $dt = $current->format('c');

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Specimen', [
            "resourceType" => "Specimen",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/specimen/10083042",
                    "value" => "SPEC000001",
                    "assigner" => [
                        // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                        "reference" => "Organization/10083042"
                    ]
                ]
            ],
            "status" => "available",
            "type" => [
                "coding" => [
                    [
                        // Kode terdapat dibuku pada lampiran 6 (326)
                        "system" => "http://snomed.info/sct",
                        "code" => "119297000",
                        "display" => "Blood specimen"
                    ]
                ]
            ],
            "collection" => [
                "method" => [
                    "coding" => [
                        [
                            // Kode terdapat dibuku pada lampiran 6 (328)
                            "system" => "https://snomed.info/sct",
                            "code" => "82078001",
                            "display" => "Collection of blood specimen for laboratory"
                        ]
                    ]
                ],
                "collectedDateTime" => $dt,
                "collector" => [
                    "reference" => "Practitioner/N10000001"
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "request" => [
                [
                    // ID Service Request Laboratorium yang sudah didaftarkan
                    "reference" => "ServiceRequest/e1de839a-6519-4f31-a470-f57c0ec15cdc"
                ]
            ],
            "receivedTime" => $dt
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : f2e97186-42c9-4104-a468-c2e0428e0e10
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Specimen/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchSubject(Request $request)
    {
        
        // Contoh subject 100000030009 id lokal pasien
        // Contoh collected 2022-12-22 tanggal dibuatnya specimen 
        // Contoh collector N10000001 ID dokter
        // Dynamic search keyword return COUNT($request->all()); menghitung jumlah request
        // Pencarian harus ada subject, jika tidak ada maka akan error meminta subject diisi
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Specimen',
            [
                'subject'   => $request->subject,   // Pasien ID Lokal
                'collected' => $request->collected,  
                'collector' => $request->collector,  
            ]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateSpecimen($id)
    {   
        // Contoh id = f2e97186-42c9-4104-a468-c2e0428e0e10
        $referensi  = Referensi::where('client_id',$this->clientId)->where('ref_nama','Jenis Alergi')->first();
        $client = Client::where('client_id',$this->clientId)->first();
        $pasien = Pasien::where('client_id',$this->clientId)->first();
        $dokter = Dokter::where('client_id',$this->clientId)->first();
        $unit   = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang      = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();
        $alergi     = json_decode($referensi->json_data);
        
        // Format tanggal yg seharusnya dipakai
        $current = Carbon::now();
        $dt = $current->format('c');

        if(!$unit || !$client)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/Specimen/'.$id, [
            "id" => $id,
            "resourceType" => "Specimen",
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/specimen/10083042",
                    "value" => "SPEC000001",
                    "assigner" => [
                        // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                        "reference" => "Organization/10083042"
                    ]
                ]
            ],
            "status" => "available",
            "type" => [
                "coding" => [
                    [
                        // Kode terdapat dibuku pada lampiran 6 (326)
                        "system" => "http://snomed.info/sct",
                        "code" => "119361006",
                        "display" => "Plasma specimen"
                    ]
                ]
            ],
            "collection" => [
                "method" => [
                    "coding" => [
                        [
                            // Kode terdapat dibuku pada lampiran 6 (328)
                            "system" => "https://snomed.info/sct",
                            "code" => "82078001",
                            "display" => "Collection of blood specimen for laboratory"
                        ]
                    ]
                ],
                "collectedDateTime" => $dt,
                "collector" => [
                    "reference" => "Practitioner/N10000001"
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "request" => [
                [
                    // ID Service Request Laboratorium yang sudah didaftarkan
                    "reference" => "ServiceRequest/e1de839a-6519-4f31-a470-f57c0ec15cdc"
                ]
            ],
            "receivedTime" => $dt
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteSpecimen($id)
    {   
        // Contoh id = f2e97186-42c9-4104-a468-c2e0428e0e10
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/Specimen/'.$id, [[
                "op" => "replace",
                "path" => "/status",
                "value" => "unavailable"
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}
