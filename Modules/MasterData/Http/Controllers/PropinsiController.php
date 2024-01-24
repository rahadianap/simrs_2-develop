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


class PropinsiController extends Controller
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
            $aktif= 'true';
            $keyword= '%%';
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage = Propinsi::where('client_id',$this->clientId)->count(); }
            }
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            $data = Propinsi::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('propinsi','ILIKE',$keyword)
                    ->orderBy('is_prioritas','DESC')
                    ->orderBy('no_urut','ASC')
                    ->orderBy('propinsi','ASC')->paginate($perPage); 

            foreach($data->items() as $item) {
                $item['bridging'] = Bridging::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('local_resource_id',$item['propinsi_id'])
                    ->get();
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Propinsi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function propBpjsLists(Request $request)
    {
        try {
            $aktif= 'true';
            $keyword= '%%';
            $perPage = Propinsi::where('client_id',$this->clientId)->count();
            
            if ($request->has('is_aktif')) {
                $aktif = '%'.$request->get('is_aktif').'%';
            }
            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            $data = Propinsi::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('propinsi','ILIKE',$keyword)
                    ->orderBy('is_prioritas','DESC')
                    ->orderBy('no_urut','ASC')
                    ->orderBy('propinsi','ASC')->paginate($perPage); 

            foreach($data->items() as $item) {
                $bdata = Bridging::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('local_resource_id',$item['propinsi_id'])
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

    public function data(Request $request, $id)
    {
        try {
            $data = Propinsi::where('client_id', $this->clientId)->where('propinsi_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            $data['bridging'] = Bridging::where('client_id',$this->clientId)->where('is_aktif',1)
                    ->where('local_resource_id',$id)
                    ->get();
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Propinsi : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getPropinsiId();

            $req = Propinsi::where('client_id', $this->clientId)->where('propinsi', 'ILIKE', $request->propinsi)->where('is_aktif', 1)->first();
            if($req){
                return response()->json(['success' => false,'message' => 'Propinsi sudah ada.']);
            }

            $data = new Propinsi();
            $data->propinsi_id = $id;
            $data->propinsi = strtoupper($request->propinsi);
            $data->no_urut = $request->no_urut;
            $data->is_prioritas = $request->is_prioritas;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Propinsi.']);
            }
            return response()->json(['success' => true, 'message' => 'Suku berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Suku :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $propId = $request->propinsi_id;
            $data = Propinsi::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $kota = Kota::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            $kecamatan = Kecamatan::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            $kelurahan = Kelurahan::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $data->propinsi = strtoupper($request->propinsi);
            $data->no_urut = $request->no_urut;
            $data->is_prioritas = $request->is_prioritas;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Propinsi.']);
            }
            //update kota
            if($kota) {
                $success = Kota::where('propinsi_id', $propId)->where('client_id', $this->clientId)->update(['propinsi'=>strtoupper($request->propinsi)]);
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Propinsi. Update propinsi kota gagal.']);
                }
            }
            //update kecamatan
            if($kecamatan) {
                $success = Kecamatan::where('propinsi_id', $propId)->where('client_id', $this->clientId)->update(['propinsi'=>strtoupper($request->propinsi)]);
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Propinsi. Update propinsi kecamatan gagal.']);
                }
            }

            //update kelurahan
            if($kelurahan) {
                $success = Kelurahan::where('propinsi_id', $propId)->where('client_id', $this->clientId)->update(['propinsi'=>strtoupper($request->propinsi)]);
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Propinsi. Update propinsi kelurahan gagal.']);
                }
            }            

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Propinsi berhasil di update.', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Propinsi :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Propinsi::where('propinsi_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Propinsi tidak ditemukan.']);
            }

            $kota = Kota::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            if($kota) {
                return response()->json(['success' => false, 'message' => 'Data Kota tidak dapat dinonaktifkan. Ada data kabupaten / kota yang aktif.']);
            }
            
            $kecamatan = Kecamatan::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            if($kecamatan) {
                return response()->json(['success' => false, 'message' => 'Data Kota tidak dapat dinonaktifkan. Ada data kecamatan yang aktif.']);
            }
            
            $kelurahan = Kelurahan::where('propinsi_id', $propId)->where('client_id', $this->clientId)->first();
            if($kelurahan) {
                return response()->json(['success' => false, 'message' => 'Data Kota tidak dapat dinonaktifkan. Ada data kelurahan yang aktif.']);
            }

            $success = Propinsi::where('propinsi_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan Propinsi']);
            }
            return response()->json(['success' => true, 'message' => 'Data Propinsi berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data Propinsi :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getPropinsiId()
    {
        try {
            $id = $this->clientId . '.P01';
            $maxId = Propinsi::withTrashed()->where('propinsi_id', 'LIKE', $this->clientId . '.P%')->max('propinsi_id');
            if (!$maxId) {
                $id = $this->clientId . '.P01';
            } else {
                $maxId = str_replace($this->clientId . '.P', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.P0' . $count;
                } else {
                    $id = $this->clientId . '.P' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.P' . Uuid::uuid4()->getHex();
        }
    }

    public function updateBridging(Request $request) {
        try {
            $data = Bridging::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('local_resource_id',$request->propinsi_id)
                ->where('bridging_group',$request->bridging_group)
                ->first();
            
            if(!$data) {
                $data = new Bridging();
                $data->bridging_id = $this->getBridgingPropinsiId();
                $data->bridging_group = $request->bridging_group;
                $data->local_resource_id = $request->propinsi_id;
                $data->client_id = $this->clientId;
                $data->is_aktif = true;
                $data->created_by = Auth::user()->username;
            }
            
            $data->local_resource_name = $request->propinsi;
            $data->bridging_resource_id = $request->kode_bridging;
            $data->bridging_resource_name = $request->nama_bridging;
            $data->updated_by = Auth::user()->username;                
            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengubah kode bridging Propinsi']);
            }
            return response()->json(['success' => true, 'message' => 'berhasil mengubah kode bridging Propinsi', 'data' =>$data ]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengubah kode bridging data Propinsi :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getBridgingPropinsiId()
    {
        try {
            $id = $this->clientId.'-'.date('YmdHis').'-BR001';

            $maxId = Bridging::withTrashed()->where('bridging_id', 'LIKE', $this->clientId.'-'.date('YmdHis').'-BR%')->max('bridging_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.date('YmdHis').'-BR001';
            } else {
                $maxId = str_replace($this->clientId.'-'.date('YmdHis').'-BR', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'-'.date('YmdHis').'-BR00' . $count;
                }
                else if ($count >= 10 && $count < 100) {
                    $id = $this->clientId.'-'.date('YmdHis').'-BR0' . $count;
                } 
                else {
                    $id = $this->clientId.'-'.date('YmdHis').'-BR' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.BR' . Uuid::uuid4()->getHex();
        }
    }

}
