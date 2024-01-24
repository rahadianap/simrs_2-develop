<?php

namespace Modules\Laboratorium\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Carbon;

use Modules\Laboratorium\Entities\TrxLab;
use Modules\Laboratorium\Entities\TrxLabHasil;

use Modules\Laboratorium\Entities\LabTemplate;
use Modules\Laboratorium\Entities\LabNormal;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;


class HasilLabController extends Controller
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
            $per_page = 20;
            $aktif = 'true';
            $keyword = '%%';
            $status = '%%';
            $unitID = '%%';
            
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = TrxLabwhere('client_id',$this->clientId)->count(); }
            }

            if($request->has('status')) {
                $status = '%'.$request->input('status').'%';
            }

            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            if ($request->has('unit_id')) {
                $unitID = '%'.$request->input('unit_id').'%';
            }

            $list = TrxLab::where([
                    ['client_id',$this->clientId],
                    ['is_aktif','ILIKE',$aktif],
                    ['unit_id','ILIKE',$unitID]
                ])->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword);
                })->orderBy('created_at','ASC')->paginate($per_page);
            
            foreach($list->items() as $dt) {
                $dt['transaksi'] = Transaksi::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt->trx_id],
                    ['reg_id',$dt->reg_id],
                ])->first();
                      
                $dt['detail'] = TransaksiDetail::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt->trx_id],
                    ['reg_id',$dt->reg_id],
                ])->get(); 
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     */
    public function data(Request $request, $TrxId)
    {
        try {
            $data = TrxLab::where([['client_id',$this->clientId],['trx_id',$TrxId]])->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi registrasi lab tidak ditemukan.']);
            } 

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$TrxId],
                    ['reg_id',$data->reg_id],
                ])->first();
                  
            $data['hasil'] = $this->retrieveHasil($data->reg_id,$data->trx_id);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat mengambil data. Error : ' . $e->getMessage()]);
        }
    }

    public function retrieveHasil($regId, $trxId) {
        try {
            $details = TransaksiDetail::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$trxId)->where('reg_id',$regId)->get();         
        
            $results = []; $i = 0;
            foreach($details as $dt) {
                $templates = LabTemplate::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('tindakan_id',$dt['item_id'])->orderBy('no_urut','ASC')->get();
                
                foreach($templates as $tmp) {
                    $resLab = TrxLabHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('trx_id',$trxId)->where('reg_id',$regId)
                        ->where('lab_hasil_id',$tmp['lab_hasil_id'])->first();

                    $results[$i]['reg_id'] = $regId;
                    $results[$i]['trx_id'] = $trxId;               
                    $results[$i]['item_id'] = $dt['item_id'];
                    $results[$i]['item_nama'] = $dt['item_nama'];                    
                    $results[$i]['lab_hasil_id'] = $tmp->lab_hasil_id;
                    $results[$i]['hasil_nama'] = $tmp->hasil_nama;                    
                    $results[$i]['detail_id'] = $dt->detail_id;
                    $results[$i]['klasifikasi'] = $tmp->klasifikasi;    
                    $results[$i]['sub_klasifikasi'] = $tmp->sub_klasifikasi;

                    if($resLab) {
                        $results[$i]['is_header'] = $resLab->is_header;
                        $results[$i]['no_urut'] = $resLab->no_urut;
                        $results[$i]['trx_lab_hasil_id'] = $resLab->trx_lab_hasil_id;
                        $results[$i]['hasil_nilai'] = $resLab->hasil_nilai;
                        $results[$i]['jenis_hasil'] = $resLab->jenis_hasil;
                        $results[$i]['satuan'] = $resLab->satuan;
                        $results[$i]['hasil_operator'] = $resLab->hasil_operator;
                        $results[$i]['hasil_pilihan'] = $resLab->hasil_pilihan;
                        $results[$i]['hasil_min'] = $resLab->hasil_min;
                        $results[$i]['hasil_maks'] = $resLab->hasil_maks;
                        $results[$i]['jns_kelamin'] = $resLab->jns_kelamin;
                        $results[$i]['normal_group'] = $resLab->normal_group;
                        $results[$i]['hasil_normal_lk'] = $resLab->hasil_normal_lk;
                        $results[$i]['hasil_normal_pr'] = $resLab->hasil_normal_pr;
                        $results[$i]['is_tandai'] = $resLab->is_tandai;
                        $results[$i]['is_aktif'] = $resLab->is_aktif;
                    }
                    else {
                        $results[$i]['is_header'] = false;
                        $results[$i]['no_urut'] = $tmp->no_urut;
                        $results[$i]['trx_lab_hasil_id'] = null;
                        $results[$i]['hasil_nilai'] = null;
                        $results[$i]['jenis_hasil'] = null; 
                        $results[$i]['satuan'] = null;
                        $results[$i]['hasil_operator'] = null;
                        $results[$i]['hasil_pilihan'] = null;
                        $results[$i]['hasil_min'] = null;
                        $results[$i]['hasil_maks'] = null;
                        $results[$i]['jns_kelamin'] = null;
                        $results[$i]['normal_group'] = null;
                        $results[$i]['hasil_normal_lk'] = null;
                        $results[$i]['hasil_normal_pr'] = null;
                        $results[$i]['is_tandai'] = false;   
                        $results[$i]['is_aktif'] = true;
                    }
                    
                    $results[$i]['normal'] = LabNormal::where('is_aktif',1)->where('client_id',$this->clientId)
                        ->where('lab_hasil_id',$tmp->lab_hasil_id)->get();
                    $i++;
                }
            }

            return $results;
        }
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function saveResult(Request $request)
    {
        try {
            $regId = $request->reg_id;
            $trxId = $request->trx_id;

            $data = TrxLab::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)
                ->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi lab tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $success = TrxLab::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)
                ->update(['normal_group'=>$request->normal_group]);
    
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data group nilai normal.']);
            }

            /**
             * save transaction
             */
            foreach($request->hasil as $res) {
                //check if result already exist
                $resLab = TrxLabHasil::where('is_aktif',true)->where('client_id',$this->clientId)
                    ->where('lab_hasil_id',$res['lab_hasil_id'])
                    ->where('reg_id',$regId)->where('trx_id',$trxId)
                    ->where('trx_lab_hasil_id',$res['trx_lab_hasil_id'])
                    ->first();

                if(!$resLab) {
                    $trxLabHasilId = $this->getTrxLabHasilId();

                    $resLab = new TrxLabHasil();

                    $resLab->trx_lab_hasil_id = $trxLabHasilId;                    
                    $resLab->created_by = Auth::user()->username;
                    $resLab->client_id = $this->clientId;
                    
                    $resLab->reg_id = $regId;
                    $resLab->trx_id = $trxId;
                    $resLab->detail_id = $res['detail_id'];                    
                }

                $resLab->item_id = $res['item_id'];
                $resLab->item_nama = $res['item_nama'];
                
                $resLab->lab_hasil_id = $res['lab_hasil_id'];
                $resLab->hasil_nama = $res['hasil_nama'];
                $resLab->klasifikasi = $res['klasifikasi'];
                $resLab->sub_klasifikasi = $res['sub_klasifikasi'];
                    
                $resLab->normal_group = $request->normal_group;//$res['normal_group'];
                $resLab->jns_kelamin = $res['jns_kelamin'];
                $resLab->is_header = $res['is_header'];
                $resLab->no_urut = $res['no_urut'];
                    
                $resLab->hasil_nilai = $res['hasil_nilai'];
                $resLab->hasil_operator = $res['hasil_operator'];
                $resLab->hasil_pilihan = $res['hasil_pilihan'];
                $resLab->hasil_min = $res['hasil_min'];
                $resLab->hasil_maks = $res['hasil_maks'];
                $resLab->hasil_normal_pr = $res['hasil_normal_pr'];
                $resLab->hasil_normal_lk = $res['hasil_normal_lk'];
                $resLab->jenis_hasil = $res['jenis_hasil'];
                $resLab->satuan = $res['satuan'];
                $resLab->is_tandai = $res['is_tandai'];
                $resLab->is_aktif = $res['is_aktif'];                
                $resLab->updated_by = Auth::user()->username;
                
                $success = $resLab->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan nilai lab.']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = TrxLab::where([['client_id',$this->clientId],['reg_id',$regId],['trx_id',$trxId]])->first();
            if(!$data) { return response()->json(['success' => false, 'message' => 'Data transaksi registrasi lab tidak ditemukan.']); } 

            $data['transaksi'] = Transaksi::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id],
                ])->first();
                  
            $data['hasil'] = $this->retrieveHasil($data->reg_id,$data->trx_id);

            return response()->json(['success' => true,'message' => 'data berhasil disimpan.','data'=>$data]);
            
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menyimpan data. Error : ' . $e->getMessage()]);
        }
    }

    public function getTrxLabHasilId()
    {
        try {
            $id = $this->clientId.'-'.date('Ymd').'-LRES0001';
            $maxId = TrxLabHasil::withTrashed()->where('client_id', $this->clientId)
                ->where('trx_lab_hasil_id', 'ILIKE', $this->clientId.'-'.date('Ymd').'-LRES%')->max('trx_lab_hasil_id');
            if (!$maxId) { $id = $this->clientId.'-'.date('Ymd').'-LRES0001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.date('Ymd').'-LRES', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.date('Ymd').'-LRES000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.date('Ymd').'-LRES00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.date('Ymd').'-LRES0' . $count; } 
                else { $id = $this->clientId.'-'.date('Ymd').'-LRES' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return $this->clientId . date('Ymd') . '-LRES' . Uuid::uuid4()->getHex();
        }
    }
    
}
