<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Pengadaan\Entities\PurchaseReceive;

class ReportPenerimaanBarangController extends Controller
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

    public function dataMedis(Request $request)
    {
        try {
            $data = PurchaseReceive::leftJoin('tb_pengadaan_detail', 'tb_pengadaan.trx_id', '=', 'tb_pengadaan_detail.trx_id')
            ->select('tb_pengadaan.tgl_transaksi', 
            'tb_pengadaan.no_invoice', 
            'tb_pengadaan.vendor_nama', 
            'tb_pengadaan_detail.produk_nama', 
            'tb_pengadaan_detail.jenis_produk',
            'tb_pengadaan_detail.jml_por',
            'tb_pengadaan_detail.satuan_beli',
            'tb_pengadaan_detail.harga',
            'tb_pengadaan_detail.nilai_diskon',
            'tb_pengadaan_detail.nilai_pajak',
            'tb_pengadaan_detail.subtotal'
            )
            ->where('tb_pengadaan.client_id', $this->clientId)
            ->where('tb_pengadaan.jns_transaksi', 'Purchase Receive')
            ->where('tb_pengadaan_detail.jenis_produk', 'MEDIS')
            ->where('tb_pengadaan.is_aktif', 1)
            ->whereBetween('tb_pengadaan.tgl_transaksi',[$request->get('start_date'),$request->get('end_date')])
            // ->groupBy('tb_pengadaan.item_nama')
            ->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataUmum(Request $request)
    {
        try {
            $data = PurchaseReceive::leftJoin('tb_pengadaan_detail', 'tb_pengadaan.trx_id', '=', 'tb_pengadaan_detail.trx_id')
            ->select('tb_pengadaan.tgl_transaksi', 
            'tb_pengadaan.no_invoice', 
            'tb_pengadaan.vendor_nama', 
            'tb_pengadaan_detail.produk_nama', 
            'tb_pengadaan_detail.jenis_produk',
            'tb_pengadaan_detail.jml_por',
            'tb_pengadaan_detail.satuan_beli',
            'tb_pengadaan_detail.harga',
            'tb_pengadaan_detail.nilai_diskon',
            'tb_pengadaan_detail.nilai_pajak',
            'tb_pengadaan_detail.subtotal'
            )
            ->where('tb_pengadaan.client_id', $this->clientId)
            ->where('tb_pengadaan.jns_transaksi', 'Purchase Receive')
            ->where('tb_pengadaan_detail.jenis_produk', 'UMUM')
            ->where('tb_pengadaan.is_aktif', 1)
            ->whereBetween('tb_pengadaan.tgl_transaksi',[$request->get('start_date'),$request->get('end_date')])
            // ->groupBy('tb_pengadaan.item_nama')
            ->get();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}