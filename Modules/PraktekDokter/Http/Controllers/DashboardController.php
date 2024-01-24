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
use Modules\PraktekDokter\Entities\PaymentPraktek;
use Modules\PraktekDokter\Entities\PencatatanKas;
use Modules\PraktekDokter\Entities\Pembayaran;
use Modules\MasterData\Entities\Pasien;

class DashboardController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function summaryTabKunjungan(){
        try {
            
            /**
             * Summary Total Kunjungan, Pasien Baru dan Pasien Lama
             */
            $qPraktekDokter = PraktekDokter::where('client_id',$this->clientId)
                            ->where('is_aktif',true)
                            ->whereNotIn('status',['ANTRI','BATAL','BOOKING'])
                            ->whereBetween('tgl_periksa', 
                            [
                                Carbon::now()->startOfMonth(), 
                                Carbon::now()->endOfMonth()
                            ]);
            $data['ttl_kunjungan'] = $qPraktekDokter->clone()->count();

            $pasien_baru = $qPraktekDokter->clone()->where('is_pasien_baru',true)->count();
            $data['pasien_baru'] = $pasien_baru;

            $pasien_lama = $qPraktekDokter->clone()->where('is_pasien_baru',false)->count();
            $data['pasien_lama'] = $pasien_lama;
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function summaryTab(){
        try {
            
            $qPraktekDokter = PraktekDokter::where('client_id',$this->clientId)
                            ->where('is_aktif',true)
                            ->whereNotIn('status',['ANTRI','BATAL','BOOKING'])
                            ->whereBetween('tgl_periksa', 
                            [
                                Carbon::now()->startOfMonth(), 
                                Carbon::now()->endOfMonth()
                            ]);
            
            /**
             * Pendapatan berasal summary transaksi selesai per tanngal selama satu bulan
             * Jika ada kebetulan hari ini masih tanggal 10, maka tanggal berikut tidak ditampilkan
             */
            $qPendapatan = PaymentPraktek::where('client_id',$this->clientId)
                          ->where('is_aktif',true)
                          ->select(DB::raw("TO_CHAR(tgl_bayar, 'FMDD') as tgl_bayar"), DB::raw('SUM(jml_bayar) as total_amount'))
                          ->whereBetween('tgl_bayar', 
                            [
                                Carbon::now()->startOfMonth(), 
                                Carbon::now()->endOfMonth()
                            ])
                            ->groupBy('tgl_bayar')
                            ->get();


            $summary= [];
            foreach ($qPendapatan as $transaction) {
                $date = $transaction['tgl_bayar'];
                $amount = (int)$transaction['total_amount']; // Convert amount to integer for proper addition

                // If the date exists in the summary array, add the amount to the existing total, else create a new entry
                if (array_key_exists($date, $summary)) {
                    $summary[$date] += $amount;
                } else {
                    $summary[$date] = $amount;
                }
            }
            $data['pendapatan'] = $summary;

            /**
             * Summray Kas Masuk dan Keluar
             */
            $qKas = PencatatanKas::where('client_id',$this->clientId)
                    ->where('is_aktif',true)
                    ->whereBetween('tgl_transaksi', 
                        [
                            Carbon::now()->startOfMonth(), 
                            Carbon::now()->endOfMonth()
                        ]);
                    
            $kasRecord = $qKas->clone()->get();
            $kas = []; 
            foreach ($kasRecord as $record) {
                $date = Carbon::parse($record->tgl_transaksi)->format('j');
                $jmlBayar = $record->jml_bayar;
                $jmlTerima = $record->jml_terima;
            
                if (!isset($kas[$date])) {
                    $kas[$date] = [
                        'total_jml_bayar' => 0,
                        'total_jml_terima' => 0,
                    ];
                }
            
                $kas[$date]['total_jml_bayar'] += $jmlBayar;
                $kas[$date]['total_jml_terima'] += $jmlTerima;
            }
            $data['kas'] = $kas;

            /**
             * Summary Pasien Pria dan Wanita
             */
            $pria = $qPraktekDokter->clone()->where('jns_kelamin','L')->count();
            $wanita = $qPraktekDokter->clone()->where('jns_kelamin','P')->count();
            $pasien = [
                'Pria' => $pria,
                'Wanita' => $wanita,
            ];
            $data['pasien'] = $pasien; 

            /**
             * Summary Cara Bayar
             */
            $qCaraBayar = $qKas->clone()->whereNotNull('interim_id')->where('jenis_transaksi','Pemasukan')->get();
            $cara_bayar = [];
            foreach ($qCaraBayar as $record) {
                $jenisBayar = $record->metode_pembayaran;
                if (!isset($cara_bayar[$jenisBayar])) {
                    $cara_bayar[$jenisBayar] = 0;
                }
                $cara_bayar[$jenisBayar] += 1;
            }
            $data['cara_bayar'] = $cara_bayar;

            /**
             * Summary Penjamin
             */
            $qPenjamin = $qPraktekDokter->clone()->get();
            $penjamin = [];
            foreach ($qPenjamin as $record) {
                $jnsPenjamin = $record->jns_penjamin;
                if (!isset($penjamin[$jnsPenjamin])) {
                    $penjamin[$jnsPenjamin] = 0;
                }
                $penjamin[$jnsPenjamin] += 1;
            }
            $data['penjamin'] = $penjamin;

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function monitoringAntrian(Request $request){
        try {

            $per_page = 10;

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PraktekDokter::query()->whereBetween('tgl_periksa', 
                        [
                            Carbon::now()->startOfMonth(), 
                            Carbon::now()->endOfMonth()
                        ]);
            
            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('reg_id','ILIKE',$keyword)
                        ->orWhere('no_rm','ILIKE',$keyword)
                        ->orWhere('nama_pasien','ILIKE',$keyword);
                    });
                }
            }

            if($request->has('dokter')) {
                $dokter ='%'.$request->input('dokter').'%'; 
                if($dokter !== '' && $dokter !== null ) {
                    $query = $query->where(function($q) use ($dokter) {
                        $q->where('dokter_id','ILIKE',$dokter)
                        ->orWhere('dokter_nama','ILIKE',$dokter);
                    });
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('client_id',$this->clientId)->where('is_aktif','ILIKE',$isAktif)->orderBy('status')->paginate($per_page);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function monitoringTransaksi(Request $request){
        try {

            $per_page = 10;

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PaymentPraktek::query()->join('tb_praktek_dokter','tb_payment_praktek.reg_id','=','tb_praktek_dokter.reg_id')
                        ->select('tb_payment_praktek.*','tb_praktek_dokter.no_antrian','tb_praktek_dokter.no_rm','tb_praktek_dokter.jns_penjamin')
                        ->whereBetween('tb_payment_praktek.tgl_bayar', 
                        [
                            Carbon::now()->startOfMonth(), 
                            Carbon::now()->endOfMonth()
                        ]);
            
            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('tb_payment_praktek.reg_id','ILIKE',$keyword)
                        ->orWhere('tb_praktek_dokter.no_rm','ILIKE',$keyword)
                        ->orWhere('tb_payment_praktek.nama_pasien','ILIKE',$keyword);
                    });
                }
            }

            if($request->has('dokter')) {
                $dokter ='%'.$request->input('dokter').'%'; 
                if($dokter !== '' && $dokter !== null ) {
                    $query = $query->where(function($q) use ($dokter) {
                        $q->where('tb_payment_praktek.dokter_id','ILIKE',$dokter)
                        ->orWhere('tb_payment_praktek.dokter_nama','ILIKE',$dokter);
                    });
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('tb_payment_praktek.client_id',$this->clientId)->where('tb_payment_praktek.is_aktif','ILIKE',$isAktif)->paginate($per_page);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function monitoringStatus(){
        try {
            $qPraktekDokter = PraktekDokter::where('client_id',$this->clientId)
                            ->where('is_aktif',true)
                            ->whereBetween('tgl_periksa', 
                            [
                                Carbon::now()->startOfMonth(), 
                                Carbon::now()->endOfMonth()
                            ]);
            $data['antrian'] = $qPraktekDokter->clone()->whereIn('status',['ANTRI','BOOKING'])->count();

            $data['dilayani'] = $qPraktekDokter->clone()->where('status','PERIKSA')->count();

            $data['selesai'] = $qPraktekDokter->clone()->where('is_bayar','True')->count();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
