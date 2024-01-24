<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\ManajemenUser\Entities\Client;

class LocationController extends Controller
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

    public function createLocation()
    {   
        $client = Client::where('client_id',$this->clientId)->first();
        $unit   = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang  = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();

        if(!$unit || !$client)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }
        
        $aktif = "";
        ($ruang->is_aktif == true) ? $aktif = "active" : $aktif = "inactive";

        $result = Http::withToken($this->createToken())->post($this->getUrl().'/Location', [
            "resourceType" => "Location",
            "identifier" => [
                [
                    "use" => "official",
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/organization/10083042",
                    "value" => $ruang->ruang_id
                ]
            ],
            "status" => $aktif,
            "name" => $ruang->ruang_nama,
            "description" => $ruang->deskripsi,
            "mode" => "kind", // Merepresentasikan kelompok/kelas lokasi
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => $client->no_telepon,
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" => $client->email,
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    "Jl. Raya Jatiasih No.72, RT.004/RW.005, Jatiasih, Kec. Jatiasih, Kota Bks, Jawa Barat 17423"
                ],
                "city" => $client->kota,
                "postalCode" => "55292",
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" => "31"
                            ],
                            [
                                "url" => "city",
                                "valueCode" => "3171"
                            ],
                            [
                                "url" => "district",
                                "valueCode" => "317101"
                            ],
                            [
                                "url" => "village",
                                "valueCode" => "31710101"
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
                "display" => $ruang->ruang_nama
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function searchID($id)
    {   
        // Contoh : 7a704c1e-fe00-409f-a20b-6d28bc21d904
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Location/'.$id);
        return json_decode($url->getBody(), true);
    }

    public function searchIdentifier($id)
    {
        // Contoh : Kamar Testing
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Location',
            ['identifier' => 'http://sys-ids.kemkes.go.id/location/10083042|'.$id]
        );
        return json_decode($url->getBody(), true);
    }

    public function searchOrgID($keyword)
    {   
        // Contoh : 10083042
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Location',
            ['organization' => $keyword]
        );
        return json_decode($url->getBody(), true);
    }

    public function searchName($keyword)
    {   
        // Contoh : Ruang
        $url = Http::withToken($this->createToken())->get($this->getUrl().'/Location',
            ['name' => $keyword]
        );
        return json_decode($url->getBody(), true);
    }

    public function updateLocation($id)
    {   
        // Contoh id = 7a704c1e-fe00-409f-a20b-6d28bc21d904
        $client = Client::where('client_id',$this->clientId)->first();
        $unit   = UnitPelayanan::where('client_id',$this->clientId)->first();
        $ruang  = RuangPelayanan::where('client_id',$this->clientId)->where('unit_id',$unit->unit_id)->first();

        if(!$unit || !$client)
        {
            return response()->json([
                'status' => 400,
                'message' => 'Data tidak ditemukan.'
            ]);
        }
        
        $aktif = "";
        ($ruang->is_aktif == true) ? $aktif = "active" : $aktif = "inactive";

        $result = Http::withToken($this->createToken())->put($this->getUrl().'/Location/'.$id, [
            "resourceType" => "Location",
            "id" => $id,
            "identifier" => [
                [
                    "use" => "official",
                    // 10083042 adalah ID Rumah Sakit yang didaftarkan di DTO.
                    "system" => "http://sys-ids.kemkes.go.id/organization/10083042",
                    "value" => $ruang->ruang_id
                ]
            ],
            "status" => $aktif,
            "name" => $ruang->ruang_nama,
            "description" => $ruang->deskripsi,
            "mode" => "kind", // Merepresentasikan kelompok/kelas lokasi
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => $client->no_telepon,
                    "use" => "work"
                ],
                [
                    "system" => "fax",
                    "value" => $client->no_telepon,
                    "use" => "work"
                ],
                [
                    "system" => "email",
                    "value" => $client->email,
                    "use" => "work"
                ]
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    "Jl. Raya Jatiasih No.72, RT.004/RW.005, Jatiasih, Kec. Jatiasih, Kota Bks, Jawa Barat 17423"
                ],
                "city" => $client->kota,
                "postalCode" => "55292",
                "country" => "ID",
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            [
                                "url" => "province",
                                "valueCode" => "31"
                            ],
                            [
                                "url" => "city",
                                "valueCode" => "3171"
                            ],
                            [
                                "url" => "district",
                                "valueCode" => "317101"
                            ],
                            [
                                "url" => "village",
                                "valueCode" => "31710101"
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
                "display" => $ruang->ruang_nama
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

    public function deleteLocation($id)
    {   
        // Contoh id = 7a704c1e-fe00-409f-a20b-6d28bc21d904
        $result = Http::withToken($this->createToken())
            ->withHeaders(
                ['Content-Type' => 'application/json-patch+json'])
            ->patch($this->getUrl().'/Location/'.$id, [[
                "op" => "replace",
                "path" => "/status",
                "value" => "inactive"
            ]], 200, [], JSON_UNESCAPED_SLASHES);

        $value = json_decode($result->getBody(), true);
        return $value;
    }

}
