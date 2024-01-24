<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;

use Modules\PraktekDokter\Entities\PraktekDokter;
use Modules\PraktekDokter\Entities\PencatatanKas;
use Modules\PraktekDokter\Entities\KasLampiran;

class KasController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request) {
        try {
            $per_page = 20;
            $keyword = '%%';
            $isAktif = 'true';
            $jnsTransaksi = '%%';
            $tglTransaksiMulai =date('Y-m-d').' 00:00:00';
            $tglTransaksiAkhir =date('Y-m-d').' 23:59:59';
            $metodePembayaran = '%%';

            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            $query = PencatatanKas::query()->where('client_id',$this->clientId);
            $queryTotal = PencatatanKas::query()->where('client_id',$this->clientId);
            
            
            if($request->has('jns_transaksi')) {
                $jnsTransaksi =$request->input('jns_transaksi'); 
                if($jnsTransaksi !== '' && $jnsTransaksi !== null ) {
                    $query = $query->where('jenis_transaksi','ILIKE',$jnsTransaksi);
                }
            }

            if($request->has('metode_pembayaran')) {
                $metodePembayaran =$request->input('metode_pembayaran'); 
                if($metodePembayaran !== '' && $metodePembayaran !== null ) {
                    $query = $query->where('metode_pembayaran','ILIKE',$metodePembayaran);
                }
            }

            if($request->has('start_date')) {
                $tglTransaksiMulai =$request->input('start_date').' 00:00:00';
                $tglTransaksiAkhir =$request->input('start_date').' 23:59:59';
                
            }
            if($request->has('end_date')) {
                $tglTransaksiAkhir =$request->input('end_date').' 23:59:59';
            }

            if($request->has('periode')) {
                $periode = $request->input('periode');
                if(strtoupper($periode) == 'BULAN-INI') {
                    $tglTransaksiMulai = date('Y-m').'-01 00:00:00';
                    $tglTransaksiAkhir = date('Y-m-d').' 23:59:59';
                }
                else if(strtoupper($periode) == 'HARI-INI') {
                    $tglTransaksiMulai = date('Y-m-d').' 00:00:00';
                    $tglTransaksiAkhir = date('Y-m-d').' 23:59:59';
                }
                else {
                    $tglTransaksiMulai = '1900-01-01 00:00:00';
                    $tglTransaksiAkhir = date('Y-m-d').' 23:59:59';
                }
            }

            $query = $query->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            $queryTotal = $queryTotal->whereBetween('tgl_transaksi',[$tglTransaksiMulai,$tglTransaksiAkhir]);
            

            if($request->has('keyword')) {
                $keyword ='%'.$request->input('keyword').'%'; 
                if($keyword !== '' && $keyword !== null ) {
                    $query = $query->where('deskripsi','ILIKE',$keyword);
                }
            }

            if($request->has('is_aktif')) {
                $isAktif ='%'.$request->input('is_aktif').'%'; 
            }
            
            $lists = $query->where('is_aktif','ILIKE',$isAktif)->orderBy('tgl_transaksi','DESC')->paginate($per_page);

            foreach($lists->items() as $item) {
                $item['nominal'] = $item['jml_terima'] - $item['jml_bayar']; 
            }

            $activeRows = $queryTotal->where('is_aktif','true')->select('jml_bayar','jml_terima')->get();
            $totalTerima = 0;
            $totalBayar = 0;
            
            foreach($activeRows as $row) {
                $totalBayar = $totalBayar + ($row['jml_bayar'] * 1);
                $totalTerima = $totalTerima + ($row['jml_terima'] * 1);
            }
            
            $total['keluar'] = $totalBayar;
            $total['terima'] = $totalTerima;
            
            $data['detail'] = $lists;
            $data['total'] = $total;

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }



    public function data(Request $request, $id) {
        try {
            $data = PencatatanKas::where('client_id',$this->clientId)
                ->where('is_aktif',true)->where('kas_id',$id)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }

            $data['nominal'] = $data['jml_terima'] - $data['jml_bayar']; 

            $data['lampiran'] = KasLampiran::where('client_id',$this->clientId)->where('is_aktif',true)->where('kas_id',$id)->get();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);    
    
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request) {
        try {
            $id = $this->getKasId();

            DB::connection('dbclient')->beginTransaction();

            $kas = new PencatatanKas();
            $kas->kas_id = $id;
            $kas->tgl_transaksi = $request->tgl_transaksi;
            $kas->jenis_transaksi = $request->jenis_transaksi;
            $kas->deskripsi = $request->deskripsi;
            $kas->metode_pembayaran = $request->metode_pembayaran;
            $kas->jml_bayar = $request->jml_bayar;
            $kas->jml_terima = $request->jml_terima;
            $kas->is_aktif = true;
            $kas->client_id = $this->clientId;
            $kas->created_by = Auth::user()->username;
            $kas->updated_by = Auth::user()->username;
            $success = $kas->save();

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam penyimpanan data.']);   
            }

            // if($request->has('bukti_bayar')) {
            //     $buktiBayar = $request->bukti_bayar;
            //     if($buktiBayar !== null && $buktiBayar !== '') {
            //         //store file attachment
            //         $attachment = new KasLampiran();
            //         $attachment->kas_lampiran_id = Uuid::uuid4()->getHex();
            //         $attachment->kas_id = $id;
            //         $attachment->deskripsi = 'lampiran kas ID:'.$id;
            //         $attachment->path_lampiran = $buktiBayar;
            //         $attachment->url_lampiran = $buktiBayar;
            //         $attachment->client_id = $this->clientId;
            //         $attachment->is_aktif = true;
            //         $attachment->created_by = Auth::user()->username;
            //         $attachment->updated_by = Auth::user()->username;
            //         $success = $attachment->save();
            //         if( !$success ){
            //             DB::connection('dbclient')->rollBack();
            //             return response()->json(['success' => false, 'message' => 'File gagal diupload']);
            //         }
            //     }
            // }
            DB::connection('dbclient')->commit();
            return response()->json(['success'=>true,'message'=>'OK','data'=>$kas]);   

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }    
    }

    public function getKasId() {
        try {
            $id = $this->clientId.date('ym').'-CASH00001';

            $maxId = PencatatanKas::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('kas_id', 'LIKE', $this->clientId.date('ym').'-CASH%')
                ->max('kas_id');

            if (!$maxId) { $id = $this->clientId.date('ym').'-CASH00001'; } 
            else {
                $maxId = str_replace($this->clientId.date('ym').'-CASH', '', $maxId);
                $count = $maxId + 1;
                
                if ($count < 10) { $id = $this->clientId.date('ym').'-CASH0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.date('ym').'-CASH000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.date('ym').'-CASH00' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.date('ym').'-CASH0' . $count; } 
                else { $id = $this->clientId.date('ym').'-CASH' . $count; }
            }
            return $id;
        }
        catch (\Exception $e) {
            throw $e;
        }

    }

    public function update(Request $request) {
        try {
            $id = $request->kas_id;
            $kas = PencatatanKas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kas_id',$id)->first();
            if(!$kas) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);   
            }

            $kas->tgl_transaksi = $request->tgl_transaksi;
            $kas->jenis_transaksi = $request->jenis_transaksi;
            $kas->deskripsi = $request->deskripsi;
            $kas->metode_pembayaran = $request->metode_pembayaran;
            $kas->jml_bayar = $request->jml_bayar;
            $kas->jml_terima = $request->jml_terima;
            $kas->updated_by = Auth::user()->username;
            $success = $kas->save();

            if(!$success) {
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam penyimpanan data.']);   
            }

            return response()->json(['success'=>true,'message'=>'Data berhasil diubah','data'=>$kas]); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }

    }

    public function remove(Request $request) {
        try {
            $id = $request->kas_id;

            $kas = PencatatanKas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kas_id',$id)->first();
            if(!$kas) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);   
            }

            $kas->is_aktif = 0;
            $kas->alasan_batal = $request->alasan_batal;
            $kas->updated_by = Auth::user()->username;
            $success = $kas->save();

            if(!$success) {
                return response()->json(['success'=>false,'message'=>'ada kesalahan dalam penyimpanan data.']);   
            }
            return response()->json(['success'=>true,'message'=>'Data berhasil dihapus']); 
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan. Error : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }

    }

    /**
     * logo client
     */
    public function buktiKas(Request $request)
    {
        try {
            $id = '';
            if($request->has('kas_id')) { $id = $request->kas_id; }
            
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            } 

            // if(!$request->has('kas_id')) { 
            //     return response()->json(['success' => false, 'message' => 'Data pencatatan kas tidak ditemukan.']);
            // }
            // $id = $request->kas_id;
            
            // $kas = PencatatanKas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kas_id',$id)->first();
            // if(!$kas) {
            //     return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);   
            // }
            
            // if (!$request->hasFile('image')) {
            //     return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            // }
            $validator = Validator::make($request->all(), ['image' => 'required|mimes:jpeg,png,jpg']);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Image hanya untuk format JPG, JPEG, dan PNG', 'error' => $validator->errors()]);
            }

            $path = Storage::disk('avatars')->putFile('avatars/kas', $request->file('image'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url; 

            if($id !== null && $id !== '') {
                //simpan di tb_kas_lampiran
                $attachment = new KasLampiran();
                $attachment->kas_lampiran_id = Uuid::uuid4()->getHex();
                $attachment->kas_id = $id;
                $attachment->deskripsi = 'lampiran kas ID:'.$id;
                $attachment->path_lampiran = $path;
                $attachment->url_lampiran = $path_url;
                $attachment->client_id = $this->clientId;
                $attachment->is_aktif = true;
                $attachment->created_by = Auth::user()->username;
                $attachment->updated_by = Auth::user()->username;
                $success = $attachment->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'File gagal diupload']);
                }
            }
            //$data = KasLampiran::where('is_aktif',true)->where('kas_id',$id)->get();
            return response()->json(['success'=>true, 'message'=>'file berhasil diupload' ,'data'=>$data]);
        }

        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menyimpan file.','error'=> $e->getMessage()]);
        }
    }

    public function removeBuktiKas(Request $request, $idLampiran) 
    {   
        try {
            $success = KasLampiran::where('client_id',$this->clientId)
                ->where('kas_lampiran_id',$idLampiran)
                ->where('is_aktif',true)
                ->update(['is_aktif'=>false,'updated_by'=>Auth::user()->username]);
            if( !$success ){
                return response()->json(['success' => false, 'message' => 'File gagal dihapus']);
            }    

            return response()->json(['success'=>true, 'message'=>'file berhasil dihapus']);
        }   
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menghapus file.','error'=> $e->getMessage()]);
        }
    }

}
