<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\UnitTindakan;
use Modules\MasterData\Entities\Harga;
use Modules\MasterData\Entities\HargaDetail;
use Modules\MasterData\Entities\TindakanBhp;
use Modules\MasterData\Entities\Produk;

class UnitTindakanController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request, $id)
    {
        try {
            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if(strtoupper($per_page) =='ALL' ) {
                    $per_page = UnitTindakan::where('client_id',$this->clientId)
                        ->where(function($q) use($id){
                            $q->where('unit_id',$id)
                            ->orWhere('tindakan_id',$id);
                        })->where('is_aktif',1)
                        ->count();
                }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            /**untuk menentukan  */
            $kelas = '%%';
            if($request->has('kelas')) {
                $kelas = '%'.$request->input('kelas').'%';
            }
            $buku = '%%';
            if($request->has('buku')) {
                $buku = '%'.$request->input('buku').'%';
            }

            $list = UnitTindakan::leftjoin('tb_tindakan', 'tb_unit_tindakan.tindakan_id', '=', 'tb_tindakan.tindakan_id')
                ->leftjoin('tb_unit', 'tb_unit_tindakan.unit_id', '=', 'tb_unit.unit_id')
                ->where('tb_unit_tindakan.is_aktif',1)
                ->where('tb_unit_tindakan.client_id',$this->clientId)
                ->where(function($q) use ($id){
                    $q->where('tb_unit_tindakan.unit_id',$id)
                    ->orWhere('tb_unit_tindakan.tindakan_id',$id);
                })
                ->where('tb_tindakan.tindakan_nama','ILIKE',$keyword)
                // ->select('tb_unit_tindakan.unit_tindakan_id','tb_unit_tindakan.unit_id','tb_unit.unit_nama','tb_unit.kepala_unit','tb_unit_tindakan.is_tampil_dokter','tb_tindakan.*')
                ->paginate($per_page);

            foreach($list->items() as $itm) {
                $itm['bhp'] = TindakanBhp::leftJoin('tb_produk','tb_produk.produk_id','=','tb_tindakan_bhp.produk_id')
                    ->where('tb_tindakan_bhp.client_id',$this->clientId)->where('tb_tindakan_bhp.is_aktif',1)
                    ->where('tb_tindakan_bhp.tindakan_id',$itm['tindakan_id'])
                    ->select('tb_produk.*','tb_tindakan_bhp.jumlah','tb_tindakan_bhp.tindakan_id','tb_tindakan_bhp.tindakan_nama')
                    ->get();

                $prices = Harga::where('tindakan_id',$itm['tindakan_id'])
                    ->where('client_id',$this->clientId)
                    ->where('kelas_id','ILIKE',$kelas)
                    ->where('buku_harga_id','ILIKE',$buku)
                    ->where('is_aktif',true)
                    ->get();   
                
                foreach($prices as $pr) {
                    $komp = HargaDetail::where('client_id',$this->clientId)
                        ->where('is_aktif',true)
                        ->where('harga_id',$pr->harga_id)
                        ->get();
                    if($komp) {
                        $pr['komponen'] = $komp; 
                    }
                    else {
                        $pr['komponen'] = [];
                    }
                }
                $itm['harga'] = $prices;
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List unit tindakan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function priceLists(Request $request, $id) {
        try {
            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if(strtoupper($per_page) =='ALL' ) {
                    $per_page = UnitTindakan::where('client_id',$this->clientId)
                        ->where(function($q) use($id){
                            $q->where('unit_id',$id)
                            ->orWhere('tindakan_id',$id);
                        })->where('is_aktif',1)
                        ->count();
                }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            /**untuk menentukan  */
            $kelas = '%%';
            if($request->has('kelas')) {
                $kelas = '%'.$request->input('kelas').'%';
            }
            $buku = '%%';
            if($request->has('buku')) {
                $buku = '%'.$request->input('buku').'%';
            }

            $list = UnitTindakan::leftjoin('tb_tindakan', 'tb_unit_tindakan.tindakan_id', '=', 'tb_tindakan.tindakan_id')
                ->leftjoin('tb_unit', 'tb_unit_tindakan.unit_id', '=', 'tb_unit.unit_id')
                ->where('tb_unit_tindakan.is_aktif',1)
                ->where('tb_unit_tindakan.client_id',$this->clientId)
                ->where(function($q) use ($id){
                    $q->where('tb_unit_tindakan.unit_id',$id)
                    ->orWhere('tb_unit_tindakan.tindakan_id',$id);
                })

                ->where('tb_tindakan.tindakan_nama','ILIKE',$keyword)
                ->select('tb_unit_tindakan.unit_tindakan_id','tb_unit_tindakan.unit_id','tb_unit.unit_nama','tb_unit.kepala_unit','tb_unit_tindakan.is_tampil_dokter','tb_tindakan.*')
                ->paginate($per_page);
            
            $listFinal = [];
            $i = 0;
            $isKompExist = false;
            foreach($list->items() as $itm) {
                $itm['bhp'] = TindakanBhp::leftJoin('tb_produk','tb_produk.produk_id','=','tb_tindakan_bhp.produk_id')
                    ->where('tb_tindakan_bhp.client_id',$this->clientId)->where('tb_tindakan_bhp.is_aktif',1)
                    ->where('tb_tindakan_bhp.tindakan_id',$itm['tindakan_id'])
                    ->select('tb_produk.*','tb_tindakan_bhp.jumlah','tb_tindakan_bhp.tindakan_id','tb_tindakan_bhp.tindakan_nama')
                    ->get();

                $prices = Harga::where('tindakan_id',$itm['tindakan_id'])
                    ->where('client_id',$this->clientId)
                    ->where('kelas_id','ILIKE',$kelas)
                    ->where('buku_harga_id','ILIKE',$buku)
                    ->where('is_aktif',true)
                    ->get();   
                
                foreach($prices as $pr) {
                    $komp = HargaDetail::where('client_id',$this->clientId)
                        ->where('is_aktif',true)
                        ->where('harga_id',$pr->harga_id)
                        ->get();
                    
                    if($komp) { $pr['komponen'] = $komp; $isKompExist = true; }
                    else { $pr['komponen'] = []; }
                }
                $itm['harga'] = $prices;

                if($isKompExist){
                    $listFinal[$i] = $itm;
                    $i++;
                    $isKompExist = false;
                }
            }
            
            $itemsFinal = new \Illuminate\Pagination\LengthAwarePaginator(
                $listFinal,
                count($listFinal),
                $list->perPage(),
                $list->currentPage(), [
                  'path' => \Request::url(),
                  'query' => [
                    'page' => $list->currentPage()
                  ]
                ]
              );
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$itemsFinal]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List unit tindakan tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = UnitTindakan::leftjoin('tb_tindakan', 'tb_unit_tindakan.tindakan_id', '=', 'tb_tindakan.tindakan_id')
            ->leftjoin('tb_unit', 'tb_unit_tindakan.unit_id', '=', 'tb_unit.unit_id')
            ->where('tb_unit_tindakan.is_aktif',1)
            ->where('tb_unit_tindakan.client_id',$this->clientId)
            ->where('tb_unit_tindakan.tindakan_id',$id)
            ->select(
                'tb_unit_tindakan.tindakan_unit_id',
                'tb_unit_tindakan.tindakan_id',
                'tb_unit_tindakan.unit_id',
                'tb_unit.unit_nama',
                'tb_tindakan.tindakan_nama',
                'tb_tindakan.klasifikasi',
                'tb_unit_tindakan.is_tampil_dokter',
            )
            ->first();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian unit tindakan tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {           
            $request->validate([
                'unit_id' => 'required',
            ]);

            $acts = $request->tindakanUnit;
            DB::connection('dbclient')->beginTransaction();
            foreach($acts as $act) {
                $exist = UnitTindakan::where('client_id',$this->clientId)->where('is_aktif',1)->where('tindakan_id',$act['tindakan_id'])->where('unit_id',$request->unit_id)->first();
                if(!$exist) {
                    $data = new UnitTindakan();
                    $data->unit_tindakan_id     = $this->getUnitTindakanId($this->clientId);
                    $data->unit_id              = $request->unit_id;
                    $data->tindakan_id          = $act['tindakan_id'];
                    $data->is_aktif             = 1;
                    $data->client_id            = $this->clientId;
                    $data->created_by           = Auth::user()->username;
                    $data->updated_by           = Auth::user()->username;
                    $success = $data->save();

                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Unit Tindakan']);
                    }
                }
            }
            DB::connection('dbclient')->commit();            
            return response()->json(['success' => true,'message' => 'Data Unit Tindakan berhasil disimpan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Unit Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    // public function update(Request $request)
    // {
    //     try {
    //         if (!$request->hasHeader('X-cid')) {
    //             return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
    //         }
    //         $clientId = $request->header('X-cid');

    //         $request->validate([
    //             'unit_id' => 'required',
    //             'tindakan_id' => 'required',
    //         ]);

    //         $id = $request->unit_tindakan_id;
    //         $data = UnitTindakan::where('unit_tindakan_id', $id)->where('client_id', $this->clientId)->first();
    //         if (!$data) {
    //             return response()->json(['success' => false, 'message' => 'Data Unit Tindakan tidak ditemukan.']);
    //         }

    //         $data->update([
    //             $data->updated_by        = Auth::user()->username,
    //         ]);
            
    //         return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false,'message' =>'Ubah Unit Tindakan tidak berhasil.','error'=>$e->getMessage()]);
    //     }
    // }

    public function delete(Request $request, $id)
    {
        try {
            $success = UnitTindakan::where('unit_tindakan_id', $id)->where('client_id',$this->clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);
            if(!$success) {
                return response()->json(['success'=>false,'message'=>'Data tindakan unit tidak berhasil dihapus.']);
            }

            return response()->json(['success'=>true,'message'=>'Data tindakan unit berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Unit Tindakan tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getUnitTindakanId($clientId) 
    {
        try {
            $id = $clientId.'-UTK'.'-00001';
            $maxId = UnitTindakan::withTrashed()->where('unit_tindakan_id','LIKE',$clientId.'-UTK'.'%')->max('unit_tindakan_id');

            if (!$maxId) { $id = $clientId.'-UTK'.'-00001'; }
            else {
                $maxId = str_replace($clientId.'-UTK'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-UTK'.'-0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-UTK'.'-000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-UTK'.'-00'.$count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $clientId.'-UTK'.'-0'.$count; } 
                else { $id = $clientId.'-UTK'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'UTK'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}
