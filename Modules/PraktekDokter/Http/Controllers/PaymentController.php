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

class PaymentController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request) {
        try {
            $per_page = 20;
            $keyword = '%%';
            $isAktif = 'true';
            
            $tglPeriksaMulai =null;
            $tglPeriksaAkhir =null;
            
            $tglTransaksiMulai =null;
            $tglTransaksiAkhir =null;

            $jnsPenjamin = '%%';
            $status = '%%';

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PaymentPraktek::query();
            
            if($request->has('status')) {
                $status ='%'.$request->input('status').'%'; 
                if($status !== '' && $status !== null ) {
                    $query = $query->where('status','ILIKE',$status);
                }
            }

            if($request->has('periode')) {
                $periode = $request->input('periode');
                if($periode != '' || $periode != null) {
                    if(strtoupper($periode) == 'BULAN-INI') {
                        $tglTransaksiMulai = date('Y-m').'-01 00:00:00';
                        $tglTransaksiAkhir = date('Y-m-d').' 23:59:59';
                    }
                    else {
                        $tglTransaksiMulai = date('Y-m-d').' 00:00:00';
                        $tglTransaksiAkhir = date('Y-m-d').' 23:59:59';
                    }
                    $query = $query->whereBetween('tgl_bayar',[$tglTransaksiMulai,$tglTransaksiAkhir]);
                }
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

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('client_id',$this->clientId)->where('is_aktif','ILIKE',$isAktif)->orderBy('tgl_bayar','DESC')->paginate($per_page);
            if(!$lists) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }

            foreach($lists->items() as $itm) {
                $itm['detail'] = Pembayaran::where('is_aktif',true)->where('client_id',$this->clientId)->where('interim_id',$itm['interim_id'])->where('jml_bayar','>',0)->get();
            }

            //calculate total 
            $total = $query->where('client_id',$this->clientId)->where('is_aktif','ILIKE',$isAktif)->get();
            $sumTotal = 0;
            if($total) {
                foreach($total as $tot) {
                    $sumTotal = $sumTotal + ($tot['total_akhir']); 
                }
            }

            //$lists['total'] = $sumTotal;
            
            return response()->json(['success'=>true,'message'=>'List Antrian','data'=>$lists, 'sumTotal' => $sumTotal]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $pembayaran = PaymentPraktek::where('client_id',$this->clientId)->where('is_aktif',true)->where('reg_id',$id)->first();
            if(!$pembayaran) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            $pembayaran['detail'] = Pembayaran::where('is_aktif',true)->where('client_id',$this->clientId)
                ->where('interim_id',$pembayaran->interim_id)
                ->where('jml_bayar','>',0)
                ->select('payment_id','interim_id','is_aktif','jns_payment','jml_bayar')
                ->get();

            // foreach($pembayaran as $byr) {
            //     $totalBayar = $totalBayar + ($byr['jml_bayar'] * 1);
            // }

            // $data['pembayaran'] = $pembayaran;
            // $data['totalBayar'] = $totalBayar;

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $pembayaran]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request) 
    {
        try {
            $regId = $request->reg_id;
            $dataExist = PraktekDokter::where('is_aktif',true)->where('client_id',$this->clientId)->where('reg_id',$regId)
                ->first();
                
            if(!$dataExist) {
                return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan']);
            }

            if($dataExist->is_bayar == true) {
                return response()->json(['success' => false, 'message' => 'Data transaksi sudah dibayar']);
            }

            if($dataExist->status !== 'PERIKSA') {
                return response()->json(['success' => false, 'message' => 'Hanya data transaksi dengan status PERIKSA yang dapat dilakukan pembayaran.']);
            }
            
            $interimId = $this->getInterimId();
            
            DB::connection('dbclient')->beginTransaction();

            $dataExist->is_bayar = true;
            $dataExist->interim_id = $interimId;
            $dataExist->updated_by = Auth::user()->username;
            $success = $dataExist->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data.']);
            }

            $data = new PaymentPraktek();
            $data->reg_id = $regId;
            $data->interim_id = $interimId;

            $data->tgl_periksa = $dataExist->tgl_periksa;
            $data->tgl_bayar = date('Y-m-d H:i:s');
            
            $data->nama_pasien = $dataExist->nama_pasien;
            $data->pasien_id = $dataExist->pasien_id;
            $data->jns_kelamin = $dataExist->jns_kelamin;
            
            $data->dokter_id = $dataExist->dokter_id;
            $data->dokter_nama = $dataExist->dokter_nama;            
            
            $data->total_tagihan = $request->total_tagihan;
            $data->diskon = $request->diskon;
            $data->total_akhir = $request->total_akhir;
            $data->jml_bayar = $request->jml_bayar;
            $data->kembalian = $request->kembalian;
            $data->status = 'BAYAR';            
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $data->client_id = $this->clientId;   
            $data->is_aktif = true;   
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data.']);
            }
                
            foreach($request->detail as $dtl) {
                $bayarId = $this->getPembayaranId();
                //$kasId = $this->getPencatatanKasId();
                
                /** Tambah data Pembayaran */
                $pay = new Pembayaran();
                $pay->payment_id = $bayarId;
                $pay->reg_id = $regId;
                $pay->interim_id = $interimId;
                $pay->tgl_bayar = date('Y-m-d H:i:s');
                $pay->jns_payment = $dtl['jns_payment'];
                $pay->jml_bayar = $dtl['jml_bayar'];
                $pay->jenis_kartu = '';
                $pay->penjamin_id = '';
                $pay->penjamin_nama = '';
                $pay->mesin_edc = '';
                $pay->no_kartu = '';
    
                $pay->is_aktif = true;
                $pay->client_id = $this->clientId;
                $pay->created_by = Auth::user()->username;
                $pay->updated_by = Auth::user()->username;
                
                $success = $pay->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pembayaran gagal disimpan.']);
                }
    
                // /** Upload Bukti Bayar */
                // if (!$request->hasFile('bukti_bayar')) {
                //     return response()->json(['success' => false, 'message' => 'Bukti bayar tidak ditemukan. Data tidak dapat disimpan.']);
                // } 
                // $path = Storage::disk('kas')->putFile('kas', $request->file('bukti_bayar'), 'public');
                // $path_url = Storage::url($path);
    
                /** Tambah data Pencatatan Kas sebagai kas masuk */
                $kas = new PencatatanKas();
                $kas->kas_id = $bayarId;
                $kas->interim_id = $interimId;
                $kas->tgl_transaksi = date('Y-m-d H:i:s');
                $kas->jenis_transaksi = "Pemasukan";
                $kas->deskripsi = "Reg No. ".$regId." pembayaran ".$dtl['jns_payment']." sebesar ". $dtl['jml_bayar'];
                $kas->metode_pembayaran = $dtl['jns_payment'];
                $kas->jml_bayar = 0;
                $kas->jml_terima = $dtl['jml_bayar'];
                //$kas->bukti_bayar = $path_url;    
                $kas->is_aktif = true;
                $kas->client_id = $this->clientId;
                $kas->created_by = Auth::user()->username;
                $kas->updated_by = Auth::user()->username;
                
                $success = $kas->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pencatatn kas gagal disimpan.']);
                }  
            }            

            if($request->kembalian > 0) {
                $bayarId = $this->getPembayaranId();
                $kas = new PencatatanKas();
                $kas->kas_id = $bayarId;
                $kas->interim_id = $interimId;
                $kas->tgl_transaksi = date('Y-m-d H:i:s');
                $kas->jenis_transaksi = "Pengeluaran";
                $kas->deskripsi = "Reg No. ".$regId." kembalian sebesar ". $request->kembalian;
                $kas->metode_pembayaran = 'TUNAI';
                $kas->jml_bayar = $request->kembalian;
                $kas->jml_terima = 0;
                $kas->is_aktif = true;
                $kas->client_id = $this->clientId;
                $kas->created_by = Auth::user()->username;
                $kas->updated_by = Auth::user()->username;
                
                $success = $kas->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pencatatan kas gagal disimpan.']);
                }  

                /** Tambah data Pembayaran */
                $pay = new Pembayaran();
                $pay->payment_id = $bayarId;
                $pay->reg_id = $regId;
                $pay->interim_id = $interimId;
                $pay->tgl_bayar = date('Y-m-d H:i:s');
                $pay->jns_payment = 'KEMBALIAN';
                $pay->jml_bayar = 0 - $request->kembalian;
                $pay->jenis_kartu = '';
                $pay->penjamin_id = '';
                $pay->penjamin_nama = '';
                $pay->mesin_edc = '';
                $pay->no_kartu = '';
    
                $pay->is_aktif = true;
                $pay->client_id = $this->clientId;
                $pay->created_by = Auth::user()->username;
                $pay->updated_by = Auth::user()->username;
                
                $success = $pay->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Kembalian gagal disimpan.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request) 
    {
        try {
            $regId = $request->reg_id;
            $data = PaymentPraktek::where('is_aktif',true)->where('client_id',$this->clientId)->where('reg_id',$regId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data pembayaran tidak ditemukan']);
            }

            $dataExist = PraktekDokter::where('is_aktif',true)->where('client_id',$this->clientId)->where('reg_id',$regId)
                ->first();
                
            if(!$dataExist) {
                return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan']);
            }

            if($dataExist->is_bayar == true) {
                return response()->json(['success' => false, 'message' => 'Data transaksi sudah dibayar']);
            }

            if($dataExist->status !== 'PERIKSA') {
                return response()->json(['success' => false, 'message' => 'Hanya data transaksi dengan status PERIKSA yang dapat dilakukan pembayaran.']);
            }
            
            $interimId = $data->interim_id;
            
            $kembalian = PencatatanKas::where('is_aktif',true)->where('client_id',$this->clientId)->where('interim_id',$interimId)->where('jml_bayar','>',0)->first();

            DB::connection('dbclient')->beginTransaction();
            
            $success = PraktekDokter::where('is_aktif',true)->where('client_id',$this->clientId)->where('reg_id',$regId)->update(['is_bayar'=>true]);
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data.']);
            }

            $data->total_tagihan = $request->total_tagihan;
            $data->diskon = $request->diskon;
            $data->total_akhir = $request->total_akhir;
            $data->jml_bayar = $request->jml_bayar;
            $data->kembalian = $request->kembalian;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data.']);
            }
                
            foreach($request->detail as $dtl) {
                //$kasId = $this->getPencatatanKasId();
                $bayarId = $dtl['payment_id'];
                $pay = Pembayaran::where('payment_id',$bayarId)->where('is_aktif',true)->where('interim_id',$interimId)->where('client_id',$this->clientId)->first();
                                
                if(!$pay) {
                    $bayarId = $this->getPembayaranId();
                    $pay = new Pembayaran();
                    $pay->payment_id = $bayarId;
                    $pay->reg_id = $regId;
                    $pay->interim_id = $interimId;
                    $pay->tgl_bayar = date('Y-m-d H:i:s');
                    $pay->client_id = $this->clientId;
                    $pay->created_by = Auth::user()->username;
                }
                
                /** Tambah data Pembayaran */
                $pay->jns_payment = $dtl['jns_payment'];
                $pay->jml_bayar = $dtl['jml_bayar'];
                $pay->jenis_kartu = '';
                $pay->penjamin_id = '';
                $pay->penjamin_nama = '';
                $pay->mesin_edc = '';
                $pay->no_kartu = '';
                $pay->is_aktif = $dtl['is_aktif'];
                $pay->updated_by = Auth::user()->username;
                
                $success = $pay->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pembayaran gagal disimpan.']);
                }
                /** Tambah data Pencatatan Kas sebagai kas masuk */
                
                $kas = PencatatanKas::where('is_aktif',true)->where('client_id',$this->clientId)->where('kas_id',$bayarId)->first();
                if(!$kas) {
                    $kas = new PencatatanKas();
                    $kas->kas_id = $bayarId;
                    $kas->interim_id = $interimId;
                    $kas->tgl_transaksi = date('Y-m-d H:i:s');
                    $kas->jenis_transaksi = "Pemasukan";
                    $kas->client_id = $this->clientId;
                    $kas->created_by = Auth::user()->username;
                }
                
                // $kas->deskripsi = "Telah diterima pembayaran ".$dtl['jns_payment']." sebesar ". $dtl['jml_bayar'] . " dengan Kode Registrasi " . $regId;
                $kas->deskripsi = "Reg No. ".$regId." pembayaran ".$dtl['jns_payment']." sebesar ". $dtl['jml_bayar'];;

                $kas->metode_pembayaran = $dtl['jns_payment'];
                $kas->jml_bayar = 0;
                $kas->jml_terima = $dtl['jml_bayar'];
                //$kas->bukti_bayar = $path_url;    
                $kas->is_aktif = $dtl['is_aktif'];
                $kas->updated_by = Auth::user()->username;
                
                $success = $kas->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pencatatn kas gagal disimpan.']);
                }  
            }           
            //update atau tambahkan kembalian
            if(!$kembalian) {
                if($request->kembalian > 0) {
                    $bayarId = $this->getPembayaranId();
                    $kas = new PencatatanKas();
                    $kas->kas_id = $bayarId;
                    $kas->interim_id = $interimId;
                    $kas->tgl_transaksi = date('Y-m-d H:i:s');
                    $kas->jenis_transaksi = "Pengeluaran";
                    $kas->deskripsi = "Reg No. ".$regId." kembalian sebesar ". $request->kembalian;
                    $kas->metode_pembayaran = 'TUNAI';
                    $kas->jml_bayar = $request->kembalian;
                    $kas->jml_terima = 0;
                    $kas->is_aktif = true;
                    $kas->client_id = $this->clientId;
                    $kas->created_by = Auth::user()->username;
                    $kas->updated_by = Auth::user()->username;
                    
                    $success = $kas->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Pencatatan kas gagal disimpan.']);
                    }  

                    /** Tambah data Pembayaran */
                    $pay = new Pembayaran();
                    $pay->payment_id = $bayarId;
                    $pay->reg_id = $regId;
                    $pay->interim_id = $interimId;
                    $pay->tgl_bayar = date('Y-m-d H:i:s');
                    $pay->jns_payment = 'KEMBALIAN';
                    $pay->jml_bayar = 0 - $request->kembalian;
                    $pay->jenis_kartu = '';
                    $pay->penjamin_id = '';
                    $pay->penjamin_nama = '';
                    $pay->mesin_edc = '';
                    $pay->no_kartu = '';
        
                    $pay->is_aktif = true;
                    $pay->client_id = $this->clientId;
                    $pay->created_by = Auth::user()->username;
                    $pay->updated_by = Auth::user()->username;
                    
                    $success = $pay->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Kembalian gagal disimpan.']);
                    }
                }
            }

            else {
                $success = Pembayaran::where('client_id',$this->clientId)->where('is_aktif',true)->where('reg_id',$regId)->where('interim_id',$interimId)->where('jns_payment','KEMBALIAN')
                    ->update([
                        'jml_bayar' => (0 - $request->kembalian) , 
                        'updated_by'=>Auth::user()->username
                    ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pencatatn pembayaran gagal disimpan.']);
                }   

                $success = PencatatanKas::where('is_aktif',true)->where('client_id',$this->clientId)->where('interim_id',$interimId)->where('jml_bayar','>',0)
                    ->update([
                        'jml_bayar' => $request->kembalian, 
                        'updated_by'=>Auth::user()->username
                    ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Pencatatn kas gagal disimpan.']);
                }   
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function remove(Request $request) 
    {
        try {
            $regId = $request->reg_id;
            $interimId = $request->interim_id;
            $alasan = $request->alasan_batal;

            $data = PaymentPraktek::where('reg_id',$regId)->where('interim_id',$interimId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data pembayaran tidak ditemukan.']);
            }

            $pembayaran = Pembayaran::where('reg_id',$regId)->where('interim_id',$interimId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $kas = PencatatanKas::where('interim_id',$interimId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $praktek = PraktekDokter::where('reg_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->where('is_bayar',true)->first();

            DB::connection('dbclient')->beginTransaction();
            
            $success = PaymentPraktek::where('reg_id',$regId)->where('interim_id',$interimId)->where('is_aktif',1)->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false, 'alasan_batal'=> $alasan , 'updated_by'=>Auth::user()->username ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data gagal dihapus(1).']);
            }
            
            if($pembayaran) {
                $success = Pembayaran::where('reg_id',$regId)->where('interim_id',$interimId)->where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data gagal dihapus(2).']);
                }
            }

            if($kas) {
                $success = PencatatanKas::where('interim_id',$interimId)->where('is_aktif',1)->where('client_id',$this->clientId)
                    ->update(['is_aktif'=>false, 'alasan_batal'=> $alasan , 'updated_by'=>Auth::user()->username ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data gagal dihapus(3).']);
                }
            }

            if($praktek) {
                $success = PraktekDokter::where('reg_id',$regId)->where('is_aktif',1)->where('client_id',$this->clientId)->where('is_bayar',true)
                    ->update(['is_bayar'=>false, 'updated_by'=>Auth::user()->username ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data gagal dihapus(4).']);
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
            $id = $this->clientId.date('ym').'-PDM00001';
            $maxId = PaymentPraktek::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('interim_id', 'LIKE', $this->clientId.date('ym').'-PDM%')
                ->max('interim_id');

            if (!$maxId) { $id = $this->clientId.date('ym').'-PDM00001'; } 
            else {
                $maxId = str_replace($this->clientId.date('ym').'-PDM', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.date('ym').'-PDM0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.date('ym').'-PDM000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.date('ym').'-PDM00' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.date('ym').'-PDM0' . $count; } 
                else { $id = $this->clientId.date('ym').'-PDM' . $count; }
            }
            return $id;
        }
        catch(\Exception $e) {
            return null;
        }
    }

    public function getPencatatanKasId()
    {
        try {
            $id = $this->clientId.date('ymd').'-KAS00001';
            $maxId = PencatatanKas::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('kas_id', 'LIKE', $this->clientId.date('ymd').'-KAS%')
                ->max('kas_id');

            if (!$maxId) { $id = $this->clientId.date('ymd').'-KAS00001'; } 
            else {
                $maxId = str_replace($this->clientId.date('ymd').'-KAS', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.date('ymd').'-KAS0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.date('ymd').'-KAS000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.date('ymd').'-KAS00' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.date('ymd').'-KAS0' . $count; } 
                else { $id = $this->clientId.date('ymd').'-KAS' . $count; }
            }
            return $id;
        }
        catch(\Exception $e) {
            return null;
        }
    }

    public function cetakKwitansi($regId)
    {
        try {
            $data = PraktekDokter::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$regId)
                ->first();

            $detail = Pembayaran::where('reg_id',$data->reg_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->orderBy('tgl_bayar','ASC')
                ->get();

            $jml_bayar = 0;
            foreach($detail as $trans){
                $jml_bayar += $trans->jml_bayar;
            }


            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();

            $data['central']            = $central;
            $data['detail']             = $detail;
            $data['jml_bayar']          = $jml_bayar;
            $data['terbilang']          = $this->terbilang($jml_bayar);
            return view('report/praktekDokter/cetakanKwitansi',  compact('data'));
        }
        catch(\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data cetakan kwitansi', 'error' => $e->getMessage()]);
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
			$temp = " Seratus" . $this->terbilang($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->terbilang($nilai/100) . " Ratus" . $this->terbilang($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " Seribu" . $this->terbilang($nilai - 1000);
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
