<?php

namespace Modules\Pengadaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use DB;

use Modules\Pengadaan\Entities\PurchaseOrder;
use Modules\Pengadaan\Entities\PurchaseOrderDetail;
use Modules\MasterData\Entities\Produk;

class PurchaseManagementsController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request, $stat = null)
    {
        try{
            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';
            $status = '%%';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = PurchaseOrder::count();
                }
            }

            if($stat) { $status = '%'.$stat.'%'; }

            $data = PurchaseOrder::select('tb_pengadaan.*', 'tb_depo.depo_nama', 'tb_vendor.vendor_nama', 'tb_unit.unit_nama')
                    ->leftJoin('tb_depo', 'tb_pengadaan.depo_id', '=', 'tb_depo.depo_id')
                    ->leftJoin('tb_vendor', 'tb_pengadaan.vendor_id', '=', 'tb_vendor.vendor_id')
                    ->leftJoin('tb_unit', 'tb_pengadaan.unit_id', '=', 'tb_unit.unit_id')
                    ->where('tb_pengadaan.client_id',$this->clientId)
                    ->where('tb_pengadaan.is_aktif','ILIKE',$active)
                    ->where('tb_pengadaan.status','ILIKE',$status)
                    ->where('tb_pengadaan.jns_transaksi','ILIKE','Purchase Order')
                    ->where(function($q) use ($keyword) {
                        $q->where('pengadaan_id','ILIKE',$keyword)
                        ->orWhere('tb_pengadaan.unit_id','ILIKE',$keyword);
                    })->orderBy('pengadaan_id', 'ASC')->paginate($rowNumber);

            foreach($data->items() as $item) {
                $penerimaan = PurchaseOrder::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('pengadaan_id',$item['pengadaan_id'])
                    ->orderBy('tgl_transaksi','ASC')
                    ->get();

                $item['history'] = $penerimaan;

                $details = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('pengadaan_id',$item['pengadaan_id'])
                    ->where('trx_id',$item['pengadaan_id'])
                    ->where('is_aktif',1)
                    ->get();
                
                foreach($details as $dt) {
                    $val = PurchaseOrderDetail::where('client_id',$this->clientId)
                        ->where('pengadaan_id',$item['pengadaan_id'])
                        ->where('detail_id',$dt['detail_id'])
                        ->where('is_aktif',1)
                        ->select(
                            DB::raw('sum(jml_por) as jml_por'),
                            DB::raw('sum(jml_porr) as jml_porr'),
                        )->groupBy('detail_id')->first();
                    
                    if($val) {
                        $dt['jml_por'] = $val->jml_por;
                        $dt['jml_porr'] = $val->jml_porr;
                    }
                }

                $item['detail'] = $details;                
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request,$trxId)
    {
        try{
            //$id = $request->pengadaan_id;
            $data = PurchaseOrder::where('trx_id',$trxId)->where('client_id',$this->clientId)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menampilkan data. Data tidak ditemukan.']);
            }
            $id = $data->pengadaan_id;
            $detail = PurchaseOrderDetail::where('pengadaan_id',$id)
                ->where('trx_id',$trxId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->get();

            foreach($detail as $dt) {
                $terima = PurchaseOrderDetail::where('client_id',$this->clientId)
                        ->where('pengadaan_id',$data['pengadaan_id'])
                        ->where('detail_id',$dt['detail_id'])
                        ->where('is_aktif',1)
                        ->select(
                            DB::raw('sum(jml_por) as jml_por'),
                            DB::raw('sum(jml_porr) as jml_porr'),
                        )->groupBy('detail_id')->first();

                if($terima) {
                    $dt['jml_por'] = $terima->jml_por;
                    $dt['jml_porr'] = $terima->jml_porr;

                    $selisih = $dt['jml_po'] + $dt['jml_porr'] - $dt['jml_por'];
                    $totalSelisih = $selisih * $dt['konversi'];
                    
                    $dt['jml_terima'] = $selisih;
                    $dt['total_terima'] = $totalSelisih;
                }

                /**
                 * maksimal pengembalian
                 */

                $kembali = PurchaseOrderDetail::where('client_id',$this->clientId)
                    ->where('pengadaan_id',$data['pengadaan_id'])
                    ->where('detail_id',$dt['detail_id'])
                    ->where('status','SELESAI')
                    ->where('is_aktif',1)
                    ->select(
                        DB::raw('sum(jml_por) as jml_por'),
                        DB::raw('sum(jml_porr) as jml_porr'),
                    )->groupBy('detail_id')->first();

                if($kembali) {
                    $dt['jml_por'] = $terima->jml_por;
                    $dt['jml_porr'] = $terima->jml_porr;

                    $sel = $dt['jml_por'] - $dt['jml_porr'];
                    $totalSel = $sel * $dt['konversi'];
                    
                    $dt['jml_kembali'] = $sel;
                    $dt['total_kembali'] = $totalSel;
                }

                $dt['items'] = PurchaseRequestDetail::where('produk_id',$dt['produk_id'])->where('pengadaan_id',$id)->where('is_aktif',1)->get();                
            }
            $data['detail_po'] = $detail; 
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }
}
