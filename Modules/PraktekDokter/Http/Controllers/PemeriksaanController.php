<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

use Modules\PraktekDokter\Entities\PraktekDokter;
use Modules\PraktekDokter\Entities\Pemeriksaan;
use Modules\PraktekDokter\Entities\PemeriksaanDetail;
use Modules\PraktekDokter\Entities\SuratSakit;
use Modules\PraktekDokter\Entities\SuratSehat;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\Transaksi\Entities\Soap;
use Modules\Transaksi\Entities\SoapDiagnosa;
use Modules\ManajemenUser\Entities\Client;

class PemeriksaanController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }


    public function listTindakan(Request $request) {
        try {

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $regId) {
        try {
            $praktek = PraktekDokter::where('reg_id',$regId)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();
            
            if(!$praktek) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Data registrasi tidak ditemukan.']);                
            }

            $pasien = Pasien::where('pasien_id',$praktek->pasien_id)->where('client_id',$this->clientId)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Data pasien tidak ditemukan.']);                
            }
            
            $praktek->nama_pasien = $pasien->nama_pasien;
            $praktek->save();

            $data = Pemeriksaan::where('trx_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                $res = $this->newPemeriksaan($praktek);
                if(!$res) {
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan saat membuat data pemeriksaan']);
                }
                $data = $res;
            }           
             
            $data['items'] = PemeriksaanDetail::where('trx_id',$regId)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function newPemeriksaan($data)
    {
        try {
            $pemeriksaan                      = new Pemeriksaan();
            $pemeriksaan->trx_id              = $data->reg_id;
            $pemeriksaan->reg_id              = $data->reg_id;
            $pemeriksaan->reff_trx_id         = $data->reg_id;
            $pemeriksaan->tgl_transaksi       = date('Y-m-d H:i:s');
            $pemeriksaan->unit_id             = 'PDM';
            $pemeriksaan->unit_nama           = 'PRAKTEK DOKTER';
            $pemeriksaan->jns_transaksi       = 'RAWAT JALAN';
            $pemeriksaan->no_antrian          = '-';
            $pemeriksaan->no_transaksi        = '-';
            
            $pemeriksaan->penjamin_id         = '';
            $pemeriksaan->penjamin_nama       = '';            
            
            $pemeriksaan->dokter_id           = $data->dokter_id;
            $pemeriksaan->dokter_nama         = $data->dokter_nama;
            
            $pemeriksaan->pasien_id           = $data->pasien_id;
            $pemeriksaan->no_rm               = $data->no_rm;
            $pemeriksaan->nama_pasien         = $data->nama_pasien;
            
            $pemeriksaan->total               = 0;
            
            $pemeriksaan->status              = 'DRAFT';
            $pemeriksaan->is_aktif            = true;
            $pemeriksaan->is_realisasi        = false;
            $pemeriksaan->is_bayar            = false;
            
            $pemeriksaan->client_id           = $this->clientId;
            $pemeriksaan->created_by          = Auth::user()->username;
            $pemeriksaan->updated_by          = Auth::user()->username;
        
            $success = $pemeriksaan->save();
            if(!$success) {
                return null;
            }
            return $pemeriksaan;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function getDataPemeriksaan($regId) {
        try {
            $data = Pemeriksaan::where('trx_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $data['items'] = PemeriksaanDetail::where('trx_id',$regId)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            return $data;
        }
        catch(\Exception $e){
            throw $e;
        }
    }

    public function savePemeriksaan(Request $request) {
        try {
            $regId = $request->reg_id;
            $praktek = PraktekDokter::where('reg_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$praktek) { return response()->json(['success' => false, 'message' => 'Data registrasi tidak ditemukan.']); }
            
            $pemeriksaan = Pemeriksaan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->trx_id)->first();
            
            if(!$pemeriksaan) { 
                $pemeriksaan = $this->newPemeriksaan($praktek);
                if(!$pemeriksaan) {
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan saat membuat data pemeriksaan']);
                }
            }
            
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$praktek->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$praktek->pasien_id)->first();
            
            DB::connection('dbclient')->beginTransaction();
        
            $pemeriksaan->updated_by          = Auth::user()->username;
            $pemeriksaan->total               = $request->total;
            $pemeriksaan->pasien_id           = $pasien->pasien_id;
            $pemeriksaan->no_rm               = $pasien->no_rm;
            $pemeriksaan->nama_pasien         = $pasien->nama_pasien;        
            $success = $pemeriksaan->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data pemeriksaan gagal diupdate.']);
            }

            //insert detail pemeriksaan
            foreach($request->items as $dt) {
                /*** insert ke table bhp detail */
                $detailId = $dt['detail_id'];
                $detail = PemeriksaanDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$regId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$detail) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $detail = new PemeriksaanDetail();
                    $detail->detail_id     = $detailId;
                    $detail->trx_id        = $regId;
                    $detail->reg_id        = $regId;
                    $detail->reff_trx_id   = $regId;
                    
                    $detail->item_id       = $dt['item_id'];
                    $detail->client_id     = $this->clientId;
                    $detail->created_by    = Auth::user()->username;
                }

                $detail->item_nama     = $dt['item_nama'];
                $detail->unit_id       = $pemeriksaan->unit_id;
                $detail->unit_nama     = $pemeriksaan->unit_nama;
                
                $detail->depo_id       = '';
                $detail->depo_nama     = '';

                $detail->dokter_id     = $pemeriksaan->dokter_id;
                $detail->dokter_nama   = $pemeriksaan->dokter_nama;
                
                $detail->jenis         = $dt['jenis'];
                $detail->jumlah        = $dt['jumlah'];
                //$detail->jml_formula   = $dt['jml_formula'];
                $detail->satuan        = $dt['satuan'];                
                $detail->nilai         = $dt['nilai'];
                $detail->diskon_persen = $dt['diskon_persen'];
                $detail->diskon        = $dt['diskon'];
                $detail->harga_diskon  = $dt['harga_diskon'];
                $detail->subtotal      = $dt['subtotal'];

                $detail->tindakan_id   = $dt['item_id'];
                $detail->tindakan_nama = $dt['item_nama'];

                $detail->is_realisasi  = false;
                //$detail->bhp_tindakan  = $dt['bhp_tindakan'];
                $detail->is_bhp        = false;
                $detail->is_aktif      = $dt['is_aktif'];
                $detail->updated_by    = Auth::user()->username;
                
                $success = $detail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail pemeriksaan.'];
                }

            }
            DB::connection('dbclient')->commit();

            $data = $this->getDataPemeriksaan($regId);
            return response()->json(['success' => true, 'message' => 'Data bhp berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }

    public function cetakSuratPasien($jnsSurat, $regId){
        try {

            $central         = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;

            $praktek = PraktekDokter::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->first();

            if(!$praktek){
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Nomor registrasi tidak ditemukan.']);                
            }

            $suratSehat = SuratSehat::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('no_rm',$praktek->no_rm)
                ->first();

            if(!$suratSehat){
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Data surat sehat tidak ditemukan.']);                
            }

            $suratSakit = SuratSakit::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('no_rm',$praktek->no_rm)
                ->first();
            
            $diagnosa = SoapDiagnosa::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('soap_id',$suratSakit->soap_id)
                ->select('tipe','nama_icd')
                ->get();

            if(!$suratSehat){
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Data surat sehat tidak ditemukan.']);                
            }

            // Create Surat Sehat
            if($jnsSurat == "SuratSehat"){
                $data['SuratSehat']     = $suratSehat;
                // return $data;
                return view('report/praktekDokter/cetakanSuratSehat',compact('data'));
            }
            // Create Surat Sakit
            if($jnsSurat == "SuratSakit"){
                $data['SuratSakit']             = $suratSakit;
                $data['SuratSakit']['diagnosa'] = $diagnosa;
                // return $data;
                return view('report/praktekDokter/cetakanSuratSakit',compact('data'));
            }

        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengambil data. Error: '.$e->getMessage()];
        }
    }

    public function dataSuratPasien($jnsSurat, $regId, ) {
        try {
            if($jnsSurat == "SuratSehat"){
                $data = SuratSehat::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$regId)
                    ->first();
            } else if ($jnsSurat == "SuratSakit") {
                $data = SuratSakit::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$regId)
                    ->first();
            } else {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Jenis surat tidak ditemukkan.']);                
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function createSuratPasien(Request $request, $jnsSurat)
    {
        try {
            $regId = $request->input('reg_id');
            if(!$request->has('reg_id'))
            {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Data registrasi tidak ditemukan.']);                
            }

            $dokter_id   = $request->dokter_id;
            $dokter_nama = $request->dokter_nama;

            $praktek = PraktekDokter::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->first();

            $pasien = Pasien::where('tb_pasien.is_aktif',1)
                ->leftJoin('tb_pasien_alamat','tb_pasien.pasien_id','=','tb_pasien_alamat.pasien_id')
                ->where('tb_pasien.client_id',$this->clientId)
                ->where('tb_pasien.no_rm',$praktek->no_rm)
                ->first();

            $umur = Carbon::parse($pasien->tgl_lahir)->diffInYears(Carbon::now());

            $tandaVital = Soap::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->select('soap_id','tinggi_badan','berat_badan','suhu','diastole','sistole','denyut_nadi','pernapasan')
                ->first();
            
            $diagnosa = SoapDiagnosa::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('soap_id',$tandaVital->soap_id)
                ->select('soap_diag_id')
                ->get();
                
            if($jnsSurat == "SuratSehat"){
                $suratSehat                 = new SuratSehat();
                $suratSehat->reg_id         = $praktek->reg_id;
                $suratSehat->soap_id        = $tandaVital->soap_id;
                $suratSehat->tgl_dibuat     = date('Y-m-d H:i:s');
                $suratSehat->nama_pasien    = $praktek->nama_pasien;
                $suratSehat->pasien_id      = $praktek->pasien_id;  
                $suratSehat->jns_kelamin    = $praktek->jns_kelamin;
                $suratSehat->no_rm          = $praktek->no_rm;
                $suratSehat->tgl_lahir      = $pasien->tgl_lahir;
                $suratSehat->umur           = $umur;
                $suratSehat->alamat         = $pasien->alamat;

                $suratSehat->sistole        = $tandaVital->sistole;
                $suratSehat->diastole       = $tandaVital->diastole;
                $suratSehat->suhu           = $tandaVital->suhu;
                $suratSehat->denyut_nadi    = $tandaVital->denyut_nadi;
                $suratSehat->pernapasan     = $tandaVital->pernapasan;
                $suratSehat->berat_badan    = $tandaVital->berat_badan;
                $suratSehat->tinggi_badan   = $tandaVital->tinggi_badan;
            
                $suratSehat->dokter_id      = $dokter_id;
                $suratSehat->dokter_nama    = $dokter_nama;
                
                $suratSehat->is_aktif       = true;
                $suratSehat->client_id      = $this->clientId;
                $suratSehat->created_by     = Auth::user()->username;
                $suratSehat->updated_by     = Auth::user()->username;
            
                $success = $suratSehat->save();
                if(!$success) {
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan. Tidak dapat membuat surat sehat.']);                
                } else {
                    return response()->json(['success' => true, 'message' => 'Berihasi menyimpan riwayat surat sehat.']);                
                }
            } else if($jnsSurat == "SuratSakit"){ 
                $suratSakit                  = new SuratSakit();
                $suratSakit->reg_id          = $praktek->reg_id;
                $suratSakit->soap_id         = $tandaVital->soap_id;
                $suratSakit->soap_diag_id    = $diagnosa;
                $suratSakit->tgl_dibuat      = date('Y-m-d H:i:s');
                $suratSakit->nama_pasien     = $praktek->nama_pasien;
                $suratSakit->pasien_id       = $praktek->pasien_id;
                $suratSehat->no_rm           = $praktek->no_rm;
                $suratSakit->umur            = $umur;
                $suratSakit->pekerjaan       = $praktek->pekerjaan;
                $suratSakit->alamat          = $pasien->alamat;

                $suratSakit->kateg_istirahat = $request->kateg_istirahat;
                $suratSakit->hari            = $request->hari;
                $suratSakit->tgl_awal        = $request->tgl_awal;
                $suratSakit->tgl_akhir       = $request->tgl_akhir;
                $suratSakit->catatan         = $request->catatan;
            
                $suratSakit->dokter_id       = $dokter_id;
                $suratSakit->dokter_nama     = $dokter_nama;
                
                $suratSakit->is_aktif        = true;
                $suratSakit->client_id       = $this->clientId;
                $suratSakit->created_by      = Auth::user()->username;
                $suratSakit->updated_by      = Auth::user()->username;
            
                $success = $suratSakit->save();
                if(!$success) {
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan. Tidak dapat membuat surat sakit.']);                
                } else {
                    return response()->json(['success' => true, 'message' => 'Berihasi menyimpan riwayat surat sakit.']);
                } 
            } else {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Jenis surat tidak ditemukkan.']);                
            }
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses membuat surat sehat', 'error' => $e->getMessage()]);
        }

        
    }
    
    public function updateSuratPasien(Request $request, $jnsSurat)
    {
        try {
            $regId = $request->input('reg_id');
            if(!$request->has('reg_id'))
            {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Data registrasi tidak ditemukan.']);                
            }

            $dokter_id = $request->dokter_id;
            $dokter_nama = $request->dokter_nama;

            if($jnsSurat == "SuratSehat"){
                $suratSehat = SuratSehat::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$regId)
                    ->first();
                
                $tekananDarah = $request->tekananDarah;
                $valTekananDarah = explode('/', $tekananDarah);

                $suratSehat->sistole        = $tekananDarah[0];
                $suratSehat->diastole       = $tekananDarah[1];
                $suratSehat->suhu           = $request->suhu;
                $suratSehat->denyut_nadi    = $request->denyut_nadi;
                $suratSehat->pernapasan     = $request->pernapasan;
                $suratSehat->berat_badan    = $request->berat_badan;
                $suratSehat->tinggi_badan   = $request->tinggi_badan;
            
                $suratSehat->dokter_id      = $dokter_id;
                $suratSehat->dokter_nama    = $dokter_nama;
                
                $suratSehat->is_aktif       = true;
                $suratSehat->client_id      = $this->clientId;
                $suratSehat->created_by     = Auth::user()->username;
                $suratSehat->updated_by     = Auth::user()->username;
            
                $success = $suratSehat->save();
                if(!$success) {
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan. Tidak dapat merubah surat sehat.']);                
                } else {
                    return response()->json(['success' => true, 'message' => 'Berihasi merubah riwayat surat sehat.']);                
                }
            } else if($jnsSurat == "SuratSakit"){ 
                $suratSakit = SuratSakit::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$regId)
                    ->first();

                $suratSakit->kateg_istirahat = $request->kateg_istirahat;
                $suratSakit->hari            = $request->hari;
                $suratSakit->tgl_awal        = $request->tgl_awal;
                $suratSakit->tgl_akhir       = $request->tgl_akhir;
                $suratSakit->catatan         = $request->catatan;
            
                $suratSakit->dokter_id       = $dokter_id;
                $suratSakit->dokter_nama     = $dokter_nama;
                
                $suratSakit->is_aktif        = true;
                $suratSakit->client_id       = $this->clientId;
                $suratSakit->created_by      = Auth::user()->username;
                $suratSakit->updated_by      = Auth::user()->username;
            
                $success = $suratSakit->save();
                if(!$success) {
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan. Tidak dapat merubah surat sakit.']);                
                } else {
                    return response()->json(['success' => true, 'message' => 'Berihasi merubah riwayat surat sakit.']);
                } 
            } else {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan. Jenis surat tidak ditemukkan.']);                
            }
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses merubah surat sehat', 'error' => $e->getMessage()]);
        }

        
    }
}
