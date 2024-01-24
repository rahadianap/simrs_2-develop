<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\BedInap;
use Illuminate\Support\Facades\Auth;

class BedPelayananController extends Controller
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
            $keyword = '%%';
            if ($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            $ruangID = '%%';
            if ($request->has('ruang_id')) {
                $ruangID = '%'.$request->ruang_id.'%';
            }

            $unitID = '%%';
            if ($request->has('unit_id')) {
                $unitID = '%'.$request->unit_id.'%';
            }

            $jenisRegistrasi = '%%';
            if ($request->has('jenis_reg')) {
                $jenisRegistrasi = '%'.$request->jenis_reg.'%';
            }

            $perpage = BedInap::where('client_id',$this->clientId)->where('is_aktif',1)->count();

            $list = BedInap::leftJoin('tb_ruang', 'tb_ruang.ruang_id', '=', 'tb_bed.ruang_id')
                ->leftJoin('tb_unit', 'tb_ruang.unit_id', '=', 'tb_unit.unit_id')
                ->leftJoin('tb_kelas', 'tb_ruang.kelas_kamar', '=', 'tb_kelas.kelas_id')
                ->where('tb_bed.client_id', $this->clientId)->where('tb_bed.is_aktif', '1')
                ->where('tb_ruang.ruang_id', 'ILIKE', $ruangID)
                ->where('tb_ruang.unit_id', 'ILIKE', $unitID) 
                ->where('tb_unit.jenis_registrasi', 'ILIKE', $jenisRegistrasi) 
                ->where(function($q) use ($keyword) {
                    $q->where('tb_ruang.ruang_nama', 'ILIKE', $keyword)
                    ->orWhere('tb_unit.unit_nama', 'ILIKE', $keyword)
                    ->orWhere('tb_bed.no_bed', 'ILIKE', $keyword);
                })
                ->select('tb_bed.*','tb_ruang.ruang_nama','tb_kelas.kelas_nama as kelas_kamar','tb_unit.unit_nama')
                ->orderBy('tb_bed.is_tersedia', 'DESC')
                ->orderBy('tb_bed.bed_id', 'ASC')
                ->orderBy('tb_bed.no_bed', 'ASC')
                ->paginate($perpage);

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);

        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data bed ruang', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $data = BedInap::where('client_id', $clientId)->where('bed_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data bed ruang', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID'], 401);
            }
            $clientId = $request->header('X-cid');
            $prefix = 'BP';
            $id = $this->getBedId($clientId, $prefix);

            $data = new BedInap();
            $data->bed_id = $id;
            $data->ruang_id = $request->ruang_id;
            $data->no_bed = $request->no_bed;
            $data->jns_kelamin_bed = $request->jns_kelamin_bed;
            $data->pasien_id = null;
            $data->reg_id = null;
            $data->trx_id = null;
            $data->tgl_masuk = null;
            $data->status = 'TERSEDIA';
            $data->is_tersedia = 1;
            $data->tgl_rencana_pulang = null;
            $data->is_aktif = 1;
            $data->client_id = $clientId;
            $data->created_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data bed ruang']);
            }

            $list = BedInap::where('client_id',$clientId)->where('is_aktif',1)->where('ruang_id',$request->ruang_id)->get();

            return response()->json(['success' => true, 'message' => 'data bed ruang berhasil disimpan', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menambah bed ruang. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID'], 401);
            }
            $clientId = $request->header('X-cid');

            $data = BedInap::where('is_aktif', 1)->where('client_id', $clientId)->where('bed_id', $request->bed_id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data bed ruang tidak ditemukan.']);
            }

            $data->no_bed = $request->no_bed;
            $data->jns_kelamin_bed = $request->jns_kelamin_bed;
            // $data->updated_by = Auth::user()->name;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data bed ruang']);
            }

            return response()->json(['success' => true, 'message' => 'data bed ruang berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan bed ruang. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $data = BedInap::where('bed_id', $id)->where('client_id', $clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus bed. Data bed tidak ditemukan.']);
            }

            $beds = BedInap::where('bed_id', $id)->where('is_tersedia', 1)->where('client_id', $clientId)->where('is_aktif', 1)->first();
            if (!$beds) {
                return response()->json(['success' => false, 'message' => 'Unit Bed masih terisi.']);
            }

            $data->updated_by = Auth::user()->username;
            $data->is_aktif = 0;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data bed.']);
            }
            return response()->json(['success' => true, 'message' => 'Bed berhasil dihapus', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data bed ruang gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function getBedId($clientId, $prefix)
    {
        try {
            $id = $clientId . '-' . $prefix . '0001';
            $maxId = BedInap::withTrashed()->where('client_id', $clientId)->where('bed_id', 'LIKE', $clientId . '-' . $prefix . '%')->max('bed_id');
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
            return 'BP' . date('Y.m.d') . '-' . Uuid::uuid4()->getHex();
        }
    }
}