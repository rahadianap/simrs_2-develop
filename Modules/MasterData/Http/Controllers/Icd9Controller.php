<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Icd9;
use App\Imports\ICD9Import;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Illuminate\Support\Facades\Auth;

class Icd9Controller extends Controller
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
            $perPage = 10;
            $keyword = '%%';
            $active = 'true';

            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
            }

            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            if ($request->has('is_aktif')) {
                $keyword = '%'.$request->get('is_aktif').'%';
            }
            
            $data = Icd9::where('client_id', $this->clientId)
                ->where('is_aktif', $active)
                ->where( function($q) use($keyword){
                    $q->where('nama_icd','ILIKE',$keyword)
                    ->orWhere('kode_icd','ILIKE',$keyword);
                })->orderBy('kode_icd', 'ASC')->paginate($perPage);

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar ICD : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data($id)
    {
        try {
            $data = Icd9::where('client_id', $this->clientId)->where('kode_icd', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data ICD : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = new Icd9();
            $rec = Icd9::where('kode_icd', $request->kode_icd)->where('client_id', $this->clientId)->first();
            if($rec){
                return response()->json(['success' => false, 'message' => 'Kode ICD sudah ada.']);
            }
            $data->kode_icd = $request->kode_icd;
            $data->nama_icd = $request->nama_icd;
            $data->kata_kunci = $request->kata_kunci;
            $data->is_valid_icd = $request->is_valid_icd;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data ICD.']);
            }
            return response()->json(['success' => true, 'message' => 'ICD berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menyimpan data ICD :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $kodeIcd = $request->kode_icd;
            $data = Icd9::where('kode_icd', $kodeIcd)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $data->nama_icd = $request->nama_icd;
            $data->kata_kunci = $request->kata_kunci;
            $data->is_valid_icd = $request->is_valid_icd;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data ICD.']);
            }
            return response()->json(['success' => true, 'message' => 'ICD berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data ICD :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $data = Icd9::where('kode_icd', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data ICD tidak ditemukan.']);
            }

            $success = Icd9::where('kode_icd', $id)
                ->where('client_id', $this->clientId)
                ->where('is_aktif', 1)
                ->update(['kode_icd'=>'DEL-'.$id, 'is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus ICD']);
            }
            return response()->json(['success' => true, 'message' => 'Data ICD berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menghapus data ICD :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function importExcel(Request $request) 
    {
        try {
            Excel::Import(new ICD9Import,$request->file);
            return response()->json(['success' => true, 'message'  => 'Berhasil import Data ICD9']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal import data ICD9. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
