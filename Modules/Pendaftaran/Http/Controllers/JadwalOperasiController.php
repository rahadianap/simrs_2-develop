<?php

namespace Modules\Pendaftaran\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon;
use Modules\Pendaftaran\Entities\Pendaftaran;
use Modules\Pendaftaran\Entities\RegPasien;
use Modules\Pendaftaran\Entities\RegPenjamin;
use Modules\Pendaftaran\Entities\SEP;
use Modules\Pendaftaran\Entities\RawatInap;
use Modules\Pendaftaran\Entities\PemakaianBed;
use Modules\Pendaftaran\Entities\DpjpLog;
use Modules\Pendaftaran\Entities\PenjaminLog;

use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\BedInap;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\Harga;
use Modules\MasterData\Entities\HargaDetail;

use Modules\Pendaftaran\Entities\TrxOperasi;
use Modules\Pendaftaran\Entities\TrxOperasiTim;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use Modules\Penunjang\Entities\Persalinan;
use Modules\Penunjang\Entities\PersalinanBayi;

use RegistrasiHelper;

class JadwalOperasiController extends Controller
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
     */
    public function lists(Request $request)
    {
        try {
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
            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword)
                    ->orWhere('trx_id','ILIKE',$keyword);
                })->orderBy('tgl_operasi','ASC')
                ->orderBy('no_antrian','ASC')
                ->paginate($per_page);
            
            foreach($list->items() as $dt) {
                $dt['transaksi'] = Transaksi::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('trx_id',$dt->trx_id)
                    ->where('reg_id',$dt->reg_id)
                    ->first();
                      
                $dt['detail'] = TransaksiDetail::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('trx_id',$dt->trx_id)
                    ->where('reg_id',$dt->reg_id)
                    ->get(); 

                $dt['tim_operasi'] = TrxOperasiTim::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('trx_id',$dt->trx_id)
                    ->where('reg_id',$dt->reg_id)
                    ->get(); 

                $dt['persalinan'] = Persalinan::leftJoin('tb_persalinan_bayi', 'tb_persalinan.persalinan_id', '=', 'tb_persalinan_bayi.persalinan_id')
                    ->leftJoin('tb_pasien', 'tb_persalinan.no_rm_bayi', '=', 'tb_pasien.no_rm')
                    ->where('tb_persalinan.is_aktif',1)
                    ->where('tb_persalinan.client_id',$this->clientId)
                    ->where('tb_persalinan.trx_id',$dt->trx_id)
                    ->where('tb_persalinan.reg_id',$dt->reg_id)
                    ->get();
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
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
            $ops->sub_trx_id = $trxId;
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
            $ops->no_bed_asal = $request->no_asal_bed;

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
            $ops->tindakan_id = $request->tindakan_id;
            $ops->tindakan_nama = $request->tindakan_nama;
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
                $detail->sub_trx_id = $trxId;
                
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
            $transaksi->sub_trx_id = $reffTrxId;
            if($transaksi->sub_trx_id == null){
                $transaksi->is_sub_trx = false;
            }
            $transaksi->is_sub_trx = true;
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

    // public function getRegId()
    // {
    //     try {
    //         $id = $this->clientId.'-'.date('Ymd').'-REG00001';
    //         $maxId = Pendaftaran::withTrashed()->where('client_id', $this->clientId)->where('reg_id', 'ILIKE', $this->clientId.'-'.date('Ymd').'-REG%')->max('reg_id');
    //         if (!$maxId) {
    //             $id = $this->clientId.'-'.date('Ymd').'-REG00001';
    //         } 
    //         else {
    //             $maxId = str_replace($this->clientId.'-'.date('Ymd').'-REG', '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-REG0000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-REG000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-REG00' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-REG0' . $count; } 
    //             else { $id = $this->clientId.'-'.date('Ymd').'-REG' . $count; }
    //         }
    //         return $id;
    //     } 
    //     catch (\Exception $e) {
    //         return $this->clientId . date('Ymd') . '-REG' . Uuid::uuid4()->getHex();
    //     }
    // }

    // public function getTransactionId()
    // {
    //     try {
    //         $id = $this->clientId.'-'.date('Ym').'-OPS00001';
    //         $maxId = TrxOperasi::withTrashed()->where('client_id', $this->clientId)->where('trx_id', 'ILIKE', $this->clientId.'-'.date('Ym').'-OPS%')->max('trx_id');
    //         if (!$maxId) {
    //             $id = $this->clientId.'-'.date('Ym').'-OPS00001';
    //         } 
    //         else {
    //             $maxId = str_replace($this->clientId.'-'.date('Ym').'-OPS', '', $maxId);
    //             $count = $maxId + 1;

    //             if ($count < 10) { $id = $this->clientId.'-'.date('Ym').'-OPS0000' . $count; } 
    //             elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ym').'-OPS000' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-OPS00' . $count; } 
    //             elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ym').'-OPS0' . $count; } 
    //             else { $id = $this->clientId.'-'.date('Ym').'-OPS' . $count; }
    //         }
    //         return $id;
    //     } 
    //     catch (\Exception $e) {
    //         return $this->clientId . date('Ym') . '-OPS' . Uuid::uuid4()->getHex();
    //     }
    // }

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

            // $data['tim_operasi'] = TrxOperasiTim::where('is_aktif',1)
            //     ->where('client_id',$this->clientId)
            //     ->where('reg_id',$data->reg_id)
            //     ->where('trx_id',$trxId)
            //     ->get();


            $data['transaksi'] = Transaksi::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$dt->trx_id)
                ->where('reg_id',$dt->reg_id)
                ->first();
                    
            $data['detail'] = TransaksiDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$dt->trx_id)
                ->where('reg_id',$dt->reg_id)
                ->get(); 

            $data['tim_operasi'] = TrxOperasiTim::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('trx_id',$dt->trx_id)
                ->where('reg_id',$dt->reg_id)
                ->get(); 

            $data['persalinan'] = Persalinan::leftJoin('tb_persalinan_bayi', 'tb_persalinan.persalinan_id', '=', 'tb_persalinan_bayi.persalinan_id')
                ->leftJoin('tb_pasien', 'tb_persalinan.no_rm_bayi', '=', 'tb_pasien.no_rm')
                ->where('tb_persalinan.is_aktif',1)
                ->where('tb_persalinan.client_id',$this->clientId)
                ->where('tb_persalinan.trx_id',$dt->trx_id)
                ->where('tb_persalinan.reg_id',$dt->reg_id)
                ->get();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data. Error : ' . $e->getMessage()]);
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
            $regId = $request->reg_id;
            $trxId = $request->trx_id;
            $isRujukanInt = $request->is_rujukan_int; //perhatikan bagian ini karena akan menjadi krusial

            //Check data exist
            $data = TrxOperasi::where('client_id',$this->clientId)->where('reg_id',$regId)
                ->where('trx_id',$trxId)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data operasi tidak ditemukan.']);
            }
            
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

            // $reg = Pendaftaran::where('is_aktif',1)->where('client_id',$this->clientId)
            //     ->where('reg_id',$regId)->where('pasien_id',$request->pasien_id)
            //     ->first();
            // if(!$reg) {
            //     return response()->json(['success' => false, 'message' => 'Data referensi pelayanan tidak ditemukan.']);
            // }
            
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
            $noAntrian = $data->no_antrian;
            if($data->tgl_operasi !== $request->tgl_operasi || $noAntrian ==  null) {
                $noAntrian = $this->getNoAntrian($request->tgl_operasi);
            }
            
            DB::connection('dbclient')->beginTransaction();
            // if(!$isRujukanInt) {
            //     $success = Pendaftaran::where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)
            //         ->where('pasien_id',$request->pasien_id)
            //         ->update([
            //             'tgl_periksa'       => $request->tgl_periksa,    
            //             'jns_registrasi'    => $request->jns_registrasi,
            //             'cara_registrasi'   => $request->cara_registrasi,
            //             'kode_booking'      => '-',
            //             'no_antrian'        => $noAntrian,                        
            //             'jadwal_id'         => null,
            //             'dokter_id'         => $request->dokter_id,
            //             'unit_id'           => $request->unit_id,
            //             'ruang_id'          => $request->ruang_id,
            //             'bed_id'            => '-',                                    
            //             'asal_pasien'       => $request->asal_pasien,
            //             'ket_asal_pasien'   => $request->ket_asal_pasien,            
            //             'pasien_id'         => $request->pasien_id,
            //             'jns_penjamin'      => $request->jns_penjamin,
            //             'penjamin_id'       => $request->penjamin_id,                        
            //             'nama_penanggung'   => $request->nama_penanggung,
            //             'hub_penanggung'    => $request->hub_penanggung,
            //             'is_bpjs'           => $penjamin->is_bpjs,
            //             'updated_by'        => Auth::user()->username,            
            //         ]);
                    
            //     if (!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data registrasi']);
            //     }
    
            //     // input tb_registrasi_pasien
            //     $pasienAlamat = PasienAlamat::where('pasien_id',$pasien->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            //     /** hitung usia **/
            //     $tgl_lahir = $pasien->tgl_lahir;
            //     $tgl_lahir = explode("-", $tgl_lahir);
            //     $usia = (date("md", date("U", mktime(0, 0, 0, $tgl_lahir[1], $tgl_lahir[2], $tgl_lahir[0]))) > date("md")
            //         ? ((date("Y") - $tgl_lahir[0]) - 1)
            //         : (date("Y") - $tgl_lahir[0]));
                
            //     $regPasien = RegPasien::where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->where('reg_id',$regId)
            //         ->where('pasien_id',$request->pasien_id)->first();
                
            //     if($regPasien) {                    
            //         $regPasien->is_pasien_luar = $pasien->is_pasien_luar;
            //         $regPasien->no_rm = $pasien->no_rm;
            //         $regPasien->nama_pasien = $pasien->nama_lengkap;
            //         $regPasien->tempat_lahir = $pasien->tempat_lahir;
            //         $regPasien->tgl_lahir = $pasien->tgl_lahir;
            //         $regPasien->usia = $usia;
            //         $regPasien->jns_kelamin = $pasien->jns_kelamin;
        
            //         $regPasien->propinsi_id = null;
            //         $regPasien->kota_id = null;
            //         $regPasien->kecamatan_id = null;
            //         $regPasien->kelurahan_id = null;
            //         $regPasien->kodepos = null;
        
            //         if($pasienAlamat) {
            //             $regPasien->propinsi_id = $pasienAlamat->propinsi_id;
            //             $regPasien->kota_id = $pasienAlamat->kota_id;
            //             $regPasien->kecamatan_id = $pasienAlamat->kecamatan_id;
            //             $regPasien->kelurahan_id = $pasienAlamat->kelurahan_id;
            //             $regPasien->kodepos = $pasienAlamat->kodepos;
            //         }
        
            //         $regPasien->is_hamil = $request->is_hamil;
            //         $regPasien->is_pasien_baru = $pasien->is_pasien_baru;
            //         $regPasien->is_aktif = true;
            //         $regPasien->client_id = $this->clientId;
            //         $regPasien->created_by = Auth::user()->username;
            //         $regPasien->updated_by = Auth::user()->username;
                    
            //         $success = $regPasien->save();
            //         if(!$success) {
            //             DB::connection('dbclient')->rollBack();
            //             return response()->json(['success' => false, 'message' => 'Data pasien luar gagal disimpan.']);
            //         }
            //     }
            // }
            
            /*** simpan data transaksi operasi*/
            $data->no_antrian = $noAntrian;
            $data->is_rujukan_int = $isRujukanInt;
            $data->tgl_operasi = $request->tgl_operasi;
            $data->jam_operasi = $request->jam_operasi;
            
            $data->tgl_selesai = $request->tgl_selesai;
            $data->jam_selesai = $request->jam_selesai;            
            
            $data->tgl_anestesi = $request->tgl_anestesi;
            $data->jam_anestesi = $request->jam_anestesi;         

            //data pasien
            $data->pasien_id = $pasien->pasien_id;
            $data->nama_pasien = $pasien->nama_lengkap;
            $data->no_rm = $pasien->no_rm;
            $data->tgl_lahir = $request->tgl_lahir;
            $data->tempat_lahir = $request->tempat_lahir;            
            $data->jns_kelamin = $request->jns_kelamin;            
            $data->nik = $request->nik;            
            
            //dokter lab dan pengirim
            $data->dokter_id = $request->dokter_id;
            $data->dokter_nama = $dokter->dokter_nama;
            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $data->dokter_pengirim_id = $request->dokter_pengirim_id;
            $data->dokter_pengirim = $request->dokter_pengirim;
            
            //data unit lab            
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $unit->unit_nama;
            $data->ruang_id = $request->ruang_id;
            $data->ruang_nama = $ruang->ruang_nama;
            $data->bed_id = $request->bed_id;
            $data->no_bed = $request->no_bed;

            //diisi jika pasien adalah rujukan dari rawat inap atau poli
            $data->unit_asal_id = $request->unit_asal_id;
            $data->unit_asal = $request->unit_asal;
            $data->ruang_asal_id = $request->ruang_asal_id;
            $data->ruang_asal = $request->ruang_asal;
            $data->bed_asal_id = $request->bed_asal_id;
            $data->no_bed_asal = $request->no_asal_bed;

            $data->asal_pasien = $request->asal_pasien;
            $data->reff_trx_id = $request->reff_trx_id;

            $data->penjamin_id = $request->penjamin_id;
            $data->penjamin_nama = $penjamin->penjamin_nama;
            $data->no_kepesertaan = $request->no_kepesertaan;
            $data->jns_penjamin = $penjamin->jns_penjamin;

            $data->buku_harga_id = $penjamin->buku_harga_id;
            $data->buku_harga = $penjamin->buku_harga;
            $data->kelas_harga_id = $request->kelas_harga_id;
            $data->kelas_harga = $request->kelas_harga;
            
            $data->diagnosa_pra = $request->diagnosa_pra;
            $data->diagnosa_pasca = $request->diagnosa_pasca;
            $data->tindakan_id = $request->tindakan_id;
            $data->tindakan_nama = $request->tindakan_nama;
            $data->skala_operasi = $request->skala_operasi;
            $data->jenis_operasi = $request->jenis_operasi;
           
            $data->jns_registrasi = $request->jns_registrasi;
            $data->cara_registrasi = $request->cara_registrasi;
            $data->is_aktif = true;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
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
                $detail = TrxOperasiTim::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('reg_id',$regId)->where('trx_id',$trxId)
                    ->where('dokter_id',$tim['dokter_id'])->first();
                
                if(!$detail) {
                    $detailId = $trxId.Uuid::uuid4()->getHex();
                    $detail = new TrxOperasiTim();
                    $detail->operasi_tim_id = $detailId;
                    $detail->reg_id = $regId;
                    $detail->trx_id = $trxId;
                    $detail->dokter_id = $tim['dokter_id'];
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }                
                $detail->dokter_nama = $tim['dokter_nama'];
                $detail->posisi = $tim['posisi'];
                $detail->is_operator = $tim['is_operator'];
                
                $detail->is_aktif = $tim['is_aktif'];
                $detail->updated_by = Auth::user()->username;
                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data tim operasi gagal disimpan.']);
                }
            }

            if($data->is_persalinan = True) {
                $persalinan = new Persalinan();
                $persalinan->persalinan_id = $this->getPersalinanId();
                $persalinan->reg_id = $regId;
                $persalinan->trx_id = $trxId;
                $persalinan->sub_trx_id = $trxId;
                $persalinan->pasien_id = $pasien->pasien_id;
                $persalinan->nama_pasien = $pasien->nama_lengkap;
                $persalinan->no_rm = $pasien->no_rm;
                $persalinan->nik_pasien = $pasien->nik;
                $persalinan->nama_ayah_bayi = $request->nama_ayah;
                $persalinan->nik_ayah_bayi = $request->nik_ayah;
                $persalinan->alamat = $request->alamat;

                $persalinan->is_aktif = true;
                $persalinan->client_id = $this->clientId;
                $persalinan->created_by = Auth::user()->username;
                $persalinan->updated_by = Auth::user()->username;
                $success = $persalinan->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data persalinan gagal disimpan.']);
                }

                dd($request);

                foreach($request->persalinan as $labor) {
                    $detail = new PersalinanBayi();
                    $detail->persalinan_bayi_id = $this->getPersalinanBayiId();
                    $detail->persalinan_id = $persalinan->persalinan_id;
                    $detail->reg_id = $regId;
                    $detail->trx_id = $trxId;
                    $detail->sub_trx_id = $trxId; 
                    $detail->tgl_kelahiran = $labor['tgl_kelahiran'];
                    $detail->jam_kelahiran = $labor['jam_kelahiran'];
                    $detail->umur_kehamilan = $labor['umur_kehamilan'];
                    $detail->jenis_persalinan = $labor['jenis_persalinan'];
                    $detail->kelahiran_ke = $labor['kelahiran_ke'];
                    $detail->kondisi_ibu = $labor['kondisi_ibu'];
                    $detail->no_rm_bayi = $labor['rm_bayi'];
                    $detail->jk_bayi = $labor['jk_bayi'];
                    $detail->bb_bayi = $labor['bb_bayi'];
                    $detail->tb_bayi = $labor['tb_bayi'];
                    $detail->lingkar_kepala = $labor['lkr_kepala'];
                    $detail->lingkar_dada = $labor['lkr_dada'];
                    $detail->frekuensi_napas = $labor['pernapasan'];
                    $detail->detak_jantung = $labor['detak_jantung'];
                    $detail->kondisi_lahir = $labor['kondisi_lahir'];

                    $detail->is_aktif = true;
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                    $detail->updated_by = Auth::user()->username;
                    $success = $detail->save();
                }
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data persalinan gagal disimpan.']);
                }
                
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

            return response()->json(['success' => true, 'message' => 'Data registrasi berhasil diubah', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan update registrasi. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $trxId)
    {
        try {
            $data = TrxOperasi::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data jadwal operasi tidak ditemukan.']);
            }
            if($data->status !== 'DAFTAR' || $data->is_realisasi == true) {
                return response()->json(['success' => false, 'message' => 'Data operasi sudah berubah status.']);
            }

            $tim = TrxOperasiTim::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();

            /**check data transaksi */
            $transaksi = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)->first();
                
            $detail = TransaksiDetail::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id) 
                ->first();
                
            $komponen = TransaksiDetailKomp::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();
                            
            DB::connection('dbclient')->beginTransaction();

            $success = TrxOperasi::where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'jadwal operasi gagal dihapus.']);
            }

            // if(!$data->is_rujukan_int) {
            //     $success = Pendaftaran::where('reg_id',$data->reg_id)
            //         ->where('pasien_id',$data->pasien_id)
            //         ->where('is_aktif',1)
            //         ->where('client_id',$this->clientId)
            //         ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status_reg' =>'BATAL']);

            //     if(!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'data operasi gagal dihapus.']);
            //     }
            // }

            if($transaksi) {
                /**update transaksi */
                    $success = Transaksi::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->where('is_realisasi',false)
                    ->where('is_bayar',false)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'jadwal operasi gagal dihapus.']);
                }
            }            

            /**update transaksi detail */
            if($detail) {
                $success = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data transaksi detail operasi gagal dihapus.']);
                }
            }
            
            /**update transaksi detail komp */
            if($komponen) {
                $success = TransaksiDetailKomp::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->where('is_realisasi',false)
                    ->where('is_bayar',false)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data detail komponen operasi gagal dihapus.']);
                }
            }

            /**hapus tim */
            if($tim) {
                $success = TrxOperasiTim::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->update(['is_aktif'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data tim operasi gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'jadwal operasi berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus jadwal operasi. Error : ' . $e->getMessage()]);
        }
    }

    /**
     * konfirmasi data rawat inap sudah benar dan dikunci. 
     * data sudah tidak dapat diubah lagi.
     * pergantian dokter, penjamin dan ruang akan menggunakan fungsi lain.
     */
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

            // if(!$request->is_rujukan_int) {
            //     //check data reg
            //     $dataReg = Pendaftaran::where('reg_id',$regId)
            //         ->where('pasien_id',$data->pasien_id) 
            //         ->where('client_id',$this->clientId)
            //         ->where('is_aktif',1)->first();

            //     if(!$dataReg) {
            //         return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            //     }
            // }    
            
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
            
            // if(!$request->is_rujukan_int) {
            //     /**update data pendaftaran */
            //     $success = Pendaftaran::where('reg_id',$regId)
            //         ->where('pasien_id',$data->pasien_id) 
            //         ->where('client_id',$this->clientId)->where('is_aktif',1)
            //         ->update(['status_reg' =>'PROSES', 'updated_by' =>Auth::user()->username]);

            //     if(!$success) {
            //         DB::connection('dbclient')->rollBack();
            //         return response()->json(['success' => false, 'message' => 'Data jadwal operasi gagal diubah.']);
            //     }
            // }
            
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
}
