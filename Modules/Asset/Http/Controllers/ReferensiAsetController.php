<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

use Modules\Asset\Entities\ReferensiAset;

class ReferensiAsetController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function refStatusLists(Request $request)
    {
        try {
            $data = ReferensiAset::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('ref_aset_id','STATUS_ASET')
                ->select('ref_aset_id','deskripsi','is_aktif','ref_data')
                ->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan', 'data' => []]);
            }
            
            $data['ref_data'] = json_decode($data->ref_data, true);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Referensi: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function refGroupLists(Request $request)
    {
        try {
            $data = ReferensiAset::where('client_id', $this->clientId)->where('is_aktif', 1)
                ->where('ref_aset_id','GROUP_ASET')
                ->select('ref_aset_id','deskripsi','is_aktif','ref_data')
                ->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan', 'data' => []]);
            }            
            $data['ref_data'] = json_decode($data->ref_data, true);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Referensi: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function refAsetUpdate(Request $request, $refId)
    {
        try {            
            $data = ReferensiAset::where('ref_aset_id', $refId)->where('is_aktif',1)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }
            
            $success = ReferensiAset::where('ref_aset_id', $refId)->where('is_aktif',1)->where('client_id', $this->clientId)->update([
                'ref_data' => $request->ref_data,
                'updated_by' => Auth::user()->username
            ]);
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Referensi.']);
            }

            $data = ReferensiAset::where('ref_aset_id', $refId)->where('is_aktif',1)->where('client_id', $this->clientId)->select('ref_aset_id','deskripsi','is_aktif','ref_data')->first();
            if($data) {
                $data['ref_data'] = json_decode($data->ref_data, true);
            }
            
            return response()->json(['success' => true, 'message' => 'data berhasil di update.', 'data' => $data]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Referensi :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
