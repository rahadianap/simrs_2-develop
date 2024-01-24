<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

use Modules\Pendaftaran\Entities\Pendaftaran;
use Modules\ManajemenUser\Entities\Client;
use Modules\Transaksi\Entities\Pembayaran;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;

class CetakanBillingController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }
    
    public function dataPembayaran($jns_cetakan, $id)
    {
        try {

            if($jns_cetakan == "kwitansi"){
                $data = Pembayaran::join('tb_registrasi','tb_pembayaran.reg_id','=','tb_registrasi.reg_id')
                    ->where('tb_pembayaran.client_id',$this->clientId)
                    ->where('tb_pembayaran.is_aktif',1)
                    ->where('tb_pembayaran.payment_id',$id)
                    ->select('tb_pembayaran.*','tb_registrasi.nama_pasien')
                    ->first();

                $detail = Transaksi::where('reg_id',$data->reg_id)
                    ->where('interim_id',$data->interim_id)
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('is_realisasi',true)
                    ->where('is_bayar',true)
                    ->orderBy('tgl_transaksi','DESC')
                    ->get();

                $totalPembulatan = 0;
                foreach($detail as $dt) {
                    $totalPembulatan = $totalPembulatan + ($dt['pembulatan'] * 1);
                }

                $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();

                $data['central']            = $central;
                $data['detail']             = $detail;
                $data['total_pembulatan']   = $totalPembulatan;   
                $data['terbilang']          = $this->terbilang($data->jml_bayar);
                return view('report/biling/cetakanKwitansi',  compact('data'));
            }

            $data = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where(function($q) use ($id) {
                    $q->where('trx_id',$id)->orWhere('reg_id',$id);
                })              
                ->where('is_realisasi',true)
                ->select('reg_id','tgl_transaksi','nama_pasien','pasien_id','no_rm','penjamin_nama','penjamin_id')
                ->first();

            $transaksi = Transaksi::where(function($q) use ($id) {
                    $q->where('trx_id',$id)->orWhere('reg_id',$id);
                }) ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',true)
                ->orderBy('tgl_transaksi','DESC')
                ->get();

            $grandTotal = 0;
            $totalPembulatan = 0;
            $totalDiskon = 0;
            foreach($transaksi as $tr) {
                //$tr['is_bayar'] = true;
                $grandTotal = $grandTotal + ($tr['total']*1) + ($tr['pembulatan']*1);
                $totalPembulatan = $totalPembulatan + ($tr['pembulatan'] * 1);
                $totalDiskon = $totalDiskon + ($tr['diskon'] * 1);
                $tr['detail'] = TransaksiDetail::where('reg_id',$tr->reg_id)
                    ->where('trx_id',$tr['trx_id'])
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->orderBy('created_at','DESC')
                    ->get();
            }

            $detail = Transaksi::where('tb_transaksi.reg_id',$id)->where('tb_transaksi.is_realisasi',true)->where('tb_transaksi.is_aktif',1)->where('tb_transaksi.client_id',$this->clientId)
                ->join('tb_trx_detail','tb_transaksi.trx_id','tb_trx_detail.trx_id')
                ->leftJoin('tb_tindakan','tb_tindakan.tindakan_id','tb_trx_detail.item_id')
                ->leftjoin('tb_kelompok_tagihan','tb_kelompok_tagihan.kelompok_tagihan_id','tb_tindakan.kelompok_tagihan_id')
                ->select('tb_trx_detail.*','tb_transaksi.tgl_transaksi','tb_transaksi.jns_transaksi',
                    DB::raw("(CASE WHEN tb_kelompok_tagihan.kelompok_tagihan IS NULL THEN 'Lain-lain' ELSE tb_kelompok_tagihan.kelompok_tagihan END) AS kelompok_tagihan"))
                ->orderByRaw("CASE WHEN tb_kelompok_tagihan.kelompok_tagihan = 'Lain-lain' THEN 1 ELSE 0 END, tb_kelompok_tagihan.kelompok_tagihan ASC")
                ->get();

            $pembayaran = Pembayaran::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();
            $totalBayar = 0;
            
            foreach($pembayaran as $byr) {
                $totalBayar = $totalBayar + ($byr['jml_bayar'] * 1);
            }

            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();

            $pendaftaran = Transaksi::where('reg_id',$id)->where('tb_transaksi.is_aktif',1)
                ->where('tb_transaksi.client_id',$this->clientId)
                ->leftJoin('tb_pasien','tb_pasien.pasien_id','=','tb_transaksi.pasien_id')
                ->select('tb_transaksi.*','tb_pasien.nama_pasien','tb_pasien.tgl_lahir','tb_pasien.tempat_lahir')
                ->first();

            $data['grand_total']        = $grandTotal;
            $data['detail']             = $detail;
            $data['pembayaran']         = $pembayaran;
            $data['total_bayar']        = $totalBayar;
            $data['total_pembulatan']   = $totalPembulatan;                        
            $data['central']            = $central;
            $data['pendaftaran']        = $pendaftaran;
            $data['sisa_tagihan']       = $grandTotal - $totalBayar;                        
            $data['terbilang']          = $this->terbilang($grandTotal);
            $data['tgl_cetak']          = Carbon::now()->format('Y-m-d H:i:s');

            
            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
            if ($jns_cetakan == "ringkas"){
                return view('report/biling/cetakanRingkas',  compact('data'));
            } else if ($jns_cetakan == "lengkap") {
                return view('report/biling/cetakanRincian',  compact('data'));
            } else {
                return response()->json(['success' => false, 'message' => 'jenis cetakan tidak ditemukan']);
            }
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function dataKwitansi($id)
    {
        try {
            $data = Pembayaran::join('tb_registrasi','tb_pembayaran.reg_id','=','tb_registrasi.reg_id')
                ->where('tb_pembayaran.client_id',$this->clientId)
                ->where('tb_pembayaran.is_aktif',1)
                ->where('tb_pembayaran.payment_id',$id)
                ->select('tb_pembayaran.*','tb_registrasi.nama_pasien')
                ->first();

            $detail = Transaksi::where('reg_id',$id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',true)
                ->orderBy('tgl_transaksi','DESC')
                ->get();

            $totalBayar = 0;
           
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();

            $data['central']            = $central;
            $data['detail']             = $detail;
            $data['total_pembulatan']   = $totalPembulatan;                        
            
            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
            // return view('report/biling/cetakanKwitansi',  compact('data'));
            return $data;
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function dataUangMukaPasien(Request $request)
    {
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if($request->input('no_rm')){
                $keyword = $request->no_rm;
            } else {
                $keyword = "000000";
            }
            
            if($keyword == "000000"){
                $datas = [
                    'no_reg' => 'REG/072023/000001',
                    'nama_pasien' => 'ANNISA RAHMAH',
                    'no_rm' => '0001001',
                    'kelas' => 'Kelas III',
                    'ruang_perawatan' => 'KERINCI KERINCI 1305',
                    'no_tempat_tidur' => '1305.01',
                    'no_identitas' => '1350083920981123',
                    'nama_penanggung' => 'ANNISA RAHMAH',
                    'hub_penanggung' => 'DIRI SENDIRI',
                    'alamat_penanggung' => 'PERUMAHAN MARANATHA PEMATANG',
                    'telp_penanggung' => '089288372601',
                    'no_identitas_penanggung' => '1350083920981123',
                    'total_uang_muka' => 0,
                    'sisa_uang_muka' => 0,
                    'pembayaran' => 0,
                    'hari_bayar' => 0,
                    'hari_dp' => 'Rabu',
                    'tgl_dp' => '29-Dec-2022',
                ];
            } else {
                $query = DB::connection('dbclient')->table('tb_transaksi as tr')
                        ->join('tb_registrasi as reg','tr.reg_id','=','reg.reg_id')
                        ->join('tb_bed as bed','bed.ruang_id','=','reg.ruang_id')
                        ->where('tr.no_rm',$keyword)
                        ->where('tr.client_id',$this->clientId)
                        ->select('tr.*','reg.*','bed.no_bed')
                        ->first();

                $data = [
                    'central' => $central,
                    'no_reg' => $query->reg_id,
                    'nama_pasien' => $query->nama_pasien,
                    'no_rm' => $query->no_rm,
                    'kelas' => $query->kelas_penjamin_id,
                    'ruang_perawatan' => $query->ruang_nama,
                    'no_tempat_tidur' => $query->no_bed,
                    'no_identitas' => $query->nik,
                    'nama_penanggung' => $query->nama_penanggung,
                    'hub_penanggung' => $query->hub_penanggung,
                    // Alamat Penanggung belum ada
                    'alamat_penanggung' => '-',
                    // No Telpn Penanggung belum ada
                    'telp_penanggung' => '-',
                    // No Identitas Penanggung belum ada
                    'no_identitas_penanggung' => '-',
                    // Total Uang Muka belum ada
                    'total_uang_muka' => 0,
                    // Sisa Uang Muka belum ada
                    'sisa_uang_muka' => 0,
                    'pembayaran' => 0,
                    'hari_bayar' => 0,
                    'tgl_bayar' => 0,
                    'hari_dp' => 'Rabu',
                    'tgl_dp' => '19 Januari 2023',
                ];
            }

            return view('cetakan/cetakanUangMukaPasien',  compact('data'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function terbilang($nilai) 
    {
		$nilai = abs($nilai);
		$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->terbilang($nilai - 10). " Belas";
		} else if ($nilai < 100) {
			$temp = $this->terbilang($nilai/10)." Puluh". $this->terbilang($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->terbilang($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->terbilang($nilai/100) . " Ratus" . $this->terbilang($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->terbilang($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->terbilang($nilai/1000) . " Ribu" . $this->terbilang($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->terbilang($nilai/1000000) . " Juta" . $this->terbilang($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->terbilang($nilai/1000000000) . " Milyar" . $this->terbilang(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->terbilang($nilai/1000000000000) . " Trilyun" . $this->terbilang(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
}
