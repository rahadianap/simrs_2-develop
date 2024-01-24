<?php

namespace Modules\Laboratorium\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use DB;
use Carbon\Carbon;
use Modules\Laboratorium\Entities\LabItemHasil;
use Modules\Laboratorium\Entities\LabNormal;
use Modules\Laboratorium\Entities\LabTemplate;

class ItemLabController extends Controller
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
        try{
            $perPage = 10;
            $aktif = "true";
            $keyword = "%%";
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage = LabItemHasil::where('client_id',$this->clientId)->count(); }
            }

            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }  
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            $lists = LabItemHasil::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use($keyword){
                        $q->where('hasil_nama','ILIKE',$keyword)
                        ->orWhere('lab_hasil_id','ILIKE',$keyword)
                        ->orWhere('klasifikasi','ILIKE',$keyword);
                    })
                    ->orderBy('lab_hasil_id','ASC')
                    ->paginate($perPage); 
            
            foreach($lists->items() as $item) {
                $item['normal'] = LabNormal::where('client_id',$this->clientId)->where('lab_hasil_id',$item['lab_hasil_id'])->where('is_aktif',1)->get();
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $lists]);
        }

        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Show the specified resource.
     */
    public function data(Request $request, $id)
    {
        try{
            $data = LabItemHasil::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }
            $data['normal'] = LabNormal::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->where('is_aktif',1)->get();
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), ['klasifikasi' => 'required','hasil_nama' => 'required']);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $this->getItemLabId();
            DB::connection('dbclient')->beginTransaction();
            
            $data = new LabItemHasil();
            $data->lab_hasil_id = $id;
            $data->hasil_nama = $request->hasil_nama;
            $data->klasifikasi = $request->klasifikasi;
            $data->sub_klasifikasi = $request->sub_klasifikasi;
            $data->no_urut = $request->no_urut;
            $data->kode_rl = $request->kode_rl;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data']);
            }

            /**
             * save normal value 
             */
            foreach($request->normal as $dt){
                $val = new LabNormal();
                $val->lab_normal_id = $id.Uuid::uuid4()->getHex();
                $val->lab_hasil_id = $id;
                $val->normal_group = $dt['normal_group'];
                $val->hasil_nama = $request->hasil_nama;
                $val->jenis_hasil = $dt['jenis_hasil'];

                $val->lk_operator = $dt['lk_operator'];
                $val->lk_nilai_min = $dt['lk_nilai_min'];
                $val->lk_nilai_maks = $dt['lk_nilai_maks'];
                $val->lk_nilai_pilihan = $dt['lk_nilai_pilihan'];
                $val->lk_normal = $dt['lk_normal'];

                $val->pr_operator = $dt['pr_operator'];
                $val->pr_nilai_min = $dt['pr_nilai_min'];
                $val->pr_nilai_maks = $dt['pr_nilai_maks'];
                $val->pr_nilai_pilihan = $dt['pr_nilai_pilihan'];
                $val->pr_normal = $dt['pr_normal'];

                $val->satuan = $dt['satuan'];
                $val->is_aktif = true;
                $val->client_id = $this->clientId;
                $val->created_by = Auth::user()->username;
                $val->updated_by = Auth::user()->username;
        
                $success = $val->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data nilai normal']);
                }
            }

            DB::connection('dbclient')->commit();

            $resdata = LabItemHasil::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->first();
            $resdata['normal'] = LabNormal::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->where('is_aktif',1)->get();

            return response()->json(['success' => true,'message' => 'Data berhasil disimpan','data'=>$resdata]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan.','error'=>$e->getMessage()]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $id = $request->lab_hasil_id;
            $data = LabItemHasil::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            $labTemp = LabTemplate::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('lab_hasil_id',$id)
                ->first();

            DB::connection('dbclient')->beginTransaction();
            $success = LabItemHasil::where('client_id',$this->clientId)
                ->where('lab_hasil_id',$id)
                ->update([
                    'hasil_nama' => $request->hasil_nama,
                    'klasifikasi' => $request->klasifikasi,
                    'sub_klasifikasi' => $request->sub_klasifikasi,
                    'no_urut' => $request->no_urut,
                    'kode_rl' => $request->kode_rl,
                    'is_aktif' => $request->is_aktif,
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data item hasil']);
            }

            /**
             * update lab template
             */
            if($labTemp) {
                $success = LabTemplate::where('client_id',$this->clientId)
                    ->where('lab_hasil_id',$id)
                    ->update([
                        'klasifikasi' => $request->klasifikasi,
                        'sub_klasifikasi' => $request->sub_klasifikasi,
                        'updated_by' => Auth::user()->username
                    ]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam mengubah data template']);
                }
            }
            
            /**
             * save normal value 
             */
            foreach($request->normal as $dt){
                $val = LabNormal::where('client_id',$this->clientId)
                    ->where('normal_group',$dt['normal_group'])
                    ->where('lab_hasil_id',$id)
                    ->where('lab_normal_id',$dt['lab_normal_id'])
                    ->first();

                if($val) {
                    $success = LabNormal::where('client_id',$this->clientId)
                        ->where('normal_group',$dt['normal_group'])
                        ->where('lab_hasil_id',$id)
                        ->where('lab_normal_id',$dt['lab_normal_id'])
                        ->update(['is_aktif'=>$dt['is_aktif'],'updated_by'=>Auth::user()->username]);
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam ubah data nilai normal']);
                    }
                }

                else {
                    $val = new LabNormal();
                    $val->lab_normal_id = $id.Uuid::uuid4()->getHex();
                    $val->lab_hasil_id = $id;
                    $val->hasil_nama = $request->hasil_nama;
                    $val->normal_group = $dt['normal_group'];
                    
                    $val->jenis_hasil = $dt['jenis_hasil'];
                    $val->lk_operator = $dt['lk_operator'];
                    $val->lk_nilai_min = $dt['lk_nilai_min'];
                    $val->lk_nilai_maks = $dt['lk_nilai_maks'];
                    $val->lk_nilai_pilihan = $dt['lk_nilai_pilihan'];
                    $val->lk_normal = $dt['lk_normal'];
    
                    $val->pr_operator = $dt['pr_operator'];
                    $val->pr_nilai_min = $dt['pr_nilai_min'];
                    $val->pr_nilai_maks = $dt['pr_nilai_maks'];
                    $val->pr_nilai_pilihan = $dt['pr_nilai_pilihan'];
                    $val->pr_normal = $dt['pr_normal'];
    
                    $val->satuan = $dt['satuan'];
                    $val->is_aktif = $dt['is_aktif'];
                    $val->created_by = Auth::user()->username;
                    $val->updated_by = Auth::user()->username;
                    $val->client_id = $this->clientId;
                    
                    $success = $val->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam ubah data nilai normal']);
                    }
                }
            }            

            DB::connection('dbclient')->commit();

            $resdata = LabItemHasil::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->first();
            $resdata['normal'] = LabNormal::where('client_id',$this->clientId)->where('lab_hasil_id',$id)->where('is_aktif',1)->get();

            return response()->json(['success' => true,'message' => 'Data berhasil diubah','data'=>$resdata]);
        }

        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diubah.','error'=>$e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete(Request $request, $id)
    {
        try{
            $template = LabTemplate::where('client_id',$this->clientId)
                ->where('lab_hasil_id',$id)
                ->where('is_aktif',true)
                ->first();

            DB::connection('dbclient')->beginTransaction();
            $success = LabItemHasil::where('lab_hasil_id', $id)->where('client_id',$this->clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'Data tidak berhasil dihapus.']);
            }

            if($template) {
                $success = LabTemplate::where('lab_hasil_id', $id)->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'Data tidak berhasil dihapus (template).']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil dihapus.','error'=>$e->getMessage()]);
        }
    }

    public function getItemLabId() {
        try {
            $id = $this->clientId.'-ILB0001';
            $maxId = LabItemHasil::withTrashed()->where('lab_hasil_id','LIKE',$this->clientId.'-ILB%')->max('lab_hasil_id');
            if (!$maxId) { $id = $this->clientId.'-ILB0001'; }
            else {
                $maxId = str_replace($this->clientId.'-ILB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-ILB000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-ILB00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-ILB0'.$count; } 
                else { $id = $this->clientId.'-ILB'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'ILB'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}
