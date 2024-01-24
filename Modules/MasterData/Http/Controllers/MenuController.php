<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\Menu;

use DB;

class MenuController extends Controller
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
                    $rowNumber = Menu::count();
                }
            }

            $data = Menu::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('nama_menu','ILIKE',$keyword)
                        ->orWhere('kelompok','ILIKE',$keyword);
                    })->orderBy('nama_menu', 'ASC')->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data makanan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $menu_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = Menu::where('client_id',$this->clientId)->where('is_aktif',1)->where('menu_id', $menu_id)->first();
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
            $id = $this->getMenuId();
            
            $data = new Menu();
            $data->menu_id = $id;
            $data->nama_menu = $request->nama_menu;
            $data->kelompok = $request->kelompok;
            $data->is_menu_khusus = $request->is_menu_khusus;
            $data->catatan = $request->catatan;
            $data->is_jual = $request->is_jual;
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
            $menu_id = $request->menu_id;
            $data = Menu::where('client_id',$this->clientId)->where('is_aktif',1)->where('menu_id', $menu_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->nama_menu = $request->nama_menu;
            $data->kelompok = $request->kelompok;
            $data->is_menu_khusus = $request->is_menu_khusus;
            $data->catatan = $request->catatan;
            $data->is_jual = $request->is_jual;
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

    public function delete(Request $request, $menu_id)
    {
        try{
            $data = Menu::where('client_id',$this->clientId)->where('is_aktif',1)->where('menu_id', $menu_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Menu.']);
            }
            return response()->json(['success' => true,'message' => 'data Menu berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getMenuId() 
    {
        try {
            $id = $this->clientId.'.FMN-0001';
            $maxId = Menu::withTrashed()->where('client_id', $this->clientId)->where('menu_id','LIKE',$this->clientId.'.FMN-%')->max('menu_id');
            if (!$maxId) { $id = $this->clientId.'.FMN-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.FMN-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.FMN-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.FMN-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.FMN-0'.$count; } 
                else { $id = $this->clientId.'.FMN-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.FMN-' . Uuid::uuid4()->getHex();
        }
    }
}
