<?php

namespace Modules\Farmasi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;

use Modules\Farmasi\Entities\Farmasi;
use Modules\Farmasi\Entities\FarmasiDetail;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\UnitTindakan;
use Modules\MasterData\Entities\Depo;
use Modules\MasterData\Entities\DepoUnit;
use Modules\MasterData\Entities\DepoProduk;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\Produk;

use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use RegistrasiHelper;

class FarmasiController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(request $request)
    {
        try {
            $keyword = '%%';
            $status = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            if($request->has('status')) {
                $status = '%'.$request->input('status').'%';
            }

            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }
            
            $rowNumber = 20;
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = Farmasi::count();
                }
            }
            $kolom = "";
            if($request->has('kolom')) {
                $kolom = "rad." . $request->input('kolom');
            }
            $unitID = "%%";
            if ($request->has('unit_id')) {
                $unitID = '%'.$request->input('unit_id').'%';
            }
            $tgl_pencarian_awal = Carbon\Carbon::now();
            if($request->has('tgl_pencarian_awal')) {
                $tgl_pencarian_awal = $request->input('tgl_pencarian_awal');
            }
            $tgl_pencarian_akhir = Carbon\Carbon::now();
            if($request->has('tgl_pencarian_akhir')) {
                $tgl_pencarian_akhir = $request->input('tgl_pencarian_akhir');
            }
            
            $lists = Farmasi::where([
                ['client_id',$this->clientId],
                ['is_aktif','ILIKE',$aktif],
                ['status','ILIKE',$status],
                ['unit_id','ILIKE',$unitID],
            ])->where(function($q) use ($keyword) {
                $q->where('unit_nama','ILIKE',$keyword)
                ->orWhere('trx_id','ILIKE',$keyword)
                ->orWhere('nama_pasien','ILIKE',$keyword)
                ->orWhere('depo_nama','ILIKE',$keyword)
                ->orWhere('peresep','ILIKE',$keyword);
            })->orderBy('created_at','ASC')->paginate($rowNumber);
            
            foreach($lists as $dt) {
                $dt['transaksi'] = Transaksi::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt->trx_id],
                    ['reg_id',$dt->reg_id],
                ])->first();
                
                $dt['detail'] = TransaksiDetail::where([
                    ['is_aktif',1],
                    ['client_id',$this->clientId],
                    ['trx_id',$dt['trx_id']],
                    ['reg_id',$dt['reg_id']],
                ])->get(); 
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data farmasi', 'error' => $e->getMessage()]);
        }
    }

    public function listDepos(Request $request) {
        try {
            $data = Depo::where('client_id', $this->clientId)
                ->where('is_sell',true)->where('is_aktif',true)
                ->get();

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function pasienReseplists(Request $request, $trxId) {
        try {
            $data = Farmasi::where('client_id', $this->clientId)
                ->where('reff_trx_id',$trxId)->where('is_aktif',true)
                ->orderBy('tgl_transaksi','DESC')
                ->get();

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengambil data', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $trxId)
    {
        try {
            $data = Farmasi::where('client_id', $this->clientId)
                ->where('trx_id', $trxId)
                ->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data transaksi farmasi tidak ditemukan.']);
            }

            $items = FarmasiDetail::where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$trxId)->get();

            foreach($items as $itm) {
                if($itm['jml_ambil'] <= 0) { $itm['jml_ambil'] = $itm['jumlah']; }
                //$itm['jml_sisa'] = $itm['jumlah'] - $itm['jml_ambil'];
            }

            $data['items'] = $items;

            $data['transaksi'] = Transaksi::where([
                ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                ['reg_id',$data->reg_id],
            ])->first();
            
            $details = TransaksiDetail::where([
                ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                ['reg_id',$data->reg_id],
            ])->get(); 
            
            foreach($details as $dt) {
                $dt['komponen'] = TransaksiDetailKomp::where([
                    ['is_aktif',1],['client_id',$this->clientId],['trx_id',$data->trx_id],
                    ['reg_id',$data->reg_id],['detail_id',$dt['detail_id']],
                ])->get();
            }
            $data['detail'] = $details;
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data resep', 'error' => $e->getMessage()]);
        }
    }

    // bisa beli resep sebagian, jika ingin membeli sisanya bisa membuat transaksi baru dan memakai reff_trx_id dari resep sebelumnya 
    public function create(Request $request)
    {
        try {
            $regId = $request->reg_id;

            $reffTrxId = $request->reff_trx_id;            
            $trxId = RegistrasiHelper::instance()->ResepTransactionId($this->clientId);

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();
            $depo = Depo::where('client_id',$this->clientId)->where('is_aktif',1)->where('depo_id',$request->depo_id)->first();
            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Data depo tidak ditemukan.']);
            }

            DB::connection('dbclient')->beginTransaction();
            /**
             * create data transaksi
             */
            $trxResep                      = new Farmasi();
            $trxResep->reg_id              = $regId;
            $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
            $trxResep->trx_id              = $trxId;
            $trxResep->reff_trx_id         = $reffTrxId;
            $trxResep->status              = 'FARMASI';
            $trxResep->is_realisasi        = false;
            $trxResep->is_bayar            = false;
            $trxResep->is_aktif            = true;
            $trxResep->jns_transaksi       = $request->jns_transaksi;
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
            }
            else {
                $trxResep->unit_id             = '';
                $trxResep->unit_nama           = '';
            }
            $trxResep->depo_id             = $request->depo_id;
            $trxResep->depo_nama           = $depo->depo_nama;
            $trxResep->dokter_id           = $request->dokter_id;
            $trxResep->dokter_nama         = $request->dokter_nama;
            $trxResep->peresep             = $request->peresep;
            
            $trxResep->pasien_id           = $request->pasien_id;
            $trxResep->no_rm               = $request->no_rm;
            $trxResep->nama_pasien         = $request->nama_pasien;
            $trxResep->jns_resep           = 'RESEP';
            
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }            

            foreach($request->items as $dt) {
                /*** insert ke table resep detail */
                $detailId = $this->clientId.'-RSP'.date('Ymd').Uuid::uuid4()->getHex();
                $rsp = FarmasiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $rsp = new FarmasiDetail();
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
                $rsp->jml_ambil         = $dt['jumlah'];
                $rsp->signa             = $dt['signa'];
                $rsp->signa_deskripsi   = $dt['signa_deskripsi'];
                $rsp->satuan            = $dt['satuan']; 
                
                if($request->item_tipe == 'HEADER_RACIKAN') {
                    $rsp->satuan_komposisi  = $dt['satuan_hasil'];
                    $rsp->jml_komposisi     = $dt['jumlah'];
                }
                
                $rsp->group_racikan     = $dt['group_racikan'];  
                $rsp->nilai             = $dt['nilai'];
                $rsp->is_racikan        = $dt['is_racikan'];
                $rsp->status            = 'FARMASI';
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
                    response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan','data' => $trxResep]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $regId = $request->reg_id;
            $trxId = $request->trx_id;
            
            // check apakah transaksi yang dipilih masih aktif dan ada
            $transaksi = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->where('pasien_id',$request->pasien_id)
                ->where('status','DAFTAR')->first();
            
            // check apakah depo yang dipilih masih aktif dan ada
            $depo = Depo::where('client_id', $this->clientId)->where('is_aktif',1)
                ->where('depo_id', $request->depo_id)->first();

            if(!$depo) {
                return response()->json(['success' => false, 'message' => 'Data depo tidak ditemukan.']);
            }

            // check apakah dokter yang dipilih masih aktif dan ada
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('dokter_id',$request->dokter_id)->first();
            if(!$dokter){
                return response()->json(['success' => false, 'message' => 'Data dokter tidak ditemukan.']);
            }
            
            // check apakah pasien exist
            $pasien = Pasien::where('pasien_id',$request->pasien_id)
                ->where('client_id',$this->clientId)->where('is_aktif',1)->first();
            if(!$pasien) {
                return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.']);
            }

            // check apakah resep yang dipilih masih aktif dan ada
            $resep = Farmasi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->first();
            if(!$resep) {
                return response()->json(['success' => false, 'message' => 'Data resep tidak ditemukan / sudah berubah status.']);
            }

            // check apakah resep yang dipilih masih aktif dan ada
            $detail = FarmasiDetail::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('reg_id',$regId)->where('trx_id',$trxId)->get();
                
            if(!$detail) {
                return response()->json(['success' => false, 'message' => 'Data resep tidak ditemukan / sudah berubah status.']);
            }
            
            $noAntrian = RegistrasiHelper::instance()->NoAntrian($this->clientId, 'FRM', $request->tgl_transaksi, '');
            $usia = RegistrasiHelper::instance()->HitungUsia($pasien->tgl_lahir);        

            // // update tb_resep
            DB::connection('dbclient')->beginTransaction();
            $resep->no_resep        = $request->no_resep;
            $resep->unit_id         = '';
            $resep->unit_nama       = '';
            $resep->depo_id         = $request->depo_id;
            $resep->depo_nama       = $depo->depo_nama;
            $resep->peresep         = $request->peresep;
            $resep->no_antrian      = $noAntrian['id'];
            $resep->status          = 'FARMASI';
            $resep->updated_by      = Auth::user()->username;
            $success                = $resep->save ();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data farmasi resep gagal diubah.']);
            }

            // update tb_resep_detail
            foreach($request->items as $dtl) {
                //check item produk
                $produk = Produk::where('produk_id',$dtl['item_id'])->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                    
                if($dtl['item_tipe'] !== 'HEADER_RACIKAN') {
                    if(!$produk) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Item produk tidak ditemukan.']);
                    }
                }

                //check apakah item ada yang sama
                $detail = FarmasiDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$regId)->where('trx_id',$trxId)->where('is_aktif',1)
                    ->where('item_id',$dtl['item_id'])->first();

                if(!$detail) {
                    $detailId = $this->getResepDetailId();
                    $detail = new FarmasiDetail();
                    $detail->detail_id = $detailId;
                    $detail->reg_id = $regId;
                    $detail->trx_id = $trxId;
                    $detail->item_id = $dtl['item_id'];
                    $detail->is_realisasi = false;                    
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                }
                else { 
                    $detailId = $detail->detail_id; 
                }
                $detail->item_nama          = $dtl['item_nama'];
                $detail->item_tipe          = $dtl['item_tipe'];
                $detail->depo_id            = $depo->depo_id;
                $detail->depo_nama          = $depo->depo_nama;
                $detail->dokter_id          = $dokter->dokter_id;
                $detail->dokter_nama        = $dokter->dokter_nama;
                $detail->satuan             = $dtl['satuan'];
                $detail->jumlah             = $dtl['jumlah'];
                $detail->jml_ambil          = $dtl['jml_ambil'];
                $detail->is_bhp             = false;
                // if($dtl['is_racikan']) {
                //     $detail->is_racikan         = $dtl['is_racikan'];
                //     $detail->racikan_id         = $dtl['racikan_id'];
                //     $detail->racikan_nama       = $dtl['racikan_nama'];
                // }
                // else {
                //     $detail->is_racikan         = false;
                // }
                $detail->unit_id            = '';
                $detail->unit_nama          = '';
                $detail->signa              = $dtl['signa'];
                $detail->signa_deskripsi    = $dtl['signa_deskripsi'];
                $detail->nilai              = $dtl['nilai'];
                $detail->diskon_persen      = $dtl['diskon_persen'];
                $detail->diskon             = $dtl['diskon'];
                $detail->harga_diskon       = $dtl['harga_diskon'];
                $detail->subtotal           = $dtl['subtotal'];
                $detail->klasifikasi        = '';
                $detail->status             = $resep->status;
                $detail->updated_by         = Auth::user()->username;
                $detail->is_aktif           = $dtl['is_aktif'];                    
                $success = $detail->save();
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Data item resep farmasi gagal disimpan.']);
                }
            }
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data farmasi berhasil diubah', 'data' => $detail]);

        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan modifikasi data farmasi. Error : ' . $e->getMessage()]);
        }
    }

    public function stockItemKeluar() {
        try {
            //update status resep
            //input transaksi detail
                /**
                 * insert ke detail Transaksi   
                 **/ 
                $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxBhpId)->first();

                if(!$trxDetail) {
                    $trxDetail = new TransaksiDetail();
                    $trxDetail->detail_id           = $detailId;
                    $trxDetail->reg_id              = $regId;
                    $trxDetail->trx_id              = $trxBhpId;
                    $trxDetail->jns_transaksi       = 'BHP';
                    $trxDetail->client_id           = $this->clientId;
                    $trxDetail->created_by          = Auth::user()->username;
                    $trxDetail->item_id             = $dt['item_id'];
                }
                $trxDetail->kelas_harga_id      = $request->kelas_harga_id;
                $trxDetail->buku_harga_id       = $penjamin->buku_harga_id;
                $trxDetail->penjamin_id         = $penjamin->penjamin_id;
                $trxDetail->item_nama           = $dt['item_nama'];
                $trxDetail->jumlah              = $dt['jumlah'];
                $trxDetail->satuan              = $dt['satuan'];
                
                $trxDetail->nilai               = $dt['nilai'];
                $trxDetail->diskon_persen       = $dt['diskon_persen'];
                $trxDetail->diskon              = $dt['diskon'];
                $trxDetail->harga_diskon        = $dt['harga_diskon'];
                $trxDetail->subtotal            = $dt['subtotal'];
                $trxDetail->dokter_id           = $request->dokter_id;
                $trxDetail->dokter_pengirim_id  = '';
                $trxDetail->is_hitung_adm       = false;
                $trxDetail->is_aktif            = $dt['is_aktif'];
                $trxDetail->updated_by          = Auth::user()->username;

                $success = $trxDetail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP.'];
                    //return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP']);
                }

                //kurangi stock persediaan

            //buat transaksi

            //input detail


        }
        catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat melakukan modifikasi data farmasi. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $trxId)
    {
        try {
            $data = TrxRad::where('trx_id',$trxId)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi radiologi tidak ditemukan.']);
            }
            if($data->status !== 'DAFTAR' || $data->is_realisasi == true) {
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi sudah berubah status.']);
            }

            /**check data transaksi */
            $transaksi = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)->first();
                
            if(!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Data transaksi radiologi sudah berubah status.']);
            }

            $detail = TransaksiDetail::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();
                
            $komponen = TransaksiDetailKomp::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->first();
            
            DB::connection('dbclient')->beginTransaction();

            $success = TrxRad::where('trx_id',$data->trx_id)
                ->where('reg_id',$data->reg_id)
                ->where('pasien_id',$data->pasien_id)
                ->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);

            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data radiologi gagal dihapus.']);
            }

            /**update transaksi */
            $success = Transaksi::where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)
                ->where('trx_id',$data->trx_id)
                ->where('is_realisasi',false)
                ->where('is_bayar',false)
                ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username, 'status' =>'BATAL']);
            
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'data transaksi radiologi gagal dihapus.']);
            }

            /**update transaksi detail */
            if($detail) {
                $success = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data detail transaksi radiologi gagal dihapus.']);
                }
            }
            
            /**update transaksi detail komp */
            if($komponen) {
                $success = TransaksiDetailKomp::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('reg_id',$data->reg_id)
                    ->where('trx_id',$data->trx_id)
                    ->where('is_realisasi',false)
                    ->where('is_bayar',false)
                    ->update(['is_aktif'=>false,'is_realisasi'=>false,'updated_by'=>Auth::user()->username]);
                
                if(!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'data detail komponen transaksi radiologi gagal dihapus.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data radiologi berhasil dihapus']);
        } catch (\Exception $e) {
            DB::connection('dbclient')->rollBack();
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menghapus data farmasi. Error : ' . $e->getMessage()]);
        }
    }

    public function getResepId()
    {
        try {
            $id = $this->clientId.'-FRM000001';
            $maxId = Farmasi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('reg_id', 'LIKE', $this->clientId.'-FRM%')
                ->max('reg_id');

            if (!$maxId) { $id = $this->clientId.'-FRM000001'; } 
            else {
                $maxId = str_replace($this->clientId.'-FRM', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-FRM00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-FRM0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-FRM000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-FRM00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-FRM0' . $count; } 
                else { $id = $this->clientId.'-FRM' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getResepDetailId()
    {
        try {
            $id = $this->clientId.'-'.'FRD'.'-000001';
            $maxId = FarmasiDetail::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.'FRD'.'-%')
                ->max('detail_id');

            if (!$maxId) { $id = $this->clientId.'-'.'FRD'.'-000001'; } 
            else {
                $maxId = str_replace($this->clientId.'-'.'FRD'.'-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'-'.'FRD'.'-00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'FRD'.'-0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'FRD'.'-000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'FRD'.'-00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.'FRD'.'-0' . $count; } 
                else { $id = $this->clientId.'-'.'FRD'.'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTrxDetailId()
    {
        try {
            $id = $this->clientId.'-'.'DTL'.'-000001';
            $maxId = TransaksiDetail::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('detail_id', 'LIKE', $this->clientId.'-'.'DTL'.'-%')->max('detail_id');
            if (!$maxId) {
                $id = $this->clientId.'-'.'DTL'.'-000001';
            } else {
                $maxId = str_replace($this->clientId.'-'.'DTL'.'-', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'-'.'DTL'.'-00000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'-'.'DTL'.'-0000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'-'.'DTL'.'-000' . $count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $this->clientId.'-'.'DTL'.'-00' . $count; } 
                elseif ($count >= 10000) { $id = $this->clientId.'-'.'DTL'.'-0' . $count; } 
                else { $id = $this->clientId.'-'.'DTL'.'-' . $count; }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . date('Ymd') . '-' . Uuid::uuid4()->getHex();
        }
    }

    public function getNoAntrian($tglPeriksa)
    {
        try {
            $id = 'FRM001';
            $maxNo = Transaksi::withTrashed()
                ->where('client_id', $this->clientId)
                ->where('tgl_periksa', $tglPeriksa)->max('no_antrian');

            if (!$maxNo) { $id = 'FRM001'; } 
            else {
                $maxNo = str_replace('FRM', '', $maxNo);
                $count = $maxNo + 1;
                if ($count < 10) { $id = 'FRM00' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'FRM0' . $count; } 
                else { $id = 'FRM' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }

    public function realisasiResep(Request $request, $resep_id) {
        try {
            $trxResep = Farmasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$resep_id)->first();
            if(!$trxResep) { 
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); 
            }
            
            //return response()->json(['success' => false, 'message' => $trxResep]); 
            
            if($trxResep->is_konfirmasi) { 
                return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']); 
            }
            
            $transaksi = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$resep_id)->where('pasien_id',$trxResep->pasien_id)
                ->where('status','DAFTAR')->first();
            
            

            DB::connection('dbclient')->beginTransaction();
        
            // $trxResep->updated_by          = Auth::user()->username;
            // $trxResep->is_realisasi        = true;
            // $trxResep->status              = 'REALISASI';
            $success =  Farmasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$trxResep->reg_id)
                ->where('trx_id',$resep_id)->update(['updated_by'=>Auth::user()->username, 'is_realisasi'=>true, 'status'=>'REALISASI']);
        
            //$success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }
            
            //create transaction
            if(!$transaksi) {
                $transaksi = new Transaksi();
                $transaksi->reg_id = $trxResep->reg_id;
                $transaksi->trx_id = $trxResep->trx_id;
                $transaksi->reff_trx_id = $trxResep->reff_trx_id;
                $transaksi->status = 'DAFTAR';
                $transaksi->is_realisasi = true;
                $transaksi->is_bayar = false;
                $transaksi->is_aktif = true;
                $transaksi->client_id = $this->clientId;
                $transaksi->created_by = Auth::user()->username;
                $transaksi->updated_by = Auth::user()->username;
                $transaksi->jns_transaksi = 'FARMASI';
                $transaksi->total = $trxResep->total; 
            }

            // update tb_transaksi
            $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
            $transaksi->no_antrian          = $trxResep->no_antrian;
            $transaksi->no_transaksi        = 'TRX/'.date('Ymd').'/'.$trxResep->no_antrian;
            $transaksi->kelas_id            = '';
            $transaksi->kelas_harga_id      = '';
            $transaksi->kelas_penjamin_id   = '';
            $transaksi->penjamin_id         = '';//$trxResep->penjamin_id;
            $transaksi->penjamin_nama       = '';//$trxResep->penjamin_nama;
            $transaksi->buku_harga_id       = '';
            $transaksi->buku_harga          = '';
            $transaksi->total               = $trxResep->total;
            $transaksi->unit_id             = '';
            $transaksi->unit_nama           = '';
            $transaksi->ruang_id            = '';
            $transaksi->ruang_nama          = '';
            $transaksi->is_realisasi        = true;
            
            $transaksi->dokter_id           = $trxResep->dokter_id;
            $transaksi->dokter_nama         = $trxResep->dokter_nama;
            $transaksi->dokter_pengirim_id  = '';
            $transaksi->dokter_pengirim     = '';
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            $transaksi->pasien_id           = $trxResep->pasien_id;
            $transaksi->no_rm               = $trxResep->no_rm;
            $transaksi->nama_pasien         = $trxResep->nama_pasien;
            
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
            }

            $rsp = FarmasiDetail::where('client_id',$this->clientId)
                    ->where('trx_id',$resep_id)->where('is_aktif',1)->get();

            foreach($rsp as $dt) {
                $dt->status        = 'REALISASI';
                $dt->is_realisasi  = true;
                $dt->updated_by    = Auth::user()->username;                
                $success = $dt->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                }

                if($dt['item_tipe'] !== 'HEADER_RACIKAN') {
                    $stock = DepoProduk::where('client_id',$this->clientId)->where('produk_id', $dt['item_id'])->where('depo_id', $trxResep->depo_id)->first();
                    if (!$stock) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep. Item stock '. $dt['item_nama'] .' tidak ditemukan.']);
                    }
                    
                    $jmlStock = $stock->jml_awal + $stock->jml_masuk + $stock->jml_penyesuaian - $dt['jml_ambil'];
                    if($jmlStock <= 0) {
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep. Item stock '. $dt['item_nama'] .' kurang dari sama dengan 0.']);
                    }

                    $stock->jml_keluar = $dt['jml_ambil'];
                    $sukses = $stock->save();
                    if (!$sukses) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                    }
                }
                
                /** insert ke detail Transaksi **/ 
                $detailId = $dt['detail_id'];
                $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxResep->trx_id)->first();

                if(!$trxDetail) {
                    $trxDetail = new TransaksiDetail();
                    $trxDetail->detail_id           = $detailId;
                    $trxDetail->reg_id              = $trxResep->reg_id;
                    $trxDetail->trx_id              = $trxResep->trx_id;
                    $trxDetail->jns_transaksi       = 'RESEP';
                    $trxDetail->client_id           = $this->clientId;
                    $trxDetail->created_by          = Auth::user()->username;
                    $trxDetail->item_id             = $dt['item_id'];
                }
                $trxDetail->kelas_harga_id      = '';//$request->kelas_harga_id;
                $trxDetail->buku_harga_id       = '';//$trxResep->buku_harga_id;
                $trxDetail->penjamin_id         = '';//$trxResep->penjamin_id;
                $trxDetail->item_nama           = $dt['item_nama'];
                $trxDetail->jumlah              = $dt['jml_ambil'];
                $trxDetail->satuan              = $dt['satuan'];
                
                $trxDetail->nilai               = $dt['nilai'];
                $trxDetail->diskon_persen       = $dt['diskon_persen'];
                $trxDetail->diskon              = $dt['diskon'];
                $trxDetail->harga_diskon        = $dt['harga_diskon'];
                $trxDetail->subtotal            = $dt['subtotal'];
                $trxDetail->dokter_id           = $trxResep->dokter_id;
                $trxDetail->dokter_pengirim_id  = '';
                $trxDetail->is_hitung_adm       = false;
                $trxDetail->is_aktif            = $dt['is_aktif'];
                $trxDetail->updated_by          = Auth::user()->username;
                $success = $trxDetail->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi Resep.']);
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data resep berhasil disimpan', 'data' => $trxResep]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses realisasi data', 'error' => $e->getMessage()]);
        }
    }

    public function derealisasiResep(Request $request, $resep_id) {
        try {

            $trxResep = Farmasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('trx_id',$resep_id)->first();
            if(!$trxResep) { 
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); 
            }
            if(!$trxResep->is_realisasi) { 
                return response()->json(['success' => false, 'message' => 'Proses tidak dapat dilanjutkan.']); 
            }
            
            $transaksi = Transaksi::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$resep_id)->where('pasien_id',$trxResep->pasien_id)
                ->where('status','DAFTAR')->first();
            
            if(!$transaksi) { 
                return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']); 
            }

            $rsp = TransaksiDetail::where('client_id',$this->clientId)->where('trx_id',$resep_id)->where('is_aktif',1)->get();
            if(!$rsp) { 
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']); 
            }

            DB::connection('dbclient')->beginTransaction();
        
            $trxResep->updated_by          = Auth::user()->username;
            $trxResep->is_realisasi        = false;
            $trxResep->status              = 'DRAFT';
        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }

            //delete transaksi
            $transaksi->trx_id              = date('ymdHis').'-'.$resep_id;
            $transaksi->updated_by          = Auth::user()->username;
            $transaksi->is_realisasi        = false;
            $transaksi->is_aktif            = false;
            $transaksi->status              = 'BATAL';
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Data transaksi resep gagal disimpan']);
            }
            
            foreach($rsp as $dt) {
                // $dt->status         = 'BATAL';
                $dt->is_aktif       = false;
                $dt->updated_by    = Auth::user()->username;                
                $success = $dt->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menghapus data detail resep.']);
                }

                // if($dt['item_tipe'] !== 'HEADER_RACIKAN') {
                    $stock = DepoProduk::where('client_id',$this->clientId)->where('produk_id', $dt['item_id'])->where('depo_id', $trxResep->depo_id)->first();
                    if($stock) {
                        $stock->jml_masuk = $dt['jumlah'];
                        $sukses = $stock->save();
                        if (!$sukses) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail resep.']);
                        }
                    }
                // }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data pembatalan realisasi resep berhasil disimpan', 'data' => $trxResep]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses pembatalan realisasi data', 'error' => $e->getMessage()]);
        }
    }
}
