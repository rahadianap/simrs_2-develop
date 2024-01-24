<?php

namespace Modules\MasterData\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Modules\ManajemenUser\Entities\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\MasterData\Entities\Vendor;
use Ramsey\Uuid\Uuid;


class MasterVendorController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request)
    {
        try {
            $rowNumber = 10;
            $active = '%true%';
            $nama =  '%%';
            if($request->has('per_page')){
                $rowNumber = $request->per_page;
                if($rowNumber == 'ALL') { $rowNumber = Vendor::where('client_id',$this->clientId)->count(); }
            }
            if($request->has('is_aktif')){
                $active = '%'.$request->is_aktif.'%';
            }
            if($request->has('vendor_nama')){
                $nama = '%'.$request->vendor_nama.'%';
            }
            $data = Vendor::where('client_id', $this->clientId)
                    ->where('is_aktif','ILIKE', $active)
                    ->where('vendor_nama','ILIKE', $nama)
                    ->paginate($rowNumber);

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);

        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan daftar Vendor. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getVendorId();
            $data = new Vendor();

            $data->vendor_id = $id;
            $data->vendor_nama = strtoupper($request->vendor_nama);
            $data->inisial = strtoupper($request->inisial);
            $data->alamat = $request->alamat;
            $data->npwp = $request->npwp;
            $data->telepon = $request->telepon;
            $data->email = $request->email;
            $data->narahubung = $request->narahubung;
            $data->no_kontrak = $request->no_kontrak;
            $data->tgl_kontrak = $request->tgl_kontrak;
            $data->tgl_akhir_kontrak = $request->tgl_akhir_kontrak;
            $data->status = $request->status;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $data->save();

            return response()->json(['success' => true, 'message'  => 'Berhasil simpan data ' . $data->vendor_nama, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menyimpan data Vendor. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request, $id)
    {
        try {
            $data = Vendor::where('client_id', $this->clientId)->find($id);

            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Data tidak ditemukan.']);
            }

            return response()->json(['success' => true, 'message'  => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Menampilkan data Vendor. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->vendor_id;
            $data = Vendor::where('client_id', $this->clientId)->find($id);
            if(!$data) {
                return response()->json(['success' => false, 'message'  => 'Data tidak ditemukan.']);
            }

            $data->vendor_nama = strtoupper($request->vendor_nama);
            $data->inisial = strtoupper($request->inisial);
            $data->alamat = $request->alamat;
            $data->npwp = $request->npwp;
            $data->telepon = $request->telepon;
            $data->email = $request->email;
            $data->narahubung = $request->narahubung;
            $data->no_kontrak = $request->no_kontrak;
            $data->tgl_kontrak = $request->tgl_kontrak;
            $data->tgl_akhir_kontrak = $request->tgl_akhir_kontrak;
            $data->status = $request->status;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;

            $data->save();

            if (!$data) {
                return response()->json(['success' => false, 'message'  => 'Ada kesalahan dalam penyimpanan data.']);
            }

            return response()->json(['success' => true, 'message'  => 'Berhasil Update data ' . $data->vendor_nama, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Update data Vendor. Error Desc: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $success = Vendor::where('client_id', $this->clientId)->where('vendor_id', $id)->update([
                'is_aktif' => '0',
                'updated_by' => Auth::user()->username,
            ]);

            if (!$success) {
                return response()->json(['success' => false, 'message'  => 'Ada kesalahan dalam menonaktifkan pemasok']);
            }

            return response()->json(['success' => true, 'message'  => 'Berhasil Hapus data ']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal Hapus data Vendor. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }


    public function getVendorId()
    {
        try {
            $id = $this->clientId.'.VND' . date('Y') . '-0001';
            $maxId = Vendor::withTrashed()->where('vendor_id', 'LIKE', $this->clientId.'.VND' . date('Y') . '-%')->max('vendor_id');
            if (!$maxId) {
                $id = $this->clientId.'.VND' . date('Y') . '-0001';
            } else {
                $maxId = str_replace($this->clientId.'.VND' . date('Y') . '-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.'.VND' . date('Y') . '-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId.'.VND' . date('Y') . '-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId.'.VND' . date('Y') . '-0' . $count;
                } else {
                    $id = $this->clientId.'.VND' . date('Y') . '-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId.'.VND' . date('Y') . '-' . Uuid::uuid4()->getHex();
        }
    }
}
