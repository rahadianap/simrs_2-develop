<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\Penunjang\Entities\BankDarahDistribusi;
use Modules\Penunjang\Entities\BankDarahDetail;
use Modules\Penunjang\Entities\BankDarahTerima;
use Modules\Penunjang\Entities\BankDarahLog;

class BankDarahRequestController extends Controller
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
    public function lists(Request $request) {
        try {
            $keyword = '%%';
            $rowNumber = 20;
            $status = '%%';
            $aktif = 'true';

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            if($request->has('status')) {
                $status = '%'.$request->input('status').'%';
            }
            
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = BankDarahDistribusi::count();
                }
            }

            $lists = BankDarahDistribusi::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('status','ILIKE',$status)   
                ->orderBy('tgl_dibutuhkan', 'DESC')->paginate($rowNumber);

            if($lists) {
                foreach($lists->items() as $dt) {
                    $dt['items'] = BankDarahDetail::where('darah_dist_id',$dt['darah_dist_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->get();
                }
            }
    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }

    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request) {
        try {            
            $registrasi = "";            

            $id = $this->createDarahKirimId();
            DB::connection('dbclient')->beginTransaction();

            $data = new BankDarahDistribusi();
            $data->darah_dist_id = $id;
            $data->reg_id = $request->reg_id;
            $data->jns_registrasi = $request->jns_registrasi;
            
            $data->pasien_id = $request->pasien_id;
            $data->pasien_nama = $request->pasien_nama;
            $data->no_rm = $request->no_rm;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $request->unit_nama;
            $data->ruang_id = $request->ruang_id;
            $data->ruang_nama = $request->ruang_nama;
            $data->bed_no = $request->bed_no;
            
            $data->dokter_nama = $request->dokter_nama;
            $data->dokter_id = $request->dokter_id;
            
            $data->penjamin_id = $request->penjamin_id;
            $data->jns_penjamin = $request->jns_penjamin;
            $data->penjamin_nama = $request->penjamin_nama;
            
            $data->usia = $request->usia;
            $data->tempat_lahir = strtoupper($request->tempat_lahir);
            $data->tgl_lahir = $request->tgl_lahir;
            
            $data->jenis_kelamin = $request->jenis_kelamin;
            $data->tgl_permintaan = date('Y-m-d');
            $data->jam_permintaan = date('H:i:s');

            $data->tgl_dibutuhkan = $request->tgl_dibutuhkan;
            $data->jam_dibutuhkan = $request->jam_dibutuhkan;

            $data->peminta = strtoupper($request->peminta);
            $data->pengirim = strtoupper($request->pengirim);
            $data->penerima = strtoupper($request->penerima);

            $data->gol_darah = $request->gol_darah;
            $data->rhesus = $request->rhesus;
            $data->group_darah = $request->group_darah;
            $data->haemoglobin = $request->haemoglobin;
            $data->jml_permintaan = $request->jml_permintaan;
            $data->volume_per_labu = $request->volume_per_labu;
            $data->diagnosa = strtoupper($request->diagnosa);

            $data->is_kirim = false;
            $data->status = 'PERMINTAAN';
            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'permintaan persediaan darah tidak berhasil disimpan.']);
            }            
            
            DB::connection('dbclient')->commit();
            $data = BankDarahDistribusi::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = BankDarahDetail::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Permintaan darah berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createDarahKirimId() {
        try {
            $id = $this->clientId.'.'.date('Ym').'.BDRD0001';
            $maxId = BankDarahDistribusi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('darah_dist_id','LIKE',$this->clientId.'.'.date('Ym').'.BDRD%')
                ->max('darah_dist_id');

            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'.BDRD0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'.BDRD','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'.BDRD000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'.BDRD00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'.BDRD0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'.BDRD'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'.BDRD' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = BankDarahDistribusi::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Permintaan darah tidak ditemukan.']);
            }
            $data['items'] = BankDarahDetail::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Data permintaan darah ditemukan.', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request) {
        try {
            $id = $request->darah_dist_id;
            $data = BankDarahDistribusi::where('client_id',$this->clientId)->where('darah_dist_id',$id)->where('status','PERMINTAAN')->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data permintaan tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            
            $data->jns_registrasi = $request->jns_registrasi;
            $data->pasien_id = $request->pasien_id;
            $data->pasien_nama = $request->pasien_nama;
            $data->no_rm = $request->no_rm;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $request->unit_nama;
            $data->ruang_id = $request->ruang_id;
            $data->ruang_nama = $request->ruang_nama;
            $data->bed_no = $request->bed_no;
            
            $data->dokter_nama = $request->dokter_nama;
            $data->dokter_id = $request->dokter_id;

            $data->penjamin_id = $request->penjamin_id;
            $data->jns_penjamin = $request->jns_penjamin;
            $data->penjamin_nama = $request->penjamin_nama;
            
            $data->usia = $request->usia;
            $data->tempat_lahir = strtoupper($request->tempat_lahir);
            $data->tgl_lahir = $request->tgl_lahir;
            
            $data->jenis_kelamin = $request->jenis_kelamin;
            $data->tgl_permintaan = $request->tgl_permintaan;
            $data->jam_permintaan = $request->jam_permintaan;

            $data->peminta = strtoupper($request->peminta);
            $data->pengirim = strtoupper($request->pengirim);
            $data->penerima = strtoupper($request->penerima);

            $data->gol_darah = $request->gol_darah;
            $data->rhesus = $request->rhesus;
            $data->group_darah = $request->group_darah;
            $data->haemoglobin = $request->haemoglobin;
            $data->jml_permintaan = $request->jml_permintaan;
            $data->volume_per_labu = $request->volume_per_labu;
            $data->diagnosa = strtoupper($request->diagnosa);
            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = $request->is_aktif;
            
            $data->updated_by = Auth::user()->username;            
            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'permintaan darah tidak berhasil diubah.']);
            }

            DB::connection('dbclient')->commit();
            $data = BankDarahDistribusi::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = BankDarahDetail::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Permintaan darah berhasil diubah.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id) {
        try {
            $data = BankDarahDistribusi::where('client_id',$this->clientId)->where('darah_dist_id',$id)->where('is_aktif',1)->where('status','PERMINTAAN')->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data permintaan tidak ditemukan.']);
            }
            $detail = BankDarahDetail::where('client_id',$this->clientId)->where('darah_dist_id',$id)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**hapus data pengiriman */
            $success = BankDarahDistribusi::where('client_id',$this->clientId)
                ->where('darah_dist_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'permintaan tidak berhasil dihapus.']);
            }
            
            /**hapus data detail pengiriman */
            if($detail) {
                $success = BankDarahDetail::where('client_id',$this->clientId)
                    ->where('darah_dist_id',$id)->where('is_aktif',1)
                    ->update(['is_terpakai' => false, 'darah_dist_id' => null, 'updated_by' => Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item permintaan tidak berhasil dihapus.']);
                }
            }
                        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Permintaan berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }


        /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function updateRealisasi(Request $request) {
        try {
            $id = $request->darah_dist_id;
            $data = BankDarahDistribusi::where('client_id',$this->clientId)
                ->where('darah_dist_id',$id)
                ->where('is_aktif',1)
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data pengiriman persediaan darah tidak ditemukan.']);
            }

            if(count($request->items) <= 0 ) {
                return response()->json(['success' => false, 'message' => 'Data item pengiriman tidak ditemukan. Data tidak dapat disimpan.']);
            }

            DB::connection('dbclient')->beginTransaction();

            $data->is_kirim = $request->is_kirim;
            if($request->is_kirim == false) {
                $data->pengirim = null;            
                $data->penerima = null;
                $data->tgl_distribusi = null;
                $data->jam_distribusi = null;
                $data->status = 'PERMINTAAN';
            }
            else {
                $data->pengirim = strtoupper($request->pengirim);            
                $data->penerima = strtoupper($request->penerima);
                $data->tgl_distribusi = $request->tgl_distribusi;
                $data->jam_distribusi = $request->jam_distribusi;
                $data->status = "KIRIM";
            }
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'distribusi persediaan darah tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $detail = BankDarahDetail::where('darah_detail_id',$item['darah_detail_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                
                if(!$detail) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item persediaan darah tidak ditemukan. data tidak dapat disimpan.']);
                }

                
                //*update detail                
                $detail->is_terpakai = $item['is_terpakai'];
                
                if($item['is_terpakai'] == false) {
                    $detail->darah_dist_id = null;                    
                    $detail->sesuai = null;
                    $detail->mayor = null;
                    $detail->minor = null;
                    $detail->ac = null;
                    $detail->dct = null;
                }
                else {
                    $detail->darah_dist_id = $id;
                    $detail->sesuai = $item['sesuai'];
                    $detail->mayor = $item['mayor'];
                    $detail->minor = $item['minor'];
                    $detail->ac = $item['ac'];
                    $detail->dct = $item['dct'];
                }
                
                $detail->updated_by = Auth::user()->username;
                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item persediaan darah tidak berhasil diubah.']);
                }

                //check status log
                $log = BankDarahLog::where('client_id',$this->clientId)->where('trx_darah_id',$id)->where('darah_detail_id',$item['darah_detail_id'])->first();
                if($log) {
                    $success = BankDarahLog::where('client_id',$this->clientId)
                        ->where('trx_darah_id',$id)
                        ->where('darah_detail_id',$item['darah_detail_id'])
                        ->update([
                            'tgl_transaksi' => $request->tgl_distribusi,
                            'jenis_transaksi' => 'KIRIM',
                            'is_aktif' => $item['is_terpakai'],
                            'updated_by' => Auth::user()->username,
                        ]);

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'log item penerimaan persediaan darah tidak berhasil disimpan.']);
                    }                    
                }
                
                else {
                    /**input bank darah log */
                    $log = new BankDarahLog();
                    $log->log_darah_id = $id.Uuid::uuid4()->getHex();
                    $log->trx_darah_id = $id;
                    $log->darah_detail_id = $item['darah_detail_id'];
                    $log->tgl_transaksi = $request->tgl_distribusi;
                    $log->jenis_transaksi = 'KIRIM';
                    $log->is_aktif = true;
                    $log->client_id = $this->clientId;
                    $log->created_by = Auth::user()->username;
                    $log->updated_by = Auth::user()->username;
                    $success = $log->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'log item penerimaan persediaan darah tidak berhasil disimpan.']);
                    }
                }
            }
            
            DB::connection('dbclient')->commit();

            $data = BankDarahDistribusi::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = BankDarahDetail::where('darah_dist_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Pengiriman persediaan darah berhasil diubah.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan mengubah data', 'error' => $e->getMessage()]);
        }
    }

}
