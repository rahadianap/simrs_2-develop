<?php

namespace Modules\Transaksi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

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
use Modules\MasterData\Entities\Penjamin;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;
use Modules\ManajemenUser\Entities\Client;

use Modules\Transaksi\Entities\RawatJalan;
use Modules\Transaksi\Entities\Pemeriksaan;
use Modules\Transaksi\Entities\PemeriksaanDetail;
use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;
use Modules\Transaksi\Entities\Pembayaran;


class BillingController extends Controller
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
            $aktif = 'true';
            $keyword = '%%';
            
            // $query = Pendaftaran::query();
            $query = Transaksi::query();
            $query = $query->where('client_id',$this->clientId); //->whereIn('status_reg',['ANTRI','DAFTAR','SELESAI','LENGKAP','RAWAT']);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_start') && $request->has('tgl_end')) {
                $dtStart = $request->input('tgl_start').' 00:00:00';
                $dtEnd = $request->input('tgl_end').' 23:59:59';                
                // $query = $query->whereBetween('tgl_registrasi', [$dtStart, $dtEnd]);
                $query = $query->whereBetween('tgl_transaksi', [$dtStart, $dtEnd]);
            }
            else {
                $dtStart = $request->date('Y-m-d').' 00:00:00';
                $dtEnd = $request->date('Y-m-d').' 23:59:59';                
                $query = $query->whereBetween('tgl_transaksi', [$dtStart, $dtEnd]);
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

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword);
                })
                ->where('is_realisasi',true)
                // ->orderBy('tgl_registrasi','ASC')
                ->orderBy('tgl_transaksi','ASC')
                ->paginate($per_page);
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data transaksi', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                // ->where('trx_id',$id) 
                ->where(function($q) use ($id) {
                    $q->where('trx_id',$id)->orWhere('reg_id',$id);
                })              
                ->select('reg_id','tgl_transaksi','nama_pasien','pasien_id','no_rm','penjamin_nama','penjamin_id')
                ->first();

            // if(!$data) {
            //     return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. Data tidak ditemukan.']);
            // }

            $transaksi = Transaksi::where('reg_id',$id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('is_realisasi',true)
                ->orderBy('tgl_transaksi','DESC')
                ->get();

            $grandTotal = 0;
            $totalPembulatan = 0;
            foreach($transaksi as $tr) {
                $tr['is_bayar'] = true;
                $grandTotal = $grandTotal + ($tr['total']*1) + ($tr['pembulatan']*1);
                $totalPembulatan = $totalPembulatan + ($tr['pembulatan'] * 1);
                $tr['detail'] = TransaksiDetail::where('reg_id',$tr->reg_id)
                    ->where('trx_id',$tr['trx_id'])
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->orderBy('created_at','DESC')
                    ->get();
            }

            $pembayaran = Pembayaran::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();
            $totalBayar = 0;
            foreach($pembayaran as $byr) {
                $totalBayar = $totalBayar + ($byr['jml_bayar'] * 1);
            }

            $data['grand_total']        = $grandTotal;
            $data['transaksi']          = $transaksi;
            $data['histori_bayar']      = $pembayaran;
            $data['total_bayar']        = $totalBayar;
            $data['total_pembulatan']   = $totalPembulatan;                        
            $data['pembayaran']         = [];

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request) 
    {
        try {
            $total_pembulatan = 0;
            foreach($request->pembayaran as $bayar) {
                $total_pembulatan = $total_pembulatan + $bayar['pembulatan'];
            }
            $interimId = $this->getInterimId();
            DB::connection('dbclient')->beginTransaction();
            foreach($request->transaksi as $tr) {
                if($tr['is_bayar'] && (!$tr['interim_id'] || $tr['interim_id'] == null ||  $tr['interim_id'] == '')) {

                    // $trans = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                    //     ->where('reg_id',$tr['reg_id'])->where('trx_id',$tr['trx_id'])->first();
                        
                    // if($trans) {
                        // if($trans->is_bayar){
                            $success = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                            ->where('reg_id',$tr['reg_id'])->where('trx_id',$tr['trx_id'])
                            ->update([
                                'interim_id'=>$interimId, 
                                'is_bayar'=>$tr['is_bayar'],
                                'pembulatan'=>$total_pembulatan,
                            ]);
                            
                            if(!$success) {
                                DB::connection('dbclient')->rollBack();
                                return response()->json(['success' => false, 'message' => 'Data gagal disimpan saat update flag transaksi']);
                            }
                        // }
                        
                    // }
                }            
            }

            foreach($request->pembayaran as $bayar) {
                $bayarId = $this->getPembayaranId();
                $pay = new Pembayaran();
                $pay->payment_id = $bayarId;
                $pay->reg_id = $request->reg_id;
                $pay->interim_id = $interimId;
                $pay->tgl_bayar = date('Y-m-d H:i:s');
                $pay->jns_payment = $bayar['jns_payment'];
                $pay->jml_bayar = round($bayar['jml_bayar'],2);
                $pay->jenis_kartu = $bayar['jenis_kartu'];
                $pay->penjamin_id = $bayar['penjamin_id'];
                $pay->penjamin_nama = $bayar['penjamin_nama'];
                $pay->mesin_edc = $bayar['mesin_edc'];
                $pay->no_kartu = $bayar['no_kartu'];
                $pay->is_aktif = true;
                $pay->client_id = $this->clientId;
                $pay->created_by = Auth::user()->username;
                $pay->updated_by = Auth::user()->username;
                
                $success = $pay->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data gagal disimpan.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request,$paymentId) 
    {
        try {
            $data = Pembayaran::where('payment_id',$paymentId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']);
            }
            
            $allPayment = Pembayaran::where('interim_id',$data->interim_id)->where('is_aktif',1)
                ->where('payment_id','<>',$paymentId)
                ->where('client_id',$this->clientId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $success = Pembayaran::where('payment_id',$paymentId)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data gagal dihapus.']);
            }

            if(!$allPayment) {
                $success = Transaksi::where('interim_id',$data->interim_id)->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->update(['interim_id' => null, 'updated_by' => Auth::user()->username]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();            
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data', 'error' => $e->getMessage()]);
        }
    }

    public function getPembayaranId()
    {
        try {
            $id = $this->clientId.date('ymd').'-BYR00001';
            $maxId = Pembayaran::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('payment_id', 'LIKE', $this->clientId.date('ymd').'-BYR%')
                ->max('payment_id');

            if (!$maxId) { $id = $this->clientId.date('ymd').'-BYR00001'; } 
            else {
                $maxId = str_replace($this->clientId.date('ymd').'-BYR', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.date('ymd').'-BYR0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.date('ymd').'-BYR000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.date('ymd').'-BYR00' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.date('ymd').'-BYR0' . $count; } 
                else { $id = $this->clientId.date('ymd').'-BYR' . $count; }
            }
            return $id;
        }
        catch(\Exception $e) {
            return null;
        }
    }

    public function getInterimId()
    {
        try {
            $id = $this->clientId.date('ymd').'-INTR00001';
            $maxId = Pembayaran::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('interim_id', 'LIKE', $this->clientId.date('ymd').'-INTR%')
                ->max('interim_id');

            if (!$maxId) { $id = $this->clientId.date('ymd').'-INTR00001'; } 
            else {
                $maxId = str_replace($this->clientId.date('ymd').'-INTR', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.date('ymd').'-INTR0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.date('ymd').'-INTR000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.date('ymd').'-INTR00' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.date('ymd').'-INTR0' . $count; } 
                else { $id = $this->clientId.date('ymd').'-INTR' . $count; }
            }
            return $id;
        }
        catch(\Exception $e) {
            return null;
        }
    }

}
