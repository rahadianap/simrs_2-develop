<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\BedInap;
use Modules\MasterData\Entities\Tindakan;
use Illuminate\Support\Facades\Auth;
use Modules\MasterData\Entities\Bridging;

use DB;

class RuangPelayananController extends Controller
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
                $keyword = '%' . $request->get('keyword') . '%';
            }

            $unitID = '%%';
            if ($request->has('unit_id')) {
                $unitID = $request->unit_id;
            }

            $perPage = 10;
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
            }
            // $perPage = $request->get('per_page');

            // $perpage = RuangPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->count();
            
            $list = RuangPelayanan::leftJoin('tb_unit', 'tb_ruang.unit_id', '=', 'tb_unit.unit_id')
                ->where('tb_ruang.client_id', $this->clientId)->where('tb_ruang.is_aktif', '1')
                ->where('tb_ruang.ruang_nama', 'ILIKE', $keyword)
                ->where('tb_unit.unit_nama', 'ILIKE', $keyword)
                ->where('tb_ruang.unit_id', 'ILIKE', $unitID)
                ->orderBy('tb_ruang.ruang_id', 'ASC')->paginate($perPage);

            foreach($list->items() as $itm) {
                $itm['bridging'] = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('local_resource_id',$itm['ruang_id'])
                    ->get();
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data ruang pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function RuangBpjsLists(Request $request)
    {
        try {
            $keyword = '%%';
            $unitID = '%%';
            if ($request->has('keyword')) { $keyword = '%' . $request->input('keyword') . '%'; }
            if ($request->has('unit_id')) { $unitID = $request->unit_id; }

            $perpage = RuangPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->count();
            
            $list = RuangPelayanan::leftJoin('tb_unit', 'tb_ruang.unit_id', '=', 'tb_unit.unit_id')
                ->where('tb_ruang.client_id', $this->clientId)->where('tb_ruang.is_aktif', '1')
                ->where('tb_ruang.ruang_nama', 'LIKE', $keyword)
                ->where('tb_unit.unit_nama', 'LIKE', $keyword)
                ->where('tb_ruang.unit_id', 'LIKE', $unitID)
                ->orderBy('tb_ruang.ruang_id', 'ASC')->paginate($perpage);

            foreach($list->items() as $item) {
                $dtm = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('local_resource_id',$item['ruang_id'])
                    ->where('bridging_group','BPJS')
                    ->first();

                if($dtm) {
                    $item['kode_bpjs'] = $dtm->bridging_resource_id;
                    $item['nama_bpjs'] = $dtm->bridging_resource_name;
                }
                else {
                    $item['kode_bpjs'] = null;
                    $item['nama_bpjs'] = null;
                }
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data ruang pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = RuangPelayanan::where('client_id', $this->clientId)->where('ruang_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $data['beds'] =  BedInap::where('client_id',$this->clientId)->where('is_aktif',1)->where('ruang_id',$id)->get();

            $data['bridging'] = Bridging::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('local_resource_id',$id)
                    ->get();

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data ruang pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $prefix = 'KMR';
            $id = $this->getRuangId($this->clientId, $prefix);

            DB::connection('dbclient')->beginTransaction();

            $data = new RuangPelayanan();
            $data->ruang_id = $id;
            $data->unit_id = $request->unit_id;
            $data->ruang_nama = strtoupper($request->ruang_nama);
            $data->is_utama = $request->is_utama;
            $data->kelas_harga = $request->kelas_harga;
            $data->kelas_kamar = $request->kelas_kamar;
            $data->harga_id = $request->harga_id;
            $data->lokasi = strtoupper($request->lokasi);
            $data->deskripsi = $request->deskripsi;

            $data->is_aktif = true;
            $data->is_utama = $request->is_utama;
            $data->is_ruang_isolasi = $request->is_ruang_isolasi;
            $data->is_ventilator = $request->is_ventilator;
            $data->is_negative_pressure = $request->is_negative_pressure;
            $data->is_ruang_operasi = $request->is_ruang_operasi;
            $data->is_pandemi = $request->is_pandemi;
           
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data ruang pelayanan']);
            }

            // $act                       = new Tindakan();
            // $act->tindakan_id          = $id;
            // $act->tindakan_nama        = "SEWA KAMAR ".strtoupper($request->ruang_nama);
            // $act->klasifikasi          = "SEWA_KAMAR";
            // $act->meta_data            = 'BIAYA SEWA KAMAR '.strtoupper($request->ruang_nama); 
            // $act->satuan               = "";
            // $act->is_paket             = false;
            // $act->is_hitung_admin      = true;
            // $act->is_diskon            = true;
            // $act->is_cito              = false;            
            // $act->is_tampilkan_dokter  = false;
            // $act->is_kamar             = true;
            // $act->rl_kode              = null;
            // $act->kelompok_tagihan_id  = null;
            // $act->kelompok_vklaim_id   = null;
            // $act->deskripsi            = 'BIAYA SEWA KAMAR '.strtoupper($request->ruang_nama); 
            // $act->is_aktif             = 1;
            // $act->client_id            = $this->clientId;
            // $act->created_by           = Auth::user()->username;
            // $act->updated_by           = Auth::user()->username;
            // $success                   = $act->save();

            // if (!$success) {
            //     DB::connection('dbclient')->rollBack();
            //     return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data biaya ruang pelayanan']);
            // }

            DB::connection('dbclient')->commit();

            $data['beds']=[];

            return response()->json(['success' => true, 'message' => 'data ruang pelayanan berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menambah ruang pelayanan. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = RuangPelayanan::where('client_id', $this->clientId)->where('ruang_id', $request->ruang_id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data ruang pelayanan tidak ditemukan.']);
            }

            $data->is_utama             = $request->is_utama;
            $data->kelas_kamar          = $request->kelas_kamar;
            $data->kelas_harga          = $request->kelas_harga;
            $data->harga_id             = $request->harga_id;
            $data->lokasi               = strtoupper($request->lokasi);
            $data->deskripsi            = $request->deskripsi;            
            $data->is_ruang_isolasi     = $request->is_ruang_isolasi;
            $data->is_ventilator        = $request->is_ventilator;
            $data->is_negative_pressure = $request->is_negative_pressure;
            $data->is_ruang_operasi     = $request->is_ruang_operasi;
            $data->is_pandemi           = $request->is_pandemi;
            $data->updated_by           = Auth::user()->username;
            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengubah data ruang pelayanan']);
            }

            return response()->json(['success' => true, 'message' => 'data ruang pelayanan berhasil diubah', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat mengubah data ruang pelayanan. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = RuangPelayanan::where('ruang_id', $id)->where('is_aktif',1)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus unit. Data unit tidak ditemukan / sudah tidak aktif.']);
            }

            $bed = BedInap::where('ruang_id',$id)
                ->where('is_tersedia',false)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->first();
            if ($bed) {
                return response()->json(['success' => false, 'message' => 'Ruang Unit tidak bisa dihapus, terdapat Bed yang terisi.']);
            }

            $data->updated_by = Auth::user()->username;
            $data->is_aktif = 0;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data ruang']);
            }
            return response()->json(['success' => true, 'message' => 'Ruang Unit berhasil dinonaktifkan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data ruang pelayanan gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function getRuangId($clientId, $prefix)
    {
        try {
            $id = $this->clientId . '-' . $prefix . '0001';
            $maxId = RuangPelayanan::withTrashed()->where('client_id', $this->clientId)->where('ruang_id', 'LIKE', $this->clientId . '-' . $prefix . '%')->max('ruang_id');
            if (!$maxId) {
                $id = $this->clientId . '-' . $prefix . '0001';
            } else {
                $maxId = str_replace($this->clientId . '-' . $prefix, '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '-' . $prefix . '000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '-' . $prefix . '00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '-' . $prefix . '0' . $count;
                } else {
                    $id = $this->clientId . '-' . $prefix . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return 'RP' . date('Y.m.d') . '-' . Uuid::uuid4()->getHex();
        }
    }
}
