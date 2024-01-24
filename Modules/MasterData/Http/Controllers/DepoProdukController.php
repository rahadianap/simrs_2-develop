<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use DB;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\Depo;

class DepoProdukController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request,$depo)
    {
        try {
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = DepoProduk::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }            
            
            $list = DepoProduk::leftJoin('tb_produk','tb_produk.produk_id','=','tb_depo_produk.produk_id') 
                    ->leftJoin('tb_depo','tb_depo_produk.depo_id','=','tb_depo.depo_id')
                    ->where('tb_depo_produk.client_id',$this->clientId)
                    ->where('tb_depo_produk.is_aktif','ILIKE',$aktif)
                    ->where('tb_depo_produk.depo_id',$depo)
                    ->where(function($q) use ($keyword) {
                        $q->where('tb_depo_produk.depo_produk_id','ILIKE',$keyword)
                        ->orWhere('tb_depo_produk.produk_id','ILIKE',$keyword)
                        ->orWhere('tb_produk.produk_nama','ILIKE',$keyword);
                    })
                    ->select(

                        'tb_depo_produk.depo_produk_id',
                        'tb_depo.depo_nama',
                        'tb_depo.depo_id',
                        'tb_depo_produk.produk_id',
                        'tb_produk.produk_nama',
                        'tb_produk.jenis_produk',
                        'tb_produk.satuan',
                        'tb_produk.satuan_beli',
                        'tb_produk.harga_beli',
                        'tb_produk.hna',
                        'tb_depo_produk.is_aktif',
                        'tb_depo_produk.jml_awal',
                        'tb_depo_produk.jml_masuk',
                        'tb_depo_produk.jml_keluar',
                        'tb_depo_produk.jml_penyesuaian',
                        'tb_depo_produk.jml_total',

                        )
                    ->orderBy('tb_depo_produk.jenis_produk','ASC')
                    ->orderBy('tb_depo_produk.depo_produk_id','ASC')->paginate($per_page); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List depo produk tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    
    public function data(Request $request, $id)
    {
        try {
            $list = DepoProduk::where('depo_produk_id',$id)->where('client_id',$this->clientId)->first(); 

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian depo produk tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request,$depo_id)
    {
        try {
            if(!$request->items) {
                return response()->json(['success' => false,'message' => 'Item depo kosong (tidak dikenali).']);
            }
            // $depo = Depo::where('depo_id',$request->depo_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            // if(!$depo) {
            //     return response()->json(['success' => false,'message' => 'Depo tidak ditemukan.']);
            // }

            DB::connection('dbclient')->beginTransaction();
            foreach($request->items as $item) {
                $data = DepoProduk::where('client_id',$this->clientId)->where('depo_id',$depo_id)->where('produk_id',$item['produk_id'])->first();
                if(!$data) {
                    $id = $depo_id.'-'.$item['produk_id'];
                    $data = new DepoProduk();
                    $data->depo_produk_id   = $id;
                    $data->depo_id          = $depo_id;
                    $data->produk_id        = $item['produk_id'];
                    $data->jenis_produk     = $item['jenis_produk'];
                    $data->jml_awal         = $item['jml_awal'];
                    $data->jml_masuk        = $item['jml_masuk'];
                    $data->jml_keluar       = $item['jml_keluar'];
                    $data->jml_penyesuaian  = $item['jml_penyesuaian'];
                    $data->jml_total        = $item['jml_total'];
                    $data->created_by       = Auth::user()->username;
                    $data->client_id        = $this->clientId;
                } 

                $data->is_aktif = 1;
                $data->updated_by = Auth::user()->username;            
                $success = $data->save();

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produk']);
                }
            }          
            DB::connection('dbclient')->commit();

            return response()->json(['success' => true,'message' => 'Data Depo Produk berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Depo Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'Unknown Data Client ID']);
            }
            $clientId = $request->header('X-cid');

            $id = $request->depo_produk_id;
            $data = DepoProduk::where('depo_produk_id', $id)->where('client_id', $clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data depo produk tidak ditemukan.']);
            }
            // return $data;
            $data->update([
                $data->depo_id          = $request->depo_id,
                $data->produk_id        = $request->produk_id,
                $data->jml_awal         = $request->jml_awal,
                $data->jml_masuk        = $request->jml_masuk,
                $data->jml_keluar       = $request->jml_keluar,
                $data->jml_penyesuaian  = $request->jml_penyesuaian,
                $data->jml_total        = $request->jml_total,
                $data->is_aktif         = $request->is_aktif,
                // $data->updated_by    = Auth::user()->username,
                $data->updated_by       = "hafiizh_update"
            ]);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Depo Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $dataExist = DepoProduk::where('depo_produk_id', $id)
                ->where('client_id',$this->clientId)
                ->where('jml_total','>',0)->first();

            if($dataExist) {
                return response()->json(['success'=>false,'message'=>'Data tidak berhasil dinonaktifkan. jumlah stok lebih dari nol atau tidak ditemukan.']);
            }

            $data = DepoProduk::where('depo_produk_id', $id)->where('client_id',$this->clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'Data berhasil dinonaktifkan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Depo Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getDepoProdukId($clientId) 
    {
        try {
            $id = $clientId.'-DPDK'.'-00001';
            $maxId = DepoProduk::withTrashed()->where('depo_produk_id','LIKE',$clientId.'-DPDK'.'%')->max('depo_produk_id');

            if (!$maxId) { $id = $clientId.'-DPDK'.'-00001'; }
            else {
                $maxId = str_replace($clientId.'-DPDK'.'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-DPDK'.'-0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-DPDK'.'-000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-DPDK'.'-00'.$count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $clientId.'-DPDK'.'-0'.$count; } 
                else { $id = $clientId.'-DPDK'.'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'DPDK'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }
}