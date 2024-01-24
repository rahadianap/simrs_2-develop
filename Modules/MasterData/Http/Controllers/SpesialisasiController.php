<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\Spesialisasi;

use DB;

class SpesialisasiController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request)
    {
        try{
            $rowNumber = 10;
            $keyword = '%%';

            if($request->has('per_page')){
                $rowNumber = $request->get('per_page');
                if($rowNumber == 'ALL') { $rowNumber = Spesialisasi::where('client_id',$this->clientId)->count(); }
            }
            if($request->has('keyword')){
                $keyword = '%'.$request->get('keyword').'%';
            }
            
            $data = Spesialisasi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where(function($q) use ($keyword){
                    $q->where('spesialisasi_id','ILIKE',$keyword)
                    ->orWhere('spesialisasi','ILIKE',$keyword);
                })->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $spesialisasiId)
    {
        try{
            $data = Spesialisasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('spesialisasi_id', $spesialisasiId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data tidak ditemukan','error'=>'data tidak ditemukan.']);
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
            $id = $this->getSpesialisId();
        
            $data = new Spesialisasi();
            $data->spesialisasi_id = $id;
            $data->spesialisasi = $request->spesialisasi;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
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
            $spesialisasiId = $request->spesialisasi_id;
            $data = Spesialisasi::where('client_id',$request->header('X-cid'))->where('is_aktif',1)->where('spesialisasi_id', $spesialisasiId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data tidak ditemukan']);
            }
            
            $success = $data->update(['spesialisasi_id' => $request->spesialisasi_id,'spesialisasi' => $request->spesialisasi,'updated_by'=>Auth::user()->username]);
            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data']);
            }
            return response()->json(['success'=>true,'message'=>'Data berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $spesialisasiId)
    {
        try{
            
            $data = Spesialisasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('spesialisasi_id', $spesialisasiId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data tidak ditemukan.']);
            }

            $success = Spesialisasi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('spesialisasi_id', $spesialisasiId)->forceDelete();
            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data spesialisasi.']);
            }
            return response()->json(['success' => true,'message' => 'data spesialisasi berhasil dihapus.']); 
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function getSpesialisId() 
    {
        try {
            $id = $this->clientId.'.SPS-0001';
            $maxId = Spesialisasi::withTrashed()->where('spesialisasi_id','ILIKE',$this->clientId.'.SPS-%')->max('spesialisasi_id');
            if (!$maxId) { $id = $this->clientId.'.SPS-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.SPS-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.SPS-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.SPS-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.SPS-0'.$count; } 
                else { $id = $this->clientId.'.SPS-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return null;//$this->clientId.'.SPS-'.Uuid::uuid4()->getHex();
        }
    }

}
