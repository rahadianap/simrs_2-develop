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

class EncounterController extends Controller
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

    public function createEncounter()
    {   
        $client = Client::where('client_id',$this->clientId)->first();
        $pasien = Pasien::where('client_id',$this->clientId)->first();
        $dokter = Dokter::where('client_id',$this->clientId)->first();
        $unit   = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang  = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();

        if(!$pasien || !$client)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $current = Carbon::now();
        $dt = $current->format('c');

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Encounter', [
            "resourceType" => "Encounter",
            "status" => "arrived", // Pasien melakukan kunjungan
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],
            "subject" => [
                // ID Pasien berhubung tidak bisa mendaftar maka menggunakan data yang sudah ada
                "reference" => "Patient/100000030009", 
                "display" => $pasien->nama_lengkap
            ],
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender"
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        // ID Dokter / Tenaga Medis N10000002 berhubung tidak bisa daftar maka menggunakan data yang sudah ada
                        "reference" => "Practitioner/N10000002",
                        "display" => $dokter->dokter_nama
                    ]
                ]
            ],
            "period" => [
                "start" => $dt
            ],
            "location" => [
                [
                    "location" => [
                        // ID Ruang / Location yang sudah didaftarkan
                        "reference" => "Location/7a704c1e-fe00-409f-a20b-6d28bc21d904",
                        "display" => $ruang->ruang_nama
                    ]
                ]
            ],
            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => $dt
                    ]
                ]
            ],
            "serviceProvider" => [
                 // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                "reference" => "Organization/10083042"
            ],
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/encounter/10083042", 
                    "value" => "P20240001" // nomor kunjungan pasien bisa generate sendiri
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : ae1e3d03-99e5-47ef-b541-8cffd125e34e
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Encounter/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchSubject($keyword)
    {
        // Contoh : 100000030009 total 672 data 21/12/22
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Location',
            ['subject' => $keyword]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateEncounter($id,$status)
    {   
        // Contoh id = ae1e3d03-99e5-47ef-b541-8cffd125e34e
        $client = Client::where('client_id',$this->clientId)->first();
        $pasien = Pasien::where('client_id',$this->clientId)->first();
        $dokter = Dokter::where('client_id',$this->clientId)->first();
        $unit   = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang  = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();
        
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

        $diagnosis = "";
        $statusHistory = [];

        if($status == "in-progress"){
            $statusHistory = json_encode([
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => "2022-12-21T18:21:27+07:00",
                        "end" => "2022-12-21T18:32:27+07:00",
                    ]
                ],
                [
                    "status" => "in-progress",
                    "period" => [
                        "start" => "2022-12-21T18:32:27+07:00",
                        "end" => "2022-12-21T19:32:27+07:00",
                    ]
                ]
            ]);
        } else if($status == "finished"){
            $statusHistory = json_encode([
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => "2022-12-21T18:21:27+07:00",
                        "end" => "2022-12-21T18:32:27+07:00",
                    ]
                ],
                [
                    "status" => "in-progress",
                    "period" => [
                        "start" => "2022-12-21T18:32:27+07:00",
                        "end" => "2022-12-21T19:32:27+07:00",
                    ]
                ],
                [
                    "status" => "finished",
                    "period" => [
                        "start" => "2022-12-21T19:32:27+07:00",
                        "end" => "2022-12-21T19:55:27+07:00",
                    ]
                ]
            ]);
            $diagnosis = json_encode([
                [
                    "condition" => [
                        "reference" => "Condition/urn:uuid:4bbbe654-14f5-4ab3-a36e-a1e307f67bb8",
                        "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                    ],
                    "use" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                "code" => "DD",
                                "display" => "Discharge diagnosis"
                            ]
                        ]
                    ],
                    "rank" => 1
                ],
                [
                    "condition" => [
                        "reference" => "Condition/urn:uuid:ffadc03c-ef03-47ec-b71a-3c96a31ab77e",
                        "display" => "Non-insulin-dependent diabetes mellitus without complications"
                    ],
                    "use" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                "code" => "DD",
                                "display" => "Discharge diagnosis"
                            ]
                        ]
                    ],
                    "rank" => 2
                ]
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Status tidak ditemukan.'
            ]);
        }
        // return $statusHistory;
        $result = Http::withToken($this->createToken())->put($this->getUrl().'/Encounter/'.$id, [
            "resourceType" => "Encounter",
            "id" => $id,
            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code" => "AMB",
                "display" => "ambulatory"
            ],
            "subject" => [
                // ID Pasien berhubung tidak bisa mendaftar maka menggunakan data yang sudah ada
                "reference" => "Patient/100000030009", 
                "display" => $pasien->nama_lengkap
            ],
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code" => "ATND",
                                    "display" => "attender"
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        "reference" => "Practitioner/N10000001",
                        "display" => $dokter->dokter_nama
                    ]
                ]
            ],
            "period" => [
                "start" => "2022-06-14T07:00:00+07:00",
            ],
            "location" => [
                [
                    "location" => [
                        // ID Ruang / Location yang sudah didaftarkan
                        "reference" => "Location/7a704c1e-fe00-409f-a20b-6d28bc21d904",
                        "display" => $ruang->ruang_nama
                    ]
                ]
            ],
            "diagnosis" => json_decode($diagnosis),
            "status" => $status,
            "statusHistory" => json_decode($statusHistory),
            "serviceProvider" => [
                "reference" => "Organization/10000004"
            ],
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/10000004",
                    "value" => "P20240001"
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteEncounter($id)
    {   
        // Contoh id = ae1e3d03-99e5-47ef-b541-8cffd125e34e
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/Encounter/'.$id, [[
                "op" => "replace",
                "path" => "/status",
                "value" => "cancelled"
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}
