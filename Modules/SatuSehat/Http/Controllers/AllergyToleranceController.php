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

class AllergyToleranceController extends Controller
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

    public function createAllergy()
    {   
        $referensi  = Referensi::where('client_id',$this->clientId)->where('ref_nama','Jenis Alergi')->first();
        $client     = Client::where('client_id',$this->clientId)->first();
        $pasien     = Pasien::where('client_id',$this->clientId)->first();
        $dokter     = Dokter::where('client_id',$this->clientId)->first();
        $unit       = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang      = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();
        $alergi     = json_decode($referensi->json_data);
        
        if(!$pasien || !$client)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $current = Carbon::now();
        $dt = $current->format('c');

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/AllergyIntolerance', [
            "resourceType" => "AllergyIntolerance",
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/AllergyIntolerance/10083042", 
                    "value" => "P20240001" // nomor kunjungan pasien bisa generate sendiri
                ]
            ],
            "clinicalStatus" => [
                "coding" => [
                    [
                        // Status : Active, Inactive, Resolved
                        "system" => "http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical",
                        "code" => "active",
                        "display" => "Active"
                    ]
                ]
            ],
            "verificationStatus" => [
                "coding" => [
                    [
                        // Status : unconfirmed, confirmed, refuted, entered-in-eror
                        "system" => "http://terminology.hl7.org/CodeSystem/allergyintolerance-verification",
                        "code" => "confirmed",
                        "display" => "Confirmed"
                    ]
                ]
            ],
            "category" => [
                "medication"
            ],
            "code" => [
                // Berikut daftar alergi yang direferensikan
                // https://dto.kemkes.go.id/kfa-browser
                // https://docs.google.com/spreadsheets/d/1tN9lFaK2GJ3itObaMOQds8JkI8rjQE2tacYRRSNgPF8/edit#gid=0
                "coding" => [
                    [
                        "system" => "http://snomed.info/sct",
                        "code" => "92000470",
                        "display" => "Metamizole 500 mg/mL Injeksi"
                    ]
                ],
                "text" => $alergi[0]->text
            ],
            "patient" => [
                // ID Pasien berhubung tidak bisa mendaftar maka menggunakan data yang sudah ada
                "reference" => "Patient/100000030009", 
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Kunjungan Budi Santoso di hari Selasa, 14 Juni 2022"
            ],
            "recordedDate" => $dt,
            "recorder" => [
                "reference" => "Practitioner/N10000001",
                "display" => $dokter->dokter_nama
            ]
            
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : 85f90e76-a399-40a0-a4a9-066bf4dbfa59
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/AllergyIntolerance/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchSubject(Request $request)
    {
        // Contoh patient 100000030009
        // Contoh code 92000470
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Location',
            [
                'patient'   => $request->patient, // pasien id
                'code'      => $request->code // alergi id
            ]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateAllergy($id)
    {   
        // Contoh id = 85f90e76-a399-40a0-a4a9-066bf4dbfa59
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

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/AllergyIntolerance/'.$id, [
            "resourceType" => "AllergyIntolerance",
            "id" => $id,
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/AllergyIntolerance/10083042",
                    "value" => "P20240001" // ID Lokal Allergi bisa digenerate
                ]
            ],
            "clinicalStatus" => [
                "coding" => [
                    [
                        // Status : Active, Inactive, Resolved
                        "system" => "http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical",
                        "code" => "ative",
                        "display" => "Active"
                    ]
                ]
            ],
            "verificationStatus" => [
                "coding" => [
                    [
                        // Status : unconfirmed, confirmed, refuted, entered-in-error
                        "system" => "http://terminology.hl7.org/CodeSystem/allergyintolerance-verification",
                        "code" => "unconfirmed",
                        "display" => "Unconfirmed"
                    ]
                ]
            ],
            "category" => [
                "medication"
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://snomed.info/sct",
                        "code" => "92000470",
                        "display" => "Metamizole 500 mg/mL Injeksi"
                    ]
                ],
                "text" => $alergi[0]->text
            ],
            "patient" => [
                // ID Pasien berhubung tidak bisa mendaftar maka menggunakan data yang sudah ada
                "reference" => "Patient/100000030009", 
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Kunjungan Budi Santoso di hari Selasa, 14 Juni 2022"
            ],
            "recordedDate" => $dt,
            "recorder" => [
                "reference" => "Practitioner/N10000001",
                "display" => $dokter->dokter_nama
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteAllergy($id)
    {   
        // Tidak ada kolom menandakan Alergi bisa dihapus atau dinonaktifkan
        // Sehingga diasumsikan mengunnakan kolom verificationStatus sebagai contoh

        // Contoh id = ae1e3d03-99e5-47ef-b541-8cffd125e34e
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/AllergyIntolerance/'.$id, [[
                "op" => "replace",
                "path" => "/verificationStatus/coding",
                "value" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/allergyintolerance-verification",
                        "code" => "refuted",
                        "display" => "Refuted"
                    ]
                ]
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}
