<?php

namespace Modules\Radiologi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\SubTindakan;
use Modules\MasterData\Entities\UnitTindakan;
use Modules\MasterData\Entities\TindakanBhp;

use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\UnitPelayanan;

class PemeriksaanRadiologiController extends Controller
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
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = Tindakan::where('client_id',$this->clientId)->count(); }
            }
            
            $klasifikasi = 'RADIOLOGI'; 

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            $list = Tindakan::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('klasifikasi',$klasifikasi)
                    ->where(function($q) use ($keyword) {
                        $q->where('tindakan_nama','ILIKE',$keyword)
                        ->orWhere('tindakan_id','ILIKE',$keyword);
                    })
                    ->with(['kelompok_tindakan','kelompok_tagihan','kelompok_vclaim'])
                    ->orderBy('klasifikasi','ASC')->orderBy('tindakan_nama','ASC')->paginate($per_page); 
            
            // foreach($list->items() as $item) {
            //     if($item['klasifikasi'] == 'RADIOLOGI') {
            //         $item['labItems'] = LabTemplate::where('tindakan_id',$item['tindakan_id'])
            //             ->where('client_id',$this->clientId)
            //             ->where('is_aktif',1)
            //             ->orderBy('klasifikasi','ASC')
            //             ->orderBy('no_urut','ASC')
            //             ->get();
            //     }
            // }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List tindakan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('radiologi::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('radiologi::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('radiologi::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
