<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\ProdukAkun;
use Ramsey\Uuid\Uuid;
use DB;
use Illuminate\Support\Facades\Auth;

class ProdukAccountController extends Controller
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
            $keyword = '%%';
            $active = '%true%';

            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') { $perPage=ProdukAkun::where('client_id', $this->clientId)->count(); }
            }

            if ($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }

            if ($request->has('is_aktif')) {
                $active = '%'.$request->get('is_aktif').'%';
            }
            
            $data = ProdukAkun::where('client_id', $this->clientId)
                ->where('is_aktif','ILIKE',$active)
                ->where( function($q) use($keyword){
                    $q->where('produk_akun','ILIKE',$keyword)
                    ->orWhere('produk_akun_id','ILIKE',$keyword);
                })->orderBy('produk_akun', 'ASC')->paginate($perPage);

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Produk Account : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = ProdukAkun::where('client_id', $this->clientId)->where('produk_akun_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }



            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data product account : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = new ProdukAkun();
            $id = $this->getProdukAkunId();
            $data->produk_akun_id = $id;
            $data->produk_akun = strtoupper($request->produk_akun);
            $data->jenis_produk = $request->jenis_produk;
            $data->is_aktif = 1;
            $data->deskripsi = $request->deskripsi;
            
            $data->coa_revenue_id = $request->coa_revenue_id;            
            $data->coa_inventory_id = $request->coa_inventory_id;            
            $data->coa_cogs_id = $request->coa_cogs_id;  

            $data->coa_revenue = $request->coa_revenue;
            $data->coa_cogs = $request->coa_cogs;
            $data->coa_inventory = $request->coa_inventory;
                        
            
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data akun produk.']);
            }
            return response()->json(['success' => true, 'message' => 'Akun Produk berhasil di simpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data akun produk :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->produk_akun_id;
            $data = ProdukAkun::where('produk_akun_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $data->deskripsi = $request->deskripsi;
            
            $data->coa_revenue_id = $request->coa_revenue_id;            
            $data->coa_inventory_id = $request->coa_inventory_id;            
            $data->coa_cogs_id = $request->coa_cogs_id;  

            $data->coa_revenue = $request->coa_revenue;
            $data->coa_cogs = $request->coa_cogs;
            $data->coa_inventory = $request->coa_inventory;
            
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data produk account']);
            }
            return response()->json(['success' => true, 'message' => 'Product account berhasil di update.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate produk account :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request,$id)
    {
        try {
            $data = ProdukAkun::where('produk_akun_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Produk akun tidak ditemukan / sudah tidak aktif.']);
            }

            $success = ProdukAkun::where('produk_akun_id', $id)
                ->where('client_id', $this->clientId)
                ->where('is_aktif', 1)
                ->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus product account']);
            }
            return response()->json(['success' => true, 'message' => 'product account berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data product account :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getProdukAkunId() 
    {
        try {
            $id = $this->clientId.'-PAC0001';
            $maxId = ProdukAkun::withTrashed()->where('produk_akun_id','LIKE',$this->clientId.'-PAC%')->max('produk_akun_id');

            if (!$maxId) { $id = $this->clientId.'-PAC0001'; }
            else {
                $maxId = str_replace($this->clientId.'-PAC','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-PAC000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-PAC00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-PAC0'.$count; } 
                else { $id = $this->clientId.'-PAC'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'PAC'.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }
}
