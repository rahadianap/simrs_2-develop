<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Laboratorium\Entities\TrxLab;
use Modules\MasterData\Entities\Tindakan;

use Modules\ManajemenUser\Entities\Client;

class ReportLaboratoriumController extends Controller
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

    public function dataPemeriksaan(Request $request)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;
            $data['tgl_awal'] = $request->get('start_date_tran_lab');
            $data['tgl_akhir'] = $request->get('end_date_tran_lab');

            // $data['detail'] = TrxLab::leftJoin('tb_trx_detail', 'tb_trx_lab.trx_id', '=', 'tb_trx_detail.trx_id')
            //     ->select('tb_trx_detail.item_nama', 
            //     DB::raw('CASE WHEN tb_trx_lab.is_rujukan_int = false THEN COUNT(tb_trx_detail.item_nama) ELSE 0 END AS rajal'),
            //     DB::raw('CASE WHEN tb_trx_lab.is_rujukan_int = true THEN COUNT(tb_trx_detail.item_nama) ELSE 0 END AS mcu'))
            //     ->where('tb_trx_lab.client_id', $this->clientId)
            //     ->where('tb_trx_detail.jns_transaksi', 'LAB')
            //     ->where('tb_trx_lab.is_aktif', 1)
            //     ->whereBetween('tb_trx_lab.tgl_transaksi',[$request->get('start_date'),$request->get('end_date')])
            //     ->groupBy('tb_trx_detail.item_nama', 'tb_trx_lab.is_rujukan_int')
            //     ->get();

            $data['detail'] = TrxLab::leftJoin('tb_trx_detail', 'tb_trx_lab.reg_id', '=', 'tb_trx_detail.reg_id')
            ->select(
                'tb_trx_lab.is_rujukan_int',
                'tb_trx_detail.item_nama', 
                'tb_trx_lab.no_rm', 
                'tb_trx_lab.tgl_transaksi', 
                'tb_trx_lab.tgl_periksa', 
                'tb_trx_lab.jam_periksa', 
                'tb_trx_lab.trx_id', 
                'tb_trx_lab.nama_pasien',
                'tb_trx_lab.unit_asal',
                'tb_trx_lab.dokter_nama',
                'tb_trx_lab.penjamin_nama',
                'tb_trx_lab.unit_nama',
                'tb_trx_detail.subtotal',
                'tb_trx_lab.status',
                'tb_trx_lab.jns_registrasi',
            )
            ->where('tb_trx_lab.client_id', $this->clientId)
            ->where('tb_trx_detail.jns_transaksi', 'LAB')
            ->where('tb_trx_lab.is_aktif', 1)
            ->whereBetween('tb_trx_lab.tgl_transaksi',[$request->get('start_date_tran_lab'),$request->get('end_date_tran_lab')])
            ->orderBy('tb_trx_lab.tgl_transaksi','DESC')
            ->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            return view('report/lab/reportPemeriksaanLab',  compact('data'));
            //return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataKunjungan(Request $request)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;
            $data['tgl_awal'] = $request->get('start_date_lab');
            $data['tgl_akhir'] = $request->get('end_date_lab');

            $data['detail'] = TrxLab::where('client_id',$this->clientId)->where('is_aktif',1)
                ->whereBetween('tgl_transaksi',[$request->get('start_date_lab'),$request->get('end_date_lab')])
                ->orderBy('tgl_transaksi','DESC')->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return view('report/lab/reportKunjunganLab',  compact('data'));

            //return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataRujukan(Request $request)
    {
        try {
            if($request->input('start_date_rujukan_lab') == NULL && $request->input('end_date_rujukan_lab') == NULL){
                return response()->json(['success' => false, 'message' => 'Tanggal pencarian belum diisi']);
            }
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central']    = $central;
            $data['tgl_awal'] = $request->get('start_date_rujukan_lab');
            $data['tgl_akhir'] = $request->get('end_date_rujukan_lab');
            $report = TrxLab::select(
                        'tb_trx_lab.dokter_nama',
                        DB::raw("case when tb_trx_lab.is_rujukan_int = true and tb_unit.jenis_registrasi = 'RAWAT_JALAN' then count(tb_trx_lab.dokter_nama) else 0 end as poli"),
                        DB::raw("case when tb_trx_lab.is_rujukan_int = true and tb_unit.jenis_registrasi = 'RAWAT_INAP' then count(tb_trx_lab.dokter_nama) else 0 end as ranap"),
                        DB::raw("case when tb_trx_lab.is_rujukan_int = true and tb_unit.jenis_registrasi = 'IGD' then count(tb_trx_lab.dokter_nama) else 0 end as igd"),
                        DB::raw("case when tb_trx_lab.is_rujukan_int = false then count(tb_trx_lab.dokter_nama) else 0 end as walk_in")
                    )
                    ->leftJoin('tb_unit', 'tb_trx_lab.unit_asal_id', '=', 'tb_unit.unit_id')
                    ->where('tb_trx_lab.client_id', $this->clientId)
                    ->where('tb_trx_lab.is_aktif', 1)
                    ->whereBetween('tb_trx_lab.tgl_periksa',[$request->get('start_date_rujukan_lab'),$request->get('end_date_rujukan_lab')])
                    ->groupBy('tb_trx_lab.dokter_nama', 'tb_trx_lab.is_rujukan_int', 'tb_unit.jenis_registrasi')
                    ->get();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $data['report']       = $report;
            return view('report/lab/reportRujukanLab',  compact('data'));
            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataRujukanDetail(Request $request)
    {
        try {
            if($request->input('start_date_rujukan_detail_lab') == NULL && $request->input('end_date_rujukan_detail_lab') == NULL){
                return response()->json(['success' => false, 'message' => 'Tanggal pencarian belum diisi']);
            }

            $start_date = Carbon::parse($request->get('start_date_rujukan_detail_lab'))->format('d M Y');
            $end_date = Carbon::parse($request->get('end_date_rujukan_detail_lab'))->format('d M Y');

            $pengirim = Auth::user()->username;
            $periode = $start_date . " s/d " . $end_date;

            $report = DB::connection('dbclient')->table('tb_trx_lab')
                    ->whereBetween('tgl_transaksi',[$request->get('start_date'),$request->get('end_date')])
                    ->where('client_id',$this->clientId)
                    ->get();
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data = [
                'central' => $central,
                'nama_pengirim' => $pengirim,
                'periode' => $periode,
                'report' => $report,
            ];

            return view('report/lab/reportRujukanDetailLab',  compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataSkriningPenyakit(){
        // Pilihan Penyakit : ALL (Semua) dan <id_penyakit> (Nama Penyakit)
        // Sementara menggunakan tes penyakit yang digunakan adalah tes ANTI_HIV
        // karena digunakan sebagai contoh Laporan Rekap Hasil Pasien Anti HIV

        /** 
         * Referensi Tabel Database
         * LAB : tb_trx_lab, tb_trx_lab_hasil
         * Pasien : tb_pasien
         */ 

        /**
         * Pencarian Data menggunakan : 
         * tb_trx_lab.tanggal_periksa   = dengan pencarian utama berupa BULAN & TAHUN
         */

        /** 
         * Kolom Rekap terdiri dari : 
         * Tanggal      = tb_trx_lab.tanggal_periksa
         * No.RM        = tb_trx_lab.no_rm
         * Nama Pasien  = tb_trx_lab.nama_pasien
         * No.Lab       = tb_trx_lab.trx_id
         * Service Unit = tb_trx_lab.unit_nama
         * Nama Dokter  = tb_trx_lab.dokter_nama (Dokter Pemeriksa / Dokter Utama)
         * Test         = tb_trx_lab.hasil_naam (PERLU DICEK LAGI UNTUK HASIL)
         * 
         * Hasil berupa = ANTI HIV I/II, HIV / Viral Load
         */ 
        
        $filterBulan = "Juli 2023";
        $skirining = "ANTI HIV";

        $datas = [
            'skrining' => $skirining,
            'periode' => $filterBulan,
            'data' => [
                [
                    'tanggal_periksa' => '07/12/2023',
                    'no_rm' => '00451992',
                    'nama_pasien' => 'ERNI',
                    'trx_id' => 'LAB230712-0012',
                    'unit_nama' => 'POLI',
                    'dokter_nama' => 'Nonny Nurul Handayani, dr., SpOG',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/07/2023',
                    'no_rm' => '00455670',
                    'nama_pasien' => 'MUTHIA HANIFA',
                    'trx_id' => 'DS230707-0045',
                    'unit_nama' => 'POLI',
                    'dokter_nama' => 'Dokter Luar',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/17/2023',
                    'no_rm' => '00456715',
                    'nama_pasien' => 'ANISA MORGAN',
                    'trx_id' => 'CU230717-0016',
                    'unit_nama' => 'POLI',
                    'dokter_nama' => 'Riana Dewi, dr., SpPK',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/17/2023',
                    'no_rm' => '00451685',
                    'nama_pasien' => 'TISA MORGAN',
                    'trx_id' => 'CU230717-0017',
                    'unit_nama' => 'POLI',
                    'dokter_nama' => 'Riana Dewi, dr., SpPK',
                    'hasil_nama' => 'HIV / Viral Load',
                ],
                [
                    'tanggal_periksa' => '07/11/2023',
                    'no_rm' => '00459244',
                    'nama_pasien' => 'ELISA MAY',
                    'trx_id' => 'CU230711-0017',
                    'unit_nama' => 'POLI',
                    'dokter_nama' => 'Riana Dewi, dr., SpPK',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/21/2023',
                    'no_rm' => '354128',
                    'nama_pasien' => 'HENDRIK SUPRATMAN',
                    'trx_id' => 'JO230721-0001',
                    'unit_nama' => 'MCU',
                    'dokter_nama' => 'Lisda Herawati Anindita, dr.',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/13/2023',
                    'no_rm' => '271201',
                    'nama_pasien' => 'DWI ASRI',
                    'trx_id' => 'JO230713-0021',
                    'unit_nama' => 'MCU',
                    'dokter_nama' => 'Lisda Herawati Anindita, dr.',
                    'hasil_nama' => 'HIV / Viral Load',
                ],
                [
                    'tanggal_periksa' => '07/21/2023',
                    'no_rm' => '354128',
                    'nama_pasien' => 'HENDRIK SUPRATMAN',
                    'trx_id' => 'JO230721-0001',
                    'unit_nama' => 'MCU',
                    'dokter_nama' => 'Lisda Herawati Anindita, dr.',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/22/2023',
                    'no_rm' => '00459619',
                    'nama_pasien' => 'NAOMI LEE',
                    'trx_id' => 'JO230722-0021',
                    'unit_nama' => 'LOTUS',
                    'dokter_nama' => 'Yani Purnamasari NP, dr., SpP',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/02/2023',
                    'no_rm' => '00292193',
                    'nama_pasien' => 'JOHN LISEEN',
                    'trx_id' => 'JO230702-0011',
                    'unit_nama' => 'LOTUS',
                    'dokter_nama' => 'Yani Purnamasari NP, dr., SpP',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                [
                    'tanggal_periksa' => '07/21/2023',
                    'no_rm' => '00232839',
                    'nama_pasien' => 'TUTU GIARTO',
                    'trx_id' => 'JO230721-0001',
                    'unit_nama' => 'MCU',
                    'dokter_nama' => 'Indrarini, dr., SpKK',
                    'hasil_nama' => 'Anti HIV I/II',
                ],
                
            ]
        ];

        return view('report/laporan/lab/reportSkriningPenyakit',  compact('datas'));
    }
}