<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\SMF;
use Modules\MasterData\Entities\Dokter;

use DB;

class SMFController extends Controller
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
            if($request->has('per_page')) {
                $rowNumber = $request->get('per_page');
                if($rowNumber == 'ALL') { $rowNumber = SMF::where('client_id',$this->clientId)->count(); }
            }
            if($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            $data = SMF::where('client_id',$this->clientId)->where('is_aktif',true)
                ->where(function($q) use ($keyword){
                    $q->where('smf_id','ILIKE',$keyword)
                    ->orWhere('smf_nama','ILIKE',$keyword);
                })->paginate($rowNumber);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan. error: '.$e->getMessage()]);
        }
    }

    public function data(Request $request, $smfId)
    {
        try{
            $data = SMF::where('client_id',$this->clientId)->where('is_aktif',1)->where('smf_id', $smfId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
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
            $id = $this->getSMFId();        
            $data = new SMF();
            $data->smf_id = $id;
            $data->smf_nama = strtoupper($request->smf_nama);
            $data->asesmen_id = $request->asesmen_id;
            $data->is_aktif = 1;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $data->client_id = $this->clientId;
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
            $smfId = $request->smf_id;
            $data = SMF::where('client_id',$this->clientId)->where('is_aktif',1)->where('smf_id', $smfId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data tidak ditemukan.']);
            }
            $success = $data->update([ 'smf_nama' => strtoupper($request->smf_nama) ]);
            if(!$success) {
                return response()->json(['success'=>false,'message'=>'ada kesalahan saat mengubah data.']);
            }
            return response()->json(['success'=>true,'message'=>'data berhasil diubah','data'=>$data]);  
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam mengupdate data. ','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $smfId)
    {
        try{
            $data = SMF::where('client_id',$this->clientId)->where('is_aktif',1)->where('smf_id', $smfId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data','error'=>'data tidak ditemukan.']);
            }
            
            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data SMF.']);
            }
            return response()->json(['success' => true,'message' => 'data SMF berhasil dihapus.']); 
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function getSMFId() 
    {
        try {
            $id = $this->clientId.'.SMF'.'-0001';
            $maxId = SMF::withTrashed()->where('smf_id','LIKE',$this->clientId.'.SMF-%')->max('smf_id');
            if (!$maxId) { $id = $this->clientId.'.SMF-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.SMF-'.$clientId,'',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.SMF-'.$clientId.'000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.SMF-'.$clientId.'00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.SMF-'.$clientId.'0'.$count; } 
                else { $id = $this->clientId.'.SMF-'.$clientId.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.SMF-'.Uuid::uuid4()->getHex();
        }
    }
}
