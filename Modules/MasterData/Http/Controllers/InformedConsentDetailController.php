<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\MasterData\Entities\InformedConsent;
use Modules\MasterData\Entities\InformedDetail;
use Modules\MasterData\Entities\InformedMapping;
use DB;

class InformedConsentDetailController extends Controller
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

            $keyword = '%%';
            $rowNumber = 10;
            $active = 'true';

            if($request->has('keyword')) {
                $keyword = $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = InformedDetail::count();
                }
            }

            $list = InformedDetail::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$active)
                ->where(function($q) use ($keyword) {
                    $q->where('pertanyaan','ILIKE',$keyword)
                    ->orWhere('inform_detail_id','ILIKE',$keyword);
                })->orderBy('inform_detail_id', 'ASC')
                ->paginate($rowNumber);

            foreach($list->items() as $itm) {
                $itm['pilihan_jawaban'] = json_decode($itm['pilihan_jawaban']);
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan semua master pertanyaan', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = InformedDetail::where('client_id', $this->clientId)->where('inform_detail_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $data['pilihan_jawaban'] = json_decode($data->pilihan_jawaban);

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data informed consent', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $idDetail = $this->getDetailID();

            $detail = new InformedDetail();
            $detail->inform_detail_id = $idDetail;
            $detail->pertanyaan = $request->pertanyaan;
            $detail->tipe_jawaban = $request->tipe_jawaban;
            $detail->pilihan_jawaban = json_encode($request->pilihan_jawaban);
            $detail->deskripsi = $request->deskripsi;            
            $detail->image_item = $request->image_item;
            $detail->is_tanda_vital = $request->is_tanda_vital;
            $detail->is_mandatory = $request->is_mandatory;
            $detail->satuan = $request->satuan;
            $detail->is_aktif = 1;
            $detail->client_id = $this->clientId;
            $detail->created_by = Auth::user()->username;
            $detail->updated_by = Auth::user()->username;
            
            $success = $detail->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan detail informed consent']);
            }

            return response()->json(['success' => true, 'message' => 'data berhasil disimpan', 'data' => $detail]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menambah data. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = InformedDetail::where('client_id', $this->clientId)->where('inform_detail_id', $request->inform_detail_id)
                ->where('is_aktif',true)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data informed consent tidak ditemukan.']);
            }

            $data->pertanyaan = $request->pertanyaan;
            $data->tipe_jawaban = $request->tipe_jawaban;
            $data->pilihan_jawaban = $request->pilihan_jawaban;
            $data->deskripsi = $request->deskripsi;
            $data->image_item = $request->image_item;
            $data->is_tanda_vital = $request->is_tanda_vital;
            $data->is_mandatory = $request->is_mandatory;
            $data->satuan = $request->satuan;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengubah detail informed consent']);
            }

            return response()->json(['success' => true, 'message' => 'data informed consent berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan informed consent. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = InformedDetail::where('inform_detail_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data']);
            }
            return response()->json(['success' => true, 'message' => 'informed consent detail berhasil dihapus']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data informed consent detail gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    

    public function getInformedID($clientId, $prefix)
    {
        try {
            $id = $clientId . '-' . $prefix . '0001';
            $maxId = InformedConsent::withTrashed()->where('client_id', $clientId)->where('template_id', 'LIKE', $clientId . '-' . $prefix . '%')->max('template_id');
            if (!$maxId) {
                $id = $clientId . '-' . $prefix . '0001';
            } else {
                $maxId = str_replace($clientId . '-' . $prefix, '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $clientId . '-' . $prefix . '000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $clientId . '-' . $prefix . '00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $clientId . '-' . $prefix . '0' . $count;
                } else {
                    $id = $clientId . '-' . $prefix . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return 'IC' . date('Y.m.d') . '-' . Uuid::uuid4()->getHex();
        }
    }

    public function getDetailID()
    {
        try {
            $id = $this->clientId . '-CD0001';
            $maxId = InformedDetail::withTrashed()->where('client_id', $this->clientId)
                ->where('inform_detail_id', 'LIKE', $this->clientId . '-CD%')->max('inform_detail_id');
            if (!$maxId) {
                $id = $this->clientId . '-CD0001';
            } else {
                $maxId = str_replace($this->clientId . '-CD','', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '-CD000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '-CD00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '-CD0' . $count;
                } else {
                    $id = $this->clientId . '-CD' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
