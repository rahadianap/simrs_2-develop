<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\DokterUnit;
use Illuminate\Support\Facades\Auth;
use DB;

class DokterUnitController extends Controller
{

    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request, $unitId)
    {
        try {
            // if (!$request->hasHeader('X-cid')) {
            //     return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            // }
            // $clientId = $request->header('X-cid');

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if(strtoupper($per_page) =='ALL' ) {
                    $per_page = DokterUnit::where('client_id',$this->clientId)
                        ->where(function($q) use($unitId){
                            $q->where('unit_id',$unitId)
                            ->orWhere('dokter_id',$unitId);
                        })->where('is_aktif',1)
                        ->count();
                }
            }

            $keyword = '%%';
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            // $unitID = '%%';
            // if ($request->has('unit_id')) {
            //     $keyword = '%' . $request->input('keyword') . '%';
            // }

            // $list = DokterUnit::leftJoin('tb_unit', 'tb_dokter_unit.unit_id', '=', 'tb_unit.unit_id')
            // ->leftjoin('tb_dokter', 'tb_dokter_unit.dokter_id', '=', 'tb_dokter.dokter_id')
            // ->leftjoin('tb_ruang', 'tb_dokter_unit.ruang_id', '=', 'tb_ruang.ruang_id')
            // ->where('tb_dokter_unit.client_id', $clientId)->where('tb_dokter_unit.is_aktif', '1')
            // ->where('tb_ruang.ruang_nama', 'LIKE', $keyword)
            // ->where('tb_unit.unit_nama', 'LIKE', $keyword)
            // ->where('tb_dokter.dokter_nama', 'LIKE', $keyword)
            // ->orderBy('tb_dokter_unit.unit_id', 'ASC')->get();

            $list = DokterUnit::leftjoin('tb_dokter', 'tb_dokter_unit.dokter_id', '=', 'tb_dokter.dokter_id')
                ->where('tb_dokter_unit.is_aktif',1)
                ->where('tb_dokter_unit.unit_id',$unitId)
                ->select('tb_dokter_unit.dokter_unit_id','tb_dokter.dokter_id','tb_dokter.dokter_nama','tb_dokter_unit.prefix_no_antrian','tb_dokter_unit.unit_id')
                ->paginate($per_page);
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan semua data mapping unit dokter', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            // if (!$request->hasHeader('X-cid')) {
            //     return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            // }
            // $clientId = $request->header('X-cid');

            $data = DokterUnit::where('client_id', $this->clientId)->where('dokter_unit_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data mapping unit dokter', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            // if (!$request->hasHeader('X-cid')) {
            //     return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            // }
            // $clientId = $request->header('X-cid');

            $data = DokterUnit::where('client_id',$this->clientId)
                ->where('dokter_id',$request->dokter_id)
                ->where('unit_id',$request->unit_id)
                ->where('is_aktif',1)->first();
            if($data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data mapping unit dokter. Dokter sudah ada.']);
            }

            $prefix = 'UD';
            $id = $this->getDokterUnitId($this->clientId, $prefix);

            $data = new DokterUnit();

            $data->dokter_unit_id = $id;
            $data->dokter_id = $request->dokter_id;
            $data->unit_id = $request->unit_id;
            $data->ruang_id = $request->ruang_id;
            $data->prefix_no_antrian = strtoupper($request->prefix_no_antrian);
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data mapping unit dokter']);
            }
            
            return response()->json(['success' => true, 'message' => 'data mapping unit dokter berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menambah mapping unit dokter. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            // if (!$request->hasHeader('X-cid')) {
            //     return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            // }
            // $clientId = $request->header('X-cid');

            $data = DokterUnit::where('client_id', $this->clientId)->where('dokter_unit_id', $request->dokter_unit_id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data mapping unit dokter tidak ditemukan.']);
            }

            //$data->dokter_id = $request->dokter_id;
            //$data->unit_id = $request->unit_id;
            $data->ruang_id = $request->ruang_id;
            $data->prefix_no_antrian = strtoupper($request->prefix_no_antrian);

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data mapping unit dokter']);
            }

            return response()->json(['success' => true, 'message' => 'data mapping unit dokter berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan mapping unit dokter. Error : ' . $e->getMessage()]);
        }
    }

    // cek tabel registrasi ada yang daftar/booking atau tidak
    public function delete(Request $request, $id)
    {
        try {
            // if (!$request->hasHeader('X-cid')) {
            //     return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            // }
            // $clientId = $request->header('X-cid');



            $data = DokterUnit::where('dokter_unit_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus unit. Data unit tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam memperbaharui data unit']);
            }
            return response()->json(['success' => true, 'message' => 'mapping unit dokter berhasil dihapus', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data mapping unit dokter gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function getDokterUnitId($clientId, $prefix)
    {
        try {
            $id = $clientId . '-' . $prefix . '0001';
            $maxId = DokterUnit::withTrashed()->where('client_id', $clientId)->where('dokter_unit_id', 'LIKE', $clientId . '-' . $prefix . '%')->max('dokter_unit_id');
            if (!$maxId) {
                $id = $clientId . '-' . $prefix . '0001';
            } else {
                $maxId = str_replace($clientId . '-' . $prefix, '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $clientId . '-' . $prefix . '000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $clientId . '-' . $prefix . '00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $clientId . '-' . $prefix . '0' . $count;
                } else {
                    $id = $clientId . '-' . $prefix . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return 'UD' . date('Y.m.d') . '-' . Uuid::uuid4()->getHex();
        }
    }
}
