<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\DepoUnit;
use Modules\MasterData\Entities\Produk;
use Modules\ManajemenUser\Entities\MemberDepo;

use Ramsey\Uuid\Uuid;

use Illuminate\Support\Facades\Auth;
use DB;

class MasterDepoController extends Controller
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

            if($request->has('per_page')) {
                $pageNumber = $request->get('per_page');
                if(strtoupper($pageNumber) == 'ALL') { 
                    $pageNumber = Depo::where('client_id',$this->clientId)->count(); 
                }
            }
            if($request->has('is_aktif')) {
                $active = $request->get('is_aktif');
            }

            $data = Depo::where('client_id', $this->clientId)
                ->where('depo_nama', 'ILIKE', '%'.$request->get('depo_nama').'%')
                ->where('is_aktif', 'LIKE', '%'.$active.'%')
                ->orderBy('updated_at','DESC')
                ->paginate($pageNumber);

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan data Depo. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getDepoId();
            $data = new Depo();

            $data->depo_id = $id;
            $data->depo_nama = strtoupper($request->depo_nama);
            $data->deskripsi = $request->deskripsi;
            $data->lokasi = $request->lokasi;
            $data->is_gudang = $request->is_gudang;
            $data->is_virtual = $request->is_virtual;
            
            $data->is_stock_adjustment = $request->is_stock_adjustment;
            $data->is_inventory_issue = $request->is_inventory_issue;
            $data->is_distribution = $request->is_distribution;
            $data->is_purchase_req = $request->is_purchase_req;
            $data->is_purchase_order = $request->is_purchase_order;
            $data->is_purchase_receive = $request->is_purchase_receive;
            $data->is_purchase_return = $request->is_purchase_return;
            $data->is_direct_purchase = $request->is_direct_purchase;
            $data->is_production = $request->is_production;
            $data->is_sell = $request->is_sell;

            $data->is_opname = false;
            $data->is_lock = false;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if(!$success){
                return response()->json(['success' => false, 'message'  => 'ada kesalahan saat menyimpan data.']);
            }
            return response()->json(['success' => true, 'message'  => 'data berhasil disimpan ', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data depo. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = Depo::where('client_id', $this->clientId)->where('depo_id',$id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Data tidak ditemukan']);
            }
            return response()->json(['success' => true, 'message'  => 'ok', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan data Depo' . '. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            // dd($this->clientId);            
            $data = Depo::where('client_id', $this->clientId)->where('depo_id', $request->depo_id)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message'  => 'Data tidak ditemukan']);
            }

            $data->depo_nama = $request->depo_nama;
            $data->deskripsi = $request->deskripsi;
            $data->lokasi = $request->lokasi;
            $data->is_gudang = $request->is_gudang;
            $data->is_virtual = $request->is_virtual;
            
            $data->is_stock_adjustment = $request->is_stock_adjustment;
            $data->is_inventory_issue = $request->is_inventory_issue;
            $data->is_distribution = $request->is_distribution;
            $data->is_purchase_req = $request->is_purchase_req;
            $data->is_purchase_order = $request->is_purchase_order;
            $data->is_purchase_receive = $request->is_purchase_receive;
            $data->is_purchase_return = $request->is_purchase_return;
            $data->is_direct_purchase = $request->is_direct_purchase;
            $data->is_production = $request->is_production;
            $data->is_sell = $request->is_sell;

            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message'  => 'Ada kesalahan dalam menyimpan data.']);
            }
            return response()->json(['success' => true, 'message'  => 'Berhasil update data', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Update data Depo. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $item = DepoProduk::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('jml_total','>',0)
                ->where('depo_id',$id)->first();
            if($item) {
                return response()->json(['success' => false, 'message'  => 'depo masih ada persediaan aktif dan lebih dari nol.']);
            }

            $depoUnit = DepoUnit::where('client_id',$this->clientId)->where('depo_id',$id)->first();
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('depo_id',$id)->first();

            DB::connection('dbclient')->beginTransaction();

            $success = Depo::where('client_id', $this->clientId)->where('depo_id', $id)->update([
                'is_aktif' => '0',
                'updated_by' => Auth::user()->username,
            ]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message'  => 'Depo data tidak ditemukan.']);
            }

            if($depoUnit) {
                $success = DepoUnit::where('client_id',$this->clientId)->where('depo_id',$id)
                    ->update([
                        'is_aktif' => false,
                        'updated_by' => Auth::user()->username,
                    ]);

                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message'  => 'Depo unit data tidak berhasil dinonaktifkan.']);
                }    
            }

            if($memberDepo) {
                $success = MemberDepo::where('client_id',$this->clientId)->where('depo_id',$id)
                    ->update([
                        'is_aktif' => false,
                        'updated_by' => Auth::user()->username,
                    ]);

                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message'  => 'Depo member data tidak berhasil dinonaktifkan.']);
                }    
            }
            
            DB::connection('dbclient')->commit();            
            return response()->json(['success' => true, 'message'  => 'Depo persediaan berhasil di-nonaktif-kan.']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Hapus data Depo. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getDepoId()
    {
        try {
            $id = $this->clientId.'.DEPO-0001';
            $maxId = Depo::withTrashed()->where('depo_id', 'LIKE', $this->clientId.'.DEPO-%')->max('depo_id');
            if (!$maxId) {
                $id = $this->clientId.'.DEPO-0001';
            } else {
                $maxId = str_replace($this->clientId.'.DEPO-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'.DEPO-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId.'.DEPO-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId.'.DEPO-0' . $count;
                } else {
                    $id = $this->clientId.'.DEPO-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId.'.DEPO-' . Uuid::uuid4()->getHex();
        }
    }

}
