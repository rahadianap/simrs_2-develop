<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\Menu;
use Modules\MasterData\Entities\MenuMakanan;
use Modules\MasterData\Entities\MenuMakananDetail;

use DB;

class MenuMakananController extends Controller
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
                    $rowNumber = MenuMakanan::count();
                }
            }

            $data = MenuMakanan::select('menu_makanan_id', 'deskripsi', 'seq_no', 'kelas', 'is_aktif')
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where(function($q) use ($keyword) {
                        $q->where('deskripsi','ILIKE',$keyword);
                    })->orderBy('menu_makanan_id', 'ASC')->groupBy('menu_makanan_id', 'deskripsi', 'is_aktif')->paginate($rowNumber);
            
            foreach($data->items() as $item) {
                $dt = MenuMakananDetail::where('client_id',$this->clientId)
                    ->where('menu_makanan_id',$item['menu_makanan_id'])
                    ->where('is_aktif',1)
                    ->get();
                $item['detail_menu'] = $dt;
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data makanan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            DB::connection('dbclient')->beginTransaction();
            $id = $this->getId();
            $menu = new MenuMakanan();
            $menu->menu_makanan_id = $id;
            $menu->menu_id = $request->menu_id;
            $menu->nama_menu = $request->nama_menu;
            $menu->deskripsi = $request->deskripsi;
            $menu->jumlah = 1;
            $menu->satuan = '-';
            $menu->seq_no = $request->sequence;
            $menu->meal_set = $request->meal_set;
            $menu->kelas = $request->nama_kelas;
            $menu->is_aktif = 1;
            $menu->created_by = Auth::user()->username;
            $menu->updated_by = Auth::user()->username;
            $menu->client_id = $this->clientId;
            $success = $menu->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }   

            foreach($request->foods as $value) {
                $detailId = $this->getDetailId();
                $detailData = new MenuMakananDetail();
                $detailData->menu_makanan_detail_id = $detailId;
                $detailData->menu_makanan_id = $menu->menu_makanan_id;
                $detailData->makanan_id = $value['makanan_id'];
                $detailData->nama_makanan = $value['nama_makanan'];
                $detailData->is_standard = $value['is_standard'];
                $detailData->is_optional = $value['is_optional'];
                $detailData->is_aktif = 1;
                $detailData->created_by = Auth::user()->username;
                $detailData->updated_by = Auth::user()->username;
                $detailData->client_id = $this->clientId;
                $success = $detailData->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
                }    
            }
            
            DB::connection('dbclient')->commit();
            
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$detailData]);    
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function getId() 
    {
        try {
            $id = $this->clientId.'.'.date('Ym').'-MN0001';
            $maxId = MenuMakanan::withTrashed()->where('client_id', $this->clientId)->where('menu_makanan_id','LIKE',$this->clientId.'.'.date('Ym').'-MN%')->max('menu_makanan_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'-MN0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'-MN','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'-MN000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'-MN00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'-MN0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'-MN'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'-MN' . Uuid::uuid4()->getHex();
        }
    }

    public function getDetailId() 
    {
        try {
            $id = $this->clientId.'.'.date('Ym').'-MND0001';
            $maxId = MenuMakananDetail::withTrashed()->where('client_id', $this->clientId)->where('menu_makanan_detail_id','LIKE',$this->clientId.'.'.date('Ym').'-MND%')->max('menu_makanan_detail_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'-MND0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'-MND','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'-MND000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'-MND00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'-MND0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'-MND'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'-MND' . Uuid::uuid4()->getHex();
        }
    }

}
