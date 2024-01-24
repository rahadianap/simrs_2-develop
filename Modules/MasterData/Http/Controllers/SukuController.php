<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Suku;
use Ramsey\Uuid\Uuid;
use DB;
use Illuminate\Support\Facades\Auth;

class SukuController extends Controller
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
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
            }
            /**
             * tambahkan semua filter yang dikirim dari client
             */
            $query = Suku::query();
            foreach ($request->except('_token') as $key => $data) {
                if ($key !== "page" && $key !== "per_page") {
                    $query = $query->where($key, 'ILIKE', '%' . $data . '%');
                }
            }
            $data = $query->where('client_id', $this->clientId)->orderBy('no_urut', 'ASC')->paginate($perPage);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Suku : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data($id)
    {
        try {
            $data = Suku::where('client_id', $this->clientId)->where('suku_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Suku : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getSukuId();
            $data = new Suku();

            $data->suku_id = $id;
            $data->suku = $request->suku;
            $data->teks = $request->teks;
            $data->no_urut = $request->no_urut;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            DB::connection('dbclient')->beginTransaction();
            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Suku.']);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Suku berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Suku :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $sukuId = $request->suku_id;
            $data = Suku::where('suku_id', $sukuId)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $data->suku = $request->suku;
            $data->teks = $request->teks;
            $data->no_urut = $request->no_urut;
            $data->updated_by = Auth::user()->username;
            // $data->updated_by = Auth::user()->username;

            DB::connection('dbclient')->beginTransaction();
            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Suku.']);
            }

            DB::connection('dbclient')->commit();

            return response()->json(['success' => true, 'message' => 'Suku berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Suku :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $data = Suku::where('suku_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Suku tidak ditemukan.']);
            }

            $success = Suku::where('suku_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan Suku']);
            }
            return response()->json(['success' => true, 'message' => 'Data Suku berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data Suku :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getSukuId()
    {
        try {
            $id = $this->clientId . '.SK-0001';
            $maxId = Suku::withTrashed()->where('suku_id', 'LIKE', $this->clientId . '.SK-%')->max('suku_id');
            if (!$maxId) {
                $id = $this->clientId . '.SK-0001';
            } else {
                $maxId = str_replace($this->clientId . '.SK-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.SK-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.SK-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.SK-0' . $count;
                } else {
                    $id = $this->clientId . '.SK-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.SK-' . Uuid::uuid4()->getHex();
        }
    }
}
