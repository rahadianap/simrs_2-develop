<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Modules\ManajemenUser\Entities\Client;

class ReportFarmasiController extends Controller
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
    
    public function dataPemakaianObat(){
        /** 
        * Referensi Tabel Database
        * Transaksi    : tb_transaksi, tb_trx_hasil
        * Pasien       : tb_pasien
        */ 
        
        /**
        * Pencarian Data menggunakan : 
        * tb_transaksi.tgl_transaksi   = dengan pencarian utama berupa BULAN & TAHUN
        * nama_obat    = jika nama obat tidak diisi maka seluruh obat muncul
        *                dengan tampilan per obat siapa-siapa saja yang menggunakan
        */
        
        /** 
        * Kolom Rekap terdiri dari : 
        * Tanggal       = tb_transaksi.tgl_transaksi
        * Qty           = tb_trx_hasil.jumlah
        * Unit          = tb_trx_hasil.satuan
        * No.RM         = tb_transaksi.no_rm
        * Nama Pasien   = tb_transaksi.nama_pasien
        * Usia          = \Carbon\Carbon::parse($tb_pasien.tgl_lahir)->diff(\Carbon\Carbon::now())->format('%y th, %m bln and %d hr"') th 2 bln 3 hr";
        * Unit          = tb_transaksi.unit_nama
        * Nama Dokter   = tb_transaksi.dokter_nama (Dokter Pemeriksa / Dokter Utama)
        */ 
                
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

            return view('report/laporan/farmasi/reportPemakaianObat',  compact('data'));
            
        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Terjadi pada error pada pengambilan data pendapatan rawat jalan. ','error'=>$e->getMessage()]);
        }
    }
            
    public function dataJumlahResep(Request $request){
        /** 
         * Referensi Tabel Database
         * RESEP            : tb_resep      
         * RESEP DETAIL     : tb_resep_detail
         * TRANSAKSI        : tb_transaksi
         */ 

        /**
         * Pencarian Data menggunakan
         * PERIODE = Tgl Awal dan Tgl Akhir
         */

        /**
         * Kolom Laporan terdiri dari : 
         * No Resep     = tb_resep.no_resep
         * Tgl Resep    = tb_resep.tgl_resep
         * Item Name    = tb_resep_detail.item_nama
         * Qty          = tb_resep_detail.jml_resep
         * Penjmain     = tb_transaksi.penjamin_nama
         */ 

        try { 
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if($request->input('start_date_jml_resep')) {
                $tgl_awal = $request->input('start_date_jml_resep');
                $tgl_akhir = $request->input('end_date_jml_resep');
            } else {
                $tgl_awal = "2023-01-01";
                $tgl_akhir = "2023-12-31";
            }

            $report = DB::connection('dbclient')->table('tb_transaksi as trx')
                ->join('tb_resep as rsp','trx.trx_id','=','rsp.trx_id')
                ->where('trx.client_id', $this->clientId)
                ->where('trx.is_aktif', 1)
                ->whereBetween('rsp.tgl_resep',[$tgl_awal,$tgl_akhir])
                ->get();

            $data['central']    = $central;
            $data['report']     = $report;
            $data['tgl_awal']   = $tgl_awal;
            $data['tgl_akhir']  = $tgl_akhir;

            return view('report/farmasi/reportJumlahResep',  compact('data'));
            
        } catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Terjadi pada error pada pengambilan resep item obat. ','error'=>$e->getMessage()]);
        }
        return view('report/farmasi/reportJumlahResep',  compact('datas'));
    }
        
        public function dataWaktuTunggu(){
            $datas = [
                
            ];
            
            return view('report/laporan/farmasi/reportWaktuTunggu',  compact('datas'));
        }
}
                