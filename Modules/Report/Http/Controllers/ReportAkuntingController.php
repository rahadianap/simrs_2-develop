<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Modules\ManajemenUser\Entities\Client;
use App\Models\UserProfile;
use App\Models\User;

class ReportAkuntingController extends Controller
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

    public function dataPendapatanRanap(Request $request)
    {
        try { 
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if($request->input('start_date_ranap'))
            {
                $tgl_awal = $request->input('start_date_ranap');
                $tgl_akhir = $request->input('end_date_ranap');
            } else {
                $tgl_awal = "2023-01-01";
                $tgl_akhir = "2023-12-31";
            }

            $report = DB::connection('dbclient')->table('tb_registrasi as reg')
                ->join('tb_transaksi as trx','trx.reg_id','=','reg.reg_id')
                ->join('tb_trx_detail as trd','trx.trx_id','=','trd.trx_id')
                ->where('trx.client_id', $this->clientId)
                ->where('trx.is_aktif', 1)
                ->where('trx.is_realisasi',1)
                ->where(function($q) {
                    $q->where('reg.jns_registrasi','ILIKE','RAWAT INAP')
                    ->orWhere('reg.jns_registrasi','ILIKE','RAWAT_INAP');
                })
                ->whereBetween('trx.tgl_transaksi',[$tgl_awal,$tgl_akhir])
                ->select('reg.penjamin_nama','trd.jns_transaksi',DB::raw('SUM(trd.subtotal) as subtotal'))
                ->groupBy('reg.penjamin_nama','trd.jns_transaksi')
                ->get();

            $data['central']    = $central;
            $data['report']     = $report;
            $data['tgl_awal']   = $tgl_awal;
            $data['tgl_akhir']  = $tgl_akhir;

            return $data;
            return view('report/akunting/reportPendapatanRanap',  compact('data'));
            
        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Terjadi pada error pada pengambilan data harian kasir rawat inap. ','error'=>$e->getMessage()]);
        }
    }

    public function dataPendapatanRajal(Request $request)
    {
        try { 
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if($request->input('start_date_rajal'))
            {
                $tgl_awal = $request->input('start_date_rajal');
                $tgl_akhir = $request->input('end_date_rajal');
            } else {
                $tgl_awal = "2023-01-01";
                $tgl_akhir = "2023-12-31";
            }
         
            $report = DB::connection('dbclient')->table('tb_registrasi as reg')
                ->join('tb_transaksi as trx','trx.reg_id','=','reg.reg_id')
                ->join('tb_trx_detail as trd','trx.trx_id','=','trd.trx_id')
                ->where('trx.client_id', $this->clientId)
                ->where('trx.is_aktif', 1)
                ->where('trx.is_realisasi',1)
                ->where(function($q) {
                    $q->where('reg.jns_registrasi','ILIKE','RAWAT JALAN')
                    ->orWhere('reg.jns_registrasi','ILIKE','RAWAT_JALAN');
                })
                ->whereBetween('trx.tgl_transaksi',[$tgl_awal,$tgl_akhir])
                ->select('reg.penjamin_nama','trd.jns_transaksi',DB::raw('SUM(trd.subtotal) as subtotal'))
                ->groupBy('reg.penjamin_nama','trd.jns_transaksi')
                ->get();

            $data['central']    = $central;
            $data['report']     = $report;
            $data['tgl_awal']   = $tgl_awal;
            $data['tgl_akhir']  = $tgl_akhir;

            return view('report/akunting/reportPendapatanRajal',  compact('data'));
            
        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Terjadi pada error pada pengambilan data pendapatan rawat jalan. ','error'=>$e->getMessage()]);
        }

    }

    public function dataPendapatanFarmasi()
    {
        try {
            /** 
             * Referensi Tabel Database
             * TRANSAKSI        : tb_transaksi
             * Transaksi untuk mengambil jenis transaksi (Rawat Jalan, Rawat Inap, IGD)
             * TRANSAKSI DETAIL : tb_trx_detail
             * Transaksi detail untuk mengambil total bayar = total harga - diskon
             */ 

            /**
             * Pencarian Data menggunakan
             * tb_transaksi.tgl_transaksi   = dd/MM/yyyy
             */

            /**
             * Kolom Laporan terdiri dari : 
             * Tanggal          = tb_transaksi.reg_id
             * 
             * filter dari tb_transaksi.jns_transaksi = "IGD"
             * IGD Total Harga  = tb_trx_detail.nilai as igd_total_harga
             * IGD Diskon       = tb_trx_detail.diskon as igd_diskon
             * 
             * filter dari tb_transaksi.jns_transaksi = "RAWAT INAP" atau "RAWAT_INAP" dengan underscore
             * RWI Total Harga  = tb_trx_detail.nilai as ri_total_harga
             * RWI Diskon       = tb_trx_detail.diskon as ri_diskon
             * 
             * filter dari tb_transaksi.jns_transaksi = "RAWAT JALAN"
             * RWJ Total Harga  = tb_trx_detail.nilai as rj_total_harga
             * RWJ Diskon       = tb_trx_detail.diskon as rj_diskon
             * 
             * TOTAL            = total masing-masing penjualan pada HARI / TANGGAL itu
             * Contoh : 15 Januari 2023 
             * Total Harga      = 27369366  didapat dari (igd_total_harga + ri_total_harga + rj_total_harga)
             * Total Diskon     = 73016     didapat dari (igd_diskon + ri_diskon + rj_diskon)
             * 
             * GRAND TOTAL      = Total keseluruhan (baris paling bawah)
             * Total Harga      = SUM(igd_total_harga + ri_total_harga + rj_total_harga)
             * Total Diskon     = SUM(igd_diskon + ri_diskon + rj_diskon)
             */ 
            
            $filterTanggal = "15 Jan 2023 s/d 25 Jan 2023";
            $transaksi = [
                [
                    'tanggal' => '15-Jan-2023',
                    'igd_total_harga' => number_format(6096546),
                    'igd_diskon' => number_format(22029),
                    'ri_total_harga' => number_format(20394929),
                    'ri_diskon' => number_format(8460),
                    'rj_total_harga' => number_format(877891),
                    'rj_diskon' => number_format(42617),
                    'total_harga' => number_format(27369366),
                    'total_diskon' => number_format(73106)
                ],
                [
                    'tanggal' => '16-Jan-2023',
                    'igd_total_harga' => number_format(5768700),
                    'igd_diskon' => number_format(11294),
                    'ri_total_harga' => number_format(23131795),
                    'ri_diskon' => number_format(106840),
                    'rj_total_harga' => number_format(44470191),
                    'rj_diskon' => number_format(368221),
                    'total_harga' => number_format(73370687),
                    'total_diskon' => number_format(486354)
                ],
                [
                    'tanggal' => '17-Jan-2023',
                    'igd_total_harga' => number_format(1935690),
                    'igd_diskon' => number_format(0),
                    'ri_total_harga' => number_format(21621062),
                    'ri_diskon' => number_format(63851),
                    'rj_total_harga' => number_format(58271581),
                    'rj_diskon' => number_format(80889),
                    'total_harga' => number_format(81828333),
                    'total_diskon' => number_format(144740)
                ],
                [
                    'tanggal' => '18-Jan-2023',
                    'igd_total_harga' => number_format(4848184),
                    'igd_diskon' => number_format(0),
                    'ri_total_harga' => number_format(26668287),
                    'ri_diskon' => number_format(77281),
                    'rj_total_harga' => number_format(47937360),
                    'rj_diskon' => number_format(302862),
                    'total_harga' => number_format(79453832),
                    'total_diskon' => number_format(380143)
                ],
                [
                    'tanggal' => '19-Jan-2023',
                    'igd_total_harga' => number_format(6411361),
                    'igd_diskon' => number_format(41307),
                    'ri_total_harga' => number_format(17725286),
                    'ri_diskon' => number_format(115685),
                    'rj_total_harga' => number_format(38228773),
                    'rj_diskon' => number_format(86724),
                    'total_harga' => number_format(62365420),
                    'total_diskon' => number_format(243716)
                ],
                [
                    'tanggal' => '20-Jan-2023',
                    'igd_total_harga' => number_format(4287334),
                    'igd_diskon' => number_format(6943),
                    'ri_total_harga' => number_format(22190421),
                    'ri_diskon' => number_format(155326),
                    'rj_total_harga' => number_format(32082238),
                    'rj_diskon' => number_format(323168),
                    'total_harga' => number_format(58559993),
                    'total_diskon' => number_format(485437)
                ],
                [
                    'tanggal' => '21-Jan-2023',
                    'igd_total_harga' => number_format(1768596),
                    'igd_diskon' => number_format(9091),
                    'ri_total_harga' => number_format(21445130),
                    'ri_diskon' => number_format(143704),
                    'rj_total_harga' => number_format(48743655),
                    'rj_diskon' => number_format(128946),
                    'total_harga' => number_format(71957381),
                    'total_diskon' => number_format(281741)
                ],
                [
                    'tanggal' => '22-Jan-2023',
                    'igd_total_harga' => number_format(4476644),
                    'igd_diskon' => number_format(0),
                    'ri_total_harga' => number_format(17227636),
                    'ri_diskon' => number_format(167452),
                    'rj_total_harga' => number_format(1309625),
                    'rj_diskon' => number_format(39375),
                    'total_harga' => number_format(23013905),
                    'total_diskon' => number_format(206827)
                ],
                [
                    'tanggal' => '23-Jan-2023',
                    'igd_total_harga' => number_format(4443913),
                    'igd_diskon' => number_format(720),
                    'ri_total_harga' => number_format(14850594),
                    'ri_diskon' => number_format(219522),
                    'rj_total_harga' => number_format(22756963),
                    'rj_diskon' => number_format(32736),
                    'total_harga' => number_format(42051470),
                    'total_diskon' => number_format(252977)
                ],
                [
                    'tanggal' => '24-Jan-2023',
                    'igd_total_harga' => number_format(4248042),
                    'igd_diskon' => number_format(0),
                    'ri_total_harga' => number_format(14469322),
                    'ri_diskon' => number_format(167339),
                    'rj_total_harga' => number_format(61891029),
                    'rj_diskon' => number_format(364771),
                    'total_harga' => number_format(80608393),
                    'total_diskon' => number_format(532110)
                ],
                [
                    'tanggal' => '25-Jan-2023',
                    'igd_total_harga' => number_format(1238683),
                    'igd_diskon' => number_format(0),
                    'ri_total_harga' => number_format(8656061),
                    'ri_diskon' => number_format(0),
                    'rj_total_harga' => number_format(11055944),
                    'rj_diskon' => number_format(17015),
                    'total_harga' => number_format(20950688),
                    'total_diskon' => number_format(17015)
                ],
            ];
            $datas = [
                'periode' => $filterTanggal,
                'grand_total' => [
                    'igd_total_harga' => number_format(45523693),
                    'igd_diskon' => number_format(91383),
                    'ri_total_harga' => number_format(208380522),
                    'ri_diskon' => number_format(1225461),
                    'rj_total_harga' => number_format(367625251),
                    'rj_diskon' => number_format(1787323),
                    'total_harga' => number_format(621529467),
                    'total_diskon' => number_format(3104166)
                ],
                'data' => json_encode($transaksi),
            ];

            return view('report/laporan/akunting/reportPendapatanFarmasi',  compact('datas'));

        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data pendaftaran', 'error' => $e->getMessage()]);
        }

    }

    public function dataHarianKasirRanap(Request $request)
    {
        try { 
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $kasir   = UserProfile::where('user_id',Auth::user()->user_id)->first();
            if($request->input('start_date_harian_ranap'))
            {
                $tgl_awal = $request->input('start_date_harian_ranap');
                $tgl_akhir = $request->input('end_date_harian_ranap');
            } else {
                $tgl_awal = "2023-01-01";
                $tgl_akhir = "2023-12-31";
            }

            $report = DB::connection('dbclient')->table('tb_transaksi as trx')
                ->join('tb_trx_detail as dtrx','trx.trx_id','=','dtrx.trx_id')
                ->where('trx.client_id', $this->clientId)
                ->where('trx.is_aktif', 1)
                ->where(function($q) {
                    $q->where('trx.jns_transaksi','ILIKE','RAWAT INAP')
                    ->orWhere('trx.jns_transaksi','ILIKE','RAWAT_INAP');
                })
                ->whereBetween('trx.tgl_transaksi',[$tgl_awal,$tgl_akhir])
                ->select('dtrx.*','trx.nama_pasien')
                ->get();

            $data['central']    = $central;
            $data['report']     = $report;
            $data['tgl_awal']   = $tgl_awal;
            $data['tgl_akhir']  = $tgl_akhir;
            if(!$kasir){
                $data['kasir'] = Auth::user()->username;
            } else {
                $data['kasir'] = $kasir->nama_lengkap;
            }

            // return $data;
            return view('report/akunting/reportHarianKasirRanap',  compact('data'));
            
        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Terjadi pada error pada pengambilan data harian kasir rawat inap. ','error'=>$e->getMessage()]);
        }
    }

    public function dataHarianKasirRajal(Request $request)
    {
        try { 
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $kasir   = UserProfile::where('user_id',Auth::user()->user_id)->first();
            if($request->input('start_date_harian_rajal'))
            {
                $tgl_awal = $request->input('start_date_harian_rajal');
                $tgl_akhir = $request->input('end_date_harian_rajal');
            } else {
                $tgl_awal = "2023-01-01";
                $tgl_akhir = "2023-12-31";
            }

            $report = DB::connection('dbclient')->table('tb_transaksi as trx')
                ->join('tb_trx_detail as dtrx','trx.trx_id','=','dtrx.trx_id')
                ->where('trx.client_id', $this->clientId)
                ->where('trx.is_aktif', 1)
                ->where(function($q) {
                    $q->where('trx.jns_transaksi','ILIKE','RAWAT JALAN')
                    ->orWhere('trx.jns_transaksi','ILIKE','RAWAT_JALAN');
                })
                ->whereBetween('trx.tgl_transaksi',[$tgl_awal,$tgl_akhir])
                ->select('dtrx.*','trx.nama_pasien')
                ->get();

            $data['central']    = $central;
            $data['report']     = $report;
            $data['tgl_awal']   = $tgl_awal;
            $data['tgl_akhir']  = $tgl_akhir;
            if(!$kasir){
                $data['kasir'] = Auth::user()->username;
            } else {
                $data['kasir'] = $kasir->nama_lengkap;
            }

            // return $data;
            return view('report/akunting/reportHarianKasirRajal',  compact('data'));
            
        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Terjadi pada error pada pengambilan data harian kasir rawat jalan. ','error'=>$e->getMessage()]);
        }
    }

    public function dataPendapatanHarianKasir()
    {
        $datas = [
            
        ];

        return view('report/akunting/reportPendapatanHarianKasir',  compact('datas'));
    }

    public function dataMarketingPerPayer()
    {
        $datas = [
            
        ];

        return view('report/akunting/reportMarketingPerPayer',  compact('datas'));
    }
}
