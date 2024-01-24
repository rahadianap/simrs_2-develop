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

class ServiceRequestController extends Controller
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

    public function createServiceRequestLab()
    {   
        // Pemubatan Service Request - Laboratorium
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

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/ServiceRequest', [
            "resourceType" => "ServiceRequest",
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/servicerequest/10083042",
                    "value" => "LAB000001" // ID Local bisa digenerate sendiri
                ]
            ],
            "status" => "active", // RequestStatus : http://hl7.org/fhir/R4/valueset-request-status.html
            "intent" => "original-order", // RequestIntent : http://hl7.org/fhir/R4/valueset-request-intent.html
            "priority" => "asap",
            "code" => [
                "coding" => [
                    // https://view.officeapps.live.com/op/view.aspx?src=https%3A%2F%2Fdto.kemkes.go.id%2FMapping%2520LOINC%2520Laboratorium.xlsx&wdOrigin=BROWSELINK
                    [
                        "system" => "http://loinc.org",
                        "code" => "11477-7",
                        "display" => "Microscopic observation [Identifier] in Sputum by Acid fast stain"
                    ]
                ],
                "text" => "Pewarnaan bakteri tahan asam sputum 1"
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                // ID Encounter diawal yang sudah didaftarkan
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Permintaan BTA Sputum Budi Santoso di hari Selasa, 14 Juni 2022 pukul 09:30 WIB"
            ],
            "occurrenceDateTime" => $dt,
            "authoredOn" => $dt,
            "requester" => [
                "reference" => "Practitioner/N10000001",
                "display" => "Dokter Bronsig"
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001",
                    "display" => $dokter->dokter_nama
                ]
            ],
            "reasonCode" => [
                [
                    "text" => "Periksa jika ada kemungkinan Tuberculosis"
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function createServiceRequestControl()
    {   
        // Pemubatan Service Request - Laboratorium
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

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/ServiceRequest', [
            "resourceType" => "ServiceRequest",
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/servicerequest/10083042",
                    "value" => "KTR000001" // ID Local bisa digenerate sendiri
                ]
            ],
            "status" => "active", // RequestStatus : http://hl7.org/fhir/R4/valueset-request-status.html
            "intent" => "original-order", // RequestIntent : http://hl7.org/fhir/R4/valueset-request-intent.html
            "priority" => "routine",
            "code" => [
                "coding" => [
                    // https://view.officeapps.live.com/op/view.aspx?src=https%3A%2F%2Fdto.kemkes.go.id%2FMapping%2520LOINC%2520Laboratorium.xlsx&wdOrigin=BROWSELINK
                    [
                        "system" => "http://snomed.info/sct",
                        "code" => "3457005",
                        "display" => "Patient referral"
                    ]
                ],
                "text" => "Kontrol rutin regimen TB bulan ke-1"
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Kunjungan ".$pasien->nama_lengkap." di hari Selasa, 14 Juni 2022"
            ],
            "occurrenceDateTime" => $dt,
            "authoredOn" => $dt,
            "requester" => [
                "reference" => "Practitioner/N10000001",
                "display" => "Dokter Bronsig"
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001",
                    "display" => $dokter->dokter_nama
                ]
            ],
            "reasonCode" => [
                [
                    "coding" => [
                        [
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => "A15.0",
                            "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                        ]
                    ],
                    "text" => "Kontrol rutin bulanan"
                ]
            ],
            "locationCode" => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-RoleCode",
                            "code" => "OF",
                            "display" => "Outpatient Facility"
                        ]
                    ]
                ]
            ],
            "locationReference" => [
                [
                    // ID Location : Ruangan yang sudah didaftarkan
                    "reference" => "Location/7a704c1e-fe00-409f-a20b-6d28bc21d904",
                    "display" => "Ruang 1A, Poliklinik Rawat Jalan"
                ]
            ],
            "patientInstruction" => "Kontrol setelah 1 bulan minum obat anti tuberkulosis. Dalam keadaan darurat dapat menghubungi hotline RS di nomor 14045"
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh Lab : e1de839a-6519-4f31-a470-f57c0ec15cdc
        // Contoh Kontrol : a0030a8c-2be0-459f-a7f1-90f245444013
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/ServiceRequest/'.$id);
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

    public function updateServiceRequest($id)
    {   
        // Contoh id = e1de839a-6519-4f31-a470-f57c0ec15cdc
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

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/ServiceRequest/'.$id, [
            "resourceType" => "ServiceRequest",
            "id" => $id,
            "identifier" => [
                [
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/servicerequest/10083042",
                    "value" => "LAB000001" // ID Local bisa digenerate sendiri
                ]
            ],
            "status" => "on-hold", // RequestStatus : http://hl7.org/fhir/R4/valueset-request-status.html
            "intent" => "original-order", // RequestIntent : http://hl7.org/fhir/R4/valueset-request-intent.html
            "priority" => "asap",
            "code" => [
                "coding" => [
                    // https://view.officeapps.live.com/op/view.aspx?src=https%3A%2F%2Fdto.kemkes.go.id%2FMapping%2520LOINC%2520Laboratorium.xlsx&wdOrigin=BROWSELINK
                    [
                        "system" => "http://loinc.org",
                        "code" => "11477-7",
                        "display" => "Microscopic observation [Identifier] in Sputum by Acid fast stain"
                    ]
                ],
                "text" => "Pewarnaan bakteri tahan asam sputum 1"
            ],
            "subject" => [
                "reference" => "Patient/100000030009",
                "display" => $pasien->nama_lengkap
            ],
            "encounter" => [
                "reference" => "Encounter/ae1e3d03-99e5-47ef-b541-8cffd125e34e",
                "display" => "Permintaan BTA Sputum Budi Santoso di hari Selasa, 14 Juni 2022 pukul 09:30 WIB"
            ],
            "occurrenceDateTime" => $dt,
            "authoredOn" => $dt,
            "requester" => [
                "reference" => "Practitioner/N10000001",
                "display" => "Dokter Bronsig"
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/N10000001",
                    "display" => $dokter->dokter_nama
                ]
            ],
            "reasonCode" => [
                [
                    "text" => "Periksa jika ada kemungkinan TBC"
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteServiceRequest($id)
    {   
        // Tidak ada kolom menandakan Alergi bisa dihapus atau dinonaktifkan
        // Sehingga diasumsikan mengunnakan kolom valueQuantity sebagai contoh

        // Contoh id = e1de839a-6519-4f31-a470-f57c0ec15cdc
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/ServiceRequest/'.$id, [[
                "op" => "replace",
                "path" => "/status",
                "value" => "revoked"
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }
}
