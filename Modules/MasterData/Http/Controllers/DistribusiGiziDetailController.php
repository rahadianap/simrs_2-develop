<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\DistribusiGiziDetail;

use DB;

class DistribusiGiziDetailController extends Controller
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
                    $rowNumber = DistribusiGiziDetail::count();
                }
            }

            $data = DistribusiGiziDetail::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('dist_gizi_id','ILIKE',$keyword)
                        ->orWhere('unit_id','ILIKE',$keyword)
                        ->orWhere('reg_id','ILIKE',$keyword)
                        ->orWhere('trx_id','ILIKE',$keyword)
                        ->orWhere('diet_id','ILIKE',$keyword)
                        ->orWhere('kelas_id','ILIKE',$keyword);
                    })->orderBy('dist_gizi_id', 'ASC')->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }
}
