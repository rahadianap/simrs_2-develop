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
use Modules\Penunjang\Entities\BankDarahMusnah;
use Modules\Penunjang\Entities\BankDarahLog;

class BankDarahMusnahController extends Controller 
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
                    $rowNumber = BankDarahMusnah::count();
                }
            }

            $lists = BankDarahMusnah::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->orderBy('tgl_pemusnahan', 'DESC')->paginate($rowNumber);

            if($lists) {
                foreach($lists->items() as $dt) {
                    $logDetail = BankDarahLog::where('trx_darah_id',$dt['darah_musnah_id'])->where('client_id',$this->clientId)
                        ->select('darah_detail_id')->get();
                    if($logDetail) {
                        $dt['items'] = BankDarahDetail::withTrashed()->whereIn('darah_detail_id',$logDetail)
                            ->where('client_id',$this->clientId)->get();
                    }
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
    public function stockLists(Request $request) {
        try {
            $data = BankDarahDetail::where('client_id', $this->clientId)
                ->where('is_aktif', 1)
                ->where('is_terpakai',false)
                ->where('is_musnah',false)
                ->orderBy('gol_darah', 'ASC')
                ->orderBy('tgl_kadaluarsa', 'ASC')
                ->get();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar persediaan: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request) {
        try {            
            $id = $this->createMusnahId();
            DB::connection('dbclient')->beginTransaction();

            $data = new BankDarahMusnah();
            $data->darah_musnah_id = $id;
            $data->tgl_pemusnahan = $request->tgl_pemusnahan;
            $data->jam_pemusnahan = $request->jam_pemusnahan;
            $data->catatan = strtoupper($request->catatan);
            $data->pembuat = Auth::user()->username;
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
                /**tandai item yang dihapus */
                $detail = BankDarahDetail::where('is_aktif',1)
                    ->where('darah_detail_id',$item['darah_detail_id'])
                    ->where('is_terpakai',0)
                    ->where('is_musnah',0)
                    ->where('client_id',$this->clientId)->first();
                if(!$detail) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item persediaan darah '.$item['no_labu'].' tidak ditemukan.']);
                }

                $success = BankDarahDetail::where('is_aktif',1)
                    ->where('darah_detail_id',$item['darah_detail_id'])
                    ->where('is_terpakai',false)
                    ->where('is_musnah',false)
                    ->where('client_id',$this->clientId)
                    ->update(['updated_by'=>Auth::user()->username, 'is_musnah'=>true, 'darah_musnah_id'=>$id]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item persediaan darah tidak berhasil diubah.']);
                }

                /**input pemusnahan bank darah log */
                $log = new BankDarahLog();
                $log->log_darah_id = $id.Uuid::uuid4()->getHex();
                $log->trx_darah_id = $id;
                $log->darah_detail_id = $detail->darah_detail_id;
                $log->tgl_transaksi = $request->tgl_pemusnahan;
                $log->jenis_transaksi = 'PEMUSNAHAN';
                $log->is_aktif = true;
                $log->client_id = $this->clientId;
                $log->created_by = Auth::user()->username;
                $log->updated_by = Auth::user()->username;

                $success = $log->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'log item pemusnahan persediaan darah tidak berhasil disimpan.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            $data = BankDarahMusnah::where('darah_musnah_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            $data['items'] = BankDarahDetail::where('darah_musnah_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true, 'message' => 'Penerimaan persediaan darah berhasil disimpan.', 'data' => $data]);                
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan menyimpan data baru', 'error' => $e->getMessage()]);
        }
    }

    public function createMusnahId() {
        try {
            $id = $this->clientId.'.'.date('Ym').'.BDEX0001';
            $maxId = BankDarahMusnah::withTrashed()->where('client_id', $this->clientId)->where('darah_musnah_id','LIKE',$this->clientId.'.'.date('Ym').'.BDEX%')->max('darah_musnah_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'.BDEX0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'.BDEX','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'.BDEX000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'.BDEX00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'.BDEX0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'.BDEX'.$count; } 
            }
            return $id;
        } 
        catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'.BDEX' . Uuid::uuid4()->getHex();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function data(Request $request, $id) {
        try {
            $data = BankDarahMusnah::where('darah_musnah_id',$id)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Penerimaan persediaan darah tidak ditemukan.']);
            }

            $logDetail = BankDarahLog::where('trx_darah_id',$id)->where('client_id',$this->clientId)
                        ->select('darah_detail_id')->get();

            if($logDetail) {
                $data['items'] = BankDarahDetail::withTrashed()->whereIn('darah_detail_id',$logDetail)
                    ->where('client_id',$this->clientId)->get();
            }
            
            return response()->json(['success' => true, 'message' => 'Data pemusnahan persediaan darah ditemukan.', 'data' => $data]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id) {
        try {
            $data = BankDarahMusnah::where('client_id',$this->clientId)->where('darah_musnah_id',$id)->where('is_aktif',1)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data pemusnahan persediaan darah tidak ditemukan.']);
            }
            $detail = BankDarahDetail::withTrashed()->where('client_id',$this->clientId)->where('darah_musnah_id',$id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**hapus data penerimaan */
            $success = BankDarahMusnah::where('client_id',$this->clientId)
                ->where('darah_musnah_id',$id)
                ->where('is_aktif',1)
                ->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username,]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'pemusnahan persediaan darah tidak berhasil dihapus.']);
            }
            
            /**hapus data detail penerimaan */
            if($detail) {
                $success = BankDarahDetail::where('client_id',$this->clientId)
                    ->where('darah_musnah_id',$id)
                    ->update(['is_musnah' => false,'darah_musnah_id'=>null, 'updated_by' => Auth::user()->username,]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'item pemusnahan persediaan darah tidak berhasil dihapus.']);
                }
            }
                        
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Pemusnahan persediaan darah berhasil dihapus.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data', 'error' => $e->getMessage()]);
        }
    }
}
