<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Referensi;
use DB;
use Illuminate\Support\Facades\Auth;

class ReferensiProdukController extends Controller
{
    public $clientId;

    public function __construct(Request $request)
    {
        /*** check apakah header menyertakan client ID atau tidak */
        if (!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'Tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request)
    {
        try {
            $data = Referensi::where('client_id', $this->clientId)->where('is_aktif', 1)->where('ref_group','PERSEDIAAN')->get();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan', 'data' => []]);
            }
            foreach($data as $dt){
                if($dt->json_data) {
                    $dt->json_data = json_decode($dt->json_data, true);
                }
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Referensi: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request,$id)
    {
        try {
            $data = Referensi::where('client_id', $this->clientId)->where('ref_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }            
            if($data->json_data) {
                $data->json_data = json_decode($data->json_data,false);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Referensi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $refId = $request->ref_id;
            
            $data = Referensi::where('ref_id', $refId)->where('allow_user_edit',1)->where('is_aktif',1)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }
            $success = Referensi::where('ref_id', $refId)->where('allow_user_edit',1)->where('is_aktif',1)->where('client_id', $this->clientId)->update([
                'json_data' => $request->json_data,
                'updated_by' => Auth::user()->username
            ]);
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Referensi.']);
            }

            $data = Referensi::where('ref_id', $refId)->where('is_aktif',1)->where('client_id', $this->clientId)->first();
            if($data) {
                $data->json_data = json_decode($data->json_data,false);
            }
            
            return response()->json(['success' => true, 'message' => 'ICD berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Referensi :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
