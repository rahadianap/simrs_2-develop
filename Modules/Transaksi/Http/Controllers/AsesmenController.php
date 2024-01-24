<?php

namespace Modules\Transaksi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Modules\Transaksi\Entities\Soap;
use Modules\Transaksi\Entities\SoapDiagnosa;

use Modules\SatuSehat\Entities\BridgingLog;

use Ramsey\Uuid\Uuid;
use Carbon;
use DB;

class AsesmenController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function dataAsesmen(Request $request, $soapId) {
        try {
            $data = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']);
            }
            $data['diagnosa'] = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function createAsesmen(Request $request) {
        try {
            $regId = $request->reg_id;
            $reffTrxId = $request->reff_trx_id;   

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();

            $soapId = $this->getSoapId();
            DB::connection('dbclient')->beginTransaction();
            /** create data asesmen */
            $soap                       = new Soap();
            $soap->soap_id              = $soapId;
            $soap->reg_id               = $regId;
            $soap->reff_trx_id          = $reffTrxId;
            $soap->tgl_soap             = date('Y-m-d H:i:s');
            $soap->unit_id              = $unit->unit_id;
            $soap->unit_nama            = $unit->unit_nama;
            $soap->dokter_id            = $request->dokter_id;
            $soap->dokter_nama          = $dokter->dokter_nama;
            
            $soap->pasien_id            = $request->pasien_id;
            $soap->no_rm                = $pasien->no_rm;
            $soap->nama_pasien          = $pasien->nama_pasien;

            $soap->saturasi_oksigen     = $request->saturasi_oksigen;
            $soap->sistole              = $request->sistole;
            $soap->diastole             = $request->diastole;
            $soap->suhu                 = $request->suhu;
            $soap->denyut_nadi          = $request->denyut_nadi;
            $soap->pernapasan           = $request->pernapasan;
            
            $soap->subjective           = $request->subjective;
            $soap->objective            = $request->objective;
            $soap->assesment            = $request->assesment;
            $soap->plan                 = $request->plan;
            $soap->catatan              = $request->catatan;
            
            $soap->status               = 'SIMPAN';
            $soap->is_aktif             = true;            
            $soap->client_id            = $this->clientId;
            $soap->created_by           = Auth::user()->username;
            $soap->updated_by           = Auth::user()->username;
        
            $success = $soap->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data soap gagal disimpan']);
            }

            foreach($request->diagnosa as $diag) {
                $soapDiagnosa                       = new SoapDiagnosa();
                $soapDiagnosa->soap_diag_id         = $this->clientId.'-DIAG'.date('Ymd').Uuid::uuid4()->getHex();
                $soapDiagnosa->soap_id              = $soapId;
                $soapDiagnosa->reg_id               = $regId;
                $soapDiagnosa->reff_trx_id          = $reffTrxId;
                $soapDiagnosa->tgl_diagnosa         = date('Y-m-d H:i:s');
                $soapDiagnosa->unit_id              = $unit->unit_id;
                $soapDiagnosa->unit_nama            = $unit->unit_nama;
                $soapDiagnosa->dokter_id            = $request->dokter_id;
                $soapDiagnosa->dokter_nama          = $dokter->dokter_nama;
                $soapDiagnosa->pasien_id            = $request->pasien_id;
                $soapDiagnosa->no_rm                = $pasien->no_rm;
                $soapDiagnosa->nama_pasien          = $pasien->nama_pasien;
                

                $soapDiagnosa->tipe                 = $diag['tipe'];
                $soapDiagnosa->kode_icd             = $diag['kode_icd'];
                $soapDiagnosa->nama_icd             = $diag['nama_icd'];
                $soapDiagnosa->diagnosa             = $diag['diagnosa'];
                $soapDiagnosa->kasus_lama           = $diag['kasus_lama'];
                
                $soapDiagnosa->is_aktif             = true;
                $soapDiagnosa->client_id            = $this->clientId;
                $soapDiagnosa->created_by           = Auth::user()->username;
                $soapDiagnosa->updated_by           = Auth::user()->username;
            
                $success = $soapDiagnosa->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data soap diagnosa gagal disimpan']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) { return response()->json(['success' => false, 'message' => 'data tidak ditemukan']); }
            $data['diagnosa'] = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function getSoapId(){
        try {
            $id = $this->clientId.'.'.date('Ymd').'-SOAP0001';
            $maxId = Soap::withTrashed()->where('client_id', $this->clientId)
                ->where('soap_id', 'ILIKE', $this->clientId.'.'.date('Ymd').'-SOAP%')
                ->max('soap_id');
                
            if (!$maxId) {
                $id = $this->clientId.'.'.date('Ymd').'-'.'SOAP0001';
            } 
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ymd').'-SOAP','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'.'.date('Ymd').'-SOAP000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ymd').'-SOAP00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ymd').'-SOAP0' . $count; } 
                else { $id = $this->clientId.'.'.date('Ymd').'-SOAP'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function updateAsesmen(Request $request) {
        try {
            $soapId = $request->soap_id;
            $regId = $request->reg_id;
            $reffTrxId = $request->reff_trx_id;   

            $soap = Soap::where('soap_id',$soapId)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$soap) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();

            
            DB::connection('dbclient')->beginTransaction();
            /** update data asesmen */
            $soap->no_rm                = $pasien->no_rm;
            $soap->nama_pasien          = $pasien->nama_pasien;

            $soap->saturasi_oksigen     = $request->saturasi_oksigen;
            $soap->sistole              = $request->sistole;
            $soap->diastole             = $request->diastole;
            $soap->suhu                 = $request->suhu;
            $soap->denyut_nadi          = $request->denyut_nadi;
            $soap->pernapasan           = $request->pernapasan;
            
            $soap->subjective           = $request->subjective;
            $soap->objective            = $request->objective;
            $soap->assesment            = $request->assesment;
            $soap->plan                 = $request->plan;
            $soap->catatan              = $request->catatan;
            $soap->updated_by           = Auth::user()->username;
        
            $success = $soap->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data soap gagal disimpan']);
            }

            foreach($request->diagnosa as $diag) {
                $soapDiagnosa = SoapDiagnosa::where('soap_diag_id',$diag['soap_diag_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if(!$soapDiagnosa) {
                    $soapDiagnosa                       = new SoapDiagnosa();
                    $soapDiagnosa->soap_diag_id         = $this->clientId.'-DIAG'.date('Ymd').Uuid::uuid4()->getHex();
                    $soapDiagnosa->soap_id              = $soapId;
                    $soapDiagnosa->reg_id               = $regId;
                    $soapDiagnosa->reff_trx_id          = $reffTrxId;
                    $soapDiagnosa->tgl_diagnosa         = date('Y-m-d H:i:s');
                    $soapDiagnosa->unit_id              = $unit->unit_id;
                    $soapDiagnosa->unit_nama            = $unit->unit_nama;
                    $soapDiagnosa->dokter_id            = $request->dokter_id;
                    $soapDiagnosa->dokter_nama          = $dokter->dokter_nama;
                    $soapDiagnosa->pasien_id            = $request->pasien_id;
                    $soapDiagnosa->no_rm                = $pasien->no_rm;
                    $soapDiagnosa->nama_pasien          = $pasien->nama_pasien;
                    $soapDiagnosa->client_id            = $this->clientId;
                    $soapDiagnosa->created_by           = Auth::user()->username;
                }
                
                $soapDiagnosa->tipe                 = $diag['tipe'];
                $soapDiagnosa->kode_icd             = $diag['kode_icd'];
                $soapDiagnosa->nama_icd             = $diag['nama_icd'];
                $soapDiagnosa->diagnosa             = $diag['diagnosa'];
                $soapDiagnosa->kasus_lama           = $diag['kasus_lama'];                
                $soapDiagnosa->is_aktif             = $diag['is_aktif'];
                $soapDiagnosa->updated_by           = Auth::user()->username;
            
                $success = $soapDiagnosa->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data soap diagnosa gagal diubah']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) { return response()->json(['success' => false, 'message' => 'data tidak ditemukan']); }
            $data['diagnosa'] = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();

            return response()->json(['success' => true, 'message' => 'Data berhasil diubah', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function deleteAsesmen(Request $request, $soapId){
        try {
            $data = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan' ]);
            }
            $diagnosa = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            DB::connection('dbclient')->beginTransaction();
            $success = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s')]);
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data soap gagal dihapus']);
            }

            if($diagnosa) {
                $success = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s')]);
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data soap diagnosa gagal dihapus']);
                }   
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data SOAP', 'error' => $e->getMessage()]);
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
            'client_id' => '75gPnX96H7gsAFm6dYCZRmbGV7RdAahtVAunKAZ6spdtzgIo',
            'client_secret' => '94AbrhUt5AcrKqGBFPqKKKGjI67mJLG3yGTa4NJIJLZYKEjeBVEjV4hIDZyy7lmR',
        ]);
        $result = json_decode($response->getBody(), true);
        $value = $result['access_token'];
        return (string)$value;
    }

    public function createDiagnosis(Request $request)
    {
        $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();

        $trxId = BridgingLog::select('ss_id')->where('trx_id', $request->trx_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();

        foreach($request->diagnosa as $diag) {
            $data = [
                "resourceType" => "Condition",
                "clinicalStatus" => [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                            "code" => "active",
                            "display" => "Active"
                        ]
                    ]
                ],
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                "code" => "encounter-diagnosis",
                                "display" => "Encounter Diagnosis"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => $diag['kode_icd'],
                            "display" => $diag['nama_icd']
                            // "code" => "K35.8",
                            // "display" => "Acute appendicitis, other and unspecified"
                        ]
                    ]
                ],
                "subject" => [
                    "reference" => "Patient"."/".$pasien->ihs_id,
                    "display" => $pasien->nama_lengkap
                ],
                "encounter" => [
                    "reference" => "Encounter"."/".$trxId,
                    "display" => "Kunjungan Budi Santoso di hari Selasa, 14 Juni 2022"
                ]
            ];

            $send = Http::withToken($this->createToken())->post($this->getUrl().'/Condition', $data, 200, [], JSON_UNESCAPED_SLASHES); 
            $result = json_decode($send->getBody(), true);
            // return $result;

            // INSERT INTO tb_bridging_log
            $log = new BridgingLog();
            $log->bridging_log_id   = $this->getBridgingLogId();
            $log->bridging_type     = 'SATUSEHAT';
            $log->bridging_resource = 'Encounter';
            $log->bridging_phase    = 'arrived';
            $log->state             = 'SUCCESS';
            $log->data              = json_encode($data);
            $log->result            = json_encode($result);
            $log->ss_id             = $result['id'];
            // $log->trx_id            = $trxId;
            $log->is_aktif          = true;
            $log->client_id         = $this->clientId;
            $log->created_by        = Auth::user()->username;
            $log->updated_by        = Auth::user()->username;

            $success = $log->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data log gagal disimpan.']);
            }
        }
    }
}
