<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Modules\MasterData\Entities\Kelas;
use Modules\MasterData\Entities\KodeRL;
use Modules\MasterData\Entities\Bridging;

use DB;

class KelasController extends Controller
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
        try{
            $rowNumber = 10;
            $keyword = '%%';            
            $klsHarga = '%%';
            $klsKamar = '%%';

            if($request->has('per_page')){
                $rowNumber = $request->get('per_page');
                if($rowNumber == 'ALL') { $rowNumber = Kelas::where('client_id',$this->clientId)->count(); }
            }

            if($request->has('keyword')){
                $keyword = '%'.$request->get('keyword').'%';
            }

            if($request->has('is_kamar')){
                $klsKamar = $request->get('is_kamar');
            }

            if($request->has('is_harga')){
                $klsHarga = $request->get('is_harga');
            }
            
            $data = Kelas::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('is_kelas_harga','ILIKE',$klsHarga)
                ->where('is_kelas_kamar','ILIKE',$klsKamar)
                ->where(function($q) use ($keyword){
                    $q->where('kelas_nama','ILIKE',$keyword)
                    ->orWhere('inisial','ILIKE',$keyword);
                })->orderBy('kelas_nama','ASC')
                ->paginate($rowNumber);
            
            foreach($data->items() as $itm) {
                $itm['bridging'] = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('local_resource_id',$itm['kelas_id'])
                    ->get();
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function KelasBpjsLists(Request $request)
    {
        try{
            $keyword = '%%';            
            $klsHarga = '%%';
            $klsKamar = '%%';

            $rowNumber = Kelas::where('client_id',$this->clientId)->count();
            if($request->has('keyword')){
                $keyword = '%'.$request->get('keyword').'%';
            }
            if($request->has('is_kamar')){
                $klsKamar = $request->get('is_kamar');
            }
            if($request->has('is_harga')){
                $klsHarga = $request->get('is_harga');
            }
            
            $data = Kelas::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('is_kelas_harga','ILIKE',$klsHarga)
                ->where('is_kelas_kamar','ILIKE',$klsKamar)
                ->where(function($q) use ($keyword){
                    $q->where('kelas_nama','ILIKE',$keyword)
                    ->orWhere('inisial','ILIKE',$keyword);
                })->orderBy('kelas_nama','ASC')
                ->paginate($rowNumber);
            
            foreach($data->items() as $item) {
                $dtm = Bridging::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('local_resource_id',$item['kelas_id'])
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

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try{
            $data = Kelas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelas_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data tidak ditemukan','error'=>'data tidak ditemukan.']);
            }

            $data['bridging'] = Bridging::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('local_resource_id',$id)
                    ->get();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try{
            $id = $this->getKelasId();

            $req = Kelas::where('client_id', $this->clientId)->where('kelas_nama', 'ILIKE', $request->kelas_nama)->where('is_aktif', 1)->first();
            if($req){
                return response()->json(['success' => false,'message' => 'Kelas Pelayanan sudah ada.']);
            }

            $data = new Kelas();
            $data->kelas_id = $id;
            $data->kelas_nama = strtoupper($request->kelas_nama);
            $data->inisial = strtoupper($request->inisial);
            $data->is_kelas_harga = $request->is_kelas_harga;
            $data->is_kelas_kamar = $request->is_kelas_kamar;
            $data->rl_kode = $request->rl_kode;
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data.']);
            }
            return response()->json(['success' => true,'message' => 'data berhasil disimpan','data'=>$data]);       
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try{
            $id = $request->kelas_id;
            $data = Kelas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelas_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'data tidak ditemukan']);
            }
            
            $success = Kelas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelas_id', $id)->update([
                'kelas_nama' => strtoupper($request->kelas_nama),
                'inisial' => strtoupper($request->inisial),
                'rl_kode' => $request->rl_kode,
                'is_kelas_harga' => $request->is_kelas_harga,
                'is_kelas_kamar' => $request->is_kelas_kamar,
                'is_aktif' => $request->is_aktif,
                'updated_by'=>Auth::user()->username
            ]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam mengubah data']);
            }
            return response()->json(['success'=>true,'message'=>'Data berhasil di ubah.','data'=>$data]);   
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try{            
            $data = Kelas::where('client_id',$this->clientId)->where('is_aktif',1)->where('kelas_id', $id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data tidak ditemukan.']);
            }

            $success = Kelas::where('client_id',$this->clientId)
                ->where('is_aktif',1)->where('kelas_id', $id)
                ->update(['is_aktif'=>0,'updated_by'=>Auth::user()->username]);

            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menghapus data kelas.']);
            }
            return response()->json(['success' => true,'message' => 'data kelas berhasil dihapus.']); 
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function getKelasId() 
    {
        try {
            $id = $this->clientId.'-CLS001';
            $maxId = Kelas::withTrashed()->where('kelas_id','LIKE',$this->clientId.'-CLS%')->max('kelas_id');
            if (!$maxId) { $id = $this->clientId.'-CLS001'; }
            else {
                $maxId = str_replace($this->clientId.'-CLS','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-CLS00'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-CLS0'.$count; } 
                else { $id = $this->clientId.'-CLS'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $this->clientId.'-CLS'.date('Y.m.d').'-'.Uuid::uuid4()->getHex();
        }
    }

}
