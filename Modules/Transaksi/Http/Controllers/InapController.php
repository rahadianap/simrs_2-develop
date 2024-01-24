<?php

namespace Modules\Transaksi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon;

use Modules\Pendaftaran\Entities\Pendaftaran;
use Modules\Pendaftaran\Entities\RegPasien;
use Modules\Pendaftaran\Entities\SEP;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\Pasien;
use Modules\MasterData\Entities\PasienDetail;
use Modules\MasterData\Entities\PasienAlamat;
use Modules\MasterData\Entities\Dokter;
use Modules\MasterData\Entities\DokterUnit;
use Modules\MasterData\Entities\Penjamin;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

use Modules\Transaksi\Entities\RawatJalan;

use Modules\Pendaftaran\Entities\RawatInap;
use Modules\Pendaftaran\Entities\PemakaianBed;


use Modules\Transaksi\Entities\Pemeriksaan;
use Modules\Transaksi\Entities\PemeriksaanDetail;
use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;

use Modules\Transaksi\Entities\Soap;
use Modules\Transaksi\Entities\SoapDiagnosa;

// FARMASI
use Modules\Farmasi\Entities\Farmasi;
use Modules\Farmasi\Entities\FarmasiDetail;

// OPERASI
use Modules\Pendaftaran\Entities\TrxOperasi;
// use Modules\Penunjang\Entities\OperasiDetail;
// use Modules\Penunjang\Entities\TimOperasi;

// BANK DARAH
use Modules\Penunjang\Entities\BankDarahDistribusi;
use Modules\Penunjang\Entities\BankDarahDetail;
use Modules\Penunjang\Entities\BankDarahTerima;

// PATOLOGI ANATOMI
use Modules\PatologiAnatomi\Entities\TrxPA;
use Modules\PatologiAnatomi\Entities\TrxPAHasil;
use Modules\PatologiAnatomi\Entities\PatologiDetail;

// LABORATORIUM
use Modules\Laboratorium\Entities\TrxLab;
use Modules\Laboratorium\Entities\TrxLabHasil;
use Modules\Laboratorium\Entities\LabTemplate;
use Modules\Laboratorium\Entities\LabNormal;
use Modules\Pendaftaran\Entities\Pelayanan;

// RADIOLOGI
use Modules\Radiologi\Entities\TrxRad;
use Modules\Radiologi\Entities\TrxRadHasil;
use Modules\Radiologi\Entities\RadiologiDetail;
use Modules\EMR\Entities\MedrecInform;
use Modules\EMR\Entities\MedrecInformDetail;


class InapController extends Controller
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
        try {
            $per_page = 2;
            $aktif = 'true';
            $keyword = '%%';
            
            $query = RawatInap::query();
            $query = $query->where('client_id',$this->clientId);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_periksa_start') && $request->has('tgl_periksa_end')) {
                $dtStart = $request->input('tgl_periksa_start').' 00:00:00';
                $dtEnd = $request->input('tgl_periksa_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_periksa', [$dtStart, $dtEnd]);
            }
            
            if ($request->has('unit')) {
                $unit = $request->input('unit');
                $query = $query->where(function($q) use ($unit) {
                        $q->where('unit_id','ILIKE',$unit)
                        ->orWhere('unit_nama','ILIKE',$unit);
                    });
            }

            if ($request->has('dokter')) {
                $dokter = $request->input('dokter');
                $query = $query->where(function($q) use ($dokter) {
                        $q->where('dokter_id','ILIKE',$dokter)
                        ->orWhere('dokter_nama','ILIKE',$dokter);
                    });
            }

            if ($request->has('status')) {
                $query = $query->where('status', 'ILIKE', '%' .$request->input('status'). '%');
            }

            if ($request->has('jns_penjamin')) {
                $query = $query->where('jns_penjamin', 'ILIKE', '%' .$request->input('jns_penjamin'). '%');
            }

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword);
                })->orderBy('tgl_periksa','ASC')
                ->orderBy('no_antrian','ASC')
                ->paginate($per_page);
            
            foreach($list->items() as $dt) {
                $dt['transactions'] = Transaksi::where('reg_id',$dt->reg_id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->orderBy('created_at','DESC')
                    ->get();
            }
            

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data rawat inap', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = RawatInap::leftJoin('tb_pasien', 'tb_rawat_inap.pasien_id', '=', 'tb_pasien.pasien_id')
                ->where('tb_rawat_inap.is_aktif',1)->where('tb_rawat_inap.client_id',$this->clientId)
                ->where('tb_rawat_inap.trx_id',$id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi (transaksi) pelayanan inap tidak ditemukan.']);
            }

            $pemeriksaan = Pemeriksaan::where('client_id',$this->clientId)
                ->where('reg_id',$data->reg_id)
                ->where('reff_trx_id',$data->trx_id)
                ->where('is_aktif',1)
                ->get();
            
            foreach($pemeriksaan as $td) {
                $td['detail'] = PemeriksaanDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$td['reg_id'])
                    ->where('trx_id',$td['trx_id'])
                    ->where('is_aktif',1)
                    ->get();
            }
            $data['pemeriksaan'] = $pemeriksaan;
            
            
            /**resep list */
            $resep = TrxResep::where('is_aktif',1)->where('client_id',$this->clientId)->where('reg_id',$data->reg_id)
                ->where('reff_trx_id',$data->trx_id)->get();

            foreach($resep as $rsp) {
                $rsp['detail'] = TrxResepDetail::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$rsp->reg_id)
                    ->where('reff_trx_id',$rsp->reff_trx_id)
                    ->where('trx_id',$rsp->trx_id)->get();
            }
            $data['resep'] = $resep;

            /**soap */
            $soap = Soap::where('is_aktif',1)->where('client_id',$this->clientId)->where('reg_id',$data->reg_id)
                ->where('reff_trx_id',$data->trx_id)->orderBy('tgl_soap','DESC')->get();

            foreach($soap as $sp) {
                $sp['diagnosa'] = SoapDiagnosa::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$sp->reg_id)
                    ->where('reff_trx_id',$sp->reff_trx_id)
                    ->where('soap_id',$sp->soap_id)->get();
            }
            $data['soap'] = $soap;

            /**inform */
            $inform = MedrecInform::where('is_aktif',1)->where('client_id',$this->clientId)->where('reg_id',$data->reg_id)
                ->where('reff_trx_id',$data->trx_id)->orderBy('tgl_assesmen','DESC')->get();

            foreach($inform as $inf) {
                $inf['detail'] = MedrecInformDetail::where('is_aktif',1)
                    ->where('client_id',$this->clientId)
                    ->where('reg_id',$inf->reg_id)
                    ->where('reff_trx_id',$inf->reff_trx_id)
                    ->where('asesmen_id',$inf->asesmen_id)->get();
            }
            $data['inform'] = $inform;

            $penunjang = [];
            /**
             * rekam medis lab
             */
            $lab = TrxLab::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$data->reg_id)->where('reff_trx_id',$data->trx_id)
                ->get();
            if($lab) {
                foreach($lab as $lb) {
                    $lb['detail'] = $this->retrieveHasilLab($data->reg_id, $lb['trx_id']);
                }
            }

            /**
             * rekam medis radiologi
             */
            $rad = TrxRad::where('client_id',$this->clientId)->where('is_aktif',1)
            ->where('reg_id',$data->reg_id)->where('reff_trx_id',$data->trx_id)
                ->get();
            if($rad) {
                foreach($rad as $rd) {
                    $rd['detail'] = $this->retrieveHasilRad($data->reg_id, $rd['trx_id']);
                }
            }
            $penunjang['lab'] = $lab;
            $penunjang['rad'] = $rad;
            
            $data['penunjang'] = $penunjang;

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function retrieveHasilLab($regId, $trxId) {
        try {
            $details = TransaksiDetail::where('is_aktif',1)->where('client_id',$this->clientId)
                ->where('trx_id',$trxId)->where('reg_id',$regId)->get();         
        
            foreach($details as $dt) {
                $results = []; $i = 0;
                $templates = LabTemplate::where('is_aktif',1)->where('client_id',$this->clientId)
                    ->where('tindakan_id',$dt['item_id'])->orderBy('no_urut','ASC')->get();
                
                foreach($templates as $tmp) {
                    $resLab = TrxLabHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('trx_id',$trxId)->where('reg_id',$regId)
                        ->where('lab_hasil_id',$tmp['lab_hasil_id'])->first();

                    $results[$i]['reg_id'] = $regId;
                    $results[$i]['trx_id'] = $trxId;               
                    $results[$i]['item_id'] = $dt['item_id'];
                    $results[$i]['item_nama'] = $dt['item_nama'];                    
                    $results[$i]['lab_hasil_id'] = $tmp->lab_hasil_id;
                    $results[$i]['hasil_nama'] = $tmp->hasil_nama;                    
                    $results[$i]['detail_id'] = $dt->detail_id;
                    $results[$i]['klasifikasi'] = $tmp->klasifikasi;    
                    $results[$i]['sub_klasifikasi'] = $tmp->sub_klasifikasi;

                    if($resLab) {
                        $results[$i]['is_header'] = $resLab->is_header;
                        $results[$i]['no_urut'] = $resLab->no_urut;
                        $results[$i]['trx_lab_hasil_id'] = $resLab->trx_lab_hasil_id;
                        $results[$i]['hasil_nilai'] = $resLab->hasil_nilai;
                        $results[$i]['jenis_hasil'] = $resLab->jenis_hasil;
                        $results[$i]['satuan'] = $resLab->satuan;
                        $results[$i]['hasil_operator'] = $resLab->hasil_operator;
                        $results[$i]['hasil_pilihan'] = $resLab->hasil_pilihan;
                        $results[$i]['hasil_min'] = $resLab->hasil_min;
                        $results[$i]['hasil_maks'] = $resLab->hasil_maks;
                        $results[$i]['jns_kelamin'] = $resLab->jns_kelamin;
                        $results[$i]['normal_group'] = $resLab->normal_group;
                        $results[$i]['hasil_normal_lk'] = $resLab->hasil_normal_lk;
                        $results[$i]['hasil_normal_pr'] = $resLab->hasil_normal_pr;
                        $results[$i]['is_tandai'] = $resLab->is_tandai;
                        $results[$i]['is_aktif'] = $resLab->is_aktif;
                    }
                    else {
                        $results[$i]['is_header'] = false;
                        $results[$i]['no_urut'] = $tmp->no_urut;
                        $results[$i]['trx_lab_hasil_id'] = null;
                        $results[$i]['hasil_nilai'] = null;
                        $results[$i]['jenis_hasil'] = null; 
                        $results[$i]['satuan'] = null;
                        $results[$i]['hasil_operator'] = null;
                        $results[$i]['hasil_pilihan'] = null;
                        $results[$i]['hasil_min'] = null;
                        $results[$i]['hasil_maks'] = null;
                        $results[$i]['jns_kelamin'] = null;
                        $results[$i]['normal_group'] = null;
                        $results[$i]['hasil_normal_lk'] = null;
                        $results[$i]['hasil_normal_pr'] = null;
                        $results[$i]['is_tandai'] = false;   
                        $results[$i]['is_aktif'] = true;
                    }
                    
                    $results[$i]['normal'] = LabNormal::where('is_aktif',1)->where('client_id',$this->clientId)
                        ->where('lab_hasil_id',$tmp->lab_hasil_id)->get();
                    $i++;
                }
                $dt['hasil'] = $results;
            }

            return $details;
        }
        catch (\Exception $e) {
            return null;
        }
    }

    public function retrieveHasilRad($regId, $trxId) {

        try {
            $data = TrxRad::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$regId)->where('trx_id',$trxId)->first();
            
            //ambil data detail transaksi
            $details = TransaksiDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$regId)->where('trx_id',$trxId)
                ->get();

            $results = []; $i = 0;
            foreach($details as $dt) {
                if($data->is_multi_hasil) {
                    $hasil = TrxRadHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('reg_id',$regId)->where('trx_id',$trxId)
                        ->where('tindakan_id',$dt['item_id'])->first();
                    
                    if(!$hasil) {
                        $hasil['detail_id'] = null;
                        $hasil['reg_id'] = $dt['reg_id'];
                        $hasil['trx_id'] = $dt['trx_id'];
                        $hasil['tindakan_id'] = $dt['item_id'];
                        $hasil['tindakan_nama'] = $dt['item_nama'];
                        $hasil['jenis_foto'] = null;
                        $hasil['no_foto'] = null;
                        $hasil['uraian_hasil'] = null;
                        $hasil['kesan'] = null;
                        $hasil['catatan'] = null;
                        $hasil['tgl_hasil'] = $data->tgl_hasil;
                        $hasil['dokter_id'] = $data->dokter_id;
                        $hasil['dokter_nama'] = $data->dokter_nama;
                        $hasil['expertise_id'] = $data->expertise_id;
                        $hasil['expertise_nama'] = $data->expertise_nama;
                        $hasil['status'] = null;
                        $hasil['is_aktif'] = true;
                    }
                    
                    $dt['hasil'] = $hasil;
                }

                else {
                    $dt['hasil'] = TrxRadHasil::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('reg_id',$regId)->where('trx_id',$trxId)->first();
                }  
            }
            
            return $details;
        }
        catch (\Exception $e) {
            return null;
        }
    }

    public function saveData(request $request) 
    {
        try {
            $trx = Transaksi::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('reg_id',$request->reg_id)->where('trx_id',$request->trx_id)->first();
            
            if(!$trx) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.']);
            }

            if($trx->is_konfirmasi) {
                return response()->json(['success' => false, 'message' => 'Data sudah dikonfimasi.']);
            }
            DB::connection('dbclient')->beginTransaction();
            
            $result = $this->saveBhp($request);
            if(!$result['success']) {
                return response()->json(['success' => false, 'message' => $result['message']]);
            }
            
            $result = $this->savePemeriksaan($request);
            if(!$result['success']) {
                return response()->json(['success' => false, 'message' => $result['message']]);
            }

            $result = $this->saveResep($request);
            if(!$result['success']) {
                return response()->json(['success' => false, 'message' => $result['message']]);
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'data berhasil disimpan.']);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data', 'error' => $e->getMessage()]);
        }
    }

    public function saveBhp($request) 
    {
        try {
            $bhp = $request->bhp;
            $regId = $request->reg_id;
            $reffTrxId = $request->trx_id;

            $trxBhp = TrxBhp::where('reg_id',$request->reg_id)->where('reff_trx_id',$request->trx_id)->first();
            
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();
            $ruang = RuangPelayanan::where('ruang_id',$request->ruang_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();


            if(!$trxBhp) { 
                $trxBhpId = $request->trx_id.'-BHP';
                $transaksiBhp = new Transaksi();
                /**
                 * create data transaksi
                 */
                $transaksiBhp                      = new Transaksi();
                $transaksiBhp->reg_id              = $regId;
                $transaksiBhp->tgl_transaksi       = date('Y-m-d H:i:s');
                $transaksiBhp->trx_id              = $trxBhpId;
                $transaksiBhp->reff_trx_id         = $reffTrxId;
                $transaksiBhp->status              = 'ANTRI';
                $transaksiBhp->is_realisasi        = false;
                $transaksiBhp->is_bayar            = false;
                $transaksiBhp->is_aktif            = true;
                $transaksiBhp->jns_transaksi       = 'BHP';
                $transaksiBhp->no_antrian          = '-';
                $transaksiBhp->no_transaksi        = '-';
                
                $transaksiBhp->client_id           = $this->clientId;
                $transaksiBhp->created_by          = Auth::user()->username;
                $transaksiBhp->updated_by          = Auth::user()->username;
            }
            else { 
                $trxBhpId =  $trxBhp->trx_id; 
                $transaksiBhp = Transaksi::where('reg_id',$request->reg_id)
                    ->where('trx_id',$trxBhpId)
                    ->where('reff_trx_id',$request->trx_id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->first();
                if(!$transaksiBhp) {
                    //return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.']);
                    return ['success' => false, 'message' => 'Data transaksi tidak ditemukan.'];
                }
            }

            //$transaksiBhp->is_sub_trx          = false;
            $transaksiBhp->kelas_id            = $request->kelas_harga_id;
            $transaksiBhp->kelas_harga_id      = $request->kelas_harga_id;
            $transaksiBhp->kelas_penjamin_id   = $request->kelas_harga_id;
            $transaksiBhp->penjamin_id         = $request->penjamin_id;
            $transaksiBhp->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksiBhp->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksiBhp->buku_harga          = $penjamin->buku_harga;
            $transaksiBhp->total               = $request->bhp['total'];
            
            $transaksiBhp->unit_id             = $request->unit_id;
            $transaksiBhp->unit_nama           = $unit->unit_nama;
            $transaksiBhp->ruang_id            = $request->ruang_id;
            $transaksiBhp->ruang_nama          = $ruang->ruang_nama;
            
            $transaksiBhp->dokter_id           = $request->dokter_id;
            $transaksiBhp->dokter_nama         = $dokter->dokter_nama;
            $transaksiBhp->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksiBhp->dokter_pengirim     = '';
            
            $transaksiBhp->unit_pengirim_id    = '';
            $transaksiBhp->unit_pengirim       = '';
            
            $transaksiBhp->pasien_id           = $request->pasien_id;
            $transaksiBhp->no_rm               = $pasien->no_rm;
            $transaksiBhp->nama_pasien         = $pasien->nama_pasien;
        
            $success = $transaksiBhp->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi BHP gagal disimpan'];
            }
            

            foreach($request->bhp['detail'] as $dt) {
                /**
                 * insert ke table BHP
                 */
                $detailId = $dt['detail_id'];
                $bhp = TrxBhp::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxBhpId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$bhp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $bhp = new TrxBhp();
                    $bhp->detail_id     = $detailId;
                    $bhp->reg_id        = $regId;
                    $bhp->trx_id        = $trxBhpId;
                    $bhp->reff_trx_id    = $reffTrxId;
                    $bhp->item_id       = $dt['item_id'];
                    $bhp->client_id     = $this->clientId;
                    $bhp->created_by    = Auth::user()->username;
                }

                $bhp->unit_id       = $request->unit_id;
                $bhp->unit_nama     = $unit->unit_nama;
                
                $bhp->depo_id       = $dt['depo_id'];
                $bhp->depo_nama     = $dt['depo_nama'];
                // $bhp->dokter_id     = $request->dokter_id;
                // $bhp->dokter_nama   = $dokter->dokter_nama;
                $bhp->item_nama     = $dt['item_nama'];
                $bhp->klasifikasi   = $dt['klasifikasi'];
                $bhp->satuan        = $dt['satuan'];
                $bhp->jumlah        = $dt['jumlah'];
                $bhp->is_realisasi  = false;
                $bhp->is_aktif      = $dt['is_aktif'];
                $bhp->updated_by    = Auth::user()->username;
                
                $success = $bhp->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail BHP.'];
                }
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
            }
            
            return ['success' => true, 'message' => 'OK.'];                    
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data bahan habis pakai. Error: '.$e->getMessage()];
                    
            //return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data bahan habis pakai', 'error' => $e->getMessage()]);
        }
    }

    public function savePemeriksaan(request $request) 
    {
        try {
            $bhp = $request->bhp;
            $regId = $request->reg_id;
            $trxId = $request->trx_id;
            $reffTrxId = $request->reff_trx_id;
            
            $transaksi = Transaksi::where('reg_id',$regId)->where('reff_trx_id',$reffTrxId)->where('trx_id',$trxId)->first();

            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();
            $ruang = RuangPelayanan::where('ruang_id',$request->ruang_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$transaksi) { 
                // $transaksi = new Transaksi();
                /**
                 * create data transaksi
                 */
                $transaksi                      = new Transaksi();
                $transaksi->reg_id              = $regId;
                $transaksi->tgl_transaksi       = date('Y-m-d H:i:s');
                $transaksi->trx_id              = $trxId;
                $transaksi->reff_trx_id         = $reffTrxId;
                $transaksi->status              = 'ANTRI';
                $transaksi->is_realisasi        = false;
                $transaksi->is_bayar            = false;
                $transaksi->is_aktif            = true;
                $transaksi->jns_transaksi       = $unit->jns_registrasi;
                $transaksi->no_antrian          = $data->no_antrian;
                $transaksi->no_transaksi        = '-';
                
                $transaksi->client_id           = $this->clientId;
                $transaksi->created_by          = Auth::user()->username;
                $transaksi->updated_by          = Auth::user()->username;
            }

            $transaksi->kelas_id            = $request->kelas_harga_id;
            $transaksi->kelas_harga_id      = $request->kelas_harga_id;
            $transaksi->kelas_penjamin_id   = $request->kelas_harga;
            $transaksi->penjamin_id         = $request->penjamin_id;
            $transaksi->penjamin_nama       = $penjamin->penjamin_nama;
            $transaksi->buku_harga_id       = $penjamin->buku_harga_id;
            $transaksi->buku_harga          = $penjamin->buku_harga;
            $transaksi->total               = $request->tindakan['total'];
            
            $transaksi->unit_id             = $request->unit_id;
            $transaksi->unit_nama           = $unit->unit_nama;
            $transaksi->ruang_id            = $request->ruang_id;
            $transaksi->ruang_nama          = $ruang->ruang_nama;
            
            $transaksi->dokter_id           = $request->dokter_id;
            $transaksi->dokter_nama         = $dokter->dokter_nama;
            $transaksi->dokter_pengirim_id  = $request->dokter_pengirim_id;
            $transaksi->dokter_pengirim     = '';
            
            $transaksi->unit_pengirim_id    = '';
            $transaksi->unit_pengirim       = '';
            
            $transaksi->pasien_id           = $request->pasien_id;
            $transaksi->no_rm               = $pasien->no_rm;
            $transaksi->nama_pasien         = $pasien->nama_pasien;
        
            $success = $transaksi->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                //return response()->json(['success' => false, 'message' => 'Data transaksi gagal disimpan.']);
                return false;
            }

            foreach($request->tindakan['detail'] as $dt) {
                /**
                 * insert ke detail Transaksi   
                 **/ 
                $detailId = $dt['detail_id'];

                $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxId)->first();

                if(!$trxDetail) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $trxDetail = new TransaksiDetail();
                    $trxDetail->detail_id           = $detailId;
                    $trxDetail->reg_id              = $regId;
                    $trxDetail->trx_id              = $trxId;
                    //$trxDetail->reff_trx_id         = $request->reff_trx_id;
                    $trxDetail->jns_transaksi       = 'TINDAKAN';
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
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP'];
                }
            }
            return ['success' => true, 'message' => 'OK'];
        }
        catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function saveResep(request $request) 
    {
        try {
            $bhp = $request->bhp;
            $regId = $request->reg_id;
            $reffTrxId = $request->trx_id;

            $trxResep = TrxResep::where('reg_id',$request->reg_id)->where('reff_trx_id',$request->trx_id)->first();
            
            $dokter = Dokter::where('client_id',$this->clientId)->where('is_aktif',1)->where('dokter_id',$request->dokter_id)->first();
            $penjamin = Penjamin::where('client_id',$this->clientId)->where('is_aktif',1)->where('penjamin_id',$request->penjamin_id)->first();
            $pasien = Pasien::where('client_id',$this->clientId)->where('is_aktif',1)->where('pasien_id',$request->pasien_id)->first();
            $unit = UnitPelayanan::where('client_id',$this->clientId)->where('is_aktif',1)->where('unit_id',$request->unit_id)->first();
            $ruang = RuangPelayanan::where('ruang_id',$request->ruang_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();

            if(!$trxResep) { 
                $trxResepId = $request->trx_id.'-RSP';
                $trxResep = new Transaksi();
                /**
                 * create data transaksi
                 */
                $trxResep                      = new TrxResep();
                $trxResep->reg_id              = $regId;
                $trxResep->tgl_transaksi       = date('Y-m-d H:i:s');
                $trxResep->trx_id              = $trxResepId;
                $trxResep->reff_trx_id         = $reffTrxId;
                $trxResep->status              = 'ANTRI';
                $trxResep->is_realisasi        = false;
                $trxResep->is_bayar            = false;
                $trxResep->is_aktif            = true;
                $trxResep->jns_transaksi       = 'RESEP';
                $trxResep->no_resep            = '-';
                $trxResep->no_transaksi        = '-';
                
                $trxResep->client_id           = $this->clientId;
                $trxResep->created_by          = Auth::user()->username;
                $trxResep->updated_by          = Auth::user()->username;
                $trxResep->tgl_resep           = date('Y-m-d H:i:s');
            }
            else { 
                $trxResepId =  $trxResep->trx_id; 
                $trxResep = Transaksi::where('reg_id',$request->reg_id)
                    ->where('trx_id',$trxResepId)
                    ->where('reff_trx_id',$request->trx_id)
                    ->where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->first();
                if(!$trxResep) {
                    return ['success' => false, 'message' => 'Data transaksi tidak ditemukan.'];
                }
            }

            // $trxResep->kelas_id            = $request->kelas_harga_id;
            // $trxResep->kelas_harga_id      = $request->kelas_harga_id;
            // $trxResep->kelas_penjamin_id   = $request->kelas_harga_id;
            $trxResep->penjamin_id         = $request->penjamin_id;
            $trxResep->penjamin_nama       = $penjamin->penjamin_nama;
            // $trxResep->buku_harga_id       = $penjamin->buku_harga_id;
            // $trxResep->buku_harga          = $penjamin->buku_harga;
            $trxResep->total               = $request->resep['total'];
            
            $trxResep->unit_id             = $request->unit_id;
            $trxResep->unit_nama           = $unit->unit_nama;
            // $trxResep->depo_id             = null;
            // $trxResep->depo_nama           = $ruang->ruang_nama;
            
            $trxResep->dokter_id           = $request->dokter_id;
            $trxResep->dokter_nama         = $dokter->dokter_nama;
            $trxResep->peresep             = $dokter->dokter_nama;
            // $trxResep->dokter_pengirim_id  = $request->dokter_pengirim_id;
            // $trxResep->dokter_pengirim     = '';
            
            // $trxResep->unit_pengirim_id    = '';
            // $trxResep->unit_pengirim       = '';
            
            $trxResep->pasien_id           = $request->pasien_id;
            $trxResep->no_rm               = $pasien->no_rm;
            $trxResep->nama_pasien         = $pasien->nama_pasien;
            $trxResep->jns_resep           = 'RESEP RAWAT JALAN';

        
            $success = $trxResep->save();
            if(!$success) {
                DB::connection('dbclient')->rollBack();
                return ['success' => false, 'message' => 'Data transaksi BHP gagal disimpan'];
            }
            

            foreach($request->resep['detail'] as $dt) {
                /**
                 * insert ke table BHP
                 */
                $detailId = $dt['detail_id'];
                $rsp = TrxResepDetail::where('client_id',$this->clientId)
                    ->where('detail_id',$detailId)
                    ->where('item_id',$dt['item_id'])
                    ->where('trx_id',$trxResepId)
                    ->where('is_aktif',1)
                    ->first();

                if(!$rsp) {
                    $detailId = $this->clientId . date('ymd') . '-' . Uuid::uuid4()->getHex();
                    $rsp = new TrxResepDetail();
                    $rsp->detail_id     = $detailId;
                    $rsp->reg_id        = $regId;
                    $rsp->trx_id        = $trxResepId;
                    $rsp->reff_trx_id   = $reffTrxId;
                    $rsp->item_id       = $dt['item_id'];
                    $rsp->client_id     = $this->clientId;
                    $rsp->created_by    = Auth::user()->username;
                }

                $rsp->unit_id                   = $request->unit_id;
                $rsp->unit_nama                 = $unit->unit_nama;
                
                $rsp->depo_id                   = $dt['depo_id'];
                $rsp->depo_nama                 = $dt['depo_nama'];
                $rsp->dokter_id                 = $request->dokter_id;
                $rsp->dokter_nama               = $dokter->dokter_nama;
                $rsp->item_nama                 = $dt['item_nama'];
                $rsp->klasifikasi               = $dt['klasifikasi'];
                $rsp->satuan                    = $dt['satuan'];
                $rsp->jumlah                    = $dt['jumlah'];
                $rsp->jml_ambil                 = $dt['jumlah'];

                $rsp->satuan                    = $dt['satuan'];                
                $rsp->nilai                     = $dt['nilai'];
                $rsp->diskon_persen             = $dt['diskon_persen'];
                $rsp->diskon                    = $dt['diskon'];
                $rsp->harga_diskon              = $dt['harga_diskon'];
                $rsp->subtotal                  = $dt['subtotal'];
                $rsp->is_realisasi  = false;
                $rsp->is_aktif      = $dt['is_aktif'];
                $rsp->updated_by    = Auth::user()->username;
                
                $success = $rsp->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail BHP.'];
                }
                // /**
                //  * insert ke detail Transaksi   
                //  **/ 
                // $trxDetail = TransaksiDetail::where('client_id',$this->clientId)
                //     ->where('detail_id',$detailId)->where('item_id',$dt['item_id'])
                //     ->where('trx_id',$trxBhpId)->first();

                // if(!$trxDetail) {
                //     $trxDetail = new TransaksiDetail();
                //     $trxDetail->detail_id           = $detailId;
                //     $trxDetail->reg_id              = $regId;
                //     $trxDetail->trx_id              = $trxBhpId;
                //     $trxDetail->jns_transaksi       = 'BHP';
                //     $trxDetail->client_id           = $this->clientId;
                //     $trxDetail->created_by          = Auth::user()->username;
                //     $trxDetail->item_id             = $dt['item_id'];
                // }
                // $trxDetail->kelas_harga_id      = $request->kelas_harga_id;
                // $trxDetail->buku_harga_id       = $penjamin->buku_harga_id;
                // $trxDetail->penjamin_id         = $penjamin->penjamin_id;
                // $trxDetail->item_nama           = $dt['item_nama'];
                // $trxDetail->jumlah              = $dt['jumlah'];
                // $trxDetail->satuan              = $dt['satuan'];
                
                // $trxDetail->nilai               = $dt['nilai'];
                // $trxDetail->diskon_persen       = $dt['diskon_persen'];
                // $trxDetail->diskon              = $dt['diskon'];
                // $trxDetail->harga_diskon        = $dt['harga_diskon'];
                // $trxDetail->subtotal            = $dt['subtotal'];
                // $trxDetail->dokter_id           = $request->dokter_id;
                // $trxDetail->dokter_pengirim_id  = '';
                // $trxDetail->is_hitung_adm       = false;
                // $trxDetail->is_aktif            = $dt['is_aktif'];
                // $trxDetail->updated_by          = Auth::user()->username;

                // $success = $trxDetail->save();
                // if (!$success) {
                //     DB::connection('dbclient')->rollBack();
                //     return ['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP.'];
                //     //return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam penyimpanan data detail transaksi BHP']);
                // }
            }
            return ['success' => true, 'message' => 'OK.'];
        }
        catch (\Exception $e) {

            return ['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan resep. Error: '.$e->getMessage()];
        }
    }

    public function cetakanGelang($trxId)
    {
        try {
            $datas = RawatInap::join('tb_pasien','tb_rawat_inap.pasien_id','=','tb_pasien.pasien_id')
                        ->where('tb_rawat_inap.client_id',$this->clientId)->where('tb_rawat_inap.is_aktif',1)
                        ->where('tb_rawat_inap.trx_id',$trxId)
                        ->select('tb_rawat_inap.reg_id','tb_rawat_inap.no_rm','tb_rawat_inap.nama_pasien','tb_rawat_inap.usia as umur','tb_pasien.tgl_lahir')
                        ->first();
            
            return view('cetakan/cetakanGelangDewasa',  compact('datas'));
            // return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses data rawat inap', 'error' => $e->getMessage()]);
        }
    }
}
