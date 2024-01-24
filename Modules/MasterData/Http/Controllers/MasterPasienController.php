<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\PasienAlergi;
use Modules\MasterData\Entities\PasienKeluarga;
use Modules\MasterData\Entities\PasienDetail;

use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kelurahan;

use Modules\MasterData\Entities\Penjamin;

use Ramsey\Uuid\Nonstandard\Uuid;
use App\Imports\CoaImport;
use App\Exports\MasterExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Illuminate\Support\Facades\Auth;
use RegistrasiHelper;

class MasterPasienController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request)
    {
        try {
            $perPage = 10;
            $aktif = "true";
            $keyword = "%%";
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') {
                    $perPage = Pasien::where('client_id',$this->clientId)->count(); 
                }
            }
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }  
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            $data = Pasien::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use($keyword){
                        $q->where('nama_pasien','ILIKE',$keyword)
                        ->orWhere('pasien_id','ILIKE',$keyword)
                        ->orWhere('no_rm','ILIKE',$keyword)
                        ->orWhere('tgl_lahir','ILIKE',$keyword);
                    })->orderBy('no_rm','DESC')->paginate($perPage); 

            foreach($data->items() as $item) {
                $id = $item['pasien_id'];
                $item['detail'] = PasienDetail::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
                $item['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
                $item['keluarga'] = PasienKeluarga::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
                $item['alergi'] = PasienAlergi::where('client_id', $this->clientId)->where('pasien_id',$id)->where('is_aktif',1)->get();
            }

            // /**
            //  * tambahkan semua filter yang dikirim dari client
            //  */
            // $query = Pasien::query();
            // if(!$request->has('is_aktif')) {
            //     $query = $query->where('is_aktif',true);
            // }
            // foreach ($request->except('_token') as $key => $data) {
            //     if ($key !== "page" && $key !== "per_page") {                    
            //         $query = $query->where($key, 'ILIKE', '%' . $data . '%');
            //     }
            // }            
            // $data = $query->where('client_id', $this->clientId)->orderBy('penjamin_nama', 'ASC')->paginate($perPage);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan daftar Pasien. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        // return $request;
        try {
            /** check client ID */
            //$id = $this->getPasienId($request->is_pasien_luar);
            //$rm = $this->getMR();
            $id = RegistrasiHelper::instance()->PasienId($this->clientId); 
            $data = new Pasien();
            $no_rm = null;

            if($request->no_rm == '' || $request->no_rm == null) {                 
                $no_rm =  RegistrasiHelper::instance()->NoRM($this->clientId,$request->is_pasien_luar);
            }
            else { 
                $no_rm = $request->no_rm;
                $pasien = Pasien::where('no_rm', $no_rm)->where('client_id', $this->clientId)->first();
                if ($pasien) {
                    return response()->json(['success' => false, 'message' => 'no rekam medis sudah ada data tidak dapat disimpan.']);
                }
            }
            
            $prop = null; $kota = null; $kec = null; $kel = null;
            $propTinggal = null; $kotaTinggal = null; $kecTinggal = null; $kelTinggal = null;
            
            $prop = Propinsi::where('propinsi_id',$request->alamat['propinsi_id'])->select('propinsi')->first(); 
            if($prop) { $prop = $prop->propinsi; }
            $kota = Kota::where('kota_id',$request->alamat['kota_id'])->select('kota')->first(); 
            if($kota) { $kota = $kota->kota; }
            $kec = Kecamatan::where('kecamatan_id',$request->alamat['kecamatan_id'])->select('kecamatan')->first(); 
            if($kec) { $kec = $kec->kecamatan; }
            $kel = Kelurahan::where('kelurahan_id',$request->alamat['kelurahan_id'])->select('kelurahan')->first(); 
            if($kel) { $kel = $kel->kelurahan; }
            
            if($request->alamat['is_ktp_sama_dgn_tinggal'] == false) {
                $propTinggal = Propinsi::where('propinsi_id',$request->alamat['propinsi_tinggal_id'])->select('propinsi')->first(); 
                if($propTinggal) { $propTinggal = $propTinggal->propinsi; }
                $kotaTinggal = Kota::where('kota_id',$request->alamat['kota_tinggal_id'])->select('kota')->first(); 
                if($kotaTinggal) { $kotaTinggal = $kotaTinggal->kota; }
                $kecTinggal = Kecamatan::where('kecamatan_id',$request->alamat['kecamatan_tinggal_id'])->select('kecamatan')->first(); 
                if($kecTinggal) { $kecTinggal = $kecTinggal->kecamatan;  }
                $kelTinggal = Kelurahan::where('kelurahan_id',$request->alamat['kelurahan_tinggal_id'])->select('kelurahan')->first(); 
                if($kelTinggal) { $kelTinggal = $kelTinggal->kelurahan;  }
            }
            else { $propTinggal = $prop; $kotaTinggal = $kota; $kecTinggal = $kec; $kelTinggal = $kel; }

            $asuransi = Penjamin::where('client_id',$this->clientId)->where('penjamin_id',$request->penjamin_id)->where('is_aktif',1)->first();
            if(!$asuransi) {
                return response()->json(['success' => false, 'message' => 'data penjamin / asuransi / instansi tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            /**
             * penyimpanan data utama pasien
             */
            $data->pasien_id = $id;
            $data->no_rm = $no_rm;
            $data->is_pasien_luar = $request->is_pasien_luar;
            $data->nama_pasien = $request->nama_pasien;
            $data->salut = $request->salut;
            $data->nama_lengkap = strtoupper($request->salut).' '.strtoupper($request->nama_pasien);
            $data->nik = $request->nik;
            $data->no_kk = $request->no_kk;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->tgl_lahir = $request->tgl_lahir;
            $data->tempat_lahir = strtoupper($request->tempat_lahir);
            
            $data->jns_penjamin = $asuransi->jns_penjamin;
            $data->penjamin_id = $request->penjamin_id;
            $data->penjamin_nama = $asuransi->penjamin_nama;            
            $data->no_kepesertaan = $request->no_kepesertaan;
            
            $data->tgl_terakhir_periksa = $request->tgl_terakhir_periksa;
            $data->is_hamil = $request->is_hamil;
            $data->is_meninggal = $request->is_meninggal;
            $data->tgl_meninggal = $request->tgl_meninggal;
            $data->penyebab_kematian = $request->penyebab_kematian;
            $data->tgl_daftar = $request->tgl_daftar;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->created_at = date('Y-m-d H:i:s');

            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data pasien.']);
            }
            /**
             * penyimpanan data detail pasien
             */
            $detail = new PasienDetail();
            $detail->pasien_id = $id;
            $detail->gol_darah = $request->detail['gol_darah'];
            $detail->rhesus = $request->detail['rhesus'];
            $detail->pendidikan = $request->detail['pendidikan'];
            $detail->pekerjaan = $request->detail['pekerjaan'];
            $detail->agama = $request->detail['agama'];
            $detail->kebangsaan = $request->detail['kebangsaan'];
            $detail->suku_bangsa = $request->detail['suku_bangsa'];
            $detail->no_telepon = $request->detail['no_telepon'];
            $detail->alamat_email = $request->detail['alamat_email'];
            $detail->no_kontak_darurat = $request->detail['no_kontak_darurat'];
            $detail->nama_kontak_darurat = $request->detail['nama_kontak_darurat'];
            $detail->hub_kontak_darurat = $request->detail['hub_kontak_darurat'];
            $detail->is_aktif = true;
            $detail->client_id = $this->clientId;
            $detail->created_by = Auth::user()->username;
            $detail->updated_by = Auth::user()->username;
            
            $success = $detail->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data detail pasien.']);
            }

            /**
             * penyimpanan data keluarga pasien
             */

            $keluarga = new PasienKeluarga();
            $keluarga->pasien_id = $id;
            $keluarga->status_perkawinan = $request->keluarga['status_perkawinan'];
            $keluarga->nama_pasangan = $request->keluarga['nama_pasangan'];
            $keluarga->nama_ayah = $request->keluarga['nama_ayah'];
            $keluarga->nama_ibu = $request->keluarga['nama_ibu'];
            $keluarga->pekerjaan_pasangan = $request->keluarga['pekerjaan_pasangan'];
            $keluarga->pekerjaan_ayah = $request->keluarga['pekerjaan_ayah'];
            $keluarga->pekerjaan_ibu = $request->keluarga['pekerjaan_ibu'];
            $keluarga->is_aktif = true;
            $keluarga->client_id = $this->clientId;
            $keluarga->created_by = Auth::user()->username;
            $keluarga->updated_by = Auth::user()->username;
            $success = $keluarga->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data keluarga pasien.']);
            }

            /**
             * penyimpanan data alamat pasien
             */
            $alamat = new PasienAlamat();
            $alamat->pasien_id = $id;
            $alamat->alamat = $request->alamat['alamat'];
            $alamat->rt = $request->alamat['rt'];
            $alamat->rw = $request->alamat['rw'];
            $alamat->propinsi_id = $request->alamat['propinsi_id'];
            $alamat->propinsi = $prop;
            $alamat->kota_id = $request->alamat['kota_id'];
            $alamat->kota = $kota;
            $alamat->kecamatan_id = $request->alamat['kecamatan_id'];
            $alamat->kecamatan = $kec;
            $alamat->kelurahan_id = $request->alamat['kelurahan_id'];
            $alamat->kelurahan = $kel;
            $alamat->kodepos = $request->alamat['kodepos'];
            $alamat->is_ktp_sama_dgn_tinggal = $request->alamat['is_ktp_sama_dgn_tinggal'];
            
            if($request->alamat['is_ktp_sama_dgn_tinggal'] == true) {
                $alamat->alamat_tinggal = $request->alamat['alamat'];
                $alamat->rt_tinggal = $request->alamat['rt'];
                $alamat->rw_tinggal = $request->alamat['rw'];
                $alamat->propinsi_tinggal_id = $request->alamat['propinsi_id'];
                $alamat->propinsi_tinggal = $prop;
                $alamat->kota_tinggal_id = $request->alamat['kota_id'];
                $alamat->kota_tinggal = $kota;
                $alamat->kecamatan_tinggal_id = $request->alamat['kecamatan_id'];
                $alamat->kecamatan_tinggal = $kec;
                $alamat->kelurahan_tinggal_id = $request->alamat['kelurahan_id'];
                $alamat->kelurahan_tinggal = $kel;
                $alamat->kodepos_tinggal = $request->alamat['kodepos'];
            }
            else {
                $alamat->alamat_tinggal = $request->alamat['alamat_tinggal'];
                $alamat->rt_tinggal = $request->alamat['rt_tinggal'];
                $alamat->rw_tinggal = $request->alamat['rw_tinggal'];
                $alamat->propinsi_tinggal_id = $request->alamat['propinsi_tinggal_id'];
                $alamat->propinsi_tinggal = $propTinggal;
                $alamat->kota_tinggal_id = $request->alamat['kota_tinggal_id'];
                $alamat->kota_tinggal = $kotaTinggal;
                $alamat->kecamatan_tinggal_id = $request->alamat['kecamatan_tinggal_id'];
                $alamat->kecamatan_tinggal = $kecTinggal;
                $alamat->kelurahan_tinggal_id = $request->alamat['kelurahan_tinggal_id'];
                $alamat->kelurahan_tinggal = $kelTinggal;
                $alamat->kodepos_tinggal = $request->alamat['kodepos_tinggal'];
            }

            $alamat->is_aktif = 1;
            $alamat->client_id = $this->clientId;
            $alamat->created_by = Auth::user()->username;
            $alamat->created_at = date('Y-m-d H:i:s');

            $success = $alamat->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data alamat.']);
            }

            foreach($request->alergi as $dt) {
                $alergi = new PasienAlergi();
                $alergi->pasien_alergi_id = Uuid::uuid4()->getHex();
                $alergi->pasien_id = $id;
                $alergi->jns_alergi = $dt['jns_alergi'];
                $alergi->catatan_alergi = $dt['catatan_alergi'];
                $alergi->akibat = $dt['akibat'];
                $alergi->tgl_kejadian_awal = $dt['tgl_kejadian_awal'];
                $alergi->is_aktif = $dt['is_aktif'];
                $alergi->client_id = $this->clientId;
                $alergi->created_by = Auth::user()->username;
                $alergi->updated_by = Auth::user()->username;
                $success = $alergi->save();    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data alergi pasien.']);
                }
            }
            
            DB::connection('dbclient')->commit();

            $data = Pasien::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['detail'] = PasienDetail::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['keluarga'] = PasienKeluarga::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alergi'] = [];
            $alergiPasien = PasienAlergi::where('client_id', $this->clientId)->where('pasien_id',$id)->where('is_aktif',1)->get();
            if($alergiPasien) { $data['alergi'] = $alergiPasien; }
            
            return response()->json(['success' => true, 'message'  => 'Berhasil simpan data ' . $data->nama_pasien, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menyimpan data Pasien. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = Pasien::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Data pasien tidak ditemukan!', 'Error Message: ' => '404']);
            }
            $data['detail'] = PasienDetail::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['keluarga'] = PasienKeluarga::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alergi'] = [];
            $alergiPasien = PasienAlergi::where('client_id', $this->clientId)->where('pasien_id',$id)->where('is_aktif',1)->get();
            if($alergiPasien) { $data['alergi'] = $alergiPasien; }

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan data Pasien' . '. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            /** check client ID */
            $id = $request->pasien_id;
            $data = Pasien::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message'  => 'Data pasien tidak ditemukan!']);
            }

            $detail = PasienDetail::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            if(!$detail) { 
                $detail = new PasienDetail(); 
                $detail->pasien_id = $id;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
            }

            $keluarga = PasienKeluarga::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            if(!$keluarga) { 
                $keluarga = new PasienKeluarga(); 
                $keluarga->pasien_id = $id;
                $keluarga->client_id = $this->clientId;
                $keluarga->created_by = Auth::user()->username;
            }

            $alamat = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            if(!$alamat) { 
                $alamat = new PasienAlamat(); 
                $alamat->pasien_id = $id;
                $alamat->client_id = $this->clientId;
                $alamat->created_by = Auth::user()->username;
            }
            
            $prop = null; $kota = null; $kec = null; $kel = null;
            $propTinggal = null; $kotaTinggal = null; $kecTinggal = null; $kelTinggal = null;
            
            $prop = Propinsi::where('propinsi_id',$request->alamat['propinsi_id'])->select('propinsi')->first(); 
            if($prop) { $prop = $prop->propinsi; }
            $kota = Kota::where('kota_id',$request->alamat['kota_id'])->select('kota')->first(); 
            if($kota) { $kota = $kota->kota; }
            $kec = Kecamatan::where('kecamatan_id',$request->alamat['kecamatan_id'])->select('kecamatan')->first(); 
            if($kec) { $kec = $kec->kecamatan; }
            $kel = Kelurahan::where('kelurahan_id',$request->alamat['kelurahan_id'])->select('kelurahan')->first(); 
            if($kel) { $kel = $kel->kelurahan; }
            
            if($request->alamat['is_ktp_sama_dgn_tinggal'] == false) {
                $propTinggal = Propinsi::where('propinsi_id',$request->alamat['propinsi_tinggal_id'])->select('propinsi')->first(); 
                if($propTinggal) { $propTinggal = $propTinggal->propinsi; }
                $kotaTinggal = Kota::where('kota_id',$request->alamat['kota_tinggal_id'])->select('kota')->first(); 
                if($kotaTinggal) { $kotaTinggal = $kotaTinggal->kota; }
                $kecTinggal = Kecamatan::where('kecamatan_id',$request->alamat['kecamatan_tinggal_id'])->select('kecamatan')->first(); 
                if($kecTinggal) { $kecTinggal = $kecTinggal->kecamatan;  }
                $kelTinggal = Kelurahan::where('kelurahan_id',$request->alamat['kelurahan_tinggal_id'])->select('kelurahan')->first(); 
                if($kelTinggal) { $kelTinggal = $kelTinggal->kelurahan;  }
            }
            else { $propTinggal = $prop; $kotaTinggal = $kota; $kecTinggal = $kec; $kelTinggal = $kel; }

            $asuransi = Penjamin::where('client_id',$this->clientId)->where('penjamin_id',$request->penjamin_id)->where('is_aktif',1)->first();
            if(!$asuransi) {
                return response()->json(['success' => false, 'message' => 'data penjamin / asuransi / instansi tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            /**
             * penyimpanan data utama pasien
             */
            $data->nama_pasien = $request->nama_pasien;
            $data->salut = $request->salut;
            $data->nama_lengkap = strtoupper($request->salut).' '.strtoupper($request->nama_pasien);
            $data->nik = $request->nik;
            $data->no_kk = $request->no_kk;
            $data->jns_kelamin = $request->jns_kelamin;
            $data->tgl_lahir = $request->tgl_lahir;
            $data->tempat_lahir = strtoupper($request->tempat_lahir);
            $data->penjamin_id = $request->penjamin_id;
            $data->jns_penjamin = $asuransi->jns_penjamin;
            $data->penjamin_nama = $asuransi->penjamin_nama;            
            
            $data->no_kepesertaan = $request->no_kepesertaan;
            $data->tgl_terakhir_periksa = $request->tgl_terakhir_periksa;
            $data->is_hamil = $request->is_hamil;
            $data->is_meninggal = $request->is_meninggal;
            $data->tgl_meninggal = $request->tgl_meninggal;
            $data->penyebab_kematian = $request->penyebab_kematian;
            $data->tgl_daftar = $request->tgl_daftar;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam mengubah data pasien.']);
            }
            
            /**
             * penyimpanan data detail pasien
             */
            $detail->gol_darah = $request->detail['gol_darah'];
            $detail->rhesus = $request->detail['rhesus'];
            $detail->pendidikan = $request->detail['pendidikan'];
            $detail->pekerjaan = $request->detail['pekerjaan'];
            $detail->agama = $request->detail['agama'];
            $detail->kebangsaan = $request->detail['kebangsaan'];
            $detail->suku_bangsa = $request->detail['suku_bangsa'];
            $detail->no_telepon = $request->detail['no_telepon'];
            $detail->alamat_email = $request->detail['alamat_email'];
            $detail->no_kontak_darurat = $request->detail['no_kontak_darurat'];
            $detail->nama_kontak_darurat = $request->detail['nama_kontak_darurat'];
            $detail->hub_kontak_darurat = $request->detail['hub_kontak_darurat'];
            $detail->is_aktif = $request->is_aktif;
            $detail->updated_by = Auth::user()->username;
            
            $success = $detail->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data detail pasien.']);
            }

            /**
             * penyimpanan data keluarga pasien
             */

            $keluarga->pasien_id = $id;
            $keluarga->status_perkawinan = $request->keluarga['status_perkawinan'];
            $keluarga->nama_pasangan = $request->keluarga['nama_pasangan'];
            $keluarga->nama_ayah = $request->keluarga['nama_ayah'];
            $keluarga->nama_ibu = $request->keluarga['nama_ibu'];
            $keluarga->pekerjaan_pasangan = $request->keluarga['pekerjaan_pasangan'];
            $keluarga->pekerjaan_ayah = $request->keluarga['pekerjaan_ayah'];
            $keluarga->pekerjaan_ibu = $request->keluarga['pekerjaan_ibu'];
            $keluarga->is_aktif = $request->is_aktif;
            $keluarga->updated_by = Auth::user()->username;
            $success = $keluarga->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data keluarga pasien.']);
            }

            /**
             * penyimpanan data alamat pasien
             */
            //$alamat->pasien_id = $id;
            $alamat->alamat = $request->alamat['alamat'];
            $alamat->rt = $request->alamat['rt'];
            $alamat->rw = $request->alamat['rw'];
            $alamat->propinsi_id = $request->alamat['propinsi_id'];
            $alamat->propinsi = $prop;
            $alamat->kota_id = $request->alamat['kota_id'];
            $alamat->kota = $kota;
            $alamat->kecamatan_id = $request->alamat['kecamatan_id'];
            $alamat->kecamatan = $kec;
            $alamat->kelurahan_id = $request->alamat['kelurahan_id'];
            $alamat->kelurahan = $kel;
            $alamat->kodepos = $request->alamat['kodepos'];

            $alamat->is_ktp_sama_dgn_tinggal = $request->alamat['is_ktp_sama_dgn_tinggal'];
            
            if($request->alamat['is_ktp_sama_dgn_tinggal'] == true) {
                $alamat->alamat_tinggal = $request->alamat['alamat'];
                $alamat->rt_tinggal = $request->alamat['rt'];
                $alamat->rw_tinggal = $request->alamat['rw'];
                $alamat->propinsi_tinggal_id = $request->alamat['propinsi_id'];
                $alamat->propinsi_tinggal = $prop;
                $alamat->kota_tinggal_id = $request->alamat['kota_id'];
                $alamat->kota_tinggal = $kota;
                $alamat->kecamatan_tinggal_id = $request->alamat['kecamatan_id'];
                $alamat->kecamatan_tinggal = $kec;
                $alamat->kelurahan_tinggal_id = $request->alamat['kelurahan_id'];
                $alamat->kelurahan_tinggal = $kel;
                $alamat->kodepos_tinggal = $request->alamat['kodepos'];
            }
            else {
                $alamat->alamat_tinggal = $request->alamat['alamat_tinggal'];
                $alamat->rt_tinggal = $request->alamat['rt_tinggal'];
                $alamat->rw_tinggal = $request->alamat['rw_tinggal'];
                $alamat->propinsi_tinggal_id = $request->alamat['propinsi_tinggal_id'];
                $alamat->propinsi_tinggal = $propTinggal;
                $alamat->kota_tinggal_id = $request->alamat['kota_tinggal_id'];
                $alamat->kota_tinggal = $kotaTinggal;
                $alamat->kecamatan_tinggal_id = $request->alamat['kecamatan_tinggal_id'];
                $alamat->kecamatan_tinggal = $kecTinggal;
                $alamat->kelurahan_tinggal_id = $request->alamat['kelurahan_tinggal_id'];
                $alamat->kelurahan_tinggal = $kelTinggal;
                $alamat->kodepos_tinggal = $request->alamat['kodepos_tinggal'];
            }

            $alamat->is_aktif = $request->is_aktif;
            $alamat->updated_by = Auth::user()->username;

            $success = $alamat->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data alamat.']);
            }

            foreach($request->alergi as $dt) {
                if($dt['pasien_alergi_id']) {
                    $alergi = PasienAlergi::where('client_id', $this->clientId)
                        ->where('pasien_id',$id)
                        ->where('pasien_alergi_id',$dt['pasien_alergi_id'])->first();
                    if(!$alergi) { 
                        $alergi = new PasienAlergi(); 
                        $alergi->pasien_alergi_id = Uuid::uuid4()->getHex();
                        $alergi->created_by = Auth::user()->username;
                        $alergi->client_id = $this->clientId;
                    }
                }
                else {
                    $alergi = new PasienAlergi(); 
                    $alergi->pasien_alergi_id = Uuid::uuid4()->getHex();
                    $alergi->created_by = Auth::user()->username;
                    $alergi->client_id = $this->clientId;
                }
                
                $alergi->pasien_id = $id;
                $alergi->jns_alergi = $dt['jns_alergi'];
                $alergi->catatan_alergi = $dt['catatan_alergi'];
                $alergi->akibat = $dt['akibat'];
                $alergi->tgl_kejadian_awal = $dt['tgl_kejadian_awal'];
                $alergi->is_aktif = $dt['is_aktif'];
                $alergi->updated_by = Auth::user()->username;
                $success = $alergi->save();    
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => true, 'message'  => 'Ada kesalahan dalam penyimpanan data alergi pasien.']);
                }
            }           

            DB::connection('dbclient')->commit();

            $data = Pasien::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['detail'] = PasienDetail::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['keluarga'] = PasienKeluarga::where('client_id', $this->clientId)->where('pasien_id',$id)->first();
            $data['alergi'] = [];
            $alergiPasien = PasienAlergi::where('client_id', $this->clientId)->where('pasien_id',$id)->where('is_aktif',1)->get();
            if($alergiPasien) { $data['alergi'] = $alergiPasien; }

            return response()->json(['success' => true, 'message'  => 'Berhasil Update data ' . $data->nama_pasien, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Update data Pasien. Error Desc: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id)
    {
        try {
            $data = Pasien::where('client_id', $this->clientId)->where('pasien_id',$id)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Data pasien tidak ditemukan / sudah non-aktif!']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = date('Y-m-d H:i:s');
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message'  => 'Ada kesalahan dalam menonaktifkan pasien.']);
            }

            return response()->json(['success' => true, 'message'  => 'Berhasil Menonaktifkan data pasien']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menonaktifkan data Pasien. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    // public function getPasienId($isPasienLuar)
    // {
    //     try {
    //         $prefix = "PSN";
    //         if($isPasienLuar == 1 || $isPasienLuar == true ) {
    //             $prefix = "PSL";
    //         }
    //         $id = $prefix . date('Ym') . '-00001';
    //         $maxId = Pasien::withTrashed()->where("pasien_id", 'LIKE', $prefix . date('Ym') . '-%')->max("pasien_id");
    //         if (!$maxId) {
    //             $id = $prefix . date('Ym') . '-00001';
    //         } else {
    //             $maxId = str_replace($prefix.date('Ym').'-', '', $maxId);
    //             $count = $maxId + 1;
    //             if ($count < 10) {
    //                 $id = $prefix . date('Ym') . '-0000' . $count;
    //             } elseif ($count >= 10 && $count < 100) {
    //                 $id = $prefix . date('Ym') . '-000' . $count;
    //             } elseif ($count >= 100 && $count < 1000) {
    //                 $id = $prefix . date('Y') . '-00' . $count;
    //             } elseif ($count >= 1000 && $count < 10000) {
    //                 $id = $prefix . date('Ym') . '-0' . $count;
    //             } else {
    //                 $id = $prefix . date('Ym') . '-' . $count;
    //             }
    //         }
    //         return $id;
    //     } catch (\Exception $e) {
    //         return $prefix . date('Ym') . '-' . Uuid::uuid4()->getHex();
    //     }
    // }

    // public function getMR($isPasienLuar)
    // {
    //     try {
    //         $rm = "";
    //         if($isPasienLuar) {
    //             $maxId = Pasien::withTrashed()->where('client_id', $this->clientId)
    //                 ->where("no_rm","ILIKE","PL%")->max('no_rm');
    //             if (!$maxId) {
    //                 $rm = 'PL000001';
    //             } else {
    //                 $maxId = str_replace('PL', '', $maxId);
    //                 $rm = (int)$maxId + 1;
    //                 while (strlen($rm) < 6) {
    //                     $rm = '0' . $rm;
    //                 }
    //                 $rm = 'PL'.$rm;
    //             }
    //         }
    //         else {
    //             $id = Pasien::withTrashed()->where('client_id', $this->clientId)->max('no_rm');
    //             $rm = (int)$id + 1;
    //             while (strlen($rm) < 8) {
    //                 $rm = "0".$rm;
    //             }
    //         }
            
    //         return $rm;
    //     } catch (\Exception $e) {
    //         return 'PSN' . date('YmdHis');
    //     }
    // }

    public function importExcel(Request $request) 
    {
        try {
            Excel::Import(new CoaImport,$request->file);
            return response()->json(['success' => true, 'message'  => 'Berhasil import Data Pasien']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal import data pasien. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportExcel(Request $request) 
    {
        try {
            $coa = new Coa;
            $column = str_replace('_', ' ', $coa->getTableColumns());
            $fixColumn = array_splice($column, 0, -5);
            
            $keyword = '%%';
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            $list = Coa::where('client_id', $this->clientId)->where('is_aktif', true)
            ->where('kode_coa', 'LIKE', $keyword)
            ->where('nama_coa', 'LIKE', $keyword)
            ->orderBy('nama_coa', 'ASC')->get();
            $data = [
                $fixColumn,
                $list
            ];
            return (new MasterExport($data))->download('MASTER_DATA_COA.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data pasien. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
