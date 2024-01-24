<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

use Modules\ManajemenUser\Entities\Client;
use Modules\Persediaan\Entities\Distribusi;
use Modules\Persediaan\Entities\KartuStock;
use Modules\Pengadaan\Entities\PurchaseOrder;
use Modules\Pengadaan\Entities\PurchaseRequest;

class CetakanPersediaanController extends Controller
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

    public function dataDistribusi(Request $request, $distId)
    {
        try {
            $data = Distribusi::select('tb_distribusi.*', 'tb_produk.*', 'tb_distribusi_detail.*')
            ->leftJoin('tb_distribusi_detail', 'tb_distribusi.distribusi_id', '=', 'tb_distribusi_detail.distribusi_id')
            ->leftJoin('tb_produk', 'tb_distribusi_detail.produk_id', '=', 'tb_produk.produk_id')
            ->where('tb_distribusi.client_id',$this->clientId)->where('tb_distribusi.is_aktif',1)->where('tb_distribusi.distribusi_id', $distId)->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataKartuStock(Request $request, $produk_id)
    {
        try{
            $data = KartuStock::where('client_id',$this->clientId)->where('produk_id', $produk_id)->orderBy('tgl_transaksi', 'asc')->get();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menampilkan data. Data tidak ditemukan.']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function dataPurchaseOrder(Request $request, $trx_id)
    {
        try {
            $data = PurchaseOrder::select('tb_pengadaan.*', 'tb_pengadaan_detail.*')
            ->leftJoin('tb_pengadaan_detail', 'tb_pengadaan.trx_id', '=', 'tb_pengadaan_detail.trx_id')
            ->where('tb_pengadaan.client_id',$this->clientId)
            ->where('tb_pengadaan.is_aktif',1)
            ->where('tb_pengadaan.pengadaan_id', $trx_id)
            ->where('tb_pengadaan.trx_id', $trx_id)
            ->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataPurchaseReceive(Request $request, $trx_id)
    {
        try {
            $data = PurchaseReceive::select('tb_pengadaan.*', 'tb_pengadaan_detail.*')
            ->leftJoin('tb_pengadaan_detail', 'tb_pengadaan.trx_id', '=', 'tb_pengadaan_detail.trx_id')
            ->where('tb_pengadaan.client_id',$this->clientId)
            ->where('tb_pengadaan.is_aktif',1)
            ->where('tb_pengadaan.trx_id', $trx_id)
            ->where('tb_pengadaan.jns_transaksi', 'Purchase Receive')
            ->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataPurchaseRequest(Request $request, $prId)
    {
        try {
            $data = PurchaseRequest::select('tb_pr.*', 'tb_produk.*', 'tb_pr_detail.*')
            ->leftJoin('tb_pr_detail', 'tb_pr.pr_id', '=', 'tb_pr_detail.pr_id')
            ->leftJoin('tb_produk', 'tb_pr_detail.produk_id', '=', 'tb_produk.produk_id')
            ->where('tb_pr.client_id',$this->clientId)->where('tb_pr.is_aktif',1)->where('tb_pr.pr_id', $prId)->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
