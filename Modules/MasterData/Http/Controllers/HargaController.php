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


class HargaController extends Controller
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
    
    public function lists(Request $request, $bukuId = null)
    {
        try {
            $perpage = 15;
            $keyword = '%%';
            $aktif = 'true';
            $buku = '%%';
            
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

            if($bukuId) {
                $buku = '%'.$bukuId.'%';
            }

            $data = Tindakan::where('client_id', $this->clientId)
                ->where('is_aktif','ILIKE', $aktif)
                ->where('tindakan_nama', 'ILIKE', $keyword)
                ->orderBy('tindakan_nama', 'DESC')
                ->paginate($perpage);

            foreach($data->items() as $dt) {
                $listHarga = Harga::where('client_id',$this->clientId)
                    ->where('is_aktif',1)
                    ->where('buku_harga_id','ILIKE',$buku)
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

    public function data(request $request, $id)
    {
        try {
            $data = $this->getData($id);
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data', 'error' => $e->getMessage()]);
        }
    }

    public function getData($hargaId) {
        try {
            $data = Harga::where('client_id', $this->clientId)->where('harga_id', $hargaId)->first();
            if (!$data) { return null; }

            $data['detail'] = HargaDetail::where('client_id',$this->clientId)->where('harga_id',$hargaId)->where('is_aktif',1)->get();
            return $data;
        } 
        catch (\Exception $e) { return null; }
    }

    public function create(Request $request)
    {
        try {
            if($request->nilai_rencana <= 0) {
                return response()->json(['success' => false, 'message' => 'Nilai tidak boleh nol atau kurang dari nol']);
            }
            
            $isDetailExist = false; 
            foreach($request->detail as $dt) {
                if($dt['nilai_rencana'] > 0) { $isDetailExist = true; }
            }
            
            if(!$isDetailExist) {
                return response()->json(['success' => false, 'message' => 'Nilai detail tidak boleh nol atau kurang dari nol semua.']);
            }

            $id = $this->getHargaId();
            DB::connection('dbclient')->beginTransaction();
            $data = new Harga();
            $data->harga_id = $id;
            $data->buku_harga_id = $request->buku_harga_id;
            $data->buku_nama = $request->buku_nama;
            $data->tindakan_id = $request->tindakan_id;
            $data->tindakan_nama = $request->tindakan_nama;
            $data->kelas_id = $request->kelas_id;
            $data->kelas_nama = $request->kelas_nama;            
            $data->nilai = 0;
            $data->nilai_rencana = $request->nilai_rencana;            
            $data->is_aktif = true;
            $data->is_approve = false;            
            $data->status = 'DRAFT';
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data.']);
            }
            
            foreach($request->detail as $dtl) {
                if($dtl['nilai'] > 0 || $dtl['nilai_rencana'] > 0) {
                    $detail = new HargaDetail();
                    $detail->harga_id = $id;
                    $detail->komp_harga_id = $dtl['komp_harga_id'];
                    $detail->komp_harga = $dtl['komp_harga'];
                    $detail->nilai = 0;
                    $detail->nilai_rencana = $dtl['nilai_rencana'];
                    $detail->is_diskon = $dtl['is_diskon'];
                    $detail->is_ubah_manual = false;
                    $detail->is_aktif = true;
                    $detail->status = 'DRAFT';
                    $detail->client_id = $this->clientId;
                    $detail->created_by = Auth::user()->username;
                    $detail->updated_by = Auth::user()->username;
                    $success = $detail->save();
    
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan detail komponen harga.']);
                    }
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getData($id);
            return response()->json(['success' => true, 'message' => 'data berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat simpan data. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            //return response()->json(['success' => false, 'message' => 'Disini']);
            if($request->nilai_rencana <= 0) {
                return response()->json(['success' => false, 'message' => 'Nilai tidak boleh nol atau kurang dari nol']);
            }
            
            $isDetailExist = false; 
            foreach($request->detail as $dt) {
                if($dt['nilai_rencana'] > 0) { $isDetailExist = true; }
            }
            
            if(!$isDetailExist) {
                return response()->json(['success' => false, 'message' => 'Nilai detail tidak boleh nol atau kurang dari nol semua.']);
            }

            $id = $request->harga_id;
            $data = Harga::where('is_aktif', 1)->where('client_id', $this->clientId)->where('harga_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data harga tidak ditemukan.']);
            }
            DB::connection('dbclient')->beginTransaction();

            $data->nilai_rencana = $request->nilai_rencana;
            $data->is_approve = false;
            $data->is_aktif = $request->is_aktif;
            $data->updated_by = Auth::user()->username;

            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam ubah data']);
            }

            foreach($request->detail as $dtl) {
                if($dtl['nilai'] > 0 || $dtl['nilai_rencana'] > 0) {
                    $stat = "UBAH";
                    $detail = HargaDetail::where('harga_id',$id)->where('komp_harga_id',$dtl['komp_harga_id'])->first();
                    if(!$detail) {
                        $stat = "DRAFT";
                        $detail = new HargaDetail();
                        $detail->harga_id = $id;
                        $detail->komp_harga_id = $dtl['komp_harga_id'];
                        $detail->komp_harga = $dtl['komp_harga'];
                        $detail->client_id = $this->clientId;
                        $detail->created_by = Auth::user()->username;
                    }
                    $detail->status = $stat;                    
                    $detail->nilai_rencana = $dtl['nilai_rencana'];
                    $detail->is_diskon = $dtl['is_diskon'];
                    $detail->is_ubah_manual = false;
                    $detail->is_aktif = $dtl['is_aktif'];
                    $detail->updated_by = Auth::user()->username;
                    $success = $detail->save();
    
                    if (!$success) {
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success' => false, 'message' => 'ada kesalahan dalam ubah detail komponen harga.']);
                    }
                }
            }

            DB::connection('dbclient')->commit();
            $data = $this->getData($id);
            return response()->json(['success' => true, 'message' => 'data berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan data. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Harga::where('harga_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }
            
            $detail = HargaDetail::where('is_aktif',1)->where('client_id',$this->clientId)->where('harga_id',$id)->where('is_aktif',1)->first();
            
            DB::connection('dbclient')->beginTransaction();

            $success = Harga::where('harga_id', $id)->where('is_aktif', 1)->where('client_id', $this->clientId)
                ->update(['is_aktif'=>false, 'status'=>'HAPUS' ,'updated_by'=>Auth::user()->username]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menghapus data.']);
            }

            if($detail) {
                $success = HargaDetail::where('harga_id', $id)->where('is_aktif', 1)->where('client_id', $this->clientId)
                    ->update(['is_aktif'=>false, 'status'=>'HAPUS' , 'updated_by'=>Auth::user()->username]);

                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam menghapus detail komponen data.']);
                }
            }

            DB::connection('dbclient')->commit();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $data = Harga::where('harga_id', $id)->where('client_id', $this->clientId)->where('is_aktif',1)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam approve data. Data tidak ditemukan.']);
            }
            
            $detail = HargaDetail::where('is_aktif',1)->where('client_id',$this->clientId)->where('harga_id',$id)->where('is_aktif',1)->get();
            
            DB::connection('dbclient')->beginTransaction();

            /**buat log harga */
            $log = new HargaLog();
            $log->harga_log_id = $this->clientId.'-LOG'.Uuid::uuid4()->getHex();
            $log->harga_id = $data->harga_id;
            $log->buku_harga_id = $data->buku_harga_id;
            $log->buku_nama = $data->buku_nama;
            $log->tindakan_id = $data->tindakan_id;
            $log->tindakan_nama = $data->tindakan_nama;                    
            $log->kelas_id = $data->kelas_id;
            $log->kelas_nama = $data->kelas_nama;
            $log->nilai_lama = $data->nilai;
            $log->nilai = $data->nilai_rencana;
            $log->client_id = $this->clientId;
            $log->created_by = Auth::user()->username;
            $log->updated_by = Auth::user()->username;
            $success = $log->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah detail komponen data.']);
            }
        
            /**update harga */
            $success = Harga::where('harga_id', $id)->where('is_aktif', 1)->where('client_id', $this->clientId)
                ->update(['is_approve'=>true, 'nilai'=> $data->nilai_rencana, 'status'=>'DISETUJUI' ,'approver'=>Auth::user()->username, 'updated_by'=>Auth::user()->username]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah data.']);
            }

            /**update komponen harga*/
            if($detail) {
                foreach($detail as $dt) {
                    if($dt['nilai'] > 0 || $dt['nilai_rencana'] > 0) {
                        $success = HargaDetail::where('harga_id', $id)->where('komp_harga_id', $dt['komp_harga_id'])
                            ->where('is_aktif', 1)
                            ->where('client_id', $this->clientId)
                            ->update(['nilai'=>$dt['nilai_rencana'], 'status'=>'DISETUJUI' , 'updated_by'=>Auth::user()->username]);

                        if (!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam mengubah detail komponen data.']);
                        }
                    }
                }
            }

            DB::connection('dbclient')->commit();

            $data = $this->getData($id);
            return response()->json(['success' => true, 'message' => 'Data berhasil disetujui', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data gagal disetujui. Error :' . $e->getMessage()]);
        }
    }

    public function getHargaId()
    {
        try {
            $id = $this->clientId.date('Ymd').'-HRG0001';
            $maxId = Harga::withTrashed()->where('client_id', $this->clientId)->where('harga_id', 'ILIKE', $this->clientId.date('Ymd').'-HRG%')->max('harga_id');
            if (!$maxId) {
                $id = $this->clientId .date('Ymd'). '-HRG0001';
            } else {
                $maxId = str_replace($this->clientId.date('Ymd').'-HRG','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId.date('Ymd').'-HRG000'.$count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId.date('Ymd').'-HRG00'.$count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId.date('Ymd').'-HRG0'.$count;
                } else {
                    $id = $this->clientId.date('Ymd').'-HRG'.$count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return 'HRG' . date('Ymd') . '-' . Uuid::uuid4()->getHex();
        }
    }

}