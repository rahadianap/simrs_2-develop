<?php

namespace Modules\EMR\Http\Controllers;

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

use Modules\EMR\Entities\MedrecInform;
use Modules\EMR\Entities\MedrecInformDetail;

use Modules\MasterData\Entities\InformedConsent;
use Modules\MasterData\Entities\InformedDetail;
use Modules\MasterData\Entities\InformedMapping;

use Modules\SatuSehat\Entities\BridgingLog;

use Ramsey\Uuid\Uuid;
use Carbon;
use DB;

class MedrecInformController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function data(Request $request, $id) {
        try {
            //return response()->json(['success' => false, 'message' => 'disini loh']);
            
            $data = MedrecInform::where('asesmen_id',$id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']);
            }
            $details = MedrecInformDetail::where('asesmen_id',$id)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            if(!$details || $details->count() <= 0) {
                $details = InformedMapping::leftJoin('tb_inform_detail','tb_inform_mapping.inform_detail_id','=','tb_inform_detail.inform_detail_id')
                    ->where('tb_inform_mapping.template_id',$data->template_id)
                    ->where('tb_inform_mapping.client_id',$this->clientId)
                    ->where('tb_inform_mapping.is_aktif',true)
                    ->select(
                        'tb_inform_detail.inform_detail_id',
                        'tb_inform_detail.pertanyaan',
                        'tb_inform_detail.tipe_jawaban',
                        'tb_inform_detail.pilihan_jawaban',
                        'tb_inform_detail.satuan',
                        'tb_inform_detail.image_item',
                        'tb_inform_detail.is_aktif',
                        'tb_inform_detail.is_tanda_vital',
                        'tb_inform_detail.deskripsi',
                        'tb_inform_mapping.inform_map_id',
                        'tb_inform_mapping.template_id',
                        'tb_inform_mapping.is_mandatory')
                    ->orderBy('tb_inform_detail.is_tanda_vital','DESC')
                    ->get();
            }
        
            foreach($details as $itm) {
                $itm['asesmen_detail_id'] = null;
                $itm['pilihan_jawaban'] = json_decode($itm['pilihan_jawaban']);
            }
        
            $data['items'] = $details;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function lists(Request $request) {

    }

    public function create(Request $request) {
        try {
            $regId = $request->reg_id;
            $reffTrxId = $request->reff_trx_id;   

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();

            $assesmenId = $this->getAssesmenId();
            DB::connection('dbclient')->beginTransaction();
            /** create data asesmen */
            $assesmen                   = new MedrecInform();
            $assesmen->asesmen_id       = $assesmenId;
            $assesmen->reg_id           = $regId;
            $assesmen->reff_trx_id      = $reffTrxId;
            $assesmen->tgl_assesmen     = date('Y-m-d H:i:s');
            
            $assesmen->template_id      = $request->template_id;
            $assesmen->template_nama    = $request->template_nama;
            
            $assesmen->unit_id          = $unit->unit_id;
            $assesmen->unit_nama        = $unit->unit_nama;
            $assesmen->dokter_id        = $request->dokter_id;
            $assesmen->dokter_nama      = $dokter->dokter_nama;
            
            $assesmen->pasien_id        = $request->pasien_id;
            $assesmen->no_rm            = $pasien->no_rm;
            $assesmen->nama_pasien      = $pasien->nama_pasien;

            $assesmen->is_aktif         = true;            
            $assesmen->client_id        = $this->clientId;
            $assesmen->created_by       = Auth::user()->username;
            $assesmen->updated_by       = Auth::user()->username;
        
            $success = $assesmen->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data assesmen gagal disimpan']);
            }

            foreach($request->items as $itm) {
                $assesmenDetailId = $this->clientId.'-MID'.date('Ymd').Uuid::uuid4()->getHex();
                
                $assesmenDetail                       = new MedrecInformDetail();
                $assesmenDetail->asesmen_detail_id    = $assesmenDetailId;
                $assesmenDetail->asesmen_id           = $assesmenId;
                $assesmenDetail->reg_id               = $regId;
                $assesmenDetail->reff_trx_id          = $reffTrxId;
                
                $assesmenDetail->pertanyaan           = $itm['pertanyaan'];
                $assesmenDetail->tipe_jawaban         = $itm['tipe_jawaban'];
                $assesmenDetail->deskripsi            = $itm['deskripsi'];
                $assesmenDetail->pilihan_jawaban      = json_encode($itm['pilihan_jawaban']);
                $assesmenDetail->value                = $itm['value'];       
                
                $assesmenDetail->is_aktif             = $itm['is_aktif'];
                $assesmenDetail->client_id            = $this->clientId;
                $assesmenDetail->created_by           = Auth::user()->username;
                $assesmenDetail->updated_by           = Auth::user()->username;
            
                $success = $assesmenDetail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal diubah']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = MedrecInform::where('asesmen_id',$assesmenId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) { return response()->json(['success' => false, 'message' => 'data tidak ditemukan']); }

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function getAssesmenId(){
        try {
            $id = $this->clientId.'.'.date('Ymd').'-ASS0001';
            $maxId = MedrecInform::withTrashed()->where('client_id', $this->clientId)
                ->where('asesmen_id', 'ILIKE', $this->clientId.'.'.date('Ymd').'-ASS%')
                ->max('asesmen_id');
                
            if (!$maxId) {
                $id = $this->clientId.'.'.date('Ymd').'-'.'ASS0001';
            } 
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ymd').'-ASS','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'.'.date('Ymd').'-ASS000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ymd').'-ASS00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ymd').'-ASS0' . $count; } 
                else { $id = $this->clientId.'.'.date('Ymd').'-ASS'. $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function update(Request $request) {
        try {
            $assesmenId = $request->asesmen_id;
            $regId = $request->reg_id;
            $reffTrxId = $request->reff_trx_id;   

            $assesmen = MedrecInform::where('asesmen_id',$assesmenId)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$assesmen) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();

            
            DB::connection('dbclient')->beginTransaction();
            /** update data asesmen */
            foreach($request->items as $itm) {
                $assesmenDetail = MedrecInformDetail::where('asesmen_detail_id',$itm['asesmen_detail_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                
                if(!$assesmenDetail) {
                    $assesmenDetail                       = new MedrecInformDetail();
                    $assesmenDetail->asesmen_detail_id    = $this->clientId.'-MID'.date('Ymd').Uuid::uuid4()->getHex();
                    $assesmenDetail->asesmen_id           = $assesmenId;
                    $assesmenDetail->reg_id               = $regId;
                    $assesmenDetail->reff_trx_id          = $reffTrxId;
                    $assesmenDetail->client_id            = $this->clientId;
                    $assesmenDetail->created_by           = Auth::user()->username;
                }
                
                $assesmenDetail->pertanyaan             = $itm['pertanyaan'];
                $assesmenDetail->tipe_jawaban           = $itm['tipe_jawaban'];
                $assesmenDetail->deskripsi              = $itm['deskripsi'];
                $assesmenDetail->pilihan_jawaban        = json_encode($itm['pilihan_jawaban']);
                $assesmenDetail->value                  = $itm['value'];       
                
                $assesmenDetail->is_aktif             = $itm['is_aktif'];
                $assesmenDetail->updated_by           = Auth::user()->username;
            
                $success = $assesmenDetail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data soap detail gagal diubah']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = MedrecInform::where('asesmen_id',$assesmenId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) { return response()->json(['success' => false, 'message' => 'data tidak ditemukan']); }
            $data['items'] = MedrecInformDetail::where('asesmen_id',$assesmenId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();

            return response()->json(['success' => true, 'message' => 'Data berhasil diubah', 'data' => $data ]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function remove(Request $request, $informId){
        try {
            $data = MedrecInform::where('asesmen_id',$informId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan' ]);
            }
            $detail = MedrecInformDetail::where('asesmen_id',$informId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            DB::connection('dbclient')->beginTransaction();
            $success = MedrecInform::where('asesmen_id',$informId)->where('is_aktif',1)->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s')]);
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data gagal dihapus']);
            }

            if($detail) {
                $success = MedrecInformDetail::where('asesmen_id',$informId)->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'updated_at'=>date('Y-m-d H:i:s')]);
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data detail gagal dihapus']);
                }   
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
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
