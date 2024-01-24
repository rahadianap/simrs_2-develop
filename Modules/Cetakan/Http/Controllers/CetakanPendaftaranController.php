<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

use Modules\ManajemenUser\Entities\Client;
use Modules\Pendaftaran\Entities\Pendaftaran;

class CetakanPendaftaranController extends Controller
{
    public $clientId;

    public function __construct(Request $request)
    {
        /*** check apakah header menyertakan client ID atau tidak */
        if (!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'Tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function dataPendaftaran(Request $request, $reg_id)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $pendaftaran = Pendaftaran::select('tb_reg_pasien.nama_pasien', 'tb_reg_pasien.no_rm', 'tb_reg_pasien.reg_id', 'tb_unit.unit_nama', 'tb_dokter.dokter_nama', 'tb_registrasi.no_antrian', 'tb_penjamin.penjamin_nama')
            ->leftJoin('tb_reg_pasien', 'tb_registrasi.reg_id', '=', 'tb_reg_pasien.reg_id')
            ->leftJoin('tb_unit', 'tb_registrasi.unit_id', '=', 'tb_unit.unit_id')
            ->leftJoin('tb_dokter', 'tb_registrasi.dokter_id', '=', 'tb_dokter.dokter_id')
            ->leftJoin('tb_penjamin', 'tb_registrasi.penjamin_id', '=', 'tb_penjamin.penjamin_id')
            ->where('tb_registrasi.client_id',$this->clientId)
            ->where('tb_registrasi.is_aktif',1)
            ->where('tb_registrasi.reg_id', $reg_id)
            ->first();

            $data['central'] = $central;
            $data['pendaftaran'] = $pendaftaran;

            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $result]);
            return view('report/pendaftaran/cetakanPendaftaran',  compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataTracer(Request $request, $reg_id)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $pendaftaran = Pendaftaran::select('tb_reg_pasien.nama_pasien', 'tb_reg_pasien.no_rm', 'tb_reg_pasien.reg_id', 'tb_unit.unit_nama', 'tb_dokter.dokter_nama', 'tb_registrasi.no_antrian', 'tb_penjamin.penjamin_nama', 'tb_registrasi.tgl_registrasi')
            ->leftJoin('tb_reg_pasien', 'tb_registrasi.reg_id', '=', 'tb_reg_pasien.reg_id')
            ->leftJoin('tb_unit', 'tb_registrasi.unit_id', '=', 'tb_unit.unit_id')
            ->leftJoin('tb_dokter', 'tb_registrasi.dokter_id', '=', 'tb_dokter.dokter_id')
            ->leftJoin('tb_penjamin', 'tb_registrasi.penjamin_id', '=', 'tb_penjamin.penjamin_id')
            ->where('tb_registrasi.client_id',$this->clientId)
            ->where('tb_registrasi.is_aktif',1)
            ->where('tb_registrasi.reg_id', $reg_id)
            ->first();

            $data['central'] = $central;
            $data['pendaftaran'] = $pendaftaran;

            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
            return view('report/pendaftaran/cetakanTracer',  compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataGelangDewasa(Request $request){
        try {

            if($request->input('no_rm')){
                $keyword = $request->no_rm;
            } else {
                $keyword = "000000";
            }
            
            if($keyword == "000000"){
                $datas = [
                    'no_reg_id' => 'REG/RJ/2023/12/00001',
                    'no_rm' => '00004532',
                    'nama_pasien' => 'CONTOH GELANG DEWASA',
                    'tgl_lahir' => '01/01/1900',
                    'umur' => '99 tahun',
                ];
            } else {
                $query = DB::connection('dbclient')->table('tb_registrasi')
                        ->where('no_rm',$keyword)
                        ->where('client_id',$this->clientId)
                        ->first();
                
                if(!$query){
                    return response()->json(['success' => false, 'message' => 'No RM '. $keyword .' tidak ditemukan']);
                }
                $age = Carbon::parse($query->tgl_lahir)->age;

                $datas = [
                    'no_reg_id' => $query->reg_id,
                    'no_rm' => $query->no_rm,
                    'nama_pasien' => $query->nama_pasien,
                    'tgl_lahir' => $query->tgl_lahir,
                    'umur' => $age . " tahun",
                ];

            }
            
            return view('cetakan/cetakanGelangDewasa',  compact('datas'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data registrasi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataFormInformasiPasien(Request $request){
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%'; 
            } else {
                $keyword = '%%';
            }

            $query = DB::connection('dbclient')->table('tb_registrasi as reg')
                    ->leftJoin('tb_bed as bed','bed.ruang_id','=','reg.ruang_id')
                    ->leftJoin('tb_ruang as ru','ru.ruang_id','=','reg.ruang_id')
                    ->leftJoin('tb_kelas as kel','kel.kelas_id','=','ru.kelas_kamar')
                    ->where(function($q) use ($keyword) {
                        $q->where('reg.no_rm','ILIKE',$keyword)
                        ->orWhere('reg.reg_id','ILIKE',$keyword);
                    })
                    ->where('reg.client_id',$this->clientId)
                    ->select('reg.*','bed.no_bed','kel.kelas_nama')
                    ->first();

            $usia = Carbon::parse($query->tgl_lahir)->age;

            $tgl_masuk = Carbon::parse($query->tgl_checkin);
            $tgl_keluar = Carbon::parse($query->tgl_pulang);
    
            $lama_rawat = $tgl_keluar->diffInDays($tgl_masuk);
            $data = [
                'central' => $central,
                'no_rm' => $query->no_rm,
                'nama_pasien' => $query->nama_pasien,
                'tgl_lahir' => $query->tgl_lahir,
                'usia' => $usia,
                'no_tempat_tidur' => $query->no_bed,
                'alamat' => $query->tempat_lahir,
                'tgl_masuk' => $query->tgl_checkin,
                'tgl_keluar' => $query->tgl_pulang,
                'status_rawat' => $query->status_reg,
                'dokter_utama' => $query->dokter_nama,
                'alasan_pulang' => $query->status_pulang,
                'jns_penjamin' => $query->jns_penjamin,
                'penjamin_nama' => $query->penjamin_nama,
                'ruang_perawatan' => $query->ruang_nama,
                'kelas' => $query->kelas_nama,
                'hak_kelas' => $query->kelas_nama,
            ];
            return view('cetakan/cetakanFormInformasiPasien',  compact('data'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data registrasi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataRegistrasiLabelPasien(Request $request){
        try {
            if($request->input('no_rm')){
                $keyword = $request->no_rm;
            } else {
                $keyword = "000000";
            }
            
            if($keyword == "000000"){
                $data = [
                    'nama_pasien' => 'ANNISA RAHMAH',
                    'no_rm' => '0001001',
                    'tgl_lahir' => '29-Dec-2022',
                    'usia' => '20th 2bl 2hr',
                ];
            } else {
                $query = DB::connection('dbclient')->table('tb_registrasi as reg')
                        ->where('reg.no_rm',$keyword)
                        ->where('reg.client_id',$this->clientId)
                        ->first();

                $data = [
                    'nama_pasien' => $query->nama_pasien,
                    'no_rm' => $query->no_rm,
                    'jns_kelamin' => $query->jns_kelamin,
                    'tgl_lahir' => $query->tgl_lahir,
                    'usia' => $this->getAge("LENGKAP", $query->tgl_lahir),
                ];
            }

            return view('cetakan/cetakanRegistrasiLabel',  compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getAge($jenis, $dateOfBirth){
        $birthDate = new DateTime($dateOfBirth);
        $currentDate = new DateTime();
        $ageInterval = $currentDate->diff($birthDate);
        $age = $ageInterval->y; // years
        $months = $ageInterval->m; // months
        $days = $ageInterval->d;   // days
        if($jenis == "LENGKAP"){
            return $age . "th " . $months . "bl " . $days . "hr" ;
        } else {
            return $age;
        }
    }

}