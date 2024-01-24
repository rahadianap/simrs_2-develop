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
use Modules\PraktekDokter\Entities\Pembayaran;
use Modules\PraktekDokter\Entities\PencatatanKas;
use Modules\PraktekDokter\Entities\PaymentPraktek;

use Modules\Pendaftaran\Entities\RegPasien;
use Modules\ManajemenUser\Entities\Client;

class ReportController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lapPendapatan(Request $request){
        try {
            $per_page = 20;
            $keyword = '%%';
            $isAktif = 'true';
            

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PaymentPraktek::query();

            // if($request->has('start_date')) {
            //     $tglPeriksaMulai =$request->input('start_date').' 00:00:00';
            //     $tglPeriksaAkhir =$request->input('start_date').' 23:59:59';                
            //     if($request->has('end_date')) {
            //         $tglPeriksaAkhir =$request->input('end_date').' 23:59:59';
            //     }
            //     $query = $query->whereBetween('tgl_bayar',[$tglPeriksaMulai,$tglPeriksaAkhir]);
            // }
            
            if($request->has('start_date') && $request->has('end_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';                
              
                $query = $query->whereBetween('tgl_bayar',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            }

            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('nama_pasien','ILIKE',$keyword)
                        ->orWhere('no_rm','ILIKE',$keyword)
                        ->orWhere('interim_id','ILIKE',$keyword);
                    });
                }
            }

            $total = $query->where('is_aktif','True')->get();
            $sumTotal = 0;
            if($total) {
                foreach($total as $tot) {
                    $sumTotal = $sumTotal + ($tot['total_akhir']); 
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('is_aktif','ILIKE',$isAktif)
                           ->where('client_id',$this->clientId)
                           ->orderBy('tgl_bayar','DESC')
                           ->paginate($per_page);
            if(!$lists) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }

            // foreach($lists->items() as $itm) {
            //     $detail = Pembayaran::query();
            //     $detail = $detail->where('is_aktif',true)
            //                      ->where('client_id',$this->clientId)
            //                      ->where('interim_id',$itm['interim_id'])
            //                      ->where('jml_bayar','>',0);

            //     if($request->has('status')) {
            //         $status ='%'.$request->input('status').'%'; 
            //         if($status !== '' && $status !== null ) {
            //             $detail = $detail->where('jns_payment','ILIKE',strtoupper($status));
            //         }
            //     }

            //     $itm['detail'] = $detail->get();
            // }

            return response()->json(['success'=>true,'data'=>$lists, 'sumTotal' => $sumTotal]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data laporan pendapatan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function lapKunjungan(Request $request){
        try {
            $per_page = 20;
            $keyword = '%%';
            $isAktif = 'true';
            // $tglPeriksaMulai = Carbon::now()->startOfMonth();
            // $tglPeriksaAkhir = Carbon::now()->endOfMonth();

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PraktekDokter::query()->whereIn('status',['PERIKSA','ANTRI']);

            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('nama_pasien','ILIKE',$keyword)
                        ->orWhere('no_rm','ILIKE',$keyword)
                        ->orWhere('interim_id','ILIKE',$keyword);
                    });
                }
            }

            if($request->has('periode')) {
                $periode = $request->input('periode');
                if(strtoupper($periode) == 'BULAN-INI') {
                    $tglPeriksaMulai = Carbon::now()->startOfMonth();
                    $tglPeriksaAkhir = Carbon::now()->endOfMonth();
                }
                else {
                    $tglPeriksaMulai = Carbon::now()->format('Y-m-d').' 00:00:00';
                    $tglPeriksaAkhir = Carbon::now()->format('Y-m-d').' 23:59:59';
                } 
                $query = $query->whereBetween('tgl_periksa',[$tglPeriksaMulai,$tglPeriksaAkhir]);
            }

            if($request->has('start_date') && $request->has('end_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';                
                $query = $query->whereBetween('tgl_periksa',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            }

            // if($request->has('start_date')) {
            //     $tglPeriksaMulai =$request->input('start_date').' 00:00:00';
            //     $tglPeriksaAkhir =$request->input('start_date').' 23:59:59';                
            //     if($request->has('end_date')) {
            //         $tglPeriksaAkhir =$request->input('end_date').' 23:59:59';
            //     }
            //     $query = $query->whereBetween('tgl_periksa',[$tglPeriksaMulai,$tglPeriksaAkhir]);
            // }

            if($request->has('jns_penjamin')) {
                $penjamin ='%'.$request->input('jns_penjamin').'%'; 
                if($penjamin !== '' && $penjamin !== null ) {
                    $query = $query->where('jns_penjamin','ILIKE',$penjamin);
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('is_aktif','ILIKE',$isAktif)
                           ->where('client_id',$this->clientId)
                           ->orderBy('tgl_periksa','DESC')
                           ->paginate($per_page);
            if(!$lists) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }

            $total = $query->where('is_aktif','true')->whereIn('status',['PERIKSA','ANTRI'])->count();

            return response()->json(['success'=>true,'data'=>$lists, 'totalKunjungan' => $total]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data laporan kunjungan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function lapKas(Request $request){
        try {
            $per_page = 20;
            $keyword = '%%';
            $isAktif = 'true';
            // $tglTransaksiMulai = Carbon::now()->startOfMonth();
            // $tglTransaksiAkhir = Carbon::now()->endOfMonth();
            
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PencatatanKas::query();

            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where('deskripsi','ILIKE',$keyword);
                }
            }

            if($request->has('metode_pembayaran')) {
                $metodeBayar ='%'.$request->input('metode_pembayaran').'%'; 
                if($metodeBayar !== '' && $metodeBayar !== null ) {
                    $query = $query->where('metode_pembayaran','ILIKE',$metodeBayar);
                }
            }
            
            if($request->has('jenis_transaksi')) {
                $jnsTransaksi ='%'.$request->input('jenis_transaksi').'%'; 
                if($jnsTransaksi !== '' && $jnsTransaksi !== null ) {
                    $query = $query->where('jenis_transaksi','ILIKE',$jnsTransaksi);
                }
            }
            
            // if($request->has('periode')) {
            //     $periode = $request->input('periode');
            //     if(strtoupper($periode) == 'BULAN-INI') {
            //         $tglTransaksiMulai = Carbon::now()->startOfMonth();
            //         $tglTransaksiAkhir = Carbon::now()->endOfMonth();
            //     }
            //     else {
            //         $tglTransaksiMulai = Carbon::now()->format('Y-m-d').' 00:00:00';
            //         $tglTransaksiAkhir = Carbon::now()->format('Y-m-d').' 23:59:59';
            //     } 
            //     $query = $query->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            // }


            // if($request->has('start_date')) {
            //     $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
            //     $tglTransaksiAkhir =$request->input('start_date').' 23:59:59';                
            // }
            // if($request->has('end_date')) {
            //     $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';
            // }
            
            // $query = $query->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);

            if($request->has('start_date') && $request->has('end_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';                
                $query = $query->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            }
            

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('is_aktif','ILIKE',$isAktif)
                           ->where('client_id',$this->clientId)
                           ->orderBy('tgl_transaksi','DESC')
                           ->paginate($per_page);
            if(!$lists) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }

            $activeRows = $query->where('is_aktif','true')->select('jml_bayar','jml_terima')->get();
            $totalTerima = 0;
            $totalBayar = 0;
            
            foreach($activeRows as $row) {
                $totalBayar = $totalBayar + ($row['jml_bayar'] * 1);
                $totalTerima = $totalTerima + ($row['jml_terima'] * 1);
            }
            
            $sumTotalPengeluaran = $totalBayar;
            $sumTotalPemasukan = $totalTerima;

            return response()->json(['success'=>true,'data'=>$lists, 'totalPengeluaran' => $sumTotalPengeluaran, 'totalPemasukan' => $sumTotalPemasukan]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data laporan kas : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function cetakanPendapatan(Request $request)
    {
        try {
            $keyword = '%%';
            $isAktif = 'true';
            
            // $tglTransaksiMulai =null;
            // $tglTransaksiAkhir =null;

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PaymentPraktek::query();
            
            // if($request->has('periode')) {
            //     $periode = $request->input('periode');
            //     if(strtoupper($periode) == 'BULAN-INI') {
            //         $tglTransaksiMulai = Carbon::now()->startOfMonth();
            //         $tglTransaksiAkhir = Carbon::now()->endOfMonth();
            //     }
            //     else if(strtoupper($periode) == 'HARI-INI') {
            //         $tglTransaksiMulai = Carbon::now()->format('Y-m-d').' 00:00:00';
            //         $tglTransaksiAkhir = Carbon::now()->format('Y-m-d').' 23:59:59';
            //     } 
            //     $query = $query->whereBetween('tgl_bayar',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            //     $data['periode'] = $tglTransaksiMulai . " s/d " . $tglTransaksiAkhir;
            // } else {
            //     $data['periode'] = "-";
            // }

            if($request->has('start_date') && $request->has('end_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';                
                $query = $query->whereBetween('tgl_bayar',[$tglTransaksiMulai,$tglTransaksiAkhir]);
                $data['periode'] = $tglTransaksiMulai . " s/d " . $tglTransaksiAkhir;
            } else {
                $data['periode'] = "-";
            }
            
            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('tb_payment_praktek.nama_pasien','ILIKE',$keyword)
                        ->orWhere('tb_payment_praktek.no_rm','ILIKE',$keyword)
                        ->orWhere('tb_payment_praktek.interim_id','ILIKE',$keyword);
                    });
                }
            }

            $total = $query->where('tb_payment_praktek.is_aktif','True')->get();
            $sumTotal = 0;
            if($total) {
                foreach($total as $tot) {
                    $sumTotal = $sumTotal + ($tot['total_akhir']); 
                }
            }
            $data['sumTotal'] = $sumTotal;

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $list = $query->join('tb_pasien','tb_payment_praktek.pasien_id','=','tb_pasien.pasien_id')
                          ->where('tb_payment_praktek.is_aktif','ILIKE',$isAktif)
                          ->where('tb_payment_praktek.client_id',$this->clientId)
                          ->orderBy('tb_payment_praktek.tgl_bayar','ASC')
                          ->select('tb_payment_praktek.*','tb_pasien.no_rm')
                          ->get();
            if(!$list) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }
            $data['report'] = $list;

            foreach($list as $itm) {
                $detail = Pembayaran::query();
                $detail = $detail->where('is_aktif',true)
                                 ->where('client_id',$this->clientId)
                                 ->where('interim_id',$itm['interim_id'])
                                 ->where('jml_bayar','>',0);

                if($request->has('status')) {
                    $status ='%'.$request->input('status').'%'; 
                    if($status !== '' && $status !== null ) {
                        $detail = $detail->where('jns_payment','ILIKE',strtoupper($status));
                    }
                }
                $itm['detail'] = $detail->get();
            }

            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;
            
            return view('report/praktekDokter/lapPendapatan', compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data pembayaran : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function cetakanKunjungan(Request $request)
    {
        try {
            $keyword = '%%';
            $isAktif = 'true';
        
            $query = PraktekDokter::query()->whereIn('status',['PERIKSA','ANTRI']);

            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('nama_pasien','ILIKE',$keyword)
                        ->orWhere('no_rm','ILIKE',$keyword)
                        ->orWhere('interim_id','ILIKE',$keyword);
                    });
                }
            }
            
            // if($request->has('periode')) {
            //     if($request->input('periode') != "") {
            //         $periode = $request->input('periode');
            //         if(strtoupper($periode) == 'BULAN-INI') {
            //             $tglPeriksaMulai = Carbon::now()->startOfMonth();
            //             $tglPeriksaAkhir = Carbon::now()->endOfMonth();
            //         }
            //         else {
            //             $tglPeriksaMulai = Carbon::now()->format('Y-m-d').' 00:00:00';
            //             $tglPeriksaAkhir = Carbon::now()->format('Y-m-d').' 23:59:59';
            //         } 
            //         $query = $query->whereBetween('tgl_periksa',[$tglPeriksaMulai,$tglPeriksaAkhir]);
            //     }
            //     $data['periode'] = $tglPeriksaMulai . " s/d " . $tglPeriksaAkhir;
            // } else {
            //     $data['periode'] = "-";
            // }

            if($request->has('start_date') && $request->has('end_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';                
                $query = $query->whereBetween('tgl_periksa',[$tglTransaksiMulai,$tglTransaksiAkhir]);
                $data['periode'] = $tglTransaksiMulai . " s/d " . $tglTransaksiAkhir;
            } else {
                $data['periode'] = "-";
            }

            if($request->has('jns_penjamin')) {
                $penjamin ='%'.$request->input('jns_penjamin').'%'; 
                if($penjamin !== '' && $penjamin !== null ) {
                    $query = $query->where('jns_penjamin','ILIKE',$penjamin);
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('is_aktif','ILIKE',$isAktif)
                           ->where('client_id',$this->clientId)
                           ->orderBy('tgl_periksa','DESC')
                           ->get();
            if(!$lists) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }
            $data['report'] = $lists;

            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;

            $total = $query->where('is_aktif','ILIKE',$isAktif)->whereIn('status',['PERIKSA','ANTRI'])->count();
            $data['totalKunjungan'] = $total;

            return view('report/praktekDokter/lapKunjungan', compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data laporan kunjungan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function cetakanKas(Request $request)
    {
        try {
            $keyword = '%%';
            $isAktif = 'true';

            $query = PencatatanKas::query();

            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where('deskripsi','ILIKE',$keyword);
                }
            }

            if($request->has('metode_pembayaran')) {
                $metodeBayar ='%'.$request->input('metode_pembayaran').'%'; 
                if($metodeBayar !== '' && $metodeBayar !== null ) {
                    $query = $query->where('metode_pembayaran','ILIKE',$metodeBayar);
                }
            }
            
            if($request->has('jenis_transaksi')) {
                $jnsTransaksi ='%'.$request->input('jenis_transaksi').'%'; 
                if($jnsTransaksi !== '' && $jnsTransaksi !== null ) {
                    $query = $query->where('jenis_transaksi','ILIKE',$jnsTransaksi);
                }
            }

            // if($request->has('periode')) {
            //     $periode = $request->input('periode');
            //     if(strtoupper($periode) == 'BULAN-INI') {
            //         $tglTransaksiMulai = Carbon::now()->startOfMonth();
            //         $tglTransaksiAkhir = Carbon::now()->endOfMonth();
            //     }
            //     else {
            //         $tglTransaksiMulai = Carbon::now()->format('Y-m-d').' 00:00:00';
            //         $tglTransaksiAkhir = Carbon::now()->format('Y-m-d').' 23:59:59';
            //     } 
            //     $query = $query->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            //     $data['periode'] = $tglTransaksiMulai . " s/d " . $tglTransaksiAkhir;
            // } else {
            //     $data['periode'] = "-";  
            // }

            if($request->has('start_date') && $request->has('end_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';                
                $query = $query->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
                $data['periode'] = $tglTransaksiMulai . " s/d " . $tglTransaksiAkhir;
            } else {
                $data['periode'] = "-";
            }
            
            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('is_aktif','ILIKE',$isAktif)
                           ->where('client_id',$this->clientId)
                           ->orderBy('tgl_transaksi','DESC')
                           ->get();
            $data['report'] = $lists;

            $activeRows = $query->where('is_aktif','true')->select('jml_bayar','jml_terima')->get();
            $totalTerima = 0;
            $totalBayar = 0;
            
            foreach($activeRows as $row) {
                $totalBayar = $totalBayar + ($row['jml_bayar'] * 1);
                $totalTerima = $totalTerima + ($row['jml_terima'] * 1);
            }
            
            $data['keluar'] = $totalBayar;
            $data['terima'] = $totalTerima;

            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['central'] = $central;

            return view('report/praktekDokter/lapPencatatanKas', compact('data'));
        } 

        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data laporan kas : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
