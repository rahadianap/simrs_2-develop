<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\KelompokMenu;

use DB;

class KelompokMenuController extends Controller
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
                if($per_page == "ALL"){ $per_page = KelompokMenu::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KelompokMenu::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('kelompok','ILIKE',$keyword)
                        ->orWhere('kelompok_menu_id','ILIKE',$keyword);
                    })->orderBy('kelompok','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $menu_group_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = KelompokMenu::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelompok_menu_id', $menu_group_id)->first();
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
            $id = $this->getKelompokMenuId();
            
            $data = new KelompokMenu();
            $data->kelompok_menu_id = $id;
            $data->kelompok = $request->kelompok;
            $data->catatan = $request->catatan;
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
            $menu_group_id = $request->kelompok_menu_id;
            $data = KelompokMenu::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelompok_menu_id', $menu_group_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->kelompok = $request->kelompok;
            $data->catatan = $request->catatan;
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

    public function delete(Request $request, $menu_group_id)
    {
        try{
            $data = KelompokMenu::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelompok_menu_id', $menu_group_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Kelompok Makanan.']);
            }
            return response()->json(['success' => true,'message' => 'data Kelompok Makanan berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getKelompokMenuId() 
    {
        try {
            $id = $this->clientId.'.MNG-0001';
            $maxId = KelompokMenu::withTrashed()->where('client_id', $this->clientId)->where('kelompok_menu_id','LIKE',$this->clientId.'.MNG-%')->max('kelompok_menu_id');
            if (!$maxId) { $id = $this->clientId.'.MNG-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.MNG-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.MNG-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.MNG-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.MNG-0'.$count; } 
                else { $id = $this->clientId.'.MNG-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.MNG-' . Uuid::uuid4()->getHex();
        }
    }
}
