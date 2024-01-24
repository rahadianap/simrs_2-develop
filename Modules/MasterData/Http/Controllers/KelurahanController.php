<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Kelurahan;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Propinsi;

use Ramsey\Uuid\Uuid;
use DB;
use Illuminate\Support\Facades\Auth;

class KelurahanController extends Controller
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
            $aktif = 'true';
            $keyword = '%%';
            $propinsi = '%%';
            $kabupaten = '%%';
            $kecamatan = '%%';
            
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage = Kota::where('client_id',$this->clientId)->count(); }
            }
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }    
            if ($request->has('propinsi')) {
                $propinsi = '%'.$request->get('propinsi').'%';
            }        
            if ($request->has('kota')) {
                $kabupaten = '%'.$request->get('kota').'%';
            }
            if ($request->has('kecamatan')) {
                $kecamatan = '%'.$request->get('kecamatan').'%';
            }
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            $data = Kelurahan::where('client_id',$this->clientId)
                    ->where('propinsi_id','ILIKE',$propinsi)
                    ->where('kota_id','ILIKE',$kabupaten)
                    ->where('kecamatan_id','ILIKE',$kecamatan)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use($keyword){
                        $q->where('kelurahan','ILIKE',$keyword)
                        ->orWhere('kelurahan_id','ILIKE',$keyword);
                    })
                    ->orderBy('is_prioritas','DESC')
                    ->orderBy('kelurahan','ASC')
                    ->paginate($perPage); 

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Kelurahan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = Kelurahan::where('client_id', $this->clientId)->where('kelurahan_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Kelurahan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $prop = Propinsi::where('client_id',$this->clientId)->where('is_aktif',1)->where('propinsi_id',$request->propinsi_id)->first();
            $kota = Kota::where('client_id',$this->clientId)->where('is_aktif',1)->where('kota_id',$request->kota_id)->first();
            $kec = Kecamatan::where('client_id',$this->clientId)->where('is_aktif',1)->where('kecamatan_id',$request->kecamatan_id)->first();
            
            if(!$prop) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kelurahan. Data Propinsi tidak ditemukan.']);
            }

            if(!$kota) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kelurahan. Data Kabupaten/kota tidak ditemukan.']);
            }

            if(!$kec) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kelurahan. Data Kecamatan tidak ditemukan.']);
            }


            $id = $this->getKelurahanId();
            $data = new Kelurahan();

            $data->kelurahan_id = $id;
            $data->propinsi_id = $request->propinsi_id;
            $data->kota_id = $request->kota_id;
            $data->kecamatan_id = $request->kecamatan_id;
            $data->kelurahan = strtoupper($request->kelurahan);
            $data->kodepos = $request->kodepos;            
            $data->is_prioritas = $request->is_prioritas;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kelurahan.']);
            }
            return response()->json(['success' => true, 'message' => 'Kelurahan berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Kelurahan :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $kelurahanId = $request->kelurahan_id;
            $data = Kelurahan::where('kelurahan_id', $kelurahanId)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data Kelurahan tidak ditemukan.']);
            }

            $data->kelurahan = strtoupper($request->kelurahan);
            $data->kodepos = $request->kodepos;            
            $data->is_prioritas = $request->is_prioritas;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Kelurahan.']);
            }

            return response()->json(['success' => true, 'message' => 'Kelurahan berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Kelurahan :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $data = Kelurahan::where('kelurahan_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Kelurahan tidak ditemukan.']);
            }

            $success = Kelurahan::where('kelurahan_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan Kelurahan']);
            }
            return response()->json(['success' => true, 'message' => 'Data Kelurahan berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data Kelurahan :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getKelurahanId()
    {
        try {
            $id = $this->clientId . '.KEL0001';
            $maxId = Kelurahan::withTrashed()->where('kelurahan_id', 'LIKE', $this->clientId . '.KEL%')->max('kelurahan_id');
            if (!$maxId) {
                $id = $this->clientId . '.KEL0001';
            } else {
                $maxId = str_replace($this->clientId . '.KEL', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.KEL000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.KEL00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.KEL0' . $count;
                } else {
                    $id = $this->clientId . '.KEL' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.KEL' . Uuid::uuid4()->getHex();
        }
    }

    public function kodepos(Request $request)
    {
        try {
            $perPage = 10;
            $aktif = 'true';
            $keyword = '%%';
            $propinsi = '%%';
            $kota = '%%';
            $kecamatan = '%%';
            $kelurahan = '%%';
                        
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage = Kelurahan::where('client_id',$this->clientId)->count(); }
            }
            if ($request->has('keyword')) { $keyword = '%'.$request->get('keyword').'%'; }
            if ($request->has('propinsi')) { $propinsi = '%'.$request->get('propinsi').'%'; }
            if ($request->has('kota')) { $kota = '%'.$request->get('kota').'%'; }
            if ($request->has('kecamatan')) { $kecamatan = '%'.$request->get('kecamatan').'%'; }
            if ($request->has('kelurahan')) { $kelurahan = '%'.$request->get('kelurahan').'%'; }

            $data = Kelurahan::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('propinsi_id','ILIKE',$propinsi)
                    ->where('kota_id','ILIKE',$kota)
                    ->where('kecamatan_id','ILIKE',$kecamatan)
                    ->where('kelurahan_id','ILIKE',$kelurahan)
                    ->where('kodepos','ILIKE',$keyword)
                    ->orderBy('is_prioritas','DESC')
                    ->orderBy('kodepos','ASC')
                    ->paginate($perPage); 

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Kodepos : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
