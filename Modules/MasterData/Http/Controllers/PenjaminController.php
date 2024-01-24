<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\Penjamin;
use Modules\MasterData\Entities\BukuHarga;

use Ramsey\Uuid\Uuid;
use DB;

class PenjaminController extends Controller
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

    public function lists(Request $request)
    {
        try {
            $perPage = 10;
            $keyword = '%%';
            $aktif = 'true';

            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                if($perPage == 'ALL') {
                    $perPage = Penjamin::where('client_id',$this->clientId)->count(); 
                }
            }
            /**
             * tambahkan semua filter yang dikirim dari client
             */
            //$query = Penjamin::query();
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
                //$query = $query->where('is_aktif','ILIKE',$aktif);
            }
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
                // $query = $query->where(function($q) use($keyword){
                //     $q->where('penjamin_nama','ILIKE',$keyword)
                //     ->orWhere('penjamin_id','ILIKE',$keyword)
                //     ->orWhere('jns_penjamin','ILIKE',$keyword);
                // });
            }

            $jns_penjamin = '%%';
            if($request->has('jns_penjamin')) {
                $jns_penjamin = '%'.$request->input('jns_penjamin').'%';
                //$query = $query->where('is_aktif','ILIKE',$aktif);
            }
            // foreach ($request->except('_token') as $key => $data) {
            //     if ($key !== "page" && $key !== "per_page" && $key !== "page") {                    
            //         $query = $query->where($key, 'ILIKE', '%' . $data . '%');
            //     }
            // }       
            $data = Penjamin::where('client_id', $this->clientId)->where('is_aktif','ILIKE',$aktif)
                ->where('jns_penjamin','ILIKE',$jns_penjamin)
                ->where(function($q) use($keyword){
                    $q->where('penjamin_nama','ILIKE',$keyword)
                    ->orWhere('penjamin_id','ILIKE',$keyword)
                    ->orWhere('jns_penjamin','ILIKE',$keyword);
                })->orderBy('penjamin_nama','ASC')->paginate($perPage);
            
            //$data = $query->where('client_id', $this->clientId)->orderBy('penjamin_nama', 'ASC')->paginate($perPage);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar Jenis Penjamin : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data($id)
    {
        try {
            $data = Penjamin::where('client_id', $this->clientId)->where('penjamin_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data Penjamin : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {

            $bukuHarga = BukuHarga::where('client_id',$this->clientId)->where('is_aktif',1)->where('buku_harga_id',$request->buku_harga_id)->first();
            if(!$bukuHarga) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data. Buku (kelompok) Harga tidak ditemukan.']);
            }

            $id = $this->GetPenjaminId();
            $data = new Penjamin();

            $data->penjamin_id = $id;
            $data->jns_penjamin = $request->jns_penjamin;
            $data->penjamin_nama = strtoupper($request->penjamin_nama);
            $data->inisial = strtoupper($request->inisial);
            $data->npwp = $request->npwp;
            $data->narahubung = strtoupper($request->narahubung);
            $data->no_telp = $request->no_telp;
            $data->email = $request->email;
            $data->alamat = $request->alamat;
            $data->buku_harga_id = $request->buku_harga_id;
            $data->buku_harga = $bukuHarga->buku_nama;
            
            $data->margin_resep_poli = $request->margin_resep_poli;
            $data->margin_resep_inap = $request->margin_resep_inap;
            $data->margin_bhp = $request->margin_bhp;
            $data->is_fix_admin = $request->is_fix_admin;
            
            $data->jenis_biaya_admin = $request->jenis_biaya_admin;
            
            $data->is_coverage_poli = $request->is_coverage_poli;
            $data->is_coverage_inap = $request->is_coverage_inap;
            $data->is_coverage_penunjang = $request->is_coverage_penunjang;
                        
            $data->nilai_admin = $request->nilai_admin;
            $data->nilai_maks_admin = $request->nilai_maks_admin;
            $data->is_bpjs = $request->is_bpjs;
            
            $data->coa_proses_id = $request->coa_proses_id;
            $data->coa_invoice_id = $request->coa_invoice_id;
            $data->coa_deposit_id = $request->coa_deposit_id;

            $data->coa_proses = $request->coa_proses;
            $data->coa_invoice = $request->coa_invoice;
            $data->coa_deposit = $request->coa_deposit;
            
            $data->is_aktif = 1;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data Penjamin.']);
            }
            return response()->json(['success' => true, 'message' => 'Jenis Penjamin berhasil disimpan.', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Penjamin :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $penjaminId = $request->penjamin_id;
            $data = Penjamin::where('penjamin_id', $penjaminId)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            $bukuHarga = BukuHarga::where('client_id',$this->clientId)->where('is_aktif',1)->where('buku_harga_id',$request->buku_harga_id)->first();
            if(!$bukuHarga) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses penyimpanan data. Buku (kelompok) Harga tidak ditemukan.']);
            }

            $data->jns_penjamin = $request->jns_penjamin;
            $data->penjamin_nama = strtoupper($request->penjamin_nama);
            $data->inisial = strtoupper($request->inisial);
            $data->npwp = $request->npwp;
            $data->narahubung = $request->narahubung;
            $data->no_telp = $request->no_telp;
            $data->email = $request->email;
            $data->alamat = $request->alamat;
            $data->buku_harga_id = $request->buku_harga_id;
            $data->buku_harga = $bukuHarga->buku_nama;

            $data->margin_resep_poli = $request->margin_resep_poli;
            $data->margin_resep_inap = $request->margin_resep_inap;
            $data->margin_bhp = $request->margin_bhp;
            $data->is_fix_admin = $request->is_fix_admin;
            
            $data->is_coverage_poli = $request->is_coverage_poli;
            $data->is_coverage_inap = $request->is_coverage_inap;
            $data->is_coverage_penunjang = $request->is_coverage_penunjang;
            
            $data->jenis_biaya_admin = $request->jenis_biaya_admin;
            
            $data->nilai_admin = $request->nilai_admin;
            $data->nilai_maks_admin = $request->nilai_maks_admin;
            $data->is_aktif = $request->is_aktif;
            $data->is_bpjs = $request->is_bpjs;
            $data->coa_proses_id = $request->coa_proses_id;
            $data->coa_invoice_id = $request->coa_invoice_id;
            $data->coa_deposit_id = $request->coa_deposit_id;

            $data->coa_proses = $request->coa_proses;
            $data->coa_invoice = $request->coa_invoice;
            $data->coa_deposit = $request->coa_deposit;
            
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data Penjamin.']);
            }
            return response()->json(['success' => true, 'message' => 'Pekerjaan berhasil di update.', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data Penjamin :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $data = Penjamin::where('penjamin_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data Penjamin tidak ditemukan.']);
            }

            $success = Penjamin::where('penjamin_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => 'Hari']);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan penjamin']);
            }
            return response()->json(['success' => true, 'message' => 'Data Penjamin berhasil dinonaktifkan']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data Penjamin :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function GetPenjaminId()
    {
        try {
            $id = $this->clientId . '.INS-00001';
            $maxId = Penjamin::withTrashed()->where('penjamin_id', 'LIKE', $this->clientId . '.INS-%')->max('penjamin_id');
            if (!$maxId) {
                $id = $this->clientId . '.INS-00001';
            } else {
                $maxId = str_replace($this->clientId . '.INS-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.INS-0000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.INS-000' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.INS-00' . $count;
                } elseif ($count >= 1000 && $count < 10000) {
                    $id = $this->clientId . '.INS-0' . $count;
                } else {
                    $id = $this->clientId . '.INS-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.INS-' . Uuid::uuid4()->getHex();
        }
    }
}
