<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\KodeRL;

class KodeRLController extends Controller
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
     * group RL
     */
    public function listGroup(Request $request) {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = KodeRL::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KodeRL::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)->where('is_group',true)
                    ->where(function($q) use ($keyword) {
                        $q->where('rl_id','ILIKE',$keyword)
                        ->orWhere('kode_rl','ILIKE',$keyword)
                        ->orWhere('nama_laporan','ILIKE',$keyword);
                    })
                    ->orderBy('no_urut','ASC')
                    ->orderBy('kode_rl','ASC')
                    ->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List group RL tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function listGroupItems(Request $request,$groupId) {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                if($aktif == "all"){ $aktif = '%%'; }
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == "ALL"){ $per_page = KodeRL::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KodeRL::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('group_id',$groupId)
                    ->where('is_group',false)
                    ->where(function($q) use ($keyword) {
                        $q->where('rl_id','ILIKE',$keyword)
                        ->orWhere('kode_rl','ILIKE',$keyword)
                        ->orWhere('nama_laporan','ILIKE',$keyword);
                    })
                    ->orderBy('no_urut','ASC')
                    ->orderBy('kode_rl','ASC')
                    ->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kode RL tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function dataGroup(Request $request, $id)
    {
        try {
            $list = KodeRL::where('rl_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            $list['rl_codes'] = KodeRL::where('group_id',$id)->where('client_id',$this->clientId)->get(); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function createGroup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_rl' => 'required|max:20',
                'nama_laporan' => 'required|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $this->getGroupRlId();

            $data                   = new KodeRL();
            $data->rl_id            = $id;
            $data->kode_rl          = strtoupper($request->kode_rl);
            $data->nama_laporan     = strtoupper($request->nama_laporan);
            $data->no_urut          = $request->no_urut;
            $data->header_rl        = null;
            $data->header_id        = null;
            $data->group_rl         = strtoupper($request->kode_rl);
            $data->group_id         = $id;
            $data->rl_level         = 0;
            $data->is_group         = true;
            $data->is_valid_rl      = false;            
            $data->client_id        = $this->clientId;
            $data->is_aktif         = true;
            $data->created_by       = Auth::user()->username;
            $data->updated_by       = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data kode RL.']);
            }
            return response()->json(['success' => true,'message' => 'Data kode RL berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah kode RL tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function updateGroup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_rl' => 'required|max:20',
                'nama_laporan' => 'required|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $request->rl_id;
            $data = KodeRL::where('client_id',$this->clientId)->where('rl_id', $id)->first();            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data kode RL tidak ditemukan']);
            }
            $namaLap = $request->nama_laporan;
            if($request->is_group == true) {
                $namaLap = strtoupper($request->nama_laporan);
            }

            $success = KodeRL::where('client_id',$this->clientId)
                ->where('rl_id', $id)
                ->update([
                    'kode_rl'       => strtoupper($request->kode_rl),
                    'nama_laporan'  => strtoupper($request->nama_laporan),
                    'no_urut'       => $request->no_urut,
                    'header_rl'     => null,
                    'header_id'     => null,
                    'group_rl'      => strtoupper($request->kode_rl),
                    'group_id'      => $id,
                    'is_group'      => true,
                    'rl_level'      => 0,
                    'is_valid_rl'   => false,
                    'is_aktif'      => $request->is_aktif,
                    'updated_by'    =>Auth::user()->username
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data kode RL']);
            }
            return response()->json(['success'=>true,'message'=>'Data kode RL berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data kode RL tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function deleteGroup(Request $request, $id)
    {
        try {
            $data = KodeRL::where('rl_id', $id)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus kode RL tidak berhasil.','error'=>$e->getMessage()]);
        }
    }


    /**
     * item kode RL
     */

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
                if($per_page == "ALL"){ $per_page = KodeRL::where('client_id',$this->clientId)->count(); }
            }

            $groupId = '%%';
            if($request->has('rl_group')) {
                $groupId = $request->input('rl_group');
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }

            $list = KodeRL::where('client_id',$this->clientId)->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword,$groupId) {
                        $q->where('rl_id','ILIKE',$keyword)
                        ->orWhere('kode_rl','ILIKE',$keyword)
                        ->orWhere('header_rl',$groupId)
                        ->orWhere('nama_laporan','ILIKE',$keyword);
                    })->orderBy('rl_id','ASC')->orderBy('rl_level','ASC')
                    ->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List kode RL tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $list = KodeRL::where('rl_id',$id)->where('client_id',$this->clientId)->first(); 
            if(!$list) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_rl' => 'required|max:20',
                'nama_laporan' => 'required|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $this->getKodeRlId();
            
            $data                   = new KodeRL();
            $data->rl_id            = $id;
            $data->kode_rl          = strtoupper($request->kode_rl);
            $data->nama_laporan     = $request->nama_laporan;
            $data->no_urut          = $request->no_urut;            
            $data->header_rl        = $request->header_rl;
            $data->header_id        = $request->header_id;
            $data->group_rl         = $request->group_rl;
            $data->group_id         = $request->group_id;
            $data->is_group         = $request->is_group;
            $data->rl_level         = $request->rl_level;
            $data->is_valid_rl      = $request->is_valid_rl;            
            $data->client_id        = $this->clientId;
            $data->is_aktif         = $request->is_aktif;
            $data->created_by       = Auth::user()->username;
            $data->updated_by       = Auth::user()->username;
    
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam penyimpanan data kode RL.']);
            }
            return response()->json(['success' => true,'message' => 'Data kode RL berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah kode RL tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_rl' => 'required|max:20',
                'nama_laporan' => 'required|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $id = $request->rl_id;
            $data = KodeRL::where('client_id',$this->clientId)->where('rl_id', $id)->first();            
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data kode RL tidak ditemukan']);
            }

            $success = KodeRL::where('client_id',$this->clientId)
                ->where('rl_id', $id)
                ->update([
                    'kode_rl'       => strtoupper($request->kode_rl),
                    'nama_laporan'  => $request->nama_laporan,
                    'no_urut'       => $request->no_urut,
                    'header_rl'     => $request->header_rl,
                    'header_id'     => $request->header_id,
                    'group_rl'     => $request->group_rl,
                    'group_id'     => $request->group_id,
                    'is_group'      => $request->is_group,
                    'rl_level'      => $request->rl_level,
                    'is_valid_rl'   => $request->is_valid_rl,
                    'is_aktif'      => $request->is_aktif,
                    'updated_by'    =>Auth::user()->username
                ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data kode RL']);
            }
            return response()->json(['success'=>true,'message'=>'Data kode RL berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data kode RL tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = KodeRL::where('rl_id', $id)
                ->where('client_id',$this->clientId)->update([
                    'updated_by' => Auth::user()->username,
                    'updated_at' => now(),
                    'is_aktif' => 0
                ]);

            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus kode RL tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function getKodeRlId() 
    {
        try {
            $id = $this->clientId.'-RL0001';
            $maxId = KodeRL::withTrashed()->where('rl_id','LIKE',$this->clientId.'-RL%')->max('rl_id');
            if (!$maxId) { $id = $this->clientId.'-RL0001'; }
            else {
                $maxId = str_replace($this->clientId.'-RL','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-RL000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-RL00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-RL0'.$count; } 
                else { $id = $this->clientId.'-RL'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'-RL'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

    public function getGroupRlId() 
    {
        try {
            $id = $this->clientId.'-GRL001';
            $maxId = KodeRL::withTrashed()->where('rl_id','LIKE',$this->clientId.'-GRL%')->max('rl_id');
            if (!$maxId) { $id = $this->clientId.'-GRL001'; }
            else {
                $maxId = str_replace($this->clientId.'-GRL','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-GRL00'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-GRL0'.$count; } 
                else { $id = $this->clientId.'-GRL'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'-GRL'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}