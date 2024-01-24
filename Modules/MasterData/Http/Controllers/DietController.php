<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\Diet;
use Modules\Penunjang\Entities\DietMenu;

use DB;

class DietController extends Controller
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
                    $rowNumber = Diet::count();
                }
            }

            $data = Diet::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('nama_diet','ILIKE',$keyword);
                    })->orderBy('nama_diet', 'ASC')->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data diet tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $diet_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = Diet::where('client_id',$this->clientId)->where('is_aktif',1)->where('diet_id', $diet_id)->first();
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
            $id = $this->getDietId();
            
            $data = new Diet();
            $data->diet_id = $id;
            $data->nama_diet = $request->nama_diet;
            $data->catatan = $request->catatan;
            $data->komplikasi = $request->komplikasi;
            $data->is_semua_kelas = $request->is_semua_kelas;
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
            $diet_id = $request->diet_id;
            $data = Diet::where('client_id',$this->clientId)->where('is_aktif',1)->where('diet_id', $diet_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->nama_diet = $request->nama_diet;
            $data->catatan = $request->catatan;
            $data->komplikasi = $request->komplikasi;
            $data->is_semua_kelas = $request->is_semua_kelas;
            $data->is_aktif = $request->is_aktif;
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

    public function delete(Request $request, $diet_id)
    {
        try{
            $data = Diet::where('client_id',$this->clientId)->where('is_aktif',1)->where('diet_id', $diet_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Diet.']);
            }
            return response()->json(['success' => true,'message' => 'data Diet berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getDietId() 
    {
        try {
            $id = $this->clientId.'.DPL-0001';
            $maxId = Diet::withTrashed()->where('client_id', $this->clientId)->where('diet_id','LIKE',$this->clientId.'.DPL-%')->max('diet_id');
            if (!$maxId) { $id = $this->clientId.'.DPL-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.DPL-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.DPL-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.DPL-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.DPL-0'.$count; } 
                else { $id = $this->clientId.'.DPL-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.DPL-' . Uuid::uuid4()->getHex();
        }
    }

    public function getDietMenuId() 
    {
        try {
            $id = $this->clientId.'.DM-0001';
            $maxId = DietMenu::withTrashed()->where('client_id', $this->clientId)->where('diet_menu_id','LIKE',$this->clientId.'.DM-%')->max('diet_menu_id');
            if (!$maxId) { $id = $this->clientId.'.DM-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.DM-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.DM-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.DM-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.DM-0'.$count; } 
                else { $id = $this->clientId.'.DM-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.DM-' . Uuid::uuid4()->getHex();
        }
    }

    public function listDietMenu(Request $request, $diet)
    {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = DepoProduk::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }            
            
            $list = DietMenu::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('diet_id',$diet)
                    ->where(function($q) use ($keyword) {
                        $q->where('diet_menu_id','ILIKE',$keyword)
                        ->orWhere('menu_id','ILIKE',$keyword)
                        ->orWhere('nama_menu','ILIKE',$keyword);
                    })
                    ->orderBy('diet_menu_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List diet menu tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function createDietMenu(Request $request, $diet_id)
    {
        try{
            $id = $this->getDietMenuId();
            DB::connection('dbclient')->beginTransaction();
            foreach($request->detail_menu as $value) {
                $detailDiet = new DietMenu();
                $detailDiet->diet_menu_id = $id;
                $detailDiet->diet_id = $diet_id;
                $detailDiet->nama_diet = $value['nama_diet'];
                $detailDiet->menu_id = $value['menu_id'];
                $detailDiet->nama_menu = $value['nama_menu'];
                $detailDiet->is_aktif = 1;
                $detailDiet->created_by = Auth::user()->username;
                $detailDiet->updated_by = Auth::user()->username;
                $detailDiet->client_id = $this->clientId;
                $success = $detailDiet->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
                }    
            }
            
            DB::connection('dbclient')->commit();
            
            $data = DietMenu::where('client_id',$this->clientId)->where('diet_menu_id',$id)->first();
            // $data['detail_req'] = PurchaseRequestDetail::where('client_id',$this->clientId)->where('menu_makanan_id',$id)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);    
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }
}
