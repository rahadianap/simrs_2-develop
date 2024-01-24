<?php

namespace Modules\Pengadaan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\Produk;
use Modules\Pengadaan\Entities\PurchaseRequest;
use Modules\Pengadaan\Entities\PurchaseRequestDetail;

use Modules\MasterData\Entities\Depo;
use Modules\ManajemenUser\Entities\MemberDepo;

use DB;

class PurchaseRequestController extends Controller
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
                    $rowNumber = PurchaseRequest::count();
                }
            }

            if($stat !== null) {
                $status = '%'.$stat.'%';
            }

            $data = PurchaseRequest::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$active)
                    ->where('status','ILIKE',$status)
                    ->where(function($q) use ($keyword) {
                        $q->where('pr_id','ILIKE',$keyword)
                        ->orWhere('unit_id','ILIKE',$keyword)
                        ->orWhere('unit_nama','ILIKE',$keyword)
                        ->orWhere('depo_nama','ILIKE',$keyword);
                    })->orderBy('pr_id', 'ASC')->paginate($rowNumber);
            
            foreach($data->items() as $item) {
                $dt = PurchaseRequestDetail::leftJoin('tb_produk','tb_pr_detail.produk_id','=','tb_produk.produk_id')
                    ->where('tb_pr_detail.status','ILIKE',$status)
                    ->where('tb_pr_detail.client_id',$this->clientId)
                    ->where('pr_id',$item['pr_id'])
                    ->where('tb_pr_detail.is_aktif',1)
                    ->select('tb_pr_detail.*','tb_produk.harga_beli')
                    ->get();

                if($dt) { foreach($dt as $d) { $d['is_pilih'] = true; } }
                $item['detail_req'] = $dt;
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function detailLists(Request $request, $stat = null) {
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
            // if($request->has('per_page')) {
            //     $rowNumber = $request->input('per_page');
            //     if($rowNumber == 'ALL') { $rowNumber = PurchaseRequest::count(); }
            // }

            $rowNumber = PurchaseRequestDetail::count();

            if($stat) { $status = '%'.$stat.'%'; }

            $data = PurchaseRequestDetail::where('client_id',$this->clientId)
                ->select('produk_id','produk_nama','pr_satuan',DB::raw("SUM(jml_pr) as jumlah"))
                ->where('is_aktif','ILIKE',$active)
                ->where('status','ILIKE',$status)
                ->where(function($q) use ($keyword) {
                    $q->where('produk_id','ILIKE',$keyword)
                    ->orWhere('produk_nama','ILIKE',$keyword);
                })->groupBy(['produk_id','produk_nama','pr_satuan'])
                ->paginate($rowNumber);
            
            foreach($data->items() as $item) {
                $details = PurchaseRequestDetail::leftJoin('tb_produk','tb_pr_detail.produk_id','=','tb_produk.produk_id')
                    ->where('tb_pr_detail.client_id',$this->clientId)
                    ->where('tb_pr_detail.is_aktif','ILIKE',$active)
                    ->where('tb_pr_detail.status','ILIKE',$status)
                    ->where('tb_pr_detail.produk_id',$item['produk_id'])
                    ->select('tb_pr_detail.*','tb_produk.harga_beli')
                    ->get();
                
                foreach($details as $dt) {
                    $pr = PurchaseRequest::where('client_id',$this->clientId)
                        ->where('pr_id',$dt['pr_id'])->where('is_aktif',1)
                        ->first();
                
                    $dt['tgl_pr'] = $pr->tgl_pr;
                    $dt['tgl_kebutuhan'] = $pr->tgl_kebutuhan;
                    $dt['depo_id'] = $pr->depo_id;
                    $dt['depo_nama'] = $pr->depo_nama;
                    $dt['unit_id'] = $pr->unit_id;
                    $dt['unit_nama'] = $pr->unit_nama;
                    $dt['is_pilih'] = true;
                }

                $item['details'] = $details;
            }

            // $data = PurchaseRequestDetail::where('client_id',$this->clientId)
            //         ->where('is_aktif','ILIKE',$active)
            //         ->where('status','ILIKE',$status)
            //         ->where(function($q) use ($keyword) {
            //             $q->where('pr_id','ILIKE',$keyword)
            //             ->orWhere('pr_detail_id','ILIKE',$keyword)
            //             ->orWhere('produk_id','ILIKE',$keyword)
            //             ->orWhere('produk_nama','ILIKE',$keyword);
            //         })->orderBy('produk_nama', 'ASC')
            //         ->paginate($rowNumber);
            
            // foreach($data->items() as $item) {
            //     $pr = PurchaseRequest::where('client_id',$this->clientId)
            //         ->where('pr_id',$item['pr_id'])
            //         ->where('is_aktif',1)
            //         ->first();
                
            //     if($pr) {
            //         $item['tgl_pr'] = $pr->tgl_pr;
            //         $item['tgl_kebutuhan'] = $pr->tgl_kebutuhan;
            //         $item['depo_id'] = $pr->depo_id;
            //         $item['depo_nama'] = $pr->depo_nama;
            //         $item['unit_id'] = $pr->unit_id;
            //         $item['unit_nama'] = $pr->unit_nama;
            //     }
            // }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $pr_id)
    {
        try{            
            $data = PurchaseRequest::where('client_id',$this->clientId)->where('pr_id',$pr_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }

            $data['detail_req'] = PurchaseRequestDetail::where('client_id',$this->clientId)->where('pr_id',$pr_id)->where('is_aktif',1)->get();
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function isUserDepo($depoId){
        try {
            $data = MemberDepo::where('user_id',Auth::user()->user_id)
                ->where('depo_id',$depoId)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            if($data) { return true; }
            else { return false; }
        }
        catch(\Exception $e) {
            return false;
        }
    }

    public function isDepoAllowed($depoId){
        try {
            $data = Depo::where('depo_id',$depoId)
                ->where('is_purchase_req',1)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            if($data) { return true; }
            else { return false; }
        }
        catch(\Exception $e) {
            return false;
        }
    }

    
    public function create(Request $request)
    {
        try{  
            if(!$this->isUserDepo($request->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($request->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase request.']);
            }   

            $id = $this->getPRId();       
            
            DB::connection('dbclient')->beginTransaction();
            $data = new PurchaseRequest();
            $data->pr_id = $id;
            $data->tgl_pr = date('Y-m-d H:i:s');
            $data->tgl_kebutuhan = $request->tgl_kebutuhan;
            $data->unit_id = $request->unit_id;
            $data->unit_nama = $request->unit_nama;
            $data->depo_id = $request->depo_id;
            $data->depo_nama = $request->depo_nama;
            $data->catatan = strtoupper($request->catatan);
            $data->status = 'PERMINTAAN';
            $data->is_aktif = 1;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $data->client_id = $this->clientId;
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }

            foreach($request->detail_req as $value) {
                $detailPR = new PurchaseRequestDetail();
                $detailPR->pr_detail_id = $id.Uuid::uuid4()->getHex();
                $detailPR->pr_id = $id;
                $detailPR->jenis_produk = $value['jenis_produk'];
                $detailPR->produk_id = $value['produk_id'];
                $detailPR->produk_nama = $value['produk_nama'];
                $detailPR->satuan = $value['satuan'];
                $detailPR->pr_satuan = $value['satuan'];
                $detailPR->konversi = "1";
                $detailPR->jml_pr = $value['jml_satuan'];
                $detailPR->jml_satuan = $value['jml_satuan'];
                $detailPR->status = 'PERMINTAAN';
                $detailPR->is_aktif = 1;
                $detailPR->created_by = Auth::user()->username;
                $detailPR->updated_by = Auth::user()->username;
                $detailPR->client_id = $this->clientId;
                $success = $detailPR->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
                }    
            }
            
            DB::connection('dbclient')->commit();
            
            $data = PurchaseRequest::where('client_id',$this->clientId)->where('pr_id',$id)->first();
            $data['detail_req'] = PurchaseRequestDetail::where('client_id',$this->clientId)->where('pr_id',$id)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);    
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try{  
            $id = $request->pr_id;
            $data = PurchaseRequest::where('client_id',$this->clientId)->where('pr_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data. Data tidak ditemukan.']);
            }
            if($data->status !== 'PERMINTAAN') {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data. Status Data permintaan sudah berubah.']);
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase request.']);
            }   

            DB::connection('dbclient')->beginTransaction();   

            $data->tgl_kebutuhan = $request->tgl_kebutuhan;
            $data->catatan = strtoupper($request->catatan);
            $data->status = 'PERMINTAAN';
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menguban data.']);
            }
            
            foreach($request->detail_req as $value) {
                $detailPR = PurchaseRequestDetail::where('client_id',$this->clientId)->where('pr_id',$id)->where('produk_id',$value['produk_id'])->where('is_aktif',1)->get();
                if(!$detailPR) {
                    $detailPR = new PurchaseRequestDetail();
                    $detailPR->pr_detail_id = $id.Uuid::uuid4()->getHex();
                    $detailPR->pr_id = $id;
                    $detailPR->jenis_produk = $value['jenis_produk'];
                    $detailPR->produk_id = $value['produk_id'];
                    $detailPR->produk_nama = $value['produk_nama'];
                    $detailPR->satuan = $value['satuan'];
                    $detailPR->pr_satuan = $value['satuan'];
                    $detailPR->konversi = "1";
                    $detailPR->jml_pr = $value['jml_satuan'];
                    $detailPR->jml_satuan = $value['jml_satuan'];
                    $detailPR->status = 'PERMINTAAN';
                    $detailPR->is_aktif = 1;
                    $detailPR->created_by = Auth::user()->username;
                    $detailPR->updated_by = Auth::user()->username;
                    $detailPR->client_id = $this->clientId;
                    $success = $detailPR->save();
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
                    }    
                }
                else {
                    $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                        ->where('pr_id',$id)
                        ->where('produk_id',$value['produk_id'])
                        ->where('is_aktif',1)->update([
                            'jml_pr' => $value['jml_satuan'],
                            'jml_satuan' => $value['jml_satuan'],
                            'is_aktif' => $value['is_aktif'],
                            'updated_by' => Auth::user()->username
                        ]);
                    
                    if(!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
                    }    
                }
            }
            
            DB::connection('dbclient')->commit();
            
            $data = PurchaseRequest::where('client_id',$this->clientId)->where('pr_id',$id)->first();
            $data['detail_req'] = PurchaseRequestDetail::where('client_id',$this->clientId)->where('pr_id',$id)->where('is_aktif',1)->get();
            
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function approvePR(Request $request, $id)
    {
        try{
            $data = PurchaseRequest::where('client_id',$this->clientId)
                ->where('status','PERMINTAAN')
                ->where('is_aktif',1)
                ->where('pr_id', $id)->first();

            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan atau telah berubah status.']);
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase request.']);
            }

            DB::connection('dbclient')->beginTransaction();
            $data->status = 'PENGAJUAN';
            $data->updated_by = Auth::user()->username;
            $data->approver_id = Auth::user()->user_id;
            $data->approver = Auth::user()->username;
            $data->approved_at = Carbon::now()->format('Y-m-d H:i:s');
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam approve permintaan.']);
            }

            $detail = PurchaseRequestDetail::where('client_id',$this->clientId)->where('is_aktif',1)->where('pr_id', $data->pr_id)->first();
            if($detail) {
                $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('pr_id', $data->pr_id)
                    ->update([
                        'status' => 'PENGAJUAN',
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel detail permintaan.']);
                }
            }

            DB::connection('dbclient')->commit();            
            return response()->json(['success' => true,'message' => 'Berhasil approve permintaan']);              
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam approve permintaan.','error'=> $e->getMessage()]);
        }
    }

    public function cancelPR(Request $request, $id)
    {
        try {
            $data = PurchaseRequest::where('client_id',$this->clientId)->where('is_aktif',1)->where('pr_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan.']);
            }
            if($data->status !== 'PENGAJUAN' && $data->status !== 'PERMINTAAN') {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam pengambilan data. Data tidak ditemukan atau telah berubah status.']);
            }

            if(!$this->isUserDepo($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak dibawah otorisasi anda.']);
            }
            
            if(!$this->isDepoAllowed($data->depo_id)) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data. Depo tidak diijinkan melakukan purchase request.']);
            }


            DB::connection('dbclient')->beginTransaction();
            $data->status = 'BATAL';
            $data->is_aktif = false;
            $data->updated_by = Auth::user()->username;
            $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $success = $data->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel permintaan.']);
            }

            $detail = PurchaseRequestDetail::where('client_id',$this->clientId)->where('is_aktif',1)->where('pr_id', $data->pr_id)->first();
            if($detail) {
                $success = PurchaseRequestDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('pr_id', $data->pr_id)
                    ->update([
                        'status' => 'BATAL',
                        'is_aktif' => false,
                        'updated_by' => Auth::user()->username,
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam cancel detail permintaan.']);
                }
            }
            
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true,'message' => 'Berhasil menghapus permintaan']);   
           
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menghapus permintaan.','error'=> $e->getMessage()]);
        }
    }

    public function getPRId() 
    {
        try {
            $id = $this->clientId.'.'.date('Ym').'-PR0001';
            $maxId = PurchaseRequest::withTrashed()->where('client_id', $this->clientId)->where('pr_id','LIKE',$this->clientId.'.'.date('Ym').'-PR%')->max('pr_id');
            if (!$maxId) { $id = $this->clientId.'.'.date('Ym').'-PR0001'; }
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'-PR','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'-PR000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'-PR00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'-PR0'.$count; } 
                else { $id = $this->clientId.'.'.date('Ym').'-PR'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'.'.date('Ym').'-PR' . Uuid::uuid4()->getHex();
        }
    }

}
