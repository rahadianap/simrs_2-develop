<?php

namespace Modules\PraktekDokter\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use DB;

use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\Pasien;
use Modules\PraktekDokter\Entities\PraktekDokter;
use Modules\PraktekDokter\Entities\Resep;
use Modules\PraktekDokter\Entities\ResepDetail;
use Modules\ManajemenUser\Entities\Client;
use RegistrasiHelper;

class ResepController extends Controller
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
            $per_page = 10;
            $aktif = 'true';
            $keyword = '%%';
            
            $query = Resep::query();
            $query = $query->where('client_id',$this->clientId);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = TrxResep::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('trx_id','ILIKE',$keyword)
                    ->orWhere('jns_resep','ILIKE',$keyword);
                })->orderBy('tgl_transaksi','ASC')
                ->paginate($per_page);
            
                foreach($list as $dt) {
                    $detail = ResepDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('trx_id',$dt['trx_id'])
                        ->orderBy('group_racikan','ASC')
                        ->get();

                    $dt['detail'] = $detail;                        
                }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $regId) {
        try {
            $data = $this->getDataResep($regId);
            if(!$data['success']) {
                $rsp = $this->createNewResep($regId);
                if(!$rsp['success']) {
                    return response()->json(['success' => false, 'message' => $data['message']]);
                }
                $data =  $this->getDataResep($regId);
                return response()->json(['success' => true, 'message' => 'OK', 'data' => $data['data']]);
            }
            else {
                return response()->json(['success' => true, 'message' => 'OK', 'data' => $data['data']]);
            }
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function getDataResep($regId) {
        try {
            $data = Resep::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->first();
                
            $data['items'] = ResepDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$data->reg_id)->get();

            return ['success' => true, 'data' => $data];
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengambil data. Error: '.$e->getMessage()];
        }
    }

    public function createNewResep($regId){
        try {
            $reffTrxId = $regId;            
            $trxId = $regId;//RegistrasiHelper::instance()->ResepTransactionId($this->clientId);
    
            $praktek = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$praktek->dokter_id)->first();
            
            /**
             * create data transaksi
             */
            $trxResep                      = new Resep();
            $trxResep->reg_id              = $regId;
            $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
            $trxResep->trx_id              = $trxId;
            $trxResep->reff_trx_id         = $reffTrxId;
            
            $trxResep->status              = 'DRAFT';
            $trxResep->is_realisasi        = false;
            $trxResep->is_bayar            = false;
            $trxResep->is_aktif            = true;
            $trxResep->jns_transaksi       = 'RAWAT JALAN';
            $trxResep->no_resep            = '-';
            $trxResep->no_transaksi        = '-';
            
            $trxResep->client_id           = $this->clientId;
            $trxResep->created_by          = Auth::user()->username;
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->tgl_resep           = date('Y-m-d H:i:s');
        
            $trxResep->penjamin_id         = '';
            $trxResep->penjamin_nama       = '';            
            $trxResep->total               = 0;
            $trxResep->unit_id             = '';
            $trxResep->unit_nama           = '';
                
            $trxResep->depo_id             = '';
            $trxResep->depo_nama             = '';
            $trxResep->dokter_id           = $dokter->dokter_id;
            $trxResep->dokter_nama         = $dokter->dokter_nama;
            $trxResep->peresep             = $dokter->dokter_nama;
            
            $trxResep->pasien_id           = $praktek->pasien_id;
            $trxResep->no_rm               = $praktek->no_rm;
            $trxResep->nama_pasien         = $praktek->nama_pasien;
            $trxResep->jns_resep           = 'RESEP';
        
            $success = $trxResep->save();
            if(!$success) {
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            
            
            return ['success' => true, 'data' => $trxResep];
    
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage() ];
        }
    }

    public function save(Request $request) {
        try {
            // dd($request);
            $regId = $request->reg_id;
            $reffTrxId = $regId;            
            
            $praktek = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
            $trxId = RegistrasiHelper::instance()->ResepTransactionId($this->clientId);

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$praktek->dokter_id)->first();
            DB::connection('dbclient')->beginTransaction();
            /**
             * create data transaksi
             */
            $trxResep                      = new Resep();
            $trxResep->reg_id              = $regId;
            $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
            $trxResep->trx_id              = $trxId;
            $trxResep->reff_trx_id         = $reffTrxId;
            
            $trxResep->status              = 'DRAFT';
            $trxResep->is_realisasi        = false;
            $trxResep->is_bayar            = false;
            $trxResep->is_aktif            = true;
            $trxResep->jns_transaksi       = 'RAWAT JALAN';
            $trxResep->no_resep            = '-';
            $trxResep->no_transaksi        = '-';
            
            $trxResep->client_id           = $this->clientId;
            $trxResep->created_by          = Auth::user()->username;
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->tgl_resep           = date('Y-m-d H:i:s');
        
            $trxResep->penjamin_id         = $request->penjamin_id;
            $trxResep->penjamin_nama       = $request->penjamin_nama;            
            $trxResep->total               = $request->total;
            if($unit) {
                $trxResep->unit_id         = $unit->unit_id;
                $trxResep->unit_nama       = $unit->unit_nama;
                // $trxResep->unit_depo_id    = $request->unit_depo_id;
                // $trxResep->unit_depo_nama  = $unit->unit_depo_nama;
            }
            else {
                $trxResep->unit_id             = '';
                $trxResep->unit_nama           = '';
                // $trxResep->unit_depo_id        = '';
                // $trxResep->unit_depo_nama      = '';
            }
            $trxResep->depo_id             = '';
            $trxResep->depo_nama             = '';
            $trxResep->dokter_id           = $request->dokter_id;
            $trxResep->dokter_nama         = $request->dokter_nama;
            $trxResep->peresep             = $request->peresep;
            
            $trxResep->pasien_id           = $request->pasien_id;
            $trxResep->no_rm               = $request->no_rm;
            $trxResep->nama_pasien         = $request->nama_pasien;
            $trxResep->jns_resep           = 'RESEP';
            // if($request->is_racikan = false) {
                //$trxResep->jns_resep           = 'RESEP RAWAT JALAN';
                // $trxResep->group_racikan       = null;
            //}
            // $trxResep->jns_resep           = 'RESEP RACIKAN';
            // $trxResep->group_racikan       = $request->no_racikan;
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            

            foreach($request->items as $dt) {
                /*** insert ke table resep detail */
                $detailId = $this->clientId.'-RSP'.date('Ymd').Uuid::uuid4()->getHex();
                $rsp = ResepDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $rsp = new ResepDetail();
                    $rsp->detail_id     = $detailId;
                    $rsp->reg_id        = $regId;
                    $rsp->trx_id        = $trxId;
                    $rsp->reff_trx_id   = $reffTrxId;
                    // $rsp->sub_trx_id    = $reffTrxId;
                    $rsp->item_id       = $dt['item_id'];
                    $rsp->client_id     = $this->clientId;
                    $rsp->created_by    = Auth::user()->username;
                }

                if($unit) {
                    $rsp->unit_id       = $request->unit_id;
                    $rsp->unit_nama     = $unit->unit_nama;
                }
                else {
                    $rsp->unit_id       = '';
                    $rsp->unit_nama     = '';
                }
                
                $rsp->depo_id           = '';
                $rsp->depo_nama         = '';
                $rsp->dokter_id         = $request->dokter_id;
                $rsp->dokter_nama       = $request->peresep;
                $rsp->item_nama         = $dt['item_nama'];
                $rsp->item_tipe         = $dt['item_tipe'];
                $rsp->klasifikasi       = '';
                $rsp->jumlah            = $dt['jumlah'];
                // $rsp->jml_resep      = $dt['jumlah'];
                $rsp->jml_ambil         = 0;
                $rsp->signa             = $dt['signa'];
                $rsp->signa_deskripsi   = $dt['signa_deskripsi'];
                $rsp->satuan            = $dt['satuan'];
                
                // $rsp->satuan_komposisi  = $dt['satuan_komposisi'];  
                // $rsp->jml_komposisi     = $dt['jumlah_komposisi'];              
                
                if($request->item_tipe == 'HEADER_RACIKAN') {
                    $rsp->satuan_komposisi  = $dt['satuan_hasil'];
                    $rsp->jml_komposisi     = $dt['jumlah'];
                }
                
                $rsp->group_racikan     = $dt['group_racikan'];  
                $rsp->nilai             = $dt['nilai'];
                $rsp->is_racikan        = $dt['is_racikan'];
                $rsp->status            = 'DRAFT';
                $rsp->diskon_persen     = $dt['diskon_persen'];
                $rsp->diskon            = $dt['diskon'];
                $rsp->harga_diskon      = $dt['harga_diskon'];
                $rsp->subtotal          = $dt['subtotal'];
                $rsp->is_realisasi      = false;
                $rsp->is_aktif          = $dt['is_aktif'];
                $rsp->updated_by        = Auth::user()->username;
                
                $success = $rsp->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataResep($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request) {
        try {
            $trxResep = Resep::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->trx_id)->first();
            
            if(!$trxResep) { return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); }
            
            $regId = $trxResep->reg_id;
            $reffTrxId = $trxResep->reff_trx_id;            
            $trxId = $request->trx_id;
            
            $praktek = PraktekDokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->first();
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            
            DB::connection('dbclient')->beginTransaction();
            /**
             * update data transaksi
             */
        
            $trxResep->updated_by          = Auth::user()->username;            
            $trxResep->total               = $request->total;
            $trxResep->pasien_id           = $request->pasien_id;
            $trxResep->no_rm               = $pasien->no_rm;
            $trxResep->nama_pasien         = $pasien->nama_pasien;
            $trxResep->jns_resep           = 'RESEP RAWAT JALAN';
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi resep gagal disimpan'];
            }            

            foreach($request->items as $dt) {
                /*** insert ke table resep detail */
                $detailId = $dt['detail_id'];
                $rsp = ResepDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $rsp = new ResepDetail();
                    $rsp->detail_id     = $detailId;
                    $rsp->reg_id        = $regId;
                    $rsp->trx_id        = $trxId;
                    $rsp->reff_trx_id   = $reffTrxId;
                    $rsp->item_id       = $dt['item_id'];
                    $rsp->client_id     = $this->clientId;
                    $rsp->created_by    = Auth::user()->username;
                }

                $rsp->unit_id       = '';
                $rsp->unit_nama     = '';
                
                $rsp->dokter_id     = $dokter->dokter_id;
                $rsp->dokter_nama   = $dokter->dokter_nama;
                $rsp->item_nama     = $dt['item_nama'];

                $rsp->item_tipe     = 'MEDIS';//$dt['item_tipe'];
                
                $rsp->jenis_produk  = '';
                $rsp->satuan        = '';
                $rsp->signa         = '';//$dt['signa'];
                $rsp->signa_deskripsi = $dt['signa_deskripsi'];                
                
                $rsp->jumlah        = $dt['jumlah'];
                $rsp->jml_ambil     = 0;

                $rsp->nilai         = 0;//$dt['nilai'];
                $rsp->diskon_persen = 0;//$dt['diskon_persen'];
                $rsp->diskon        = 0;//$dt['diskon'];
                $rsp->harga_diskon  = 0;//$dt['harga_diskon'];
                $rsp->subtotal      = 0;//$dt['subtotal'];
                $rsp->is_realisasi  = false;
                $rsp->is_aktif      = $dt['is_aktif'];
                $rsp->updated_by    = Auth::user()->username;
                
                $success = $rsp->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.'];
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getDataResep($trxId);
            if(!$data['success']) {
                return response()->json(['success' => false, 'message' => $data['message']]);
            }
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $data['data']]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data', 'error' => $e->getMessage()]);
        }
    }

    public function cetakResep($regId){
        try {
            $central = Client::where('client_id',$this->clientId)->where('is_aktif',1)->first();

            $pasien = Resep::leftJoin('tb_pasien','tb_resep.no_rm','=','tb_pasien.no_rm')
                ->where('tb_resep.client_id',$this->clientId)
                ->where('tb_resep.reg_id',$regId)
                ->where('tb_resep.is_aktif',1)
                ->select('tb_resep.*','tb_pasien.tgl_lahir')
                ->first();

            $umur = Carbon::parse($pasien->tgl_lahir)->diffInYears(Carbon::now());

            $detail = ResepDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->get();

            $data['central']            = $central;
            $data['pasien']             = $pasien;
            $data['pasien']['umur']     = $umur;
            $data['detail']             = $detail;
            // return $data;
            return view('report/praktekDokter/cetakanResep',compact('data'));
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ada kesalahan dalam mengambil data. Error: '.$e->getMessage()];
        }
    }
}

