<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use App\Imports\CoaImport;
use App\Exports\MasterExport;
use Maatwebsite\Excel\Facades\Excel;

use Modules\MasterData\Entities\Coa;
use DB;

class CoaController extends Controller
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
            $pageNumber = 10;
            $active = 'true';
            $keyword = '%%';

            if($request->has('per_page')) {
                $pageNumber = $request->get('per_page');
                if($pageNumber == 'ALL') {
                    $pageNumber = Coa::where('client_id',$this->clientId)->count();
                }
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->get('is_aktif').'%';
            }
            if($request->has('keyword')) {
                $keyword = $request->get('keyword');
            }

            $data = Coa::where('client_id', $this->clientId)->where('is_aktif','ILIKE',$active)
                ->where(function($q) use($keyword){
                    $q->where('nama_coa', 'ILIKE', '%'.$keyword.'%')
                    ->orWhere('kode_coa','ILIKE', $keyword.'%')
                    ->orWhere('level_coa','ILIKE', $keyword);
                })
                ->orderBy('kode_coa','ASC')
                ->orderBy('level_coa','ASC')
                ->paginate($pageNumber);

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan data Depo. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function coaHeader(Request $request, $level) {
        try {
            $data = Coa::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('level_coa',$level)
                ->orderBy('kode_coa','ASC')
                ->orderBy('level_coa','ASC')
                ->get();

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan data Depo. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_coa' => 'required|max:100',
                'nama_coa' => 'required|string|max:100',
                'nilai_normal' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }
            
            $id = $this->getCoaId();

            $req = Coa::where('client_id', $this->clientId)->where('kode_coa', 'ILIKE', $request->kode_coa)->orWhere('nama_coa', 'ILIKE', $request->nama_coa)->where('is_aktif', 1)->first();
            if($req){
                return response()->json(['success' => false,'message' => 'COA sudah ada.']);
            }

            $data = new Coa();
            $data->coa_id = $id;
            $data->kode_coa = $request->kode_coa;
            $data->nama_coa = $request->nama_coa;
            $data->coa_header = $request->coa_header;
            $data->coa_header_id = $request->coa_header_id;
            $data->level_coa = $request->level_coa;
            $data->level_nama = $request->level_nama;
            $data->nilai_normal = $request->nilai_normal;
            $data->is_valid_coa = $request->is_valid_coa;
            $data->text_coa = $request->kode_coa.' - '.$request->nama_coa;
            
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success){
                return response()->json(['success' => false, 'message'  => 'ada kesalahan saat menyimpan data.']);
            }
            return response()->json(['success' => true, 'message'  => 'data COA berhasil disimpan ', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data COA. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = Coa::where('client_id', $this->clientId)->where('coa_id',$id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Data tidak ditemukan']);
            }
            return response()->json(['success' => true, 'message'  => 'ok', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan data COA' . '. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {      
            $validator = Validator::make($request->all(), [
                'kode_coa' => 'required|max:100',
                'nama_coa' => 'required|string|max:100',
                'nilai_normal' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $data = Coa::where('client_id', $this->clientId)->where('coa_id',$request->coa_id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message'  => 'Data tidak ditemukan']);
            }
            $success = Coa::where('client_id', $this->clientId)->where('coa_id',$request->coa_id)->update([
                        'kode_coa' => $request->kode_coa,
                        'nama_coa' => $request->nama_coa,
                        'coa_header' => $request->coa_header,
                        'coa_header_id' => $request->coa_header_id,
                        'level_coa' => $request->level_coa,
                        'level_nama' => $request->level_nama,
                        'nilai_normal' => $request->nilai_normal,
                        'is_valid_coa' => $request->is_valid_coa,
                        'text_coa' => $request->kode_coa.' - '.$request->nama_coa,
                        'is_aktif' => $request->is_aktif,
                        'updated_by' => Auth::user()->username
                    ]);
            
            if (!$success) {
                return response()->json(['success' => false, 'message'  => 'Ada kesalahan dalam menyimpan data.']);
            }
            return response()->json(['success' => true, 'message'  => 'Berhasil update data', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Update data COA. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $coa = Coa::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('coa_id',$id)->first();
            if(!$coa) {
                return response()->json(['success' => false, 'message'  => 'COA tidak ditemukan.']);
            }

            $subCount = Coa::where('client_id', $this->clientId)
                ->where(function($q) use($id){
                    $q->where('coa_id',$id)
                    ->orWhere('coa_header_id',$id);
                })->where('is_aktif',1)->count();
            if($subCount > 1) {
                return response()->json(['success' => true, 'message'  => 'COA masih memiliki sub-akun yang aktif. Hapus dulu sub akun yang ada.']);
            }
            
            $success = Coa::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('coa_id',$id)->update([
                    'is_aktif' => 0,
                    'updated_by' =>Auth::user()->username,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if (!$success) {
                return response()->json(['success' => false, 'message'  => 'Gagal menonaktifkan akun COA.']);
            }
            return response()->json(['success' => true, 'message'  => 'akun COA berhasil dinonaktifkan.']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menonaktifkan data akun COA. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getCoaId()
    {
        try {
            $id = $this->clientId.'.COA'.date('Y').'-0001';
            $maxId = Coa::withTrashed()->where('coa_id', 'LIKE', $this->clientId.'.COA'.date('Y').'-%')->max('coa_id');
            if (!$maxId) {
                $id = $this->clientId.'.COA'.date('Y').'-0001';
            } else {
                $maxId = str_replace($this->clientId.'.COA'.date('Y').'-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'.COA'.date('Y').'-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId.'.COA'.date('Y').'-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId.'.COA'.date('Y').'-0' . $count;
                } else {
                    $id = $this->clientId.'.COA'.date('Y').'-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId.'.COA'.date('Y').'-' . Uuid::uuid4()->getHex();
        }
    }

    public function importExcel(Request $request) 
    {
        try {
            Excel::Import(new CoaImport,$request->file);
            return response()->json(['success' => true, 'message'  => 'Berhasil import Data COA']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal import data COA. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportExcel(Request $request) 
    {
        try {
            $coa = new Coa;
            $column = str_replace('_', ' ', $coa->getTableColumns());
            $fixColumn = array_splice($column, 0, -5);
            
            $keyword = '%%';
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            $list = Coa::where('client_id', $this->clientId)->where('is_aktif', true)
            ->where('kode_coa', 'LIKE', $keyword)
            ->where('nama_coa', 'LIKE', $keyword)
            ->orderBy('nama_coa', 'ASC')->get();
            $data = [
                $fixColumn,
                $list
            ];
            return (new MasterExport($data))->download('MASTER_DATA_COA.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data CoA. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
