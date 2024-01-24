<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Asset\Entities\Asset;
use Carbon\Carbon;
use DB;
use Modules\ManajemenUser\Entities\ClientMember;

class AssetController extends Controller
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
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = Asset::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = Asset::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('nama_asset','ILIKE',$keyword)
                        ->orWhere('asset_id','ILIKE',$keyword);
                    })->orderBy('nama_asset','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $asset_id)
    {
        try{
            if(!$this->clientId) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }

            $data = Asset::where('client_id',$this->clientId)->where('is_aktif',1)->where('asset_id', $asset_id)->first();
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
            $id = $this->getAssetId();
            
            $data = new Asset();
            $data->asset_id = $id;
            $data->nomor_asset = $request->nomor_asset;
            $data->group_asset = $request->group_asset;
            $data->tgl_perolehan = $request->tgl_perolehan;
            $data->nama_asset = $request->nama_asset;
            $data->brand = $request->brand;
            $data->nomor_seri = $request->nomor_seri;
            $data->lokasi = $request->lokasi;
            $data->deskripsi = $request->deskripsi;
            $data->tgl_pemakaian = $request->tgl_pemakaian;
            $data->nilai_asset = $request->nilai_asset;
            $data->masa_pakai = $request->masa_pakai;
            $data->status = $request->status;
            $data->is_aktif = true;
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
            $asset_id = $request->asset_id;
            $data = Asset::where('client_id',$this->clientId)->where('is_aktif',1)->where('asset_id', $asset_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data->nomor_asset = $request->nomor_asset;
            $data->group_asset = $request->group_asset;
            $data->tgl_perolehan = $request->tgl_perolehan;
            $data->nama_asset = $request->nama_asset;
            $data->brand = $request->brand;
            $data->nomor_seri = $request->nomor_seri;
            $data->lokasi = $request->lokasi;
            $data->deskripsi = $request->deskripsi;
            $data->tgl_pemakaian = $request->tgl_pemakaian;
            $data->nilai_asset = $request->nilai_asset;
            $data->masa_pakai = $request->masa_pakai;
            $data->status = $request->status;
            $data->is_aktif = true;
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

    public function delete(Request $request, $asset_id)
    {
        try{
            $data = Asset::where('client_id',$this->clientId)->where('is_aktif',1)->whereIn('asset_id', explode(',',$asset_id))->get();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            foreach($data as $record) {
                $record->is_aktif = 0;
                $record->updated_by = Auth::user()->username;
                $record->updated_at = Carbon::now()->format('Y-m-d H:i:s');
                $success = $record->save();

                if(!$success) {
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan data Menu.']);
                }
            }
    
            return response()->json(['success' => true,'message' => 'data Menu berhasil dinonaktifkan.']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menonkatifkan data.','error'=> $e->getMessage()]);
        }
    }

    public function getAssetId() 
    {
        try {
            $id = $this->clientId.'.AST-0001';
            $maxId = Asset::withTrashed()->where('client_id', $this->clientId)->where('asset_id','LIKE',$this->clientId.'.AST-%')->max('asset_id');
            if (!$maxId) { $id = $this->clientId.'.AST-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.AST-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.AST-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.AST-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.AST-0'.$count; } 
                else { $id = $this->clientId.'.AST-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.AST-' . Uuid::uuid4()->getHex();
        }
    }

    public function overviewData(Request $request)
    {
        //data seluruh asset
        try {
            $data = Asset::select(DB::raw(
                'count(asset_id) as totalAsetTerdata, 
                sum(nilai_asset) as totalNilaiAset,
                count(distinct(lokasi)) as totalLokasi'
                )
                )->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kelompok tagihan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function listTeknisi(Request $request)
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
                if($per_page == "ALL"){ $per_page = Asset::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = ClientMember::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)->where('is_teknisi',true)
                    ->where(function($q) use ($keyword) {
                        $q->where('user_id','ILIKE',$keyword)
                        ->orWhere('client_id','ILIKE',$keyword);
                    })->orderBy('client_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List teknisi tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
}
