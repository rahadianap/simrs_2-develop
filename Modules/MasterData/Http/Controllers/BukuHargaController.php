<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MasterData\Entities\BukuHarga;
use Illuminate\Support\Facades\Auth;

use Ramsey\Uuid\Uuid;

use Modules\MasterData\Entities\Tindakan;
use Modules\MasterData\Entities\Harga;
use Modules\MasterData\Entities\HargaDetail;
use Modules\MasterData\Entities\HargaLog;
use DB;

class BukuHargaController extends Controller
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
            $perpage = 15;
            $keyword = '%%';
            $aktif = 'true';
            
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            
            if ($request->has('is_aktif')) {
                $aktif = '%' . $request->input('is_aktif') . '%';
            }

            if ($request->has('per_page')) {
                $perpage = $request->input('per_page');
                if($perpage == 'ALL') { $perpage = BukuHarga::where('client_id',$this->clientId)->where('is_aktif',1)->count(); }
            }

            $list = BukuHarga::where('client_id', $this->clientId)->where('is_aktif','ILIKE', $aktif)
            ->where('buku_nama', 'LIKE', $keyword)
            ->orderBy('buku_harga_id', 'DESC')->paginate($perpage);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan list data', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = BukuHarga::where('client_id', $this->clientId)->where('buku_harga_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $id = $this->getBukuId();

            $data = new BukuHarga();
            $data->buku_harga_id = $id;
            $data->buku_nama = strtoupper($request->buku_nama);
            $data->deskripsi = $request->deskripsi;
            $data->is_standar_sistem = $request->is_standar_sistem;
            $data->tgl_berlaku = $request->tgl_berlaku;
            $data->jam_berlaku = $request->jam_berlaku;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data']);
            }
            return response()->json(['success' => true, 'message' => 'data berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menambah bed ruang. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->buku_harga_id;
            $data = BukuHarga::where('is_aktif', 1)->where('client_id', $this->clientId)->where('buku_harga_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data buku harga tidak ditemukan.']);
            }

            $data->deskripsi = $request->deskripsi;
            $data->tgl_berlaku = $request->tgl_berlaku;
            $data->jam_berlaku = $request->jam_berlaku;
            $data->is_standar_sistem = $request->is_standar_sistem;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data']);
            }

            return response()->json(['success' => true, 'message' => 'data berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan bed ruang. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = BukuHarga::where('buku_harga_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }

            /**
             * chek adakah penjamin / asuransi yang menggunakan
             */
            $penjamin = Penjamin::where('is_aktif',1)->where('client_id',$this->clientId)->where('buku_harga_id',$id)->first();
            if ($penjamin) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data masih digunakan.']);
            }

            $success = BukuHarga::where('buku_harga_id', $id)->where('is_aktif', 1)->where('client_id', $this->clientId)
                ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username]);

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menghapus data.']);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function getBukuId()
    {
        try {
            $id = $this->clientId.date('Y').'-PB0001';
            $maxId = BukuHarga::withTrashed()->where('client_id', $this->clientId)->where('buku_harga_id', 'ILIKE', $this->clientId.date('Y').'-PB%')->max('buku_harga_id');
            if (!$maxId) {
                $id = $this->clientId .date('Y'). '-PB0001';
            } else {
                $maxId = str_replace($this->clientId.date('Y').'-PB','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.date('Y').'-PB000'.$count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId.date('Y').'-PB00'.$count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId.date('Y').'-PB0'.$count;
                } else {
                    $id = $this->clientId.date('Y').'-PB'.$count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return 'PB' . date('Ymd') . '-' . Uuid::uuid4()->getHex();
        }
    }


    public function listHargaTindakan(Request $request, $bukuId) {
        try {
            $perpage = 15;
            $keyword = '%%';
            $aktif = 'true';
            
            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            
            if ($request->has('is_aktif')) {
                $aktif = '%' . $request->input('is_aktif') . '%';
            }

            if ($request->has('per_page')) {
                $perpage = $request->input('per_page');
                if($perpage == 'ALL') { $perpage = Tindakan::where('client_id',$this->clientId)->where('is_aktif',1)->count(); }
            }

            $data = Tindakan::where('client_id', $this->clientId)
                ->where('is_aktif','ILIKE', $aktif)
                ->where('tindakan_nama', 'ILIKE', $keyword)
                ->orderBy('tindakan_nama', 'DESC')
                ->paginate($perpage);

            foreach($data->items() as $dt) {
                $listHarga = Harga::where('client_id',$this->clientId)
                    ->where('is_aktif',1)->where('buku_harga_id',$bukuId)
                    ->where('tindakan_id',$dt['tindakan_id'])
                    ->get(); 
                
                foreach($listHarga as  $l) {
                    $komponen = HargaDetail::where('client_id',$this->clientId)->where('is_aktif',1)
                        ->where('harga_id',$l['harga_id'])->get();
                    $l['komponen'] = $komponen;
                }
                $dt['detail'] = $listHarga;
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data gagal ditampilkan. Error :' . $e->getMessage()]);
        }
    }

    public function approve(Request $request, $bukuId)
    {
        try {
            $data = BukuHarga::where('buku_harga_id', $bukuId)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam approve data. Data tidak ditemukan.']);
            }
            
            $prices = Harga::where('is_aktif',1)->where('client_id',$this->clientId)->where('buku_harga_id',$bukuId)->where('is_aktif',1)->where('is_approve',false)->get();
            
            DB::connection('dbclient')->beginTransaction();
            /**update komponen harga*/
            if($prices) {
                foreach($prices as $hrg) {
                    if($hrg['is_approve'] == 0) {
                        /**buat log harga */
                        $log = new HargaLog();
                        $log->harga_log_id = $this->clientId.'-LOG'.Uuid::uuid4()->getHex();
                        $log->harga_id = $hrg['harga_id'];
                        $log->buku_harga_id = $hrg['buku_harga_id'];
                        $log->buku_nama = $hrg['buku_nama'];
                        $log->tindakan_id = $hrg['tindakan_id'];
                        $log->tindakan_nama = $hrg['tindakan_nama'];                    
                        $log->kelas_id = $hrg['kelas_id'];
                        $log->kelas_nama = $hrg['kelas_nama'];
                        $log->nilai_lama = $hrg['nilai'];
                        $log->nilai = $hrg['nilai_rencana'];
                        $log->client_id = $this->clientId;
                        $log->created_by = Auth::user()->username;
                        $log->updated_by = Auth::user()->username;
                        $success = $log->save();
                        if (!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam membuat log harga.']);
                        }

                        /**update harga */
                        $success = Harga::where('harga_id', $hrg['harga_id'])
                            ->where('is_approve', 0)
                            ->where('is_aktif', 1)
                            ->where('client_id', $this->clientId)
                            ->update([
                                'is_approve'=>true, 
                                'nilai'=> $hrg['nilai_rencana'], 
                                'status'=>'DISETUJUI',
                                'approver'=>Auth::user()->username, 
                                'updated_by'=>Auth::user()->username
                            ]);

                        if (!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data.']);
                        }

                        //**update komponen harga */

                        $komponens = HargaDetail::where('harga_id', $hrg['harga_id'])
                            ->where('is_aktif', 1)
                            ->where('client_id', $this->clientId)->get();
                        
                        if($komponens) {
                            foreach($komponens as $komp) {
                                $success = HargaDetail::where('harga_id', $hrg['harga_id'])
                                    ->where('client_id', $this->clientId)
                                    ->where('komp_harga_id',$komp['komp_harga_id'])
                                    ->where('is_aktif', 1)
                                    ->update(['nilai'=>$komp['nilai_rencana'], 'status'=>'DISETUJUI' , 'updated_by'=>Auth::user()->username]);
        
                                if (!$success) {
                                    DB::connection('dbclient')->rollBack();
                                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah detail komponen data.']);
                                }
                            }
                        }
                    }
                }
            }

            DB::connection('dbclient')->commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil diubah']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data gagal diubah. Error :' . $e->getMessage()]);
        }
    }
}