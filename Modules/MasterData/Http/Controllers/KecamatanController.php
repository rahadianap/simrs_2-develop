<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kelurahan;
use Ramsey\Uuid\Uuid;
use DB;
use Illuminate\Support\Facades\Auth;
use Modules\MasterData\Entities\Bridging;

class KecamatanController extends Controller
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
            
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage = Kota::where('client_id',$this->clientId)->count(); }
            }
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }    
            if ($request->has('propinsi')) {
                $propinsi = $request->get('propinsi');
            }        
            if ($request->has('kota')) {
                $kabupaten = $request->get('kota');
            }
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            $data = Kecamatan::where('client_id',$this->clientId)
                    ->where('kota_id','ILIKE',$kabupaten)
                    ->where('propinsi_id','ILIKE',$propinsi)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('kecamatan','ILIKE',$keyword)
                        ->orWhere('kecamatan_id','ILIKE',$keyword);
                    })
                    ->orderBy('is_prioritas','DESC')
                    ->orderBy('kecamatan','ASC')
                    ->paginate($perPage); 

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Kecamatan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function kecBpjsLists(Request $request)
    {
        try {
            $perPage = 10;
            $aktif = 'true';
            $keyword = '%%';
            $propinsi = '%%';
            $kabupaten = '%%';
            
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage = Kota::where('client_id',$this->clientId)->count(); }
            }
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }    
            if ($request->has('propinsi')) {
                $propinsi = $request->get('propinsi');
            }        
            if ($request->has('kota')) {
                $kabupaten = $request->get('kota');
            }
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            $data = Kecamatan::where('client_id',$this->clientId)
                    ->where('kota_id','ILIKE',$kabupaten)
                    ->where('propinsi_id','ILIKE',$propinsi)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use ($keyword) {
                        $q->where('kecamatan','ILIKE',$keyword)
                        ->orWhere('kecamatan_id','ILIKE',$keyword);
                    })
                    ->orderBy('is_prioritas','DESC')
                    ->orderBy('kecamatan','ASC')
                    ->paginate($perPage); 

            foreach($data->items() as $item) {
                $bdata = Bridging::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('local_resource_id',$item['kecamatan_id'])
                    ->where('bridging_group','BPJS')
                    ->first();
                if($bdata) {
                    $item['kode_bpjs'] = $bdata->bridging_resource_id;
                    $item['nama_bpjs'] = $bdata->bridging_resource_name;
                }
                else {
                    $item['kode_bpjs'] = null;
                    $item['nama_bpjs'] = null;
                }
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Kecamatan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = Kecamatan::where('client_id', $this->clientId)->where('kecamatan_id', $id)->first();
            
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $data['propinsi'] = Propinsi::where('client_id', $this->clientId)->where('propinsi_id', $data->propinsi_id)->select('propinsi')->first()->propinsi;
            $data['kota'] = Kota::where('client_id', $this->clientId)->where('kota_id', $data->kota_id)->select('kota')->first()->kota;
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Kecamatan : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $prop = Propinsi::where('client_id',$this->clientId)->where('is_aktif',1)->where('propinsi_id',$request->propinsi_id)->first();
            $kota = Kota::where('client_id',$this->clientId)->where('is_aktif',1)->where('kota_id',$request->kota_id)->first();
            if(!$prop) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kecamatan. Propinsi tidak dikenali.']);
            }
            if(!$kota) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kecamatan. Kabupaten/kota tidak dikenali.']);
            }

            $id = $this->getKecamatanId();
            $data = new Kecamatan();

            $data->kecamatan_id = $id;
            $data->propinsi_id = $prop->propinsi_id;
            $data->kota_id = $kota->kota_id;
            $data->kecamatan = strtoupper($request->kecamatan);
            $data->propinsi = $prop->propinsi;
            $data->kota = $kota->kota;
            
            $data->is_prioritas = $request->is_prioritas;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kecamatan.']);
            }
            return response()->json(['success' => true, 'message' => 'Kecamatan berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Kecamatan :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $kecamatanId = $request->kecamatan_id;
            $data = Kecamatan::where('kecamatan_id', $kecamatanId)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data Kecamatan tidak ditemukan.']);
            }

            $kelurahan = Kelurahan::where('kecamatan_id', $kecamatanId)->where('client_id', $this->clientId)->first();

            DB::connection('dbclient')->beginTransaction();
            $data->kecamatan = strtoupper($request->kecamatan);
            $data->is_prioritas = $request->is_prioritas;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Kecamatan.']);
            }
            //update nama kecamatan di kelurahan
            if($kelurahan) {
                $success = Kelurahan::where('kecamatan_id', $kecamatanId)->where('client_id', $this->clientId)->update(['kecamatan' => strtoupper($request->kecamatan)]);
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Kecamatan. Update data kelurahan gagal.']);
                }
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Kecamatan berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Kecamatan :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Kecamatan::where('kecamatan_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Kecamatan tidak ditemukan.']);
            }
            /**
             * jika kelurahan dari kecamatan masih ada yang aktif, data tidak dapat dihapus
             */
            $kelurahan = Kelurahan::where('kecamatan_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if ($kelurahan) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan Kecamatan. Ada kelurahan yang masih aktif']);
            }

            $success = Kecamatan::where('kecamatan_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan Kecamatan']);
            }
            return response()->json(['success' => true, 'message' => 'Data Kecamatan berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data Kecamatan :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getKecamatanId()
    {
        try {
            $id = $this->clientId . '.KEC0001';
            $maxId = Kecamatan::withTrashed()->where('kecamatan_id', 'LIKE', $this->clientId . '.KEC%')->max('kecamatan_id');
            if (!$maxId) {
                $id = $this->clientId . '.KEC0001';
            } else {
                $maxId = str_replace($this->clientId . '.KEC', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.KEC000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.KEC00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.KEC0' . $count;
                } else {
                    $id = $this->clientId . '.KEC' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.KEC' . Uuid::uuid4()->getHex();
        }
    }
}
