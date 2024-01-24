<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;

use Modules\MasterData\Entities\Dokter;

use Modules\Pendaftaran\Entities\TrxOperasi;
use Modules\Pendaftaran\Entities\TrxOperasiTim;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use Modules\Penunjang\Entities\Persalinan;
use Modules\Penunjang\Entities\PersalinanBayi;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;

use Modules\MasterData\Entities\BedInap;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\Harga;
use Modules\MasterData\Entities\HargaDetail;

use RegistrasiHelper;

class OperasiController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function lists(Request $request)
    {
        try {
            // $keyword = '%%';
            // $per_page = 20;
            // $status = '%%';
            // $aktif = true;

            // if($request->has('keyword')) {
            //     $keyword = '%'.$request->input('keyword').'%';
            // }
            
            // if($request->has('is_aktif')) {
            //     $aktif = '%'.$request->input('is_aktif').'%';
            // }

            // if($request->has('status')) {
            //     $status = '%'.$request->input('status').'%';
            // }
            
            // if($request->has('per_page')) {
            //     $rowNumber = $request->input('per_page');
            //     if($rowNumber == 'ALL') { 
            //         $rowNumber = Operasi::count();
            //     }
            // }

            // $lists = Operasi::where('client_id',$this->clientId)
            //     ->where('is_aktif','ILIKE',$aktif)
            //     ->where('status','ILIKE',$status)                
            //     ->where(function($q) use ($keyword) {
            //         $q->where('unit_pengirim_id','ILIKE',$keyword)
            //         ->orWhere('unit_pengirim','ILIKE',$keyword)
            //         ->orWhere('nama_pasien','ILIKE',$keyword)
            //         ->orWhere('no_rm','ILIKE',$keyword)
            //         ->orWhere('pasien_id','ILIKE',$keyword);
            //     })->orderBy('tgl_operasi', 'DESC')->paginate($rowNumber);
    
            // return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);   
            $per_page = 20;
            $aktif = 'true';
            $keyword = '%%';
            
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = TrxOperasi::where('client_id',$this->clientId)->count(); }
            }

            $query = TrxOperasi::query();
            $query = $query->where('client_id',$this->clientId);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_operasi_start') && $request->has('tgl_operasi_end')) {
                $dtStart = $request->input('tgl_operasi_start').' 00:00:00';
                $dtEnd = $request->input('tgl_operasi_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_operasi', [$dtStart, $dtEnd]);
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
            $lists = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword)
                    ->orWhere('trx_id','ILIKE',$keyword);
                })->orderBy('tgl_operasi','ASC')
                ->orderBy('no_antrian','ASC')
                ->paginate($per_page);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);   
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }
   
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    // public function create(Request $request)
    // {
    //     try {
    //         $data = new TrxOperasi();

    //         $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
    //         if(!$pasien) {
    //             return response()->json(['success'=>false,'message'=>'Data pasien tidak ditemukan']);   
    //         }

    //         DB::connection('dbclient')->beginTransaction();
    //         $data->reg_id = $request->reg_id;
    //         $data->trx_id = $id;
    //         $data->sub_trx_id = $id;
    //         $data->jenis_tindakan = $request->jenis_tindakan; //cito atau elektif
    //         $data->skala_operasi = $request->skala_operasi; //khusus, kecil,besar, sedang 

    //         $data->pasien_id = $request->pasien_id;
    //         $data->no_rm = $pasien->no_rm;
    //         $data->nama_pasien = $pasien->nama_pasien;
    //         $data->tgl_lahir = $pasien->tgl_lahir;
    //         $data->jenis_kelamin = $pasien->jenis_kelamin;
            
    //         $data->unit_asal_id = $request->unit_asal_id;
    //         $data->unit_asal = $request->unit_asal;
            
    //         $data->tgl_booking = date('Y-m-d H:i:s');
    //         $data->tgl_operasi = $request->tgl_operasi;
    //         $data->jam_mulai = $request->jam_mulai;
    //         $data->tgl_selesai = $request->tgl_selesai;
    //         $data->jam_selesai = $request->jam_selesai;
    //         $data->ruang_id = $request->ruang_id;
    //         $data->ruang_nama = $request->ruang_nama;
                        
    //         $data->diag_pra_operasi = $request->diag_pra_operasi;
            
    //         $data->tindakan_operasi_id = $request->tindakan_operasi_id;
    //         $data->tindakan_operasi =  $request->tindakan_operasi;
    //         $data->jenis_anasthesi =  $request->jenis_anasthesi;
    //         $data->tgl_anasthesi =  $request->tgl_anasthesi;
    //         $data->jam_anasthesi =  $request->jam_anasthesi;
            
    //         $data->diag_pasca_operasi = $request->diag_pasca_operasi;
    //         $data->catatan_operasi =  $request->catatan_operasi;
    //         $data->laporan_operasi =  $request->laporan_operasi;
            
    //         $data->is_pa = $request->is_pa;
    //         $data->pa_trx_id = $request->pa_trx_id;
    //         $data->jaringan_eksisi_insisi= $request->jaringan_eksisi_insisi; //catatan jaringan yang di eksisi
    //         $data->is_operasi_tambahan =  $request->is_operasi_tambahan;
    //         $data->status = 'BOOKING';

    //         $data->is_meninggal = $request->is_meninggal;
    //         $data->ruang_recovery_id = $request->ruang_recovery_id;
    //         $data->ruang_recovery = $request->ruang_recovery;
            
    //         $data->is_aktif = true;
    //         $data->client_id = $this->clientId;
    //         $data->created_by = Auth::user()->username;
    //         $data->updated_by = Auth::user()->username;

    //         $success = $data->save();
    //         if(!$success) {
    //             DB::connection('dbclient')->rollBack();
    //             return response()->json(['success'=>false,'message'=>'Data operasi tidak berhasil disimpan']);  
    //         }

    //         foreach($request->tim as $tim) {
    //             $dokter = Dokter::where('dokter_id',$tim['dokter_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
    //             if(!$dokter) {
    //                 return response()->json(['success'=>false,'message'=>'Data dokter tidak ditemukan']);  
    //             }
    //             $timOperasi = new TrxOperasiTim();
    //             $timOperasi->tim_operasi_id = $id.Uuid::uuid4()->getHex();
    //             $timOperasi->reg_id = $request->reg_id;
    //             $timOperasi->trx_id = $id;
    //             $timOperasi->sub_trx_id = $id;
    //             $timOperasi->dokter_id = $tim['dokter_id'];
    //             $timOperasi->dokter_nama = $dokter->dokter_nama;
    //             $timOperasi->posisi = $tim['posisi'];
    //             $timOperasi->is_aktif = true;
    //             $timOperasi->client_id = $this->clientId;
    //             $timOperasi->created_by = Auth::user()->username;
    //             $timOperasi->updated_by = Auth::user()->username;
                
    //             $success = $timOperasi->save();
    //             if(!$success) {
    //                 DB::connection('dbclient')->rollBack();
    //                 return response()->json(['success'=>false,'message'=>'Data operasi tidak berhasil disimpan']);  
    //             }
    //         }

    //         DB::connection('dbclient')->commit();
    //         //ambil data untuk dikirim kembali
    //         $dataOps = TrxOperasi::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
    //         $dataOps['tim'] = TrxOperasiTim::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
    //         //$dataOps['detail'] = OperasiDetail::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
              
    //         return response()->json(['success'=>true,'message'=>'Data operasi berhasil disimpan','data'=>$dataOps]);
    //     }
    //     catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
    //     }
    // }

    public function create(Request $request)
    {
        /**
         * penyimpanan data registrasi operasi
         * JIKA : pendaftaran langsung maka dibuat 3 data
         * - Pendaftaran
         * - Trx Operasi
         * - Trx Tim Operasi
         * Jika referensi dari poli / rawat inap :
         * - Trx Operasi
         * - Trx Tim Operasi
         */
        try {
            $regId = null;
            $reffTrxId = $request->reff_trx_id;
            $isRujukanInt = false; 
            $trxId = RegistrasiHelper::instance()->OperasiTransactionId($this->clientId); 

            if($reffTrxId) {
                $pelayanan = Transaksi::where('trx_id',$reffTrxId)->where('client_id',$this->clientId)
                    ->where('is_aktif',1)->first();

                if(!$pelayanan) {
                    return response()->json(['success' => false, 'message' => 'Data referensi tidak ditemukan.']);
                }
                else { $regId = $pelayanan->reg_id; $isRujukanInt = true; }
            }

            else { $regId =  $trxId; $reffTrxId = $trxId; }

            //check apakah unit yang dipilih masih aktif dan ada
            $unit = UnitPelayanan::where('client_id', $this->clientId)->where('is_aktif',1)->where('unit_id', $request->unit_id)->first();
            if(!$unit) {
                return response()->json(['success' => false, 'message' => 'Data unit pelayanan tidak ditemukan.']);
            }

            //check apakah ruang yang dipilih masih aktif dan ada
            $ruang = RuangPelayanan::where('client_id', $this->clientId)
                ->where('is_aktif',1)
                ->where('unit_id', $request->unit_id)
                ->where('ruang_id', $request->ruang_id)
                ->first();
            if(!$ruang) {
                return response()->json(['success' => false, 'message' => 'Data ruang pelayanan tidak ditemukan.']);
            }

            //check apakah dokter ada dan aktif
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }

            //check penjamin apakah aktif / tidak
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            if(!$penjamin){
                return response()->json(['success' => false, 'message' => 'Data penjamin tidak ditemukan.']);
            }

            //check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }
            
            // input tb_registrasi
            $noAntrian = $this->getNoAntrian($request->tgl_operasi);

            DB::connection('dbclient')->beginTransaction();            
            /*** simpan data transaksi operasi*/
            $ops = new TrxOperasi();
            $ops->reg_id = $regId;
            $ops->trx_id = $trxId;
            // $ops->sub_trx_id = $trxId;
            $ops->reff_trx_id = $reffTrxId;

            $ops->no_antrian = $noAntrian;

            $ops->is_rujukan_int = $isRujukanInt;
            $ops->tgl_transaksi = date('Y-m-d H:i:s');
            $ops->tgl_operasi = $request->tgl_operasi;
            $ops->jam_operasi = $request->jam_operasi;            
            
            $ops->tgl_selesai = $request->tgl_selesai;
            $ops->jam_selesai = $request->jam_selesai;            
            
            $ops->tgl_anestesi = $request->tgl_anestesi;
            $ops->jam_anestesi = $request->jam_anestesi;            
            
            //data pasien
            $ops->pasien_id = $pasien->pasien_id;
            $ops->nama_pasien = $pasien->nama_lengkap;
            $ops->no_rm = $pasien->no_rm;
            $ops->tgl_lahir = $request->tgl_lahir;
            $ops->tempat_lahir = $request->tempat_lahir;            
            $ops->jns_kelamin = $request->jns_kelamin;            
            $ops->nik = $request->nik;            
            
            //dokter lab dan pengirim
            $ops->dokter_id = $request->dokter_id;
            $ops->dokter_nama = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $ops->dokter_pengirim_id = $request->dokter_pengirim_id;
            $ops->dokter_pengirim = $request->dokter_pengirim;
            
            //data unit lab            
            $ops->unit_id = $request->unit_id;
            $ops->unit_nama = $unit->unit_nama;
            $ops->ruang_id = $request->ruang_id;
            $ops->ruang_nama = $ruang->ruang_nama;
            $ops->bed_id = $request->bed_id;
            $ops->no_bed = $request->no_bed;

            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $ops->unit_asal_id = $request->unit_asal_id;
            $ops->unit_asal = $request->unit_asal;
            $ops->ruang_asal_id = $request->ruang_asal_id;
            $ops->ruang_asal = $request->ruang_asal;
            $ops->bed_asal_id = $request->bed_asal_id;
            $ops->no_asal_bed = $request->no_asal_bed;

            $ops->asal_pasien = $request->asal_pasien;
            
            $ops->penjamin_id = $request->penjamin_id;
            $ops->penjamin_nama = $penjamin->penjamin_nama;
            $ops->no_kepesertaan = $request->no_kepesertaan;
            $ops->jns_penjamin = $penjamin->jns_penjamin;
            $ops->is_bpjs = $penjamin->is_bpjs;

            $ops->kelas_harga_id = $request->kelas_harga_id;
            $ops->kelas_harga = $request->kelas_harga;
            $ops->buku_harga_id = $penjamin->buku_harga_id;
            $ops->buku_harga = $penjamin->buku_harga;
            
            $ops->diagnosa_pra = $request->diagnosa_pra;
            $ops->diagnosa_pasca = $request->diagnosa_pasca;
            $ops->tindakan_operasi_id = $request->tindakan_id;
            $ops->tindakan_operasi = $request->tindakan_nama;
            $ops->skala_operasi = $request->skala_operasi;
            $ops->jenis_operasi = $request->jenis_operasi;
           
            $ops->jns_registrasi = $request->jns_registrasi;
            $ops->cara_registrasi = $request->cara_registrasi;

            $ops->status = 'DAFTAR';
            $ops->is_aktif = true;
            $ops->is_realisasi = false;
            $ops->is_persalinan = $request->is_persalinan;
            
            $ops->client_id = $this->clientId;
            $ops->created_by = Auth::user()->username;
            $ops->updated_by = Auth::user()->username;
            
            $success = $ops->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data jadwal operasi gagal disimpan.']);
            } 
            /**
             * simpan transaksi detail
             */
            $total = 0;
            foreach($request->tim_operasi as $tim) {                
                /**
                 * create transaksi operasi tim
                 */
                $detailId = $trxId.Uuid::uuid4()->getHex();
                $detail = new TrxOperasiTim();
                $detail->operasi_tim_id = $detailId;
                $detail->reg_id = $regId;
                $detail->trx_id = $trxId;
                //$detail->sub_trx_id = $trxId;
                
                $detail->dokter_id = $tim['dokter_id'];
                $detail->dokter_nama = $tim['dokter_nama'];
                $detail->posisi = $tim['posisi'];
                $detail->is_operator = $tim['is_operator'];
                
                $detail->is_aktif = true;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data tim operasi gagal disimpan.']);
                }
            }

            /**
             * create data transaksi
             */
            $transaksi = new Transaksi();
            $transaksi->reg_id = $regId;
            $transaksi->trx_id = $trxId;
            $transaksi->reff_trx_id = $reffTrxId;
            //$transaksi->sub_trx_id = $reffTrxId;
            // if($transaksi->sub_trx_id == null){
            //     $transaksi->is_sub_trx = false;
            // }
            // $transaksi->is_sub_trx = true;
            $transaksi->status = 'DAFTAR';
            $transaksi->is_realisasi = false;
            $transaksi->is_bayar = false;
            $transaksi->is_aktif = true;
            $transaksi->client_id = $this->clientId;
            $transaksi->created_by = Auth::user()->username;
            $transaksi->updated_by = Auth::user()->username;
            $transaksi->jns_transaksi = 'OPERASI';
        
            $transaksi->tgl_transaksi = $request->tgl_operasi .':'. $request->jam_operasi;
            $transaksi->no_antrian = $noAntrian;
            $transaksi->no_transaksi = 'TRX/'.date('Ymd').'/'.$noAntrian;
            
            $transaksi->penjamin_id = $request->penjamin_id;
            $transaksi->penjamin_nama = $penjamin->penjamin_nama;
            
            $transaksi->kelas_id = $request->kelas_harga;
            $transaksi->kelas_harga_id = $request->kelas_harga_id;
            //$transaksi->kelas_harga = $request->kelas_harga;
            $transaksi->kelas_penjamin_id = $request->kelas_penjamin_id;            
            $transaksi->buku_harga_id = $penjamin->buku_harga_id;
            $transaksi->buku_harga = $penjamin->buku_harga;
            $transaksi->total = $request->total;
            
            $transaksi->unit_id = $request->unit_id;
            $transaksi->unit_nama = $unit->unit_nama;
            $transaksi->ruang_id = $request->ruang_id;
            $transaksi->ruang_nama = $ruang->ruang_nama;
            
            $transaksi->dokter_id = $request->dokter_id;
            $transaksi->dokter_nama = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim = $dokter->dokter_pengirim;
            
            $transaksi->unit_pengirim_id = $request->unit_pengirim_id;
            $transaksi->unit_pengirim = $dokter->unit_pengirim;
            
            $transaksi->pasien_id = $request->pasien_id;
            $transaksi->no_rm = $pasien->no_rm;
            $transaksi->nama_pasien = $request->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }
            
            DB::connection('dbclient')->commit();

            $data = TrxOperasi::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('trx_id',$trxId)
                ->first();

            $data['tim_operasi'] = TrxOperasiTim::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)
                ->where('trx_id',$trxId)
                ->get();

            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan registrasi. Error : ' . $e->getMessage()]);
        }
    }    

    /**
     * Show the specified resource.
     * @param int $id
     */
    public function data(Request $request, $trxId)
    {
        try {
           
            $data = TrxOperasi::leftJoin('tb_pasien', 'tb_trx_operasi.pasien_id', '=', 'tb_pasien.pasien_id')
                ->leftJoin('tb_pasien_alamat', 'tb_pasien.pasien_id', '=', 'tb_pasien_alamat.pasien_id')
                ->where('tb_trx_operasi.is_aktif',1)
                ->where('tb_trx_operasi.client_id',$this->clientId)
                ->where('tb_trx_operasi.trx_id',$trxId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data. Data tidak ditemukan']);
            }

            $data['tim_operasi'] = TrxOperasiTim::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$trxId)
                ->get();

            $data['transaksi'] = Transaksi::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->first();
                    
            $data['detail'] = TransaksiDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->get(); 

            $persalinan = Persalinan::where('client_id',$this->clientId)
                    ->where('trx_id',$data->trx_id)
                    ->where('reg_id',$data->reg_id)
                    ->where('is_aktif',1)
                    ->first();

            if($persalinan) {
                $persalinan['detail'] = PersalinanBayi::where('client_id',$this->clientId)->where('is_aktif',1)->where('persalinan_id', $persalinan->persalinan_id)->get();
                $data['persalinan'] = $persalinan;
            }

            else {
                $persalinan['persalinan_id'] = null;
                $persalinan['reg_id'] = $data->reg_id;
                $persalinan['trx_id'] = $data->trx_id;                
                $persalinan['detail'] = [];
                $data['persalinan'] = $persalinan;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        try {
            $id = $request->trx_id;

            $data = TrxOperasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$id)->first();
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'Data operasi tidak ditemukan']);   
            }

            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success'=>false,'message'=>'Data pasien tidak ditemukan']);   
            }

            DB::connection('dbclient')->beginTransaction();
            $data->jenis_tindakan = $request->jenis_tindakan; //cito atau elektif
            $data->skala_operasi = $request->skala_operasi; //khusus, kecil,besar, sedang 
            $data->pasien_id = $request->pasien_id;
            $data->no_rm = $pasien->no_rm;
            $data->nama_pasien = $pasien->nama_pasien;
            $data->tgl_lahir = $pasien->tgl_lahir;
            $data->jns_kelamin = $pasien->jenis_kelamin;
            $data->unit_asal_id = $request->unit_asal_id;
            $data->unit_asal = $request->unit_asal;
            
            $data->tgl_booking = date('Y-m-d H:i:s');
            $data->tgl_operasi = $request->tgl_operasi;
            $data->jam_mulai = $request->jam_mulai;
            $data->tgl_selesai = $request->tgl_selesai;
            $data->jam_selesai = $request->jam_selesai;
            $data->ruang_id = $request->ruang_id;
            $data->ruang_nama = $request->ruang_nama;
            //$data->diag_pra_operasi = $request->diag_pra_operasi;
            $data->diagnosa_pra = $request->diagnosa_pra;
            
            $data->tindakan_operasi_id = $request->tindakan_operasi_id;
            $data->tindakan_operasi =  $request->tindakan_operasi;

            $data->jenis_anasthesi =  $request->jenis_anasthesi;
            $data->tgl_anasthesi =  $request->tgl_anasthesi;
            $data->jam_anasthesi =  $request->jam_anasthesi;
            
            // $data->diag_pasca_operasi = $request->diag_pasca_operasi;
            $data->diagnosa_pasca = $request->diagnosa_pasca;
            
            $data->catatan_operasi =  $request->catatan_operasi;
            $data->laporan_operasi =  $request->laporan_operasi;
            // $data->is_pa = $request->is_pa;
            // $data->pa_trx_id = $request->pa_trx_id;
            // $data->jaringan_eksisi_insisi= $request->jaringan_eksisi_insisi; //catatan jaringan yang di eksisi
            $data->is_operasi_tambahan =  $request->is_operasi_tambahan;
            $data->status = $request->status;
            $data->is_meninggal = $request->is_meninggal;
            $data->is_persalinan = $request->is_persalinan;
            $data->ruang_recovery_id = $request->ruang_recovery_id;
            $data->ruang_recovery = $request->ruang_recovery;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'Data operasi tidak berhasil disimpan']);  
            }

            foreach($request->tim_operasi as $tim) {
                $dokter = Dokter::where('dokter_id',$tim['dokter_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
                if(!$dokter) {
                    return response()->json(['success'=>false,'message'=>'Data dokter tidak ditemukan']);  
                }

                //$timOperasi = TrxOperasiTim::where('client_id',$this->clientId)->where('tim_operasi_id',$tim['tim_operasi_id'])->where('is_aktif',1)->first();
                $timOperasi = TrxOperasiTim::where('client_id',$this->clientId)->where('operasi_tim_id',$tim['operasi_tim_id'])->where('is_aktif',1)->first();
                if(!$timOperasi) {
                    $timOperasi = new TrxOperasiTim();
                    //$timOperasi->tim_operasi_id = $id.Uuid::uuid4()->getHex();
                    $timOperasi->operasi_tim_id = $id.Uuid::uuid4()->getHex();
                    $timOperasi->reg_id = $request->reg_id;
                    $timOperasi->trx_id = $id;
                    // $timOperasi->sub_trx_id = $id;
                    $timOperasi->is_aktif = true;
                    $timOperasi->client_id = $this->clientId;
                    $timOperasi->created_by = Auth::user()->username;
                }
                else {
                    $timOperasi->is_aktif = $tim['is_aktif'];
                }
                
                $timOperasi->dokter_id = $tim['dokter_id'];
                $timOperasi->dokter_nama = $dokter->dokter_nama;
                $timOperasi->posisi = $tim['posisi'];
                $timOperasi->updated_by = Auth::user()->username;
                
                $success = $timOperasi->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'Data operasi tidak berhasil diubah']);  
                }
            }


            if($data->is_persalinan) {
                $persalinanId = $this->getPersalinanId();
                $persalinan = Persalinan::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$data->reg_id)->where('trx_id',$data->trx_id)->first();
                if(!$persalinan) {
                    $persalinan = new Persalinan();
                    $persalinan->persalinan_id = $this->getPersalinanId();
                    $persalinan->reg_id = $data->reg_id;
                    $persalinan->trx_id = $data->trx_id;
                    $persalinan->sub_trx_id = $data->trx_id;
                    $persalinan->pasien_id = $pasien->pasien_id;
                    $persalinan->nama_pasien = $pasien->nama_lengkap;
                    $persalinan->no_rm = $pasien->no_rm;
                    $persalinan->nik_pasien = $pasien->nik;                    
                    $persalinan->created_by = Auth::user()->username;
                    $persalinan->is_aktif = true;
                    $persalinan->client_id = $this->clientId;
                }

                else {
                    $persalinanId = $persalinan->persalinan_id;
                }
                
                $persalinan->tgl_kelahiran = $request->persalinan['tgl_kelahiran'];
                $persalinan->jam_kelahiran = $request->persalinan['jam_kelahiran'];
                $persalinan->umur_kehamilan = $request->persalinan['umur_kehamilan'];
                $persalinan->jenis_persalinan = $request->persalinan['jenis_persalinan'];
                $persalinan->kelahiran_ke = $request->persalinan['kelahiran_ke'];
                $persalinan->kondisi_ibu = $request->persalinan['kondisi_ibu'];
                
                $persalinan->nama_ayah_bayi = $request->persalinan['nama_ayah_bayi'];
                $persalinan->nik_ayah_bayi = $request->persalinan['nik_ayah_bayi'];
                $persalinan->alamat = '';//$request->persalinan['alamat'];                
                $persalinan->updated_by = Auth::user()->username;
                $success = $persalinan->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data persalinan gagal disimpan.']);
                }

                foreach($request->persalinan['detail'] as $ps) {
                    $detail = PersalinanBayi::where('client_id',$this->clientId)->where('persalinan_bayi_id',$ps['persalinan_bayi_id'])->first();
                    if(!$detail) {
                        $detail = new PersalinanBayi();
                        $detail->persalinan_bayi_id = $this->getPersalinanBayiId();
                        $detail->persalinan_id = $persalinanId;
                        $detail->reg_id = $data->reg_id;
                        $detail->trx_id = $data->trx_id;
                        $detail->sub_trx_id = $data->trx_id; 
                        $detail->client_id = $this->clientId;
                        $detail->created_by = Auth::user()->username;
                    }
                    $detail->tgl_kelahiran = $request->persalinan['tgl_kelahiran'];
                    $detail->jam_kelahiran = $request->persalinan['jam_kelahiran'];
                    $detail->umur_kehamilan = $request->persalinan['umur_kehamilan'];
                    $detail->jenis_persalinan = $request->persalinan['jenis_persalinan'];
                    $detail->kelahiran_ke = $request->persalinan['kelahiran_ke'];
                    $detail->kondisi_ibu = $request->persalinan['kondisi_ibu'];
                    $detail->pasien_id_bayi = $ps['pasien_id_bayi'];
                    $detail->pasien_id_ibu = $data->pasien_id;
                    $detail->nama_bayi = $ps['nama_bayi'];
                    $detail->no_rm_bayi = $ps['no_rm_bayi'];
                    $detail->jk_bayi = $ps['jk_bayi'];
                    $detail->bb_bayi = $ps['bb_bayi'];
                    $detail->tb_bayi = $ps['tb_bayi'];
                    $detail->lingkar_kepala = $ps['lingkar_kepala'];
                    $detail->lingkar_dada = $ps['lingkar_dada'];
                    $detail->frekuensi_napas = $ps['frekuensi_napas'];
                    $detail->detak_jantung = $ps['detak_jantung'];
                    $detail->kondisi_lahir = $ps['kondisi_lahir'];

                    $detail->is_aktif = $ps['is_aktif'];
                    $detail->updated_by = Auth::user()->username;
                    $success = $detail->save();
                }
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data persalinan gagal disimpan.']);
                }
                
            }

            DB::connection('dbclient')->commit();

            //ambil data untuk dikirim kembali
            $dataOps = TrxOperasi::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $dataOps['tim_operasi'] = TrxOperasiTim::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            $dataOps['transaksi'] = Transaksi::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->first();
                
            $dataOps['detail'] = TransaksiDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->get(); 

            return response()->json(['success'=>true,'message'=>'Data operasi berhasil diubah','data'=>$dataOps]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id)
    {
        try {   
            $data = TrxOperasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$id)->first();
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'Data operasi tidak ditemukan']);   
            }
            $tim = TimOperasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$id)->first();
            $detail = OperasiDetail::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $success =  Operasi::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('trx_id',$id)
                ->update(['is_aktif'=>false,'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'Data tim operasi tidak berhasil diubah']);  
            }

            if($tim) {
                $success = TimOperasi::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('trx_id',$id)
                    ->update(['is_aktif'=>false,'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'Data tim operasi tidak berhasil dihapus']);  
                }
            }

            if($detail) {
                $success = OperasiDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('trx_id',$id)
                    ->update(['is_aktif'=>false,'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'Data detail operasi tidak berhasil dihapus']);  
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'Data operasi berhasil dihapus']);  
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update status the specified resource.
     * @param int $id
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $data = TrxOperasi::where('trx_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data operasi tidak ditemukan.']);
            }
            $tim = TrxOperasiTim::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$id)->first();
            $detail = OperasiDetail::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $success = TrxOperasi::where('trx_id',$id)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->update(['status'=>$request->status,'updated_by'=>Auth::user()->username]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'Data operasi tidak berhasil diubah status']);  
            } 
            
            if($tim) {
                $success = TrxOperasiTim::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('trx_id',$id)
                    ->update(['updated_by'=>Auth::user()->username, 'status'=>$request->status]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'Data tim operasi tidak berhasil diubah status']);  
                }
            }

            if($detail) {
                $success = OperasiDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('trx_id',$id)
                    ->update(['updated_by'=>Auth::user()->username, 'status'=>$request->status]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'Data detail operasi tidak berhasil diubah status']);  
                }
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data operasi diubah status.', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah status data', 'error' => $e->getMessage()]);
        }
    }

    public function confirm(Request $request)
    {
        try {
            $trxId = $request->trx_id;
            $regId = $request->reg_id;
            
            $data = TrxOperasi::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data jadwal operasi tidak ditemukan / sudah berubah status.']);
            }
            
            /**check transaksi */
            $transaksi = Transaksi::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)->where('client_id',$this->clientId)
                ->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**update data rawat inap */
            $success = TrxOperasi::where('trx_id',$trxId)->where('reg_id',$regId)
                ->where('pasien_id',$request->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('status','DAFTAR')
                ->update(['status' => 'PROSES', 'updated_by' => Auth::user()->username]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data operasi gagal diproses.']);
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi jadwal operasi berhasil disimpan', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengkonfirmasi jadwal operasi. Error : ' . $e->getMessage()]);
        }
    }

    public function getPersalinanId()
    {
        try {
            $id = $this->clientId . '.LBR-0001';
            $maxId = Persalinan::withTrashed()->where('persalinan_id', 'LIKE', $this->clientId . '.LBR-%')->max('persalinan_id');
            if (!$maxId) {
                $id = $this->clientId . '.LBR-0001';
            } else {
                $maxId = str_replace($this->clientId . '.LBR-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.LBR-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.LBR-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.LBR-0' . $count;
                } else {
                    $id = $this->clientId . '.LBR-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.LBR-' . Uuid::uuid4()->getHex();
        }
    }

    public function getPersalinanBayiId()
    {
        try {
            $id = $this->clientId . '.DLBR-0001';
            $maxId = PersalinanBayi::withTrashed()->where('persalinan_bayi_id', 'LIKE', $this->clientId . '.DLBR-%')->max('persalinan_bayi_id');
            if (!$maxId) {
                $id = $this->clientId . '.DLBR-0001';
            } else {
                $maxId = str_replace($this->clientId . '.DLBR-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.DLBR-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.DLBR-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.DLBR-0' . $count;
                } else {
                    $id = $this->clientId . '.DLBR-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.DLBR-' . Uuid::uuid4()->getHex();
        }
    }

    public function getNoAntrian($tglPeriksa)
    {
        try {
            $id = 'OPS001';
            $maxNo = TrxOperasi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('tgl_operasi', $tglPeriksa)->max('no_antrian');

            if (!$maxNo) { $id = 'OPS001'; } 
            else {
                $maxNo = str_replace('OPS', '', $maxNo);
                $count = $maxNo + 1;
                if ($count < 10) { $id = 'OPS00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'OPS0' . $count; } 
                else { $id = 'OPS' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return Uuid::uuid4()->getHex();
        }
    }
}
