<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Dtd;
use Modules\MasterData\Entities\Icd;
use App\Imports\DTDImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Illuminate\Support\Facades\Auth;

class DtdController extends Controller
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
            
            $data = Dtd::where('client_id', $this->clientId)
                ->where('is_aktif', $active)
                ->where( function($q) use($keyword){
                    $q->where('nama_dtd','ILIKE',$keyword)
                    ->orWhere('no_dtd','ILIKE',$keyword);
                })->orderBy('no_dtd', 'ASC')->paginate($perPage);

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar DTD : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data($id)
    {
        try {
            $data = Dtd::where('client_id', $this->clientId)->where('no_dtd', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data DTD : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = new Dtd();
            $rec = Dtd::where('no_dtd', $request->no_dtd)->where('client_id', $this->clientId)->first();
            if($rec){
                return response()->json(['success' => false, 'message' => 'Kode DTD sudah ada.']);
            }
            $data->no_dtd = $request->no_dtd;
            $data->nama_dtd = $request->nama_dtd;
            $data->label_dtd = $request->label_dtd;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data DTD.']);
            }
            return response()->json(['success' => true, 'message' => 'DTD berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data DTD :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $noDtd = $request->no_dtd;
            $data = Dtd::where('no_dtd', $noDtd)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $data->nama_dtd = $request->nama_dtd;
            $data->label_dtd = $request->label_dtd;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data DTD.']);
            }
            return response()->json(['success' => true, 'message' => 'DTD berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data DTD :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request,$id)
    {
        try {
            $data = Dtd::where('no_dtd', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data DTD tidak ditemukan.']);
            }

            $success = Dtd::where('no_dtd', $id)
                ->where('client_id', $this->clientId)
                ->where('is_aktif', 1)
                ->update(['no_dtd'=>'DEL-'.$id,'is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus DTD']);
            }
            return response()->json(['success' => true, 'message' => 'Data DTD berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menghapus data DTD :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function importExcel(Request $request) 
    {
        try {
            Excel::Import(new DTDImport,$request->file);
            return response()->json(['success' => true, 'message'  => 'Berhasil import Data DTD']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal import data DTD. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
