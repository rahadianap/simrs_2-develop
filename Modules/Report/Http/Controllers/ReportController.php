<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\Report\Entities\Report;

class ReportController extends Controller
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
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = Report::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = Report::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('report_name','ILIKE',$keyword)
                        ->orWhere('report_id','ILIKE',$keyword);
                    })->orderBy('report_name','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            $id = $this->getReportId();
            
            $data = new Report();
            $data->report_id      = $id;
            $data->report_name    = $request->report_name;
            $data->report_url     = $request->report_url;
            $data->is_aktif       = 1;
            $data->client_id      = $this->clientId;
            $data->created_by     = Auth::user()->username;
            $data->updated_by     = Auth::user()->username;

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

    public function getReportId() 
    {
        try {
            $id = $this->clientId.'.RPT-0001';
            $maxId = Report::withTrashed()->where('client_id', $this->clientId)->where('report_id','LIKE',$this->clientId.'.RPT-%')->max('report_id');
            if (!$maxId) { $id = $this->clientId.'.RPT-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.RPT-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.RPT-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.RPT-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.RPT-0'.$count; } 
                else { $id = $this->clientId.'.RPT-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.RPT-' . Uuid::uuid4()->getHex();
        }
    }
}
