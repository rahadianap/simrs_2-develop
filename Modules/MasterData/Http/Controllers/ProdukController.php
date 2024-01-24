<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;
use Modules\MasterData\Entities\Produk;
use Modules\MasterData\Entities\DepoProduk;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{

    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request, $tipe)
    {
        try {
            $tipeProduk = '%%';            
            if($tipe == 'medical') { $tipeProduk = 'MEDIS'; }
            elseif($tipe == 'general') { $tipeProduk = 'UMUM'; }
            elseif($tipe == 'kitchen') { $tipeProduk = 'DAPUR'; }
            elseif($tipe == 'all') { $tipeProduk = '%%'; }            
            else {
                return response()->json(['success'=>false,'message'=>'Jenis produk tidak diketahui']);
            }
            
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $per_page = 10;
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
                if($per_page) { $per_page = Produk::where('client_id',$this->clientId)->count(); }
            }

            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }            
            
            $list = Produk::where('client_id',$this->clientId)
                    ->where('is_aktif','ILIKE',$aktif)
                    ->where('jenis_produk','ILIKE',$tipeProduk)
                    ->where(function($q) use ($keyword) {
                        $q->where('produk_nama','ILIKE',$keyword)
                        ->orWhere('produk_id','ILIKE',$keyword);
                    })
                    ->orderBy('jenis_produk','ASC')
                    ->orderBy('produk_nama','ASC')
                    ->paginate($per_page); 

            $datas = $list->items();
            if($datas) {
                foreach($datas as $dt) {
                    $dt['golongan'] = json_decode($dt['golongan']);
                    $dt['klasifikasi'] = json_decode($dt['klasifikasi']);
                }
            }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$list]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'List produk tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    
    public function data(Request $request,$id)
    {
        try {
            $data = Produk::where('produk_id',$id)->where('client_id',$this->clientId)->first(); 
            if($data) {
                $data->klasifikasi = json_decode($data->klasifikasi,true);
                $data->golongan = json_decode($data->golongan,true);                
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'Pencarian produk tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
           
            $request->validate([
                'produk_nama' => 'required|max:100',
            ]);
            
            $prefix = 'PRD';
            if($request->jenis_produk == 'MEDIS') {
                $prefix = 'ITM';
            }
            elseif($request->jenis_produk == 'UMUM') {
                $prefix = 'ITU';
            }
            elseif($request->jenis_produk == 'DAPUR') {
                $prefix = 'ITK';
            }
            $metadata = "";
            if($request->klasifikasi) {
               foreach($request->klasifikasi as $klas) {
                    $metadata = $metadata.'; '.$klas['value']; 
                    $metadata = $metadata.trim(";");
               }               
            }

            $id = $this->getProdukId($this->clientId,$prefix);
            $data = new Produk();
            $data->produk_id            = $id;
            $data->produk_nama          = $request->produk_nama;
            $data->barcode              = $request->barcode;
            $data->jenis_produk         = $request->jenis_produk;
            $data->satuan_beli          = $request->satuan_beli;
            $data->satuan               = $request->satuan;
            $data->konversi             = $request->konversi;
            $data->harga_beli           = $request->harga_beli; 
            $data->hna                  = $request->hna;
            $data->het                  = $request->het;
            $data->is_hasil_produksi    = $request->is_hasil_produksi;
            $data->is_jual              = $request->is_jual;
            $data->is_pengadaan         = $request->is_pengadaan;
            $data->is_sterilisasi       = $request->is_sterilisasi;
            $data->is_kontrol_kadaluarsa = $request->is_kontrol_kadaluarsa;
            $data->is_kontrol_stok      = $request->is_kontrol_stok;
            $data->is_laundry_item      = $request->is_laundry_item;
            $data->cara_pakai           = $request->cara_pakai;
            $data->signa                = $request->signa;
            $data->aturan_pakai         = $request->aturan_pakai;
            $data->label_obat           = $request->label_obat;
            $data->klasifikasi          = json_encode($request->klasifikasi,true);
            $data->sediaan              = $request->sediaan;
            $data->golongan             = json_encode($request->golongan,true);
            $data->komposisi            = $request->komposisi;
            $data->kontra_indikasi      = $request->kontra_indikasi;
            $data->vendor_id            = $request->vendor_id;
            $data->vendor               = $request->vendor;
            $data->pabrikan_id          = $request->pabrikan_id;
            $data->pabrikan             = $request->pabrikan;
            $data->meta_data            = $request->meta_data;
            $data->produk_akun          = $request->produk_akun;
            $data->produk_akun_id       = $request->produk_akun_id;
            $data->kelompok_vclaim_id   = $request->kelompok_vclaim_id;
            $data->kelompok_vclaim      = $request->kelompok_vclaim;
            $data->kelompok_tagihan_id  = $request->kelompok_tagihan_id;
            $data->kelompok_tagihan     = $request->kelompok_tagihan;
            $data->jenis_etiket         = $request->jenis_etiket;
            $data->is_aktif             = 1;
            $data->client_id            = $this->clientId;
            $data->created_by           = Auth::user()->username;
            $data->updated_by           = Auth::user()->username;
            $success = $data->save();

            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam pembuatan data Produk']);
            }

            /**
             * kemblaikan data
             */
            $data = Produk::where('produk_id',$id)->where('client_id',$this->clientId)->first(); 
            if($data) {
                $data->klasifikasi = json_decode($data->klasifikasi,true);
                $data->golongan = json_decode($data->golongan,true);                
            }
            return response()->json(['success' => true,'message' => 'Data Produk berhasil disimpan','data'=>$data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Tambah Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            
            $request->validate([
                'produk_nama' => 'required|max:100',
            ]);

            $id = $request->produk_id;
            $data = Produk::where('produk_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data produk dengan nama '. $request->produk_nama .' tidak ditemukan.']);
            }

            $data->barcode              = $request->barcode;
            $data->jenis_produk         = $request->jenis_produk;
            $data->satuan_beli          = $request->satuan_beli;
            $data->satuan               = $request->satuan;
            $data->konversi             = $request->konversi;
            $data->harga_beli           = $request->harga_beli; 
            $data->hna                  = $request->hna;
            $data->het                  = $request->het;
            $data->is_hasil_produksi    = $request->is_hasil_produksi;
            $data->is_jual              = $request->is_jual;
            $data->is_pengadaan         = $request->is_pengadaan;
            $data->is_sterilisasi       = $request->is_sterilisasi;
            $data->is_kontrol_kadaluarsa = $request->is_kontrol_kadaluarsa;
            $data->is_kontrol_stok      = $request->is_kontrol_stok;
            $data->is_laundry_item      = $request->is_laundry_item;
            $data->cara_pakai           = $request->cara_pakai;
            $data->signa                = $request->signa;
            $data->aturan_pakai         = $request->aturan_pakai;
            $data->label_obat           = $request->label_obat;
            $data->klasifikasi          = json_encode($request->klasifikasi,true);
            $data->sediaan              = $request->sediaan;
            $data->golongan             = json_encode($request->golongan,true);
            $data->komposisi            = $request->komposisi;
            $data->kontra_indikasi      = $request->kontra_indikasi;
            $data->vendor_id            = $request->vendor_id;
            $data->vendor               = $request->vendor;
            $data->pabrikan_id          = $request->pabrikan_id;
            $data->pabrikan             = $request->pabrikan;
            $data->meta_data            = $request->meta_data;
            $data->produk_akun          = $request->produk_akun;
            $data->produk_akun_id       = $request->produk_akun_id;
            $data->kelompok_vclaim_id   = $request->kelompok_vclaim_id;
            $data->kelompok_vclaim      = $request->kelompok_vclaim;
            $data->kelompok_tagihan_id  = $request->kelompok_tagihan_id;
            $data->kelompok_tagihan     = $request->kelompok_tagihan;
            $data->jenis_etiket         = $request->jenis_etiket;
            $data->is_aktif             = $request->is_aktif;
            $data->updated_by           = Auth::user()->username;

            $success = $data->save();
            if(!$success) {
                return response()->json(['success' => false,'message' => 'Ada kesalahan dalam update data Produk']);
            }

            /**
             * kemblaikan data
             */
            $data = Produk::where('produk_id',$id)->where('client_id',$this->clientId)->first(); 
            if($data) {
                $data->klasifikasi = json_decode($data->klasifikasi,true);
                $data->golongan = json_decode($data->golongan,true);                
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=> $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Ubah Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            // Produk yang berada di Depo dengan stok lebih dari 0 tidak boleh dihapus
            $cek = DepoProduk::where('produk_id', $id)
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)
                ->where('jml_total','>',0)
                ->count();
                
            if($cek > 0){
                return response()->json(['success'=>false,'message'=>'Data tidak bisa dihapus karena masih digunakan pada Depo dengan jumlah lebih dari 0.']);
            }

            $data = Produk::where('produk_id', $id)->where('client_id',$this->clientId)->update([
                'updated_by' => Auth::user()->username,
                'updated_at' => now(),
                'is_aktif' => 0
            ]);

            return response()->json(['success'=>true,'message'=>'Data produk berhasil di non-aktifkan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' =>'Hapus Produk tidak berhasil.','error'=>$e->getMessage()]);
        }
        
    }

    public function getProdukId($clientId,$prefix) 
    {
        try {
            $id = $clientId.'-'.$prefix.'00001';
            $maxId = Produk::withTrashed()->where('produk_id','LIKE',$clientId.'-'.$prefix.'%')->max('produk_id');

            if (!$maxId) { $id = $clientId.'-'.$prefix.'00001'; }
            else {
                $maxId = str_replace($clientId.'-'.$prefix,'',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $clientId.'-'.$prefix.'0000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $clientId.'-'.$prefix.'000'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $clientId.'-'.$prefix.'00'.$count; } 
                elseif ($count >= 1000 && $count < 10000) { $id = $clientId.'-'.$prefix.'0'.$count; } 
                else { $id = $clientId.'-'.$prefix.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return $prefix.date('Ymd').'-'.Uuid::uuid4()->getHex();
        }
    }
}
