<?php

namespace Modules\EMR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Ramsey\Uuid\Uuid;
use Carbon;
use DB;

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
use Modules\Transaksi\Entities\Pembayaran;


// use Modules\Transaksi\Entities\Transaksi;
// use Modules\Transaksi\Entities\TransaksiDetail;
// use Modules\Transaksi\Entities\TransaksiDetailKomp;

// use Modules\Transaksi\Entities\RawatJalan;
// use Modules\Transaksi\Entities\Pemeriksaan;
// use Modules\Transaksi\Entities\PemeriksaanDetail;
// use Modules\Transaksi\Entities\TrxResep;
// use Modules\Transaksi\Entities\TrxResepDetail;

// RAWAT JALAN
use Modules\Transaksi\Entities\Soap;
use Modules\Transaksi\Entities\SoapDiagnosa;

use Modules\Transaksi\Entities\RawatJalan;
use Modules\Transaksi\Entities\Pemeriksaan;
use Modules\Transaksi\Entities\PemeriksaanDetail;

use Modules\Transaksi\Entities\TrxResep;
use Modules\Transaksi\Entities\TrxResepDetail;

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

// TRANSAKSI
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Transaksi\Entities\TransaksiDetailKomp;

class MedicalRecordController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(Request $request, $pasienId)
    {
        try {
            $per_page = 20;
            $aktif = 'true';
            $keyword = '%%';
            
            $query = Pendaftaran::query();
            $query = $query->where('client_id',$this->clientId)->whereIn('status_reg',['ANTRI','DAFTAR','SELESAI','LENGKAP']);
            if ($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' .$request->input('is_aktif'). '%');
            }
            else {
                $query = $query->where('is_aktif', 'ILIKE', '%true%');
            }

            if ($request->has('tgl_start') && $request->has('tgl_end')) {
                $dtStart = $request->input('tgl_start').' 00:00:00';
                $dtEnd = $request->input('tgl_end').' 23:59:59';                
                $query = $query->whereBetween('tgl_registrasi', [$dtStart, $dtEnd]);
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

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }

            if ($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page == 'ALL') { $per_page = Pendaftaran::where('client_id',$this->clientId)->count(); }
            }

            $list = $query->where('pasien_id',$pasienId)->where(function($q) use ($keyword) {
                    $q->where('no_rm','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword);
                })->orderBy('tgl_registrasi','ASC')
                ->paginate($per_page);
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data transaksi', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = Pendaftaran::where('reg_id',$id)->where('is_aktif',1)
                ->where('client_id',$this->clientId)
                ->select('reg_id','tgl_registrasi','tgl_periksa','unit_nama','unit_id','dokter_nama','dokter_id')
                ->first();

            if(!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data. Data tidak ditemukan.']);
            }

            /**
             * rekam medis lab
             */
            $lab = TrxLab::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();
            if($lab) {
                foreach($lab as $lb) {
                    $lb['detail'] = $this->retrieveHasilLab($id,$lb['trx_id']);
                }
            }
            
            /**
             * rekam medis radiologi
             */
            $rad = TrxRad::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();
            if($rad) {
                foreach($rad as $rd) {
                    $rd['detail'] = $this->retrieveHasilRad($id,$rd['trx_id']);
                }
            }
            /**
             * rekam medis resep
             */
            $resep = TrxResep::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();
            if($resep) {
                foreach($resep as $rsp) {
                    $rsp['detail'] = TrxResepDetail::where('client_id',$this->clientId)
                        ->where('is_aktif',1)->where('reg_id',$id)
                        ->where('trx_id',$rsp['trx_id'])->get(); 
                }
            }

            /**
             * rekam medis operasi
             */
            $operasi = TrxOperasi::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();

            /**
             * pemeriksaan
             */
            $pemeriksaan = Pemeriksaan::where('client_id',$this->clientId)
                ->where('reg_id',$id)
                ->where('is_aktif',1)
                ->get();
            
            foreach($pemeriksaan as $td) {
                $td['detail'] = PemeriksaanDetail::where('client_id',$this->clientId)
                    ->where('reg_id',$td['reg_id'])
                    ->where('trx_id',$td['trx_id'])
                    ->where('is_aktif',1)
                    ->get();
            }

            /**
             * rekam medis rawat jalan
             */


            /**
             * rekam medis rawat inap
             */

            // $transaksi = Transaksi::where('reg_id',$id)->where('is_aktif',1)
            //     ->where('client_id',$this->clientId)
            //     ->where('is_realisasi',true)
            //     ->get();

            // $grandTotal = 0;
            // foreach($transaksi as $tr) {
            //     $tr['is_bayar'] = true;
            //     $grandTotal = $grandTotal + ($tr['total']*1);
            //     $tr['detail'] = TransaksiDetail::where('reg_id',$tr->reg_id)
            //         ->where('trx_id',$tr['trx_id'])
            //         ->where('client_id',$this->clientId)
            //         ->where('is_aktif',1)
            //         ->orderBy('created_at','DESC')
            //         ->get();
            // }

            // $pembayaran = Pembayaran::where('client_id',$this->clientId)->where('is_aktif',1)->where('reg_id',$id)->get();
            // $totalBayar = 0;
            // foreach($pembayaran as $byr) {
            //     $totalBayar = $totalBayar + ($byr['jml_bayar'] * 1);
            // }

            // $data['grand_total'] = $grandTotal;
            // $data['transaksi'] = $transaksi;
            // $data['histori_bayar'] = $pembayaran;
            // $data['total_bayar'] = $totalBayar;                        
            // $data['pembayaran'] = [];

            $data['lab'] = $lab;
            $data['rad'] = $rad;
            $data['resep'] = $resep;
            $data['operasi'] = $operasi;            
            $data['pemeriksaan'] = $pemeriksaan;

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

    public function dataTransaksi() {
        $data = RawatJalan::leftJoin('tb_pasien', 'tb_rawat_jalan.pasien_id', '=', 'tb_pasien.pasien_id')
            ->where('tb_rawat_jalan.is_aktif',1)->where('tb_rawat_jalan.client_id',$this->clientId)->where('tb_rawat_jalan.trx_id',$id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data registrasi (transaksi) pelayanan poli tidak ditemukan.']);
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

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
    }
}
