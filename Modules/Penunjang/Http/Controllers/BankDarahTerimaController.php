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
use Modules\MasterData\Entities\Referensi;
use Modules\Penunjang\Entities\BankDarahLog;

class BankDarahTerimaController extends Controller
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
                    $rowNumber = BankDarahTerima::count();
                }
            }

            $lists = BankDarahTerima::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('status','ILIKE',$status)                
                ->where(function($q) use ($keyword) {
                    $q->where('asal_darah','ILIKE',$keyword);
                })->orderBy('tgl_terima', 'DESC')->paginate($rowNumber);

            if($lists) {
                foreach($lists->items() as $dt) {
                    $dt['items'] = BankDarahDetail::where('darah_terima_id',$dt['darah_terima_id'])
                        ->where('client_id',$this->clientId)->where('is_aktif',1)->get();
                }
            }
    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource product cssd.
     * @return Renderable
     */
    public function referensiLists(Request $request) {
        try {
            $data = Referensi::where('client_id', $this->clientId)->where('is_aktif', 1)->where('ref_group','BANK_DARAH')->get();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan', 'data' => []]);
            }

            foreach($data as $dt){
                if($dt->json_data) {
                    $dt->json_data = json_decode($dt->json_data, false);
                }
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Referensi: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request) {
        try {            
            $id = $this->createTerimaId();
            DB::connection('dbclient')->beginTransaction();

            $data = new BankDarahTerima();
            $data->darah_terima_id = $id;
            $data->asal_darah = $request->asal_darah;
            $data->nama_donor = strtoupper($request->nama_donor);
            $data->tgl_terima = $request->tgl_terima;
            $data->catatan = strtoupper($request->catatan);
            $data->penerima = Auth::user()->username;
            $data->status = 'TERIMA';
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan persediaan darah tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                /**input bank darah detail */
                $detailId = $id.Uuid::uuid4()->getHex();

                $detail = new BankDarahDetail();
                $detail->darah_detail_id = $detailId;
                $detail->darah_terima_id = $id;
                $detail->tgl_terima = $request->tgl_terima;
                
                $detail->no_labu = strtoupper($item['no_labu']);
                $detail->gol_darah = $item['gol_darah'];
                $detail->rhesus = $item['rhesus'];
                $detail->group_darah = $item['group_darah'];
                $detail->volume = $item['volume'];
                $detail->satuan = $item['satuan'];
                $detail->jumlah = 1;
                $detail->tgl_kadaluarsa = $item['tgl_kadaluarsa'];
                $detail->catatan = $item['catatan'];
                $detail->status = 'TERIMA';
                $detail->is_terpakai = false;
                $detail->is_aktif = true;
                $detail->client_id = $this->clientId;
                $detail->created_by = Auth::user()->username;
                $detail->updated_by = Auth::user()->username;

                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item penerimaan persediaan darah tidak berhasil disimpan.']);
                }

                /**input bank darah log */
                $log = new BankDarahLog();
                $log->log_darah_id = $id.Uuid::uuid4()->getHex();
                $log->trx_darah_id = $id;
                $log->darah_detail_id = $detailId;
                $log->tgl_transaksi = $request->tgl_terima;
                $log->jenis_transaksi = 'PENERIMAAN';
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
            
            DB::connection('dbclient')->commit();
            $data = BankDarahTerima::where('darah_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = BankDarahDetail::where('darah_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Penerimaan persediaan darah berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createTerimaId() {
        try {
            $id = $this->clientId.'.'.date('Ymd').'.BDRR0001';
            $maxId = BankDarahTerima::withTrashed()->where('client_id', $this->clientId)->where('darah_terima_id','LIKE',$this->clientId.'.'.date('Ymd').'.BDRR%')->max('darah_terima_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ymd').'.BDRR0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ymd').'.BDRR','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ymd').'.BDRR000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ymd').'.BDRR00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ymd').'.BDRR0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ymd').'.BDRR'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.'.date('Ymd').'.BDRR' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = BankDarahTerima::where('darah_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Penerimaan persediaan darah tidak ditemukan.']);
            }
            $data['items'] = BankDarahDetail::where('darah_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Data penerimaan persediaan darah ditemukan.', 'data' => $data]); 
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
            $id = $request->darah_terima_id;
            $data = BankDarahTerima::where('client_id',$this->clientId)->where('darah_terima_id',$id)->where('is_aktif',1)->where('status','TERIMA')->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan persediaan darah tidak ditemukan atau item sudah terpakai.']);
            }

            $itemdetail = BankDarahDetail::where('client_id',$this->clientId)->where('darah_terima_id',$id)->where('is_aktif',1)->where('is_terpakai',true)->first();
            if($itemdetail) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan persediaan darah tidak dapat diubah. Item persediaan sudah terpakai.']);
            }

            DB::connection('dbclient')->beginTransaction();

            $data->asal_darah = $request->asal_darah;
            $data->nama_donor = strtoupper($request->nama_donor);            
            $data->tgl_terima = $request->tgl_terima;
            $data->penerima = Auth::user()->username;
            $data->catatan = strtoupper($request->catatan);
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;            
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan persediaan darah tidak berhasil disimpan.']);
            }

            foreach($request->items as $item) {
                $detail = BankDarahDetail::where('darah_detail_id',$item['darah_detail_id'])
                    ->where('is_aktif',1)
                    ->where('client_id',$this->clientId)->first();
                
                if($detail) { 
                    $detailId = $detail->darah_detail_id; 
                    //*update detail
                    $detail->gol_darah = $item['gol_darah'];
                    $detail->rhesus = $item['rhesus'];
                    $detail->group_darah = $item['group_darah'];
                    $detail->volume = $item['volume'];
                    $detail->satuan = $item['satuan'];
                    $detail->jumlah = 1;
                    $detail->tgl_kadaluarsa = $item['tgl_kadaluarsa'];
                    $detail->catatan = $item['catatan'];
                    $detail->updated_by = Auth::user()->username;
                    $detail->is_aktif = $item['is_aktif'];

                    $success = $detail->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'item penerimaan persediaan darah tidak berhasil disimpan.']);
                    }
                }    
                
                else {
                    //detail baru
                    $detailId = $id.Uuid::uuid4()->getHex();

                    $detail = new BankDarahDetail();
                    $detail->darah_detail_id = $detailId;
                    $detail->darah_terima_id = $id;
                    $detail->tgl_terima = $request->tgl_terima;
                    $detail->status = "TERIMA";
                    $detail->no_labu = strtoupper($item['no_labu']);
                    $detail->gol_darah = $item['gol_darah'];
                    $detail->rhesus = $item['rhesus'];
                    $detail->group_darah = $item['group_darah'];
                    $detail->volume = $item['volume'];
                    $detail->satuan = $item['satuan'];
                    $detail->jumlah = 1;
                    $detail->tgl_kadaluarsa = $item['tgl_kadaluarsa'];
                    $detail->catatan = $item['catatan'];
                    $detail->is_terpakai = false;
                    $detail->is_aktif = true;
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                    $detail->updated_by = Auth::user()->username;
    
                    $success = $detail->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'item penerimaan persediaan darah tidak berhasil disimpan.']);
                    }

                    /**input bank darah log */
                    $log = new BankDarahLog();
                    $log->log_darah_id = $id.Uuid::uuid4()->getHex();
                    $log->trx_darah_id = $id;
                    $log->darah_detail_id = $detailId;
                    $log->tgl_transaksi = $request->tgl_terima;
                    $log->jenis_transaksi = 'PENERIMAAN';
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
            $data = BankDarahTerima::where('darah_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = BankDarahDetail::where('darah_terima_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Penerimaan persediaan darah berhasil diubah.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan mengubah data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id) {
        try {
            $data = BankDarahTerima::where('client_id',$this->clientId)->where('darah_terima_id',$id)->where('is_aktif',1)->where('status','TERIMA')->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan persediaan darah tidak ditemukan.']);
            }

            $itemdetail = BankDarahDetail::where('client_id',$this->clientId)->where('darah_terima_id',$id)->where('is_aktif',1)->where('is_terpakai',true)->first();
            if($itemdetail) {
                return response()->json(['success' => false, 'message' => 'Data penerimaan persediaan darah tidak dapat diubah. Item persediaan sudah terpakai.']);
            }

            $detail = BankDarahDetail::where('client_id',$this->clientId)->where('darah_terima_id',$id)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**hapus data penerimaan */
            $success = BankDarahTerima::where('client_id',$this->clientId)
                ->where('darah_terima_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username, 'status'=>'BATAL']);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'penerimaan persediaan darah tidak berhasil dihapus.']);
            }
            
            /**hapus data detail penerimaan */
            if($detail) {
                $success = BankDarahDetail::where('client_id',$this->clientId)
                    ->where('darah_terima_id',$id)
                    ->where('is_aktif',1)
                    ->where('is_terpakai',0)
                    ->update(['is_aktif' => false, 'updated_by' => Auth::user()->username, 'status'=>'BATAL']);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item penerimaan persediaan darah tidak berhasil dihapus.']);
                }
            }
                        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Penerimaan persediaan darah berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }
}
