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

class ProcedureController extends Controller
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

    public function createProcedure()
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

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Procedure', [
            "resourceType" => "Procedure",
            "status" => "in-progress", // Terdapat dalam buku (131)
            "category" => [
                "coding" => [
                    [
                        "system" => "http://snomed.info/sct",
                        "code" => "103693007",
                        "display" => "Diagnostic procedure"
                    ]
                ],
                "text" => "Diagnostic procedure"
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                        "code" => "87.44",
                        "display" => "Routine chest x-ray, so described"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter diawal yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Tindakan Rontgen Dada Budi Santoso pada Selasa tanggal 14 Juni 2022"
            ],
            "performedPeriod" => [
                "start" => $dt,
                "end" => $dt
            ],
            "performer" => [
                [
                    "actor" => [
                        "reference" => "Practitioner/N10000001",
                        "display" => $dokter->dokter_nama
                    ]
                ]
            ],
            "reasonCode" => [
                [
                    "coding" => [
                        [
                            // Daftar Item : https://icd.who.int/browse10/2010/en#!X
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => "A15.0",
                            "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                        ]
                    ]
                ]
            ],
            "bodySite" => [
                [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "302551006",
                            "display" => "Entire Thorax"
                        ]
                    ]
                ]
            ],
            "note" => [
                [
                    "text" => "Rontgen thorax melihat perluasan infiltrat dan kavitas."
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : 23d7019f-ba30-4c6c-99d9-02c28b1e6c69
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Procedure/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchSubject(Request $request)
    {
        
        // Contoh subject 100000030009 id lokal pasien
        // Contoh encounter ae1e3d03-99e5-47ef-b541-8cffd125e34e
        // Dynamic search keyword return COUNT($request->all()); menghitung jumlah request
        // Pencarian harus ada subject, jika tidak ada maka akan error meminta subject diisi
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Procedure',
            [
                'subject'   => $request->subject,   // Pasien ID Lokal
                'encounter' => $request->encounter,  
            ]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateProcedure($id)
    {   
        // Contoh id = 23d7019f-ba30-4c6c-99d9-02c28b1e6c69
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

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/Procedure/'.$id, [
            "id" => $id,
            "resourceType" => "Procedure",
            "status" => "completed", // Terdapat dalam buku (131)
            "category" => [
                "coding" => [
                    [
                        "system" => "http://snomed.info/sct",
                        "code" => "103693007",
                        "display" => "Diagnostic procedure"
                    ]
                ],
                "text" => "Diagnostic procedure"
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                        "code" => "87.44",
                        "display" => "Routine chest x-ray, so described"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter diawal yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Tindakan Rontgen Dada Budi Santoso pada Selasa tanggal 14 Juni 2022"
            ],
            "performedPeriod" => [
                "start" => $dt,
                "end" => $dt
            ],
            "performer" => [
                [
                    "actor" => [
                        "reference" => "Practitioner/N10000001",
                        "display" => $dokter->dokter_nama
                    ]
                ]
            ],
            "reasonCode" => [
                [
                    "coding" => [
                        [
                            // Daftar Item : https://icd.who.int/browse10/2010/en#!X
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => "A15.0",
                            "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                        ]
                    ]
                ]
            ],
            "bodySite" => [
                [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "302551006",
                            "display" => "Entire Thorax"
                        ]
                    ]
                ]
            ],
            "note" => [
                [
                    "text" => "Tindakan Rontgen thorax melihat perluasan infiltrat dan kavitas."
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteProcedure($id)
    {   
        // Contoh id = 23d7019f-ba30-4c6c-99d9-02c28b1e6c69
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/Procedure/'.$id, [[
                "op" => "replace",
                "path" => "/status",
                "value" => "not-done"
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}