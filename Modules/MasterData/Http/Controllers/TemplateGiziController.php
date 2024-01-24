<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\TemplateGizi;

use DB;

class TemplateGiziController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request)
    {
        try{
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = TemplateGizi::count();
                }
            }

            $data = TemplateGizi::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('nama_template','ILIKE',$keyword);
                    })->orderBy('nama_template', 'ASC')->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data bahan makanan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $temp_gizi_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = TemplateGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('temp_gizi_id', $temp_gizi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            $id = $this->getTemplateGiziId();
            
            $data = new TemplateGizi();
            $data->temp_gizi_id = $id;
            $data->nama_template = $request->nama_template;
            $data->tgl_dibuat = $request->tgl_dibuat;
            $data->tgl_berlaku = $request->tgl_berlaku;
            $data->catatan = $request->catatan;
            $data->jml_hari = $request->jml_hari;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data']);
            }
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try{
            $temp_gizi_id = $request->temp_gizi_id;
            $data = TemplateGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('temp_gizi_id', $temp_gizi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->nama_template = $request->nama_template;
            $data->tgl_dibuat = $request->tgl_dibuat;
            $data->tgl_berlaku = $request->tgl_berlaku;
            $data->catatan = $request->catatan;
            $data->jml_hari = $request->jml_hari;
            $data->is_aktif = 1;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success'=>true,'message'=>'Ada kesalahan saat mengubah data.']);    
            }
            return response()->json(['success'=>true,'message'=>'data berhasil disimpan','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ada kesalahan saat mengubah data','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $temp_gizi_id)
    {
        try{
            $data = TemplateGizi::where('client_id',$this->clientId)->where('is_aktif',1)->where('temp_gizi_id', $temp_gizi_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Template Gizi.']);
            }
            return response()->json(['success' => true,'message' => 'data Template Gizi berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getTemplateGiziId() 
    {
        try {
            $id = $this->clientId.'.TNT-0001';
            $maxId = TemplateGizi::withTrashed()->where('client_id', $this->clientId)->where('temp_gizi_id','LIKE',$this->clientId.'.TNT-%')->max('temp_gizi_id');
            if (!$maxId) { $id = $this->clientId.'.TNT-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.TNT-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.TNT-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.TNT-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.TNT-0'.$count; } 
                else { $id = $this->clientId.'.TNT-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.TNT-' . Uuid::uuid4()->getHex();
        }
    }
}
