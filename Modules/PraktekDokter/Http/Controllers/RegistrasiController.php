<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon;

use Modules\PraktekDokter\Entities\PraktekDokter;
use Modules\HumanResource\Entities\PencatatanKas;

use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;


use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;

use Modules\Transaksi\Entities\Soap;
use Modules\Transaksi\Entities\SoapDiagnosa;
use Modules\Transaksi\Entities\Pembayaran;

class RegistrasiController extends Controller
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

            $query = PraktekDokter::query();
            $query = $query->join('tb_pasien','tb_pasien.pasien_id','=','tb_praktek_dokter.pasien_id');
            
            if($request->has('jns_penjamin')) {
                $jnsPenjamin =$request->input('jns_penjamin'); 
                if($jnsPenjamin !== '' && $jnsPenjamin !== null ) {
                    $query = $query->where('tb_praktek_dokter.jns_penjamin','ILIKE',$jnsPenjamin);
                }
            }

            if($request->has('status')) {
                $status ='%'.$request->input('status').'%'; 
                if($status !== '' && $status !== null ) {
                    $query = $query->where('tb_praktek_dokter.status','ILIKE',$status);
                }
            }
            
            if($request->has('tgl_periksa_start') && $request->has('tgl_periksa_end')) { 
                $tglPeriksaMulai =$request->input('tgl_periksa_start').' 00:00:00';
                $tglPeriksaAkhir =$request->input('tgl_periksa_end').' 23:59:59';
                $query = $query->whereBetween('tb_praktek_dokter.tgl_periksa',[$tglPeriksaMulai,$tglPeriksaAkhir]);
            }

            if($request->has('tgl_transaksi_start') && $request->has('tgl_transaksi_start')) { 
                $tglTransaksiMulai =$request->input('tgl_transaksi_start').' 00:00:00';
                $tglTransaksiAkhir =$request->input('tgl_transaksi_end').' 23:59:59';
                $query = $query->whereBetween('tb_praktek_dokter.tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            }

            if($request->has('tgl_periksa')) {
                if($request->input('tgl_periksa') !== null && $request->input('tgl_periksa') !== '' ) {
                    $tglPeriksaMulai =$request->input('tgl_periksa').' 00:00:00';
                    $tglPeriksaAkhir =$request->input('tgl_periksa').' 23:59:59';
                    $query = $query->whereBetween('tb_praktek_dokter.tgl_periksa',[$tglPeriksaMulai,$tglPeriksaAkhir]);
                } 
            }
            
            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where(function($q) use ($keyword) {
                        $q->where('tb_pasien.nama_pasien','ILIKE',$keyword)
                        ->orWhere('tb_pasien.no_rm','ILIKE',$keyword);
                    });
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('tb_praktek_dokter.is_aktif','ILIKE',$isAktif)
                ->where('tb_praktek_dokter.client_id',$this->clientId)
                ->select(
                    'tb_pasien.*',
                    'tb_praktek_dokter.reg_id',
                    'tb_praktek_dokter.tgl_periksa', 
                    // 'tb_praktek_dokter.tgl_transaksi',
                    'tb_praktek_dokter.jns_penjamin',
                    'tb_praktek_dokter.penjamin_id',
                    'tb_praktek_dokter.penjamin_nama',
                    'tb_praktek_dokter.dokter_id',
                    'tb_praktek_dokter.dokter_nama',
                    'tb_praktek_dokter.alamat',
                    'tb_praktek_dokter.is_periksa',
                    'tb_praktek_dokter.is_aktif',
                    'tb_praktek_dokter.catatan',
                    'tb_praktek_dokter.no_antrian',
                    'tb_praktek_dokter.alasan_batal',
                    'tb_praktek_dokter.interim_id',
                    'tb_praktek_dokter.status',
                    'tb_praktek_dokter.is_bayar'
                )->orderBy('tb_praktek_dokter.tgl_periksa','ASC')
                ->paginate($per_page);

            if(!$lists) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan']);
            }

            return response()->json(['success'=>true,'message'=>'List Antrian','data'=>$lists]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id) {
        try {
            $data = PraktekDokter::where('client_id',$this->clientId)
                ->where('is_aktif',true)
                ->where('reg_id',$id)->first();

            if($data) {
                // $formattedDate = strtotime($data->tgl_periksa);//->format('Y-m-d');
                // $data->tgl_periksa = date('Y-m-d H:i:s',$formattedDate);
                $pasien = Pasien::where('client_id',$this->clientId)->where('pasien_id',$data->pasien_id)->first();
                if($pasien) {
                    $data->nama_pasien = $pasien->nama_pasien;
                    $success = $data->save();
                    if($success) {
                        $data = PraktekDokter::where('client_id',$this->clientId)
                            ->where('is_aktif',true)
                            ->where('reg_id',$id)->first();
                    }
                }
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
    
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request) {
        try {
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$pasien) {
                return response()->json(['success'=>false,'message'=>'pasien tidak ditemukan']);
            }
            $alamat = '';
            $pasienAlamat = PasienAlamat::where('pasien_id',$request->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if($pasienAlamat) {
                $alamat = $pasienAlamat->alamat. 'RT.'.$pasienAlamat->rt. ' /RW.'.$pasienAlamat->rw.' , Kec.'.$pasienAlamat->kelurahan.' , Kel.'.$pasienAlamat->kecamatan.' ,'.$pasienAlamat->kota.' ,'.$pasienAlamat->propinsi.' - '.$pasienAlamat->kodepos;
            }

            $isPasienBaru = $request->is_pasien_baru;
            if($isPasienBaru == null) {
                $isPasienBaru = false;
            }

            $noAntrian = $this->createNoAntrian($request->dokter_id,$request->tgl_periksa);

            $id = $this->getRegId();
            $data = new PraktekDokter();
            $data->reg_id = $id;
            $data->no_antrian = $noAntrian;
            
            $data->tgl_registrasi = date('Y-m-d H:i:s');
            $data->tgl_periksa = $request->tgl_periksa;
            $data->pasien_id = $pasien->pasien_id;
            $data->nama_pasien = $pasien->nama_lengkap;
            $data->no_rm = $pasien->no_rm;
            $data->jns_kelamin = $pasien->jns_kelamin;
            $data->tgl_lahir = $pasien->tgl_lahir;
            $data->tempat_lahir = $pasien->tempat_lahir;
            $data->alamat = $alamat;
            
            $data->dokter_id = $request->dokter_id;
            $data->dokter_nama = $request->dokter_nama;
            $data->jns_penjamin = $request->jns_penjamin;
            
            $data->penjamin_id = '';
            $data->penjamin_nama = '';
            $data->catatan = $request->catatan;
            
            $data->status = 'BOOKING';
            $data->is_pasien_baru = $isPasienBaru;
            $data->is_periksa = false;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam penyimpanan data.']);   
            }

            return response()->json(['success'=>true,'message'=>'Data berhasil disimpan','data'=>$data]);   

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }    
    }

    public function createNoAntrian($dokterId, $tglPeriksa) {
        try {
            $id = '001';

            $maxId = PraktekDokter::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('dokter_id',$dokterId)
                ->where('tgl_periksa',$tglPeriksa)
                ->max('no_antrian');

            if (!$maxId) { $id = '001'; } 
            else {
                $count = $maxId + 1;
                
                if ($count < 10) { $id = '00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = '0' . $count; } 
                else { $id = $count; }
            }
            return $id;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function getRegId() {
        try {
            $id = $this->clientId.date('ymd').'-REG00001';

            $maxId = PraktekDokter::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('reg_id', 'LIKE', $this->clientId.date('ymd').'-REG%')
                ->max('reg_id');

            if (!$maxId) { $id = $this->clientId.date('ymd').'-REG00001'; } 
            else {
                $maxId = str_replace($this->clientId.date('ymd').'-REG', '', $maxId);
                $count = $maxId + 1;
                
                if ($count < 10) { $id = $this->clientId.date('ymd').'-REG0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.date('ymd').'-REG000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.date('ymd').'-REG00' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.date('ymd').'-REG0' . $count; } 
                else { $id = $this->clientId.date('ymd').'-REG' . $count; }
            }
            return $id;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function update(Request $request) {
        try {
            $id = $request->reg_id;
            $data = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->first();
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);   
            }
            $pasien = Pasien::where('pasien_id',$request->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if(!$pasien) {
                return response()->json(['success'=>false,'message'=>'pasien tidak ditemukan']);
            }
            $alamat = '';
            $pasienAlamat = PasienAlamat::where('pasien_id',$request->pasien_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            if($pasienAlamat) {
                $alamat = $pasienAlamat->alamat. 'RT.'.$pasienAlamat->rt. ' /RW.'.$pasienAlamat->rw.' , Kec.'.$pasienAlamat->kelurahan.' , Kel.'.$pasienAlamat->kecamatan.' ,'.$pasienAlamat->kota.' ,'.$pasienAlamat->propinsi.' - '.$pasienAlamat->kodepos;
            }

            //$data->tgl_registrasi = date('Y-m-d H:i:s');
            //$data->tgl_periksa = $request->tgl_periksa;
            //$data->pasien_id = $pasien->pasien_id;
            $data->nama_pasien = $pasien->nama_lengkap;
            $data->no_rm = $pasien->no_rm;
            $data->jns_kelamin = $pasien->jns_kelamin;
            $data->tgl_lahir = $pasien->tgl_lahir;
            $data->tempat_lahir = $pasien->tempat_lahir;
            $data->alamat = $alamat;
            
            //$data->dokter_id = $request->dokter_id;
            //$data->dokter_nama = $request->dokter_nama;
            $data->jns_penjamin = $request->jns_penjamin;
            
            // $data->penjamin_id = $request->penjamin_id;
            // $data->penjamin_nama = $request->penjamin_nama;
            $data->catatan = $request->catatan;
            
            $data->is_pasien_baru = $request->is_pasien_baru;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam penyimpanan data.']);   
            }

            return response()->json(['success'=>true,'message'=>'Data berhasil diubah','data'=>$data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function remove(Request $request) {
        try {
            $id = $request->reg_id;

            $data = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->first();
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);   
            }

            if($data->is_bayar) {
                return response()->json(['success'=>false,'message'=>'Pemeriksaan sudah dibayar. hapus terlebih dahulu pembayaran untuk menghapus data.']);   
            }            

            $soap = Soap::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->first();
            $diagnosa = SoapDiagnosa::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->first();
            $pembayaran = Pembayaran::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->first();

            DB::connection('dbclient')->beginTransaction();

            $success = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)
                ->update(['is_aktif'=>0, 'alasan_batal'=>$request->alasan_batal, 'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam penyimpanan data.']);   
            }

            if($soap) {
                $success = Soap::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'ada kesalahan dalam menghapus SOAP.']);   
                }
            }

            if($diagnosa) {
                $success = SoapDiagnosa::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'ada kesalahan dalam menghapus diagnosa.']);   
                }
            }

            if($pembayaran) {
                $success = Pembayaran::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username, 'status'=>'BATAL']);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'ada kesalahan dalam menghapus pembayaran.']);   
                }
            }
            DB::connection('dbclient')->commit();

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request) {
        try {
            $id = $request->reg_id;
            $data = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->first();
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);   
            }
            
            if($data->is_bayar) {
                return response()->json(['success'=>false,'message'=>'pembayaran sudah dilakukan. status sudah tidak dapat diubah.']);   
            }
            

            $currentStatus = $data->status;
            $status = $request->status;

            if($currentStatus == 'BOOKING'){
                $date = strtotime(date('Y-m-d'));
                $dateReg =  strtotime(date("Y-m-d", strtotime($data->tgl_periksa)));
                $jarak = $dateReg - $date;
                $selisihHari = $jarak / 60 / 60 / 24;
    
                if($selisihHari > 0) { 
                    return response()->json(['success'=>false,'message'=>'Perubahan status tidak dapat dilakukan. Jadwal periksa bukan di hari ini.']);   
                }
            }
            
            if($status == 'BOOKING') {
                if($currentStatus == 'PERIKSA') {
                    return response()->json(['success'=>false,'message'=>'status tidak dapat kembali dari periksa ke booking.']);   
                }
            }

            else if($status == 'ANTRI') {
                if($currentStatus == 'PERIKSA') {
                    return response()->json(['success'=>false,'message'=>'status tidak dapat kembali dari periksa ke antri.']);   
                }
            }

            else if($status == 'PERIKSA') {
                if($currentStatus == 'BOOKING') {
                    return response()->json(['success'=>false,'message'=>'status booking hanya bisa dibatalkan atau diubah ke antria.']);   
                }
            }
            
            $data->status = $request->status;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam mengubah status data.']);   
            }

            return response()->json(['success'=>true,'message'=>'status berhasil diubah','data'=>$data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
