<?php

namespace Modules\Pendaftaran\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

use Modules\Pendaftaran\Entities\Pendaftaran;
use Modules\Pendaftaran\Entities\RegPasien;
use Modules\Pendaftaran\Entities\SEP;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\DokterJadwal;
use Modules\MasterData\Entities\Penjamin;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Pendaftaran\Entities\RawatJalan;

use RegistrasiHelper;

class BookingController extends Controller
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
            $per_page = 20;
            $keyword = '%%';
            $status = '%%';

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') {
                    $per_page = Pendaftaran::where('client_id',$this->clientId)->count();
                }
            }

            $query = Pendaftaran::query();
            $query = $query->where('client_id',$this->clientId);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_periksa_start') && $request->has('tgl_periksa_end')) {
                $dtStart = $request->input('tgl_periksa_start').' 00:00:00';
                $dtEnd = $request->input('tgl_periksa_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_periksa', [$dtStart, $dtEnd]);
            }
            else {
                $today = date('Y-m-d');
                $nextMonth = date('Y-m-d', strtotime('+1 month'));
                $query = $query->where('tgl_periksa','>=', $today.' 00:00:00');
            }
            
            if ($request->has('unit')) {
                $unit = $request->input('unit');
                $query = $query->where(function($q) use ($unit) {
                        $q->where('unit_id','ILIKE',$unit)
                        ->orWhere('unit_nama','ILIKE',$unit);
                    });
            }

            if ($request->has('dokter')) {
                $dokter = $request->input('dokter');
                $query = $query->where(function($q) use ($dokter) {
                        $q->where('dokter_id','ILIKE',$dokter)
                        ->orWhere('dokter_nama','ILIKE',$dokter);
                    });
            }

            if ($request->has('status')) {
                $query = $query->where('status', 'ILIKE', '%' .$request->input('status'). '%');
            }

            if ($request->has('jns_penjamin')) {
                $query = $query->where('jns_penjamin', 'ILIKE', '%' .$request->input('jns_penjamin'). '%');
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword)
                    ->orWhere('kode_booking','ILIKE',$keyword);
                })->where('jns_registrasi', 'RAWAT_JALAN')
                ->where('status_reg', 'DAFTAR')
                ->orderBy('tgl_periksa','ASC')
                ->orderBy('no_antrian','ASC')
                ->paginate($per_page);


            foreach($list->items() as $itm) {
                $itm['pasien'] = Pasien::where('is_aktif',1)->where('client_id',$this->clientId)->where('pasien_id',$itm->pasien_id)->first();
                //$pasien['detail'] = PasienDetail::where('is_aktif',1)->where('client_id',$this->clientId)->where('pasien_id',$itm->pasien_id)->first();
                //$itm['pasien'] = $pasien;
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data pendaftaran', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = Pendaftaran::select('tb_registrasi.*', 'tp.*', 'pd.*')
                ->leftjoin('tb_pasien AS tp', 'tb_registrasi.pasien_id', '=', 'tp.pasien_id')
                ->leftjoin('tb_pasien_detail AS pd', 'tb_registrasi.pasien_id', '=', 'pd.pasien_id')
                ->leftJoin('tb_unit AS tu', 'tb_registrasi.unit_id', '=', 'tu.unit_id')
                ->where('tb_registrasi.client_id', $this->clientId)
                ->where('tb_registrasi.reg_id', $id)
                ->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            }
            
            $pasien = Pasien::where('client_id',$this->clientId)->where('pasien_id',$data->pasien_id)->first();
            $pasien['alamat'] = PasienAlamat::where('client_id', $this->clientId)->where('pasien_id',$data->pasien_id)->first();
            $data['pasien'] = $pasien;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran ds', 'error' => $e->getMessage()]);
        }
    }

    // Lists Registrasi pasien Penunjang berdasarkan jenis
    public function listsRegJns(request $request, $jns)
    {
        try {            
            $data = Pendaftaran::leftjoin('tb_pasien AS tp', 'tb_registrasi.pasien_id', '=', 'tp.pasien_id')
                ->leftJoin('tb_unit AS tu', 'tb_registrasi.unit_id', '=', 'tu.unit_id')
                ->where('tb_registrasi.client_id', $this->clientId)
                ->where('tb_registrasi.jns_registrasi', $jns)
                ->orderBy('tb_registrasi.tgl_registrasi', 'DESC')->paginate(10);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran rad', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request) 
    {
        try {
            // dd($request);
            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)->first();

            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //check apakah ruang yang dipilih masih aktif dan ada
            $ruang = RuangPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)->where('ruang_id', $request->ruang_id)
                ->first();

            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            }

            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){ return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']); }

            //check penjamin apakah aktif / tidak
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            if(!$penjamin){ return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']); }

            //check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {  return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);  }           
            $alamat = PasienAlamat::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            
            /**ambil prefix untuk no antrian */
            $prefixNo = DokterUnit::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)->where('unit_id',$request->unit_id)
                ->select('prefix_no_antrian')
                ->first();

            if(!$prefixNo) { return response()->json(['success' => false, 'message' => 'Jadwal gagal dibuat (0). Prefix no antrian tidak ditemukan.' ]);  }           

            $jadwal = DokterJadwal::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('jadwal_id',$request->jadwal_id)
                ->first();

            if(!$jadwal) { return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.' ]); }

            // input tb_registrasi
            $regId = RegistrasiHelper::instance()->RegistrasiId($this->clientId); 
            $modeReg = RegistrasiHelper::instance()->ModeReg($request->tgl_periksa);
            $kodeBooking = RegistrasiHelper::instance()->BookingCode($this->clientId);
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir);
            $noAntrian = RegistrasiHelper::instance()->NoAntrian($this->clientId, $prefixNo->prefix_no_antrian, $request->tgl_periksa, $request->jadwal_id);
                        
            /** estimasi pelayanan **/
            $time = strtotime($jadwal->mulai);
            $minute = $jadwal->interval_periksa;
            $inter = date('H:i', $time + ($minute * $noAntrian['angka']) - $minute); 
            $startTime = date('H:i', $time + (($minute * 60 * $noAntrian['angka'])) - ($minute * 60));
            
            DB::connection('dbclient')->beginTransaction();
            
            $dataReg                    = new Pendaftaran();
            $dataReg->reg_id            = $regId;
            $dataReg->tgl_registrasi    = date('Y-m-d H:i:s');
            
            $dataReg->cara_registrasi   = $request->cara_registrasi;
            $dataReg->tgl_periksa       = $request->tgl_periksa;
            $dataReg->jam_periksa       = $startTime;
            $dataReg->mode_reg          = $modeReg;
            $dataReg->kode_booking      = $kodeBooking;

            $dataReg->no_antrian        = $noAntrian['id'];
            $dataReg->no_urut           = $noAntrian['angka'];
            
            $dataReg->jns_registrasi    = $unit->jenis_registrasi;
            $dataReg->jadwal_id         = $jadwal->jadwal_id;
            $dataReg->dokter_id         = $dokter->dokter_id;
            $dataReg->unit_id           = $unit->unit_id;
            $dataReg->ruang_id          = $request->ruang_id;
            $dataReg->bed_id            = $ruang->ruang_nama;
            $dataReg->dokter_nama       = $dokter->dokter_nama;
            $dataReg->unit_nama         = $unit->unit_nama;
            
            $dataReg->tgl_checkin       = null;
            $dataReg->no_pendaftaran    = null;
            $dataReg->status_reg        = 'DAFTAR';
        
            $dataReg->asal_pasien       = $request->asal_pasien;
            $dataReg->ket_asal_pasien   = $request->ket_asal_pasien;
            
            $dataReg->pasien_id         = $pasien->pasien_id;
            $dataReg->nama_pasien       = $pasien->nama_pasien;
            $dataReg->jns_kelamin       = $pasien->jns_kelamin;
            $dataReg->nik               = $pasien->nik;
            $dataReg->no_kk             = $pasien->no_kk;
            $dataReg->no_rm             = $pasien->no_rm;
            $dataReg->tgl_lahir         = $pasien->tgl_lahir;
            $dataReg->tempat_lahir      = $pasien->tempat_lahir;
            
            $dataReg->jns_penjamin      = $penjamin->jns_penjamin;
            $dataReg->penjamin_id       = $penjamin->penjamin_id;
            $dataReg->penjamin_nama     = $penjamin->penjamin_nama;
            $dataReg->is_bpjs           = $penjamin->is_bpjs;
                
            $dataReg->nama_penanggung   = strtoupper($request->nama_penanggung);
            $dataReg->hub_penanggung    = $request->hub_penanggung;

            $dataReg->is_pasien_luar    = $request->is_pasien_luar;
            $dataReg->is_pasien_baru    = $request->is_pasien_baru;
            $dataReg->is_aktif          = true;

            $dataReg->kelas_nama        = $request->kelas_nama;
            $dataReg->kelas_id          = $request->kelas_id;

            $dataReg->client_id         = $this->clientId;
            $dataReg->created_by        = Auth::user()->username;
            $dataReg->updated_by        = Auth::user()->username;

            $success = $dataReg->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menyimpan data antrian(2).' ]);
            }
            
            /**insert di registrasi pasien */
            $regPasien                  = new RegPasien();
            $regPasien->reg_id          = $regId;
            $regPasien->is_pasien_luar  = false;
            $regPasien->pasien_id       = $pasien->pasien_id;
            $regPasien->no_rm           = $pasien->no_rm;
            $regPasien->nama_pasien     = $pasien->nama_pasien;
            $regPasien->tempat_lahir    = $pasien->tempat_lahir;
            $regPasien->tgl_lahir       = $pasien->tgl_lahir;
            $regPasien->usia            = $usia;
            $regPasien->jns_kelamin     = $pasien->jns_kelamin;
            $regPasien->is_hamil        = $pasien->is_hamil;
            $regPasien->is_pasien_baru  = $request->is_pasien_baru;
            if($alamat){
                $regPasien->propinsi_id     = $alamat->propinsi_tinggal_id;
                $regPasien->kota_id         = $alamat->kota_tinggal_id;
                $regPasien->kecamatan_id    = $alamat->kecamatan_tinggal_id;
                $regPasien->kelurahan_id    = $alamat->kelurahan_tinggal_id;
                $regPasien->kodepos         = $alamat->kodepos_tinggal;;
            }
            $regPasien->is_aktif        = true;
            $regPasien->client_id       = $this->clientId;
            $regPasien->created_by      = Auth::user()->username;
            $regPasien->updated_by      = Auth::user()->username;

            $success = $regPasien->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
            } 

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data booking rawat jalan berhasil disimpan.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data registrasi. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->reg_id;
            $pasienId = $request->pasien_id;
            $penjaminId = $request->penjamin_id;
            
            $data = Pendaftaran::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$id)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi tidak ditemukan.']);
            }
            //return response()->json(['success' => false, 'message' => 'Disini bro.']);
            
            if($data->status_reg !== 'DAFTAR') {
                return response()->json(['success' => false, 'message' => 'Data registrasi sudah berubah status. Data tidak dapat diubah.']);
            }

            if($data->pasien_id !== null && $data->pasien_id !== '') {
                $pasienId = $data->pasien_id;
                $penjaminId = $data->penjamin_id;
            }

            /**periksa apakah data pasien ada */
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('pasien_id',$pasienId)->first();

            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            /**periksa apakah data penjamin ada */
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('penjamin_id',$penjaminId)->first();

            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }
            /**periksa apakah data unit pelayanan ada */
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            /** ambil data ruang */
            $ruang = RuangPelayanan::where('ruang_id',$request->ruang_id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)->first();

            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data Ruang tidak ditemukan.']);
            }     

            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)->first();

            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }
            
            //check apakah jadwal ada / tidak
            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)
                ->where('jadwal_id',$request->jadwal_id)
                ->first();

            if(!$jadwal){
                return response()->json(['success' => false, 'message' => 'Data jadwal dokter tidak ditemukan.']);
            }
            
            //check kelengkapan data pasien di registrasi
            $regPasien = RegPasien::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('pasien_id',$pasienId)->where('reg_id',$data->reg_id)->first();

                /** hitung usia **/
            $tgl_lahir = $pasien->tgl_lahir;
            $tgl_lahir = explode("-", $tgl_lahir);
            $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
                ? ((date("Y") - $tgl_lahir[0]) - 1)
                : (date("Y") - $tgl_lahir[0]));
            
            DB::connection('dbclient')->beginTransaction();

            $data->jns_registrasi    = $request->jns_registrasi;
            $data->cara_registrasi   = $request->cara_registrasi;            
            $data->ruang_id          = $request->ruang_id;
            $data->ruang_nama        = $ruang->ruang_nama;
            $data->asal_pasien       = $request->asal_pasien;
            $data->ket_asal_pasien   = $request->ket_asal_pasien;
            
            $data->pasien_id         = $pasien->pasien_id;
            $data->nama_pasien       = $pasien->nama_pasien;
            $data->jns_kelamin       = $pasien->jns_kelamin;
            $data->nik               = $pasien->nik;
            $data->no_kk             = $pasien->no_kk;
            $data->no_rm             = $pasien->no_rm;
            $data->tgl_lahir         = $pasien->tgl_lahir;
            $data->tempat_lahir      = $pasien->tempat_lahir;
            
            $data->jns_penjamin      = $penjamin->jns_penjamin;
            $data->penjamin_id       = $penjamin->penjamin_id;
            $data->penjamin_nama     = $penjamin->penjamin_nama;
            $data->is_bpjs           = $penjamin->is_bpjs;
            
            $data->nama_penanggung   = strtoupper($request->nama_penanggung);
            $data->hub_penanggung    = $request->hub_penanggung;

            $data->kelas_nama        = $request->kelas_nama;
            $data->kelas_id          = $request->kelas_id;

            $data->no_sep               = $request->no_sep;
            $data->no_rujukan           = $request->no_rujukan;            
            $data->updated_by           = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'Data registrasi gagal diupdate.']);
            }

            $alamat = PasienAlamat::where('client_id',$this->clientId)->where('pasien_id',$request->pasien_id)->first();
            /**reg pasien */
            if(!$regPasien) {                
                $regPasien                  = new RegPasien();
                $regPasien->reg_id          = $id;
                $regPasien->is_aktif        = true;
                $regPasien->client_id       = $this->clientId;
                $regPasien->created_by      = Auth::user()->username;
            }
            $regPasien->is_pasien_luar  = $pasien->is_pasien_luar;
            $regPasien->pasien_id       = $pasien->pasien_id;
            $regPasien->no_rm           = $pasien->no_rm;
            $regPasien->nama_pasien     = $pasien->nama_pasien;
            $regPasien->tempat_lahir    = $pasien->tempat_lahir;
            $regPasien->tgl_lahir       = $pasien->tgl_lahir;
            $regPasien->usia            = $usia;
            $regPasien->jns_kelamin     = $pasien->jns_kelamin;
            $regPasien->propinsi_id     = $alamat->propinsi_tinggal_id;
            $regPasien->kota_id         = $alamat->kota_tinggal_id;
            $regPasien->kecamatan_id    = $alamat->kecamatan_tinggal_id;
            $regPasien->kelurahan_id    = $alamat->kelurahan_tinggal_id;
            $regPasien->kodepos         = $alamat->kodepos_tinggal;;
            $regPasien->is_hamil        = $pasien->is_hamil;
            $regPasien->is_pasien_baru  = $request->is_pasien_baru;
            $regPasien->updated_by      = Auth::user()->username;
            $success = $regPasien->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
            } 
        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil diubah.','data' => $data]);
            
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data registrasi. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $dataReg = Pendaftaran::where('reg_id', $id)->where('client_id', $this->clientId)->first();
            if (!$dataReg) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data booking tidak ditemukan.']);
            }

            if ($dataReg->status_reg !== 'DAFTAR') {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data booking sudah berubah status.']);
            }

            $dataReg->updated_by = Auth::user()->username;
            $dataReg->is_aktif = 0;
            $success = $dataReg->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menonaktifkan data registrasi']);
            }
            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil dinonaktifkan', 'data' => $dataReg]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data registrasi gagal dinonaktifkan. Error :' . $e->getMessage()]);
        }
    }

    // public function getDetailId()
    // {
    //     try {
    //         $id = $this->clientId.'-'.'DTL'.'-000001';
    //         $maxId = TransaksiDetail::withTrashed()
    //             ->where('client_id', $this->clientId)
    //             ->where('detail_id', 'LIKE', $this->clientId.'-'.'DTL'.'-%')->max('detail_id');
    //         if (!$maxId) {
    //             $id = $this->clientId.'-'.'DTL'.'-000001';
    //         } else {
    //             $maxId = str_replace($this->clientId.'-'.'DTL'.'-', '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = $this->clientId.'-'.'DTL'.'-00000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'DTL'.'-0000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'DTL'.'-000' . $count; } 
    //             elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'DTL'.'-00' . $count; } 
    //             elseif ($count >= 10000) { $id = $this->clientId.'-'.'DTL'.'-0' . $count; } 
    //             else { $id = $this->clientId.'-'.'DTL'.'-' . $count; }
    //         }
    //         return $id;
    //     } catch (\Exception $e) {
    //         return $this->clientId . date('Ymd') . '-' . Uuid::uuid4()->getHex();
    //     }
    // }

    public function confirm(Request $request)
    {   
        try { 
            $id = $request->reg_id;
            $data = Pendaftaran::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$id)->first();

            if(!$data) { 
                return response()->json(['success' => false, 'message' => 'Data registrasi tidak ditemukan.']);  
            }

            /**check status data registrasi */
            if(!$data->status_reg == 'DAFTAR') {
                return response()->json(['success' => false, 'message' => 'Data registrasi sudah berubah status.']);
            }

            /** check apakah tanggal periksa sama dengan hari ini */
            if($data->tgl_periksa !== date('Y-m-d')) {
                return response()->json(['success' => false, 'message' => 'Tanggal periksa bukan hari ini.']);
            }

            /**periksa apakah data pasien ada */
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('pasien_id',$data->pasien_id)->first();

            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            /**periksa apakah data penjamin ada */
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('penjamin_id',$data->penjamin_id)->first();

            if(!$penjamin) {
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }
            /**periksa apakah data unit pelayanan ada */
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('unit_id', $data->unit_id)->first();

            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            /** ambil data ruang */
            $ruang = RuangPelayanan::where('ruang_id',$data->ruang_id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)->first();

            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data Ruang tidak ditemukan.']);
            }     

            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$data->dokter_id)->first();

            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }
            
            //check apakah jadwal ada / tidak
            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$data->dokter_id)
                ->where('jadwal_id',$data->jadwal_id)
                ->first();

            if(!$jadwal){
                return response()->json(['success' => false, 'message' => 'Data jadwal dokter tidak ditemukan.']);
            }
            
            //check kelengkapan data pasien di registrasi
            $regPasien = RegPasien::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('pasien_id',$data->pasien_id)->where('reg_id',$data->reg_id)->first();

            //check apakah data rawat jalan sudah ada
            $rawatJalan = RawatJalan::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->first();

            $trxId = RegistrasiHelper::instance()->PoliTransactionId($this->clientId); 
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir);             
            $alamat = PasienAlamat::where('client_id',$this->clientId)->where('pasien_id',$request->pasien_id)->first();                
                
            DB::connection('dbclient')->beginTransaction();

            $data->status_reg = 'ANTRI';
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'Data registrasi gagal dikonfirmasi.']);
            }

            /**reg pasien */
            if(!$regPasien) {                
                $regPasien                  = new RegPasien();
                $regPasien->reg_id          = $id;
                $regPasien->is_pasien_luar  = $pasien->is_pasien_luar;
                $regPasien->pasien_id       = $pasien->pasien_id;
                $regPasien->no_rm           = $pasien->no_rm;
                $regPasien->nama_pasien     = $pasien->nama_pasien;
                $regPasien->tempat_lahir    = $pasien->tempat_lahir;
                $regPasien->tgl_lahir       = $pasien->tgl_lahir;
                $regPasien->usia            = $usia;
                $regPasien->jns_kelamin     = $pasien->jns_kelamin;
                $regPasien->propinsi_id     = $alamat->propinsi_tinggal_id;
                $regPasien->kota_id         = $alamat->kota_tinggal_id;
                $regPasien->kecamatan_id    = $alamat->kecamatan_tinggal_id;
                $regPasien->kelurahan_id    = $alamat->kelurahan_tinggal_id;
                $regPasien->kodepos         = $alamat->kodepos_tinggal;;
                $regPasien->is_hamil        = $pasien->is_hamil;
                $regPasien->is_pasien_baru  = $data->is_pasien_baru;
                $regPasien->is_aktif        = true;
                $regPasien->client_id       = $this->clientId;
                $regPasien->created_by      = Auth::user()->username;
                $regPasien->updated_by      = Auth::user()->username;
                $success = $regPasien->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data pasien gagal disimpan.']);
                } 
            }
            
            /**
             * create data transaksi
             */
            $transaksi                      = new Transaksi();
            $transaksi->reg_id              = $data->reg_id;
            $transaksi->trx_id              = $trxId;
            $transaksi->reff_trx_id         = $trxId;
            $transaksi->status              = 'ANTRI';
            $transaksi->is_realisasi        = false;
            $transaksi->is_bayar            = false;
            $transaksi->is_aktif            = true;
            $transaksi->client_id           = $this->clientId;
            $transaksi->created_by          = Auth::user()->username;
            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->jns_transaksi       = 'RAWAT JALAN';
        
            //$transaksi->is_sub_trx          = false;
            $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
            $transaksi->no_antrian          = $data->no_antrian;
            $transaksi->no_transaksi        = 'RJ/'.date('Ymd').'/'.$data->no_urut;
            $transaksi->kelas_id            = $request->kelas_harga_id;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;
            $transaksi->kelas_penjamin_id   = $request->kelas_harga_id;
            $transaksi->penjamin_id         = $request->penjamin_id;
            $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksi->buku_harga          = $penjamin->buku_harga;
            $transaksi->total               = 0;
            
            $transaksi->unit_id             = $request->unit_id;
            $transaksi->unit_nama           = $unit->unit_nama;
            $transaksi->ruang_id            = $request->ruang_id;
            $transaksi->ruang_nama          = $ruang->ruang_nama;
            
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = '';
            
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            
            $transaksi->pasien_id           = $request->pasien_id;
            $transaksi->no_rm               = $pasien->no_rm;
            $transaksi->nama_pasien         = $pasien->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            /**
             * insert data rawat jalan
             */
            $rawatJalan                      = new RawatJalan();
            $rawatJalan->reg_id              = $data->reg_id;
            $rawatJalan->trx_id              = $trxId;
            $rawatJalan->tgl_transaksi       = date('Y-m-d H:i:s');
            $rawatJalan->is_rujukan_int      = false;
            
            $rawatJalan->reff_trx_id         = $trxId;
            $rawatJalan->jns_registrasi      = $data->jns_registrasi;
            $rawatJalan->cara_registrasi     = $data->cara_registrasi;

            $rawatJalan->asal_pasien         = $data->asal_pasien;
            $rawatJalan->ket_asal_pasien     = $data->ket_asal_pasien;
            
            $rawatJalan->unit_id             = $data->unit_id;
            $rawatJalan->unit_nama           = $unit->unit_nama;
            $rawatJalan->ruang_id            = $data->ruang_id;
            $rawatJalan->ruang_nama          = $ruang->ruang_nama;

            $rawatJalan->jadwal_id           = $data->jadwal_id;
            $rawatJalan->dokter_id           = $dokter->dokter_id;
            $rawatJalan->dokter_nama         = $dokter->dokter_nama;            
            $rawatJalan->dokter_pengirim_id  = $data->dokter_pengirim_id;
            $rawatJalan->dokter_pengirim     = $data->dokter_pengirim;
            
            $rawatJalan->tgl_periksa         = $data->tgl_periksa;
            $rawatJalan->jam_periksa         = $data->jam_periksa;
            $rawatJalan->no_antrian          = $data->no_antrian;

            $rawatJalan->hub_penanggung      = $data->hub_penanggung;
            $rawatJalan->nama_penanggung     = $data->nama_penanggung;
            
            $rawatJalan->penjamin_id         = $penjamin->penjamin_id;
            $rawatJalan->penjamin_nama       = $penjamin->penjamin_nama;
            $rawatJalan->jns_penjamin        = $penjamin->jns_penjamin;
            $rawatJalan->no_kepesertaan      = $data->no_kepesertaan;
            $rawatJalan->no_rujukan          = $data->no_rujukan;
            $rawatJalan->no_sep              = $data->no_sep;
            
            if($penjamin->is_bpjs == true) {
                $rawatJalan->is_bpjs             = true;
            }
            else {
                $rawatJalan->is_bpjs             = false;
            }
            
            $rawatJalan->kelas_harga_id      = $request->kelas_harga_id;
            $rawatJalan->kelas_harga         = $request->kelas_harga;
            $rawatJalan->buku_harga_id       = $penjamin->buku_harga_id;
            $rawatJalan->buku_harga          = $penjamin->buku_harga;
            $rawatJalan->kelas_penjamin      = $request->kelas_harga;
            $rawatJalan->kelas_penjamin_id   = $request->kelas_harga_id;
            
            $rawatJalan->pasien_id           = $pasien->pasien_id;
            $rawatJalan->no_rm               = $pasien->no_rm;
            $rawatJalan->nama_pasien         = $pasien->nama_lengkap;
            $rawatJalan->usia                = $usia;
            $rawatJalan->jns_kelamin         = $pasien->jns_kelamin;
            $rawatJalan->diag_awal           = $request->diag_awal;            
            $rawatJalan->status              = 'ANTRI';
            $rawatJalan->is_aktif            = true;
            $rawatJalan->is_konfirmasi       = false;            
            $rawatJalan->client_id           = $this->clientId;
            $rawatJalan->created_by          = Auth::user()->username;
            $rawatJalan->updated_by          = Auth::user()->username;
            
            $success = $rawatJalan->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data rawat jalan gagal disimpan.']);
            } 
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dikonfirmasi.','data' => $data]);            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat konfirmasi registrasi pasien. Error : ' . $e->getMessage()]);
        }
    }

    public function DokterJadwalByDay(Request $request) {
        try {
            /**mengambil data poli dan jadwal dokter aktif pada tanggal periksa */
            $jenisReg = $request->input('jenis_reg');
            $today = Carbon::now();
            
            if ($request->has('tanggal')) {
                $tglPeriksa = $request->input('tanggal');
                if(strtoupper($tglPeriksa) !== 'UNDEFINED') {
                    $today = $tglPeriksa;
                }
            }
            
            $timestamp = strtotime($today);
            $day = date('w',$timestamp);
            /**ambil jadwal dokter di hari tersebut */
            $jadwal = DokterJadwal::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('hari',$day)->select('unit_id')->groupBy('unit_id')->get();           

            if(!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Tidak ada jadwal dokter hari ini']);
            }

            $list = UnitPelayanan::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('jenis_registrasi',$jenisReg)
                ->whereIn('unit_id',$jadwal)
                ->select('unit_id','unit_nama')
                ->get();
            
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Jadwal Unit Pelayanan (poli) tidak ditemukan']);
            }       

            foreach($list as $item) {
                /**tambahkan data ruang*/
                $item['ruang'] = RuangPelayanan::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('unit_id',$item['unit_id'])->get();
                
                $dokter = DokterJadwal::leftJoin('tb_dokter','tb_dokter_jadwal.dokter_id','=','tb_dokter.dokter_id')
                    ->where('tb_dokter_jadwal.unit_id',$item['unit_id'])
                    ->where('tb_dokter_jadwal.is_aktif',1)
                    ->where('tb_dokter.is_aktif',1)
                    ->where('tb_dokter_jadwal.hari',$day)
                    ->where('tb_dokter_jadwal.client_id',$this->clientId)
                    ->select(
                        'tb_dokter_jadwal.jadwal_id',
                        'tb_dokter_jadwal.unit_id',
                        'tb_dokter_jadwal.hari',
                        'tb_dokter_jadwal.mulai',
                        'tb_dokter_jadwal.selesai',
                        'tb_dokter_jadwal.nama_hari',
                        'tb_dokter.*'
                    )->get();

                if($dokter) { $item['dokter'] = $dokter; }
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        }
        
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }
    
}
