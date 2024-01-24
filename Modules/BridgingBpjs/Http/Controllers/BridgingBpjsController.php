<?php

namespace Modules\BridgingBpjs\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ManajemenUser\Entities\Client;

use Illuminate\Support\Facades\Auth;
use Modules\MasterData\Entities\Bridging;
use Ramsey\Uuid\Uuid;

class BridgingBpjsController extends Controller
{
    public $clientId;

    public function __construct(Request $request) {
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }
    
    public function dataCredential(Request $request) {
        try {
            $data = Client::where('client_id',$this->clientId)->where('is_aktif',1)
                ->select('client_id','client_nama','bpjs_cons_id','bpjs_secret','bpjs_user_key','is_bpjs_aktif')
                ->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
            }

            return response()->json(['success' => true,'message'=>'OK','data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    public function updateCredential(Request $request) {
        try {
            $success = Client::where('client_id',$this->clientId)
                ->where('is_aktif',1)->update([
                    'bpjs_cons_id' => $request->bpjs_cons_id,
                    'bpjs_secret' => $request->bpjs_secret,
                    'bpjs_user_key' => $request->bpjs_user_key,
                    'is_bpjs_aktif' => $request->is_bpjs_aktif,                    
                ]);
            
            if(!$success) {
                return response()->json(['success' => false,'message'=>'Data gagal di update']);
            }

            return response()->json(['success' => true,'message'=>'Data berhasil di update']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }

    public function updateBridging(Request $request) {
        try {
            $data = Bridging::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('local_resource_id',$request->kode_lokal)
                ->where('bridging_group',$request->bridging_group)
                ->first();
            
            if(!$data) {
                $data = new Bridging();
                $data->bridging_id = $this->getBridgingPropinsiId();
                $data->bridging_group = $request->bridging_group;
                $data->local_resource_id = $request->kode_lokal;
                $data->client_id = $this->clientId;
                $data->is_aktif = true;
                $data->created_by = Auth::user()->username;
            }
            
            $data->local_resource_name = $request->nama_lokal;
            $data->bridging_resource_id = $request->kode_bridging;
            $data->bridging_resource_name = $request->nama_bridging;
            $data->bridging_sub_resource_id = $request->sub_kode_bridging;
            $data->bridging_sub_resource_name = $request->sub_nama_bridging;

            $data->updated_by = Auth::user()->username;                
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengubah kode bridging']);
            }
            return response()->json(['success' => true, 'message' => 'berhasil mengubah kode bridging', 'data' =>$data ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengubah kode bridging data :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getBridgingPropinsiId() {
        try {
            $id = $this->clientId.'-'.date('YmdHis').'-BR001';

            $maxId = Bridging::withTrashed()->where('bridging_id', 'LIKE', $this->clientId.'-'.date('YmdHis').'-BR%')->max('bridging_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('YmdHis').'-BR001';
            } else {
                $maxId = str_replace($this->clientId.'-'.date('YmdHis').'-BR', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'-'.date('YmdHis').'-BR00' . $count;
                }
                else if ($count >= 10 && $count < 100) {
                    $id = $this->clientId.'-'.date('YmdHis').'-BR0' . $count;
                } 
                else {
                    $id = $this->clientId.'-'.date('YmdHis').'-BR' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.BR' . Uuid::uuid4()->getHex();
        }
    }

    public function removeBridging(Request $request) {
        try {
            $data = Bridging::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('local_resource_id',$request->kode_lokal)
                ->where('bridging_group',$request->bridging_group)
                ->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            
            $data->is_aktif = false;
            $data->updated_by = Auth::user()->username;                
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus kode bridging']);
            }
            return response()->json(['success' => true, 'message' => 'berhasil menghapus kode bridging']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menghapus kode bridging data :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataJknCredential(Request $request) {
        try {
            $data = Client::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->select('client_id','client_nama','jkn_username','jkn_password')->first();
            
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
            }

            return response()->json(['success' => true,'message'=>'OK','data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data', 'error' => $e->getMessage()]);
        }
    }

    public function updateJknCredential(Request $request) {
        try {
            $id = $this->clientId.Uuid::uuid4()->getHex();
            $id = str_replace('-','',$id);
            $pass = Uuid::uuid4()->getHex();
            $success = Client::where('client_id',$this->clientId)
                ->where('is_aktif',1)->update([
                    'jkn_username' => $id,
                    'jkn_password' => $pass,
                    'jkn_token' => null,
                ]);
            
            if(!$success) {
                return response()->json(['success' => false,'message'=>'Data gagal di update']);
            }

            return response()->json(['success' => true,'message'=>'Data berhasil di update']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }
}
