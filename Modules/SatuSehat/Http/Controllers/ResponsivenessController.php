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

class ResponsivenessController extends Controller
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

    public function createResponsiveness()
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

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Observation', [
            "resourceType" => "Observation",
            "status" => "final", // Hasil observasi awal
            "category" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                            "code" => "exam",
                            "display" => "Exam"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://loinc.org",
                        "code" => "67775-7", // Code untuk tingkat kesadaran
                        "display" => "Level of responsiveness"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001"
                ]
            ],
            "encounter" => [
                // ID Encounter yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Pemeriksaan Kesadaran Budi Santoso di hari Selasa, 14 Juni 2022"
            ],
            "effectiveDateTime" => $dt,
            "valueCodeableConcept" => [
                "coding" => [
                    [
                        "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                        "code" => "TK000001", // Terdapat beberapa kode dan penamaan
                        "display" => "Alert"
                    ]
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : 66a511d4-f5e5-4402-ad8b-da4d18eade2e
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Observation/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchSubject(Request $request)
    {
        
        // Contoh subject 100000030009
        // Contoh encounter ae1e3d03-99e5-47ef-b541-8cffd125e34e
        // Dynamic search keyword return COUNT($request->all()); menghitung jumlah request
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Observation',
            [
                'subject'   => $request->subject,   // Pasien ID Lokal
                'encounter' => $request->ecounter,  // Kunjungan Pasien
            ]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateResponsiveness($id)
    {   
        // Contoh id = 66a511d4-f5e5-4402-ad8b-da4d18eade2e
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

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/Observation/'.$id, [
            "resourceType" => "Observation",
            "id" => $id,
            "status" => "final", // Hasil observasi awal
            "category" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                            "code" => "exam",
                            "display" => "Exam"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://loinc.org",
                        "code" => "67775-7", // Code untuk tingkat kesadaran
                        "display" => "Level of responsiveness"
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001"
                ]
            ],
            "encounter" => [
                // ID Encounter yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Pemeriksaan Kesadaran Budi Santoso di hari Selasa, 14 Juni 2022"
            ],
            "effectiveDateTime" => $dt,
            "valueCodeableConcept" => [
                "coding" => [
                    [
                        "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                        "code" => "TK000002", // Terdapat beberapa kode dan penamaan
                        "display" => "Voice"
                    ]
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteResponsiveness($id)
    {   
        // Tidak ada kolom menandakan Alergi bisa dihapus atau dinonaktifkan
        // Sehingga diasumsikan mengunnakan kolom valueQuantity sebagai contoh

        // Contoh id = 66a511d4-f5e5-4402-ad8b-da4d18eade2e
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/Observation/'.$id, [[
                "op" => "replace",
                "path" => "/valueCodeableConcept/coding",
                "value" => [
                    [
                        "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                        "code" => "TK000003", // Terdapat beberapa kode dan penamaan
                        "display" => "Pain"
                    ]
                ]
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}
