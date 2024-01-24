<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon;

use Modules\PraktekDokter\Entities\PraktekDokter;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;

use Modules\Transaksi\Entities\Soap;
use Modules\Transaksi\Entities\SoapDiagnosa;
use Modules\Transaksi\Entities\Pembayaran;

use Modules\PraktekDokter\Entities\Pemeriksaan;
use Modules\PraktekDokter\Entities\PemeriksaanDetail;

use Modules\PraktekDokter\Entities\Resep;
use Modules\PraktekDokter\Entities\ResepDetail;

use Modules\ManajemenUser\Entities\Client;

class PraktekDokterController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request, $pasienId) {
        try {
            $perPage = 20;
            $dokter = '%%';
            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                /**modif try */
            }

            if($request->has('dokter')) {
                $dokter = '%'.$request->input('dokter').'%';
            }

            $pasien = Pasien::where('pasien_id',$pasienId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'pasien tidak ditemukan.']);
            } 
            
            $regLists = PraktekDokter::where('pasien_id',$pasienId)->where('is_aktif',1)
                ->where('dokter_id','ILIKE',$dokter)
                ->where('client_id',$this->clientId)
                ->orderBy('tgl_periksa','DESC')
                ->paginate($perPage);

            foreach($regLists->items() as $reg) {
                /**ambil data tanda vital */
                $reg['tanda_vital'] = Soap::where('reg_id',$reg['reg_id'])->where('client_id',$this->clientId)
                    ->select('soap_id','suhu','tinggi_badan','berat_badan','saturasi_oksigen','pernapasan','diastole','sistole','denyut_nadi')
                    ->where('is_aktif',1)->first();

                $soap = Soap::where('reg_id',$reg['reg_id'])->where('client_id',$this->clientId)
                    ->select('soap_id','subjective','objective','assesment','plan','evaluation','intervention','catatan','note_assesmen')
                    ->where('is_aktif',1)->first();
                
                $reg['soap'] = $soap;
                if($soap) {
                    $reg['diagnosa'] = SoapDiagnosa::where('reg_id',$reg['reg_id'])->where('soap_id',$soap->soap_id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
                }
                else {
                    $reg['diagnosa'] = [];
                }
                
                $reg['tindakan'] = PemeriksaanDetail::where('reg_id',$reg['reg_id'])->where('client_id',$this->clientId)->where('is_aktif',1)
                    ->select('detail_id','trx_id','reg_id','tindakan_id','tindakan_nama','satuan','jumlah','subtotal')->get();

                $reg['resep'] = ResepDetail::where('reg_id',$reg['reg_id'])->where('client_id',$this->clientId)->where('is_aktif',1)
                    ->select('reg_id','trx_id','detail_id','item_id','item_nama','jumlah','satuan','signa_deskripsi')->get();
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'pasien' => $pasien, 'data' => $regLists]);
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $soapId) {
        try {
            $data = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $data['diagnosa'] = SoapDiagnosa::where('reg_id',$data['reg_id'])->where('soap_id',$soapId)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function medrecDataByReg(Request $request, $regId) {
        try {
            $data = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',true)->where('pasien_id',$data->pasien_id)->first();
            $data['pasien'] = $pasien;
            $data->nama_pasien = $pasien->nama_pasien;
            $soap = Soap::where('reg_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$soap) {
                $soapId = $this->getSoapId();
                /** create data asesmen */
                $soap                       = new Soap();
                $soap->soap_id              = $soapId;
                $soap->reg_id               = $regId;
                $soap->reff_trx_id          = $regId;
                $soap->tgl_soap             = date('Y-m-d H:i:s');
                $soap->unit_id              = '';
                $soap->unit_nama            = '';
                $soap->dokter_id            = $data->dokter_id;
                $soap->dokter_nama          = $data->dokter_nama;
                
                $soap->pasien_id            = $data->pasien_id;
                $soap->no_rm                = $pasien->no_rm;
                $soap->nama_pasien          = $pasien->nama_pasien;
                
                $soap->status               = 'DRAFT';
                $soap->is_aktif             = true;            
                $soap->client_id            = $this->clientId;
                $soap->created_by           = Auth::user()->username;
                $soap->updated_by           = Auth::user()->username;
            
                $success = $soap->save();
                if(!$success) {
                    return response()->json(['success' => false, 'message' => 'Data soap gagal diambil']);
                }
            }            
            $soap['diagnosa'] = SoapDiagnosa::where('reg_id',$data['reg_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            $data['soap'] = $soap;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request) {
        try {
            $reg = PraktekDokter::where('is_aktif',1)->where('client_id',$this->clientId)->where('reg_id',$request->reg_id)->first();
            if(!$reg) {
                return response()->json(['success' => false, 'message' => 'data registrasi tidak ditemukan.']);
            }
            $regId = $reg->reg_id;

            $soapId = $this->getSoapId();
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /** create data asesmen */
            $soap                       = new Soap();
            $soap->soap_id              = $soapId;
            $soap->reg_id               = $regId;
            $soap->reff_trx_id          = $regId;
            $soap->tgl_soap             = date('Y-m-d H:i:s');
            $soap->unit_id              = '';
            $soap->unit_nama            = '';
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
            
            $soap->status               = 'AKTIF';
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
                $soapDiagnosa->reff_trx_id          = $regId;
                $soapDiagnosa->tgl_diagnosa         = date('Y-m-d H:i:s');
                $soapDiagnosa->unit_id              = '';
                $soapDiagnosa->unit_nama            = '';
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
            if(!$data) { 
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']); 
            }

            $data['diagnosa'] = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan', 'data' => $data ]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
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

    public function update(Request $request, $regId) {
        try {
            $soapId = $request->soap_id;

            $soap = Soap::where('is_aktif',1)->where('client_id',$this->clientId)->where('soap_id',$soapId)->first();
            if(!$soap) {
                return response()->json(['success' => false, 'message' => 'data soap dan diagnosa tidak ditemukan.']);
            }

            // $soapId = $this->getSoapId();
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /** create data asesmen */
            $soap->saturasi_oksigen     = $request->saturasi_oksigen;
            $soap->sistole              = $request->sistole;
            $soap->diastole             = $request->diastole;
            $soap->suhu                 = $request->suhu;
            $soap->denyut_nadi          = $request->denyut_nadi;
            $soap->pernapasan           = $request->pernapasan;
            $soap->tinggi_badan         = $request->tinggi_badan;
            $soap->berat_badan          = $request->berat_badan;
            $soap->note_assesmen        = $request->note_assesmen;
            
            $soap->subjective           = $request->subjective;
            $soap->objective            = $request->objective;
            $soap->assesment            = $request->assesment;
            $soap->plan                 = $request->plan;
            $soap->intervention         = $request->intervention;
            $soap->evaluation           = $request->evaluation;
            
            $soap->catatan              = $request->catatan;
            $soap->status               = 'AKTIF';
            
            $soap->updated_by           = Auth::user()->username;
        
            $success = $soap->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data soap gagal diubah']);
            }

            foreach($request->diagnosa as $diag) {
                $soapDiagnosa = SoapDiagnosa::where('client_id',$this->clientId)->where('is_aktif',1)->where('soap_id',$soapId)->where('soap_diag_id',$diag['soap_diag_id'])->first();
                
                $diagId = $this->clientId.'-DIAG'.date('Ymd').Uuid::uuid4()->getHex();
                if(!$soapDiagnosa) {
                    $soapDiagnosa                       = new SoapDiagnosa();
                    $soapDiagnosa->soap_diag_id         = $this->clientId.'-DIAG'.date('Ymd').Uuid::uuid4()->getHex();
                    $soapDiagnosa->soap_id              = $soapId;
                    $soapDiagnosa->reg_id               = $soap->reg_id;
                    $soapDiagnosa->reff_trx_id          = $soap->reg_id;
                    $soapDiagnosa->tgl_diagnosa         = date('Y-m-d H:i:s');
                    $soapDiagnosa->unit_id              = '';
                    $soapDiagnosa->unit_nama            = '';
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
                    return response()->json(['success' => false, 'message' => 'Data soap diagnosa gagal disimpan']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = Soap::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) { 
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']); 
            }

            $data['diagnosa'] = SoapDiagnosa::where('soap_id',$soapId)->where('is_aktif',1)->where('client_id',$this->clientId)->get();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan', 'data' => $data ]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function remove(Request $request, $soapId) {
        try {
            $soap = Soap::where('is_aktif',1)->where('client_id',$this->clientId)->where('soap_id',$soapId)->first();
            if(!$soap) {
                return response()->json(['success' => false, 'message' => 'data soap dan diagnosa tidak ditemukan.']);
            }

            $soapDiagnosa = SoapDiagnosa::where('client_id',$this->clientId)->where('is_aktif',1)->where('soap_id',$soapId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            
            $success = Soap::where('is_aktif',1)->where('client_id',$this->clientId)->where('soap_id',$soapId)
                ->update(['is_aktif'=>0, 'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data soap gagal dihapus']);
            }

            if($soapDiagnosa) {
                $success = SoapDiagnosa::where('is_aktif',1)->where('client_id',$this->clientId)->where('soap_id',$soapId)
                    ->update(['is_aktif'=>0, 'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data diagnosa soap gagal dihapus']);
                }
            }
            DB::connection('dbclient')->commit();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus' ]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function cetakanPendaftaran($regId){
        try {
            $data['pendaftaran'] = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;

            return view('report/praktekDokter/cetakanBuktiPendaftaran', compact('data'));
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
    
    public function cetakanHistory($regId){
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central']  = $central;

            $praktek = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
            if(!$praktek) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $data['praktek']  = $praktek;
            
            $soap = Soap::where('reg_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$soap) {
                $soapId = $this->getSoapId();
                /** create data asesmen */
                $soap                       = new Soap();
                $soap->soap_id              = $soapId;
                $soap->reg_id               = $regId;
                $soap->reff_trx_id          = $regId;
                $soap->tgl_soap             = date('Y-m-d H:i:s');
                $soap->unit_id              = '';
                $soap->unit_nama            = '';
                $soap->dokter_id            = $praktek->dokter_id;
                $soap->dokter_nama          = $praktek->dokter_nama;
                
                $soap->pasien_id            = $praktek->pasien_id;
                $soap->no_rm                = $pasien->no_rm;
                $soap->nama_pasien          = $pasien->nama_pasien;
                
                $soap->status               = 'DRAFT';
                $soap->is_aktif             = true;            
                $soap->client_id            = $this->clientId;
                $soap->created_by           = Auth::user()->username;
                $soap->updated_by           = Auth::user()->username;
            
                $success = $soap->save();
                if(!$success) {
                    return response()->json(['success' => false, 'message' => 'Data soap gagal diambil']);
                }
            }            
            $data['soap']     = $soap;

            $data['diagnosa'] = SoapDiagnosa::where('reg_id',$praktek['reg_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->get();

            $data['tindakan'] = PemeriksaanDetail::where('reg_id',$regId)->where('client_id',$this->clientId)->where('is_aktif',1)->get();

            $data['resep']    = ResepDetail::where('is_aktif',1)->where('client_id',$this->clientId)->where('reg_id',$regId)->get();


            return view('report/praktekDokter/cetakanHistoryPasien', compact('data'));
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
