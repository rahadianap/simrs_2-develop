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
     * Show the specified resource.
     */
    public function data(Request $request, $tindakanId)
    {
        try{
            $data = LabTemplate::where('client_id',$this->clientId)->where('tindakan_id',$tindakanId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }
            $datalists = LabTemplate::where('client_id',$this->clientId)->where('tindakan_id',$tindakanId)->where('is_aktif',1)->get();
            if($datalists) {
                foreach($datalists as $dt) {
                    $dt['normal'] = LabNormal::where('client_id',$this->clientId)->where('lab_hasil_id',$dt['lab_hasil_id'])->where('is_aktif',1)->get();
                }
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $datalists]);
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
            $validator = Validator::make($request->all(), ['lab_hasil_id' => 'required','tindakan_id' => 'required']);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $this->getTemplateId();
            
            $data = new LabTemplate();
            $data->lab_template_id = $id;

            $data->lab_hasil_id = $request->lab_hasil_id;
            $data->hasil_nama = $request->hasil_nama;            
            $data->tindakan_id = $request->tindakan_id;
            $data->tindakan_nama = $request->tindakan_nama;

            $data->no_urut = $request->no_urut;
            $data->level = $request->level;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
    
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data']);
            }
            return response()->json(['success' => true,'message' => 'Data berhasil disimpan','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     */
    public function delete(Request $request, $id)
    {
        try{
            $success = LabTemplate::where('lab_template_id', $id)->where('client_id',$this->clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            if(!$success) {
                return response()->json(['success'=>false,'message'=>'Data tidak berhasil dihapus.']);
            }
            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil dihapus.','error'=>$e->getMessage()]);
        }
    }

    public function getTemplateId() {
        try {
            $id = $this->clientId.'-TLB0001';
            $maxId = LabTemplate::withTrashed()->where('lab_template_id','LIKE',$this->clientId.'-TLB%')->max('lab_template_id');
            if (!$maxId) { $id = $this->clientId.'-TLB0001'; }
            else {
                $maxId = str_replace($this->clientId.'-TLB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-TLB000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-TLB00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-TLB0'.$count; } 
                else { $id = $this->clientId.'-TLB'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'TLB'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }
}
