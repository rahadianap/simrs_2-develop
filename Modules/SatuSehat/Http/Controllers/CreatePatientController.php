<?php

namespace Modules\SatuSehat\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

use Modules\MasterData\Entities\Pasien;
use Modules\SatuSehat\Entities\BridgingLog;

class CreatePatientController extends Controller
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
            'client_id' => '75gPnX96H7gsAFm6dYCZRmbGV7RdAahtVAunKAZ6spdtzgIo',
            'client_secret' => '94AbrhUt5AcrKqGBFPqKKKGjI67mJLG3yGTa4NJIJLZYKEjeBVEjV4hIDZyy7lmR',
        ]);
        $result = json_decode($response->getBody(), true);
        $value = $result['access_token'];
        return (string)$value;
    }

    public function create(Request $request)
    {
        $pasien = Pasien::leftJoin('tb_pasien_alamat', 'tb_pasien.pasien_id', '=', 'tb_pasien_alamat.pasien_id')
        ->leftJoin('tb_pasien_keluarga', 'tb_pasien.pasien_id', '=', 'tb_pasien_keluarga.pasien_id')
        ->leftJoin('tb_pasien_relasi', 'tb_pasien.pasien_id', '=', 'tb_pasien_relasi.pasien_id')
        ->leftJoin('tb_propinsi', 'tb_pasien_alamat.propinsi_id', '=', 'tb_propinsi.propinsi_id')
        ->leftJoin('tb_kota', 'tb_pasien_alamat.kota_id', '=', 'tb_kota.kota_id')
        ->leftJoin('tb_kecamatan', 'tb_pasien_alamat.kecamatan_id', '=', 'tb_kecamatan.kecamatan_id')
        ->leftJoin('tb_kelurahan', 'tb_pasien_alamat.kelurahan_id', '=', 'tb_kelurahan.kelurahan_id')
        ->where('tb_pasien.pasien_id',$request[0])->where('tb_pasien.client_id',$this->clientId)->where('tb_pasien.is_aktif',1)->first();

        $data = [
            "resourceType" => "Patient",
            "meta" => [
                "profile" => [
                    "https://fhir.kemkes.go.id/r4/StructureDefinition/Patient"
                ]
            ],
            "identifier" => [
                [
                   "use" => "official",
                   "system" => "https://fhir.kemkes.go.id/id/nik",
                   "value" => $pasien->nik // NIK pasien WAJIB
                ],
                [
                   "use" => "official",
                   "system" => "https://fhir.kemkes.go.id/id/kk",
                   "value" => $pasien->no_kk // Nomor KK Pasien TIDAK WAJIB
                ]
            ],
            "active" => true,
            "name" => [
                [
                    "use" => "official",
                    "text" => $pasien->nama_lengkap // Nama Pasien
                ]
            ],
            "telecom" => [
                [
                    "system" => "phone",
                    "value" => "08123456789",
                    "use" => "mobile"
                ],
                [
                    "system" => "email",
                    "value" => "john.smith@xyz.com",
                    "use" => "home"
                ]
            ],
            "gender" => $pasien->jns_kelamin,
            "birthDate" => $pasien->tgl_lahir,
            "deceasedBoolean" => $pasien->is_meninggal,
            "address" => [
                [
                    "use" => "home",
                    "line" => [
                        $pasien->alamat
                    ],
                    "city" => $pasien->kota,
                    "postalCode" => $pasien->kodepos,
                    "country" => "ID",
                    "extension" => [
                        [
                            "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                            // KODE ADMINISTRASI DAERAH MENGIKUTI PEMERINTAH, SUDAH TERSEDIA DI DATABASE UNTUK MAPPING KODE INI.
                            "extension" => [
                                [
                                    "url" => "province",
                                    "valueCode" => $pasien->kode_kemendagri_prop
                                ],
                                [
                                    "url" => "city",
                                    "valueCode" => $pasien->kode_kemendagri_kota
                                ],
                                [
                                    "url" => "district",
                                    "valueCode" => $pasien->kode_kemendagri_kecamatan
                                ],
                                [
                                    "url" => "village",
                                    "valueCode" => $pasien->kode_kemendagri_kelurahan
                                ],
                                [
                                    "url" => "rt",
                                    "valueCode" => $pasien->rt
                                ],
                                [
                                    "url" => "rw",
                                    "valueCode" => $pasien->rw
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "maritalStatus" => [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-MaritalStatus",
                        "code" => "M", //Kode menyesuaikan dengan status pasien.
                        "display" => $pasien->status_perkawinan
                    ]
                ],
                "text" => $pasien->status_perkawinan
            ],
            "multipleBirthInteger" => 0,
            // TIDAK ADA DI DATABASE.
            "contact" => [
                [
                    "relationship" => [
                        [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/v2-0131",
                                "code" => "C"
                            ]
                        ]
                        ]
                    ],
                    "name" => [
                        "use" => "official",
                        "text" => "Jane Smith"
                    ],
                    "telecom" => [
                        [
                        "system" => "phone",
                        "value" => "0690383372",
                        "use" => "mobile"
                        ]
                    ]
                ]
            ],
            "communication" => [
                [
                    "language" => [
                        "coding" => [
                            [
                            "system" => "urn:ietf:bcp:47",
                            "code" => "id-ID",
                            "display" => "Indonesian"
                            ]
                        ],
                        "text" => "Indonesian"
                    ],
                    "preferred" => true
                ]
            ],
            "extension" => [
                [
                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/birthPlace",
                    "valueAddress" => [
                        "city" => $pasien->tempat_lahir,
                        "country" => "ID"
                    ]
                ],
                [
                    "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/citizenshipStatus",
                    "valueCode" => "WNI"
                ]
            ]
        ];

        $send = Http::withToken($this->createToken())->post($this->getUrl().'/Patient', $data, 200, [], JSON_UNESCAPED_SLASHES); 
        $result = json_decode($send->getBody(), true);
        return $result;

        // // INSERT INTO tb_bridging_log
        // $log = new BridgingLog();
        // $log->bridging_log_id   = $this->getBridgingLogId();
        // $log->bridging_type     = 'SATUSEHAT';
        // $log->bridging_resource = 'Patient';
        // $log->bridging_phase    = '';
        // $log->state             = 'SUCCESS';
        // $log->data              = json_encode($data);
        // $log->result            = json_encode($result);
        // $log->ss_id             = $result['id'];
        // $log->is_aktif          = true;
        // $log->client_id         = $this->clientId;
        // $log->created_by        = Auth::user()->username;
        // $log->updated_by        = Auth::user()->username;

        // $success = $log->save();
        // if(!$success) {
        //     DB::connection('dbclient')->rollBack();
        //     return response()->json(['success' => false, 'message' => 'Data log gagal disimpan.']);
        // }

        // // UPDATE DATA PASIEN
        // $patient = Pasien::where('pasien_id', $request[0])->where('client_id',$this->clientId)->update(['ihs_id', $result['id']]);
    }

    public function getBridgingLogId()
    {
        try {
            $id = $this->clientId.'-'.date('Ym').'-LOG00001';
            $maxId = BridgingLog::withTrashed()->where('client_id', $this->clientId)->where('bridging_log_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-LOG%')->max('bridging_log_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('Ym').'-LOG00001';
            } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ym').'-LOG', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-LOG0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-LOG000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-LOG00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-LOG0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ym').'-LOG' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }
}
