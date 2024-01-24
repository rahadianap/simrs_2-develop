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

class DiagnosticReportController extends Controller
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

    public function createDiagnosticReport()
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

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/DiagnosticReport', [
            "resourceType" => "DiagnosticReport",
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/diagnostic/10083042/lab",
                    "use" => "official",
                    "value" => "RPTLAB00001" // ID Lokal
                ]
            ],
            "status" => "final", // Terdapat pada buku (123)
            "category" => [
                [
                    "coding" => [
                        [
                            // Kode dapat dilihat pada buku Lampiran 7 (329)
                            "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                            "code" => "MB",
                            "display" => "Microbiology"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        // https://loinc.org/11477-7/
                        "system" => "http://loinc.org", 
                        "code" => "11477-7",
                        "display" => "Microscopic observation [Identifier] in Sputum by Acid fast stain"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter diawal yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e"
            ],
            "effectiveDateTime" => $dt,
            "issued" => $dt,
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001"
                ],
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "reference" => "Organization/10083042"
                ]
            ],
            "result" => [
                [
                    // ID Observasi Lab yang didaftarkan
                    "reference" => "Observation/ec11dd0c-6977-45e1-9493-1d4e6fe3a1cc"
                ]
            ],
            "specimen" => [
                [
                    // ID Specimen
                    "reference" => "Specimen/f2e97186-42c9-4104-a468-c2e0428e0e10"
                ]
            ],
            "conclusionCode" => [
                [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "260347006",
                            "display" => "+"
                        ]
                    ]
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : 4e6d0b79-b648-4c67-b21e-f6302bb15fbe
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/DiagnosticReport/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchSubject(Request $request)
    {
        
        // Contoh subject 100000030009 id lokal pasien
        // Contoh encounter ae1e3d03-99e5-47ef-b541-8cffd125e34e
        // Contoh specimen f2e97186-42c9-4104-a468-c2e0428e0e10
        // Dynamic search keyword return COUNT($request->all()); menghitung jumlah request
        // Pencarian harus ada subject, jika tidak ada maka akan error meminta subject diisi
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/DiagnosticReport',
            [
                'subject'   => $request->subject,   // Pasien ID Lokal
                'specimen'  => $request->specimen,  
                'encounter' => $request->encounter,  
            ]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateDiagnosticReport($id)
    {   
        // Contoh id = 4e6d0b79-b648-4c67-b21e-f6302bb15fbe
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

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/DiagnosticReport/'.$id, [
            "id" => $id,
            "resourceType" => "DiagnosticReport",
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/diagnostic/10083042/lab",
                    "use" => "official",
                    "value" => "RPTLAB00001" // ID Lokal
                ]
            ],
            "status" => "final", // Terdapat pada buku (123)
            "category" => [
                [
                    "coding" => [
                        [
                            // Kode dapat dilihat pada buku Lampiran 7 (329)
                            "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                            "code" => "MB",
                            "display" => "Microbiology"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        // https://loinc.org/11477-7/
                        "system" => "http://loinc.org", 
                        "code" => "11477-7",
                        "display" => "Microscopic observation [Identifier] in Sputum by Acid fast stain"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter diawal yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e"
            ],
            "effectiveDateTime" => $dt,
            "issued" => $dt,
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001"
                ],
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "reference" => "Organization/10083042"
                ]
            ],
            "result" => [
                [
                    // ID Observasi Lab yang didaftarkan
                    "reference" => "Observation/ec11dd0c-6977-45e1-9493-1d4e6fe3a1cc"
                ]
            ],
            "specimen" => [
                [
                    // ID Specimen
                    "reference" => "Specimen/f2e97186-42c9-4104-a468-c2e0428e0e10"
                ]
            ],
            "conclusionCode" => [
                [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "2667000",
                            "display" => "Absent"
                        ]
                    ]
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteDiagnosticReport($id)
    {   
        // Contoh id = 4e6d0b79-b648-4c67-b21e-f6302bb15fbe
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/DiagnosticReport/'.$id, [[
                "op" => "replace",
                "path" => "/status",
                "value" => "cancelled"
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}
