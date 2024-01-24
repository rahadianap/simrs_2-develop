<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Propinsi;
use Modules\MasterData\Entities\Kota;
use Modules\MasterData\Entities\Kecamatan;
use Modules\MasterData\Entities\Kelurahan;
use Ramsey\Uuid\Uuid;
use DB;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\Bridging;

class KotaController extends Controller
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
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            $data = Kota::where('client_id',$this->clientId)
                    ->where('propinsi_id','ILIKE',$propinsi)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use($keyword){
                        $q->where('kota_id','ILIKE',$keyword)
                        ->orWhere('kota','ILIKE',$keyword);
                    })
                    ->orderBy('kota','ASC')
                    ->paginate($perPage); 

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Propinsi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function kabBpjsLists(Request $request)
    {
        try {
            $aktif = 'true';
            $keyword = '%%';
            $propinsi = '%%';
            $perPage = Kota::where('client_id',$this->clientId)->count(); 
            
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }            
            if ($request->has('propinsi')) {
                $propinsi = $request->get('propinsi');
            }
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            $data = Kota::where('client_id',$this->clientId)
                    ->where('propinsi_id','ILIKE',$propinsi)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where(function($q) use($keyword){
                        $q->where('kota_id','ILIKE',$keyword)
                        ->orWhere('kota','ILIKE',$keyword);
                    })
                    ->orderBy('kota','ASC')
                    ->paginate($perPage); 
            
            foreach($data->items() as $item) {
                $bdata = Bridging::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('local_resource_id',$item['kota_id'])
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
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Propinsi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request,$id)
    {
        try {
            $data = Kota::where('client_id', $this->clientId)->where('kota_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);            
            }
            $data['propinsi'] = Propinsi::where('client_id', $this->clientId)
                        ->where('propinsi_id', $data->propinsi_id)->select('propinsi')->first()->propinsi;
            
            $data['bridging'] = Bridging::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('local_resource_id',$id)
                        ->get();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Kota : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            
            $prop = Propinsi::where('client_id',$this->clientId)->where('propinsi_id',$request->propinsi_id)->where('is_aktif',1)->first();
            if(!$prop) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kota / kabupaten. Propinsi tidak ditemukan.']);
            }

            $id = $this->getKotaId($request->propinsi_id);

            $req = Kota::where('client_id', $this->clientId)->where('kota', 'ILIKE', $request->kota)->where('is_aktif', 1)->first();
            if($req){
                return response()->json(['success' => false,'message' => 'Nama Kota sudah ada.']);
            }

            $data = new Kota();
            $data->kota_id = $id;
            $data->propinsi_id = $prop->propinsi_id;
            $data->kota = strtoupper($request->kota);
            $data->propinsi = $prop->propinsi;
            $data->is_prioritas = $request->is_prioritas;
            $data->jenis = $request->jenis;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Kota / kabupaten.']);
            }
            return response()->json(['success' => true, 'message' => 'Kota / kabupaten berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Kabupaten/kota :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $kotaId = $request->kota_id;
            $data = Kota::where('kota_id', $kotaId)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $kecamatan = Kecamatan::where('kota_id', $kotaId)->where('client_id', $this->clientId)->first();
            $kelurahan = Kelurahan::where('kota_id', $kotaId)->where('client_id', $this->clientId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $data->kota = strtoupper($request->kota);
            $data->is_prioritas = $request->is_prioritas;
            $data->jenis = $request->jenis;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Kota.']);
            }
            //update nama kota di kecamatan
            if($kecamatan) {
                $success = Kecamatan::where('kota_id', $kotaId)->where('client_id', $this->clientId)->update(['kota' => $request->kota]);
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Kota. Update data kecamatan gagal.']);
                }
            }
            //update nama kota di kelurahan
            if($kelurahan) {
                $success = Kelurahan::where('kota_id', $kotaId)->where('client_id', $this->clientId)->update(['kota' => $request->kota]);
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Kota. Update data kelurahan gagal.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Kota berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Kota :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $data = Kota::where('kota_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Kota tidak ditemukan.']);
            }
            
            $kecamatan = Kecamatan::where('kota_id', $kotaId)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if($kecamatan) {
                return response()->json(['success' => false, 'message' => 'Data Kota tidak dapat dinonaktifkan. Ada data kecamatan yang aktif.']);
            }

            $kelurahan = Kelurahan::where('kota_id', $kotaId)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if($kelurahan) {
                return response()->json(['success' => false, 'message' => 'Data Kota tidak dapat dinonaktifkan. Ada data kelurahan yang aktif.']);
            }
            
            $success = Kota::where('kota_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan Kota']);
            }
            return response()->json(['success' => true, 'message' => 'Data Kota berhasil dinonaktifkan']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data Kota :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getKotaId($propinsiId)
    {
        try {
            $id = $propinsiId.'.K001';
            $maxId = Kota::withTrashed()->where('kota_id', 'LIKE', $propinsiId . '.K%')->max('kota_id');
            if (!$maxId) {
                $id = $propinsiId . '.K001';
            } else {
                $maxId = str_replace($propinsiId.'.K', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $propinsiId.'.K00' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $propinsiId.'.K0' . $count;
                } else {
                    $id = $propinsiId.'.K' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $propinsiId.'.K' . Uuid::uuid4()->getHex();
        }
    }
}
