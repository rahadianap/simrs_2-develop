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

use Ramsey\Uuid\Uuid;

class InformedConsentController extends Controller
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
                    $rowNumber = InformedConsent::count();
                }
            }

            $list = InformedConsent::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$active)
                ->where(function($q) use ($keyword) {
                    $q->where('template_nama','ILIKE',$keyword);
                })->orderBy('template_nama', 'ASC')
                ->paginate($rowNumber);
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan semua data informed consent', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $id)
    {
        try {
            $data = InformedConsent::where('client_id', $this->clientId)->where('template_id', $id)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $data['items'] = InformedMapping::leftJoin('tb_inform_detail','tb_inform_mapping.inform_detail_id','=','tb_inform_detail.inform_detail_id')
                ->where('tb_inform_mapping.template_id',$id)
                ->where('tb_inform_mapping.client_id',$this->clientId)
                ->where('tb_inform_mapping.is_aktif',true)
                ->select('tb_inform_detail.*','tb_inform_mapping.inform_map_id','tb_inform_mapping.template_id','tb_inform_mapping.is_mandatory')
                ->orderBy('tb_inform_detail.is_tanda_vital','DESC')
                ->get();

            foreach($data->items as $itm) {
                $itm['pilihan_jawaban'] = json_decode($itm['pilihan_jawaban']);
            }
            
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data informed consent', 'error' => $e->getMessage()]);
        }
    }

    public function informData($templateId) {
        try {

        } catch (\Exception $e) {
            return null;
        }
    }

    public function create(Request $request)
    {
        try {
            $prefix = 'IC';
            $idConsent = $this->getInformedID($prefix);

            DB::connection('dbclient')->beginTransaction();
            $informed = new InformedConsent();
            $informed->template_id = $idConsent;
            $informed->template_nama = $request->template_nama;
            $informed->deskripsi = $request->deskripsi;
            $informed->is_aktif = true;
            $informed->client_id = $this->clientId;
            $informed->created_by = Auth::user()->username;
            $informed->updated_by = Auth::user()->username;
            $success = $informed->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data informed consent']);
            }

            foreach($request->items as $itm) {
                $id = $this->clientId.'-'.Uuid::uuid4()->getHex();
                $map = new InformedMapping();
                $map->inform_map_id = $id;
                $map->template_id = $idConsent;
                $map->template_nama = $request->template_nama;
                $map->inform_detail_id = $itm['inform_detail_id'];
                $map->pertanyaan = $itm['pertanyaan'];
                $map->tipe_jawaban = $itm['tipe_jawaban'];
                $map->pilihan_jawaban = json_encode($itm['pilihan_jawaban']);
                $map->image_item = $itm['image_item'];
                $map->is_tanda_vital = $itm['is_tanda_vital'];
                $map->is_aktif = true;
                $map->is_mandatory = $itm['is_mandatory'];
                $map->client_id = $this->clientId;
                $map->created_by = Auth::user()->username;
                $map->updated_by = Auth::user()->username;
                $success = $map->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data detail informed consent']);
                }
            }

            DB::connection('dbclient')->commit();

            $data = InformedConsent::where('client_id', $this->clientId)->where('template_id', $idConsent)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $data['items'] = InformedMapping::leftJoin('tb_inform_detail','tb_inform_mapping.inform_detail_id','=','tb_inform_detail.inform_detail_id')
                ->where('tb_inform_mapping.template_id',$idConsent)
                ->where('tb_inform_mapping.client_id',$this->clientId)
                ->where('tb_inform_mapping.is_aktif',true)
                ->select('tb_inform_detail.*','tb_inform_mapping.inform_map_id','tb_inform_mapping.template_id','tb_inform_mapping.is_mandatory')
                ->get();
            
            return response()->json(['success' => true, 'message' => 'data informed consent berhasil disimpan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan saat menambah informed consent. Error : ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            
            $id = $request->template_id;
            $informed = InformedConsent::where('client_id',$this->clientId)->where('is_aktif',1)->where('template_id',$id)->first();
            if(!$informed) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan']);
            }
            
            DB::connection('dbclient')->beginTransaction();
            $informed->template_nama = $request->template_nama;
            $informed->deskripsi = $request->deskripsi;
            $informed->updated_by = Auth::user()->username;
            $success = $informed->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data informed consent']);
            }
            
            foreach($request->items as $itm) {
                $map = InformedMapping::where('inform_detail_id',$itm['inform_detail_id'])->where('template_id',$id)->first();
                if(!$map) {
                    $idMap = $this->clientId.'-'.Uuid::uuid4()->getHex();
                    $map = new InformedMapping();
                    $map->inform_map_id = $idMap;
                    $map->template_id = $id;
                    $map->inform_detail_id = $itm['inform_detail_id'];
                    $map->client_id = $this->clientId;
                    $map->created_by = Auth::user()->username;
                }
                
                $map->template_nama = $request->template_nama;
                $map->pertanyaan = $itm['pertanyaan'];
                $map->tipe_jawaban = $itm['tipe_jawaban'];
                $map->pilihan_jawaban = json_encode($itm['pilihan_jawaban']);
                $map->image_item = $itm['image_item'];
                $map->is_tanda_vital = $itm['is_tanda_vital'];
                $map->is_aktif = true;
                $map->is_mandatory = $itm['is_mandatory'];
                $map->updated_by = Auth::user()->username;
                $success = $map->save();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data detail informed consent']);
                }
            }
            
            DB::connection('dbclient')->commit();

            $data = InformedConsent::where('client_id', $this->clientId)->where('template_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            $data['items'] = InformedMapping::leftJoin('tb_inform_detail','tb_inform_mapping.inform_detail_id','=','tb_inform_detail.inform_detail_id')
                ->where('tb_inform_mapping.template_id',$id)
                ->where('tb_inform_mapping.client_id',$this->clientId)
                ->where('tb_inform_mapping.is_aktif',true)
                ->select('tb_inform_detail.*','tb_inform_mapping.inform_map_id','tb_inform_mapping.template_id','tb_inform_mapping.is_mandatory')
                ->get();
            
            return response()->json(['success' => true, 'message' => 'data informed consent berhasil disimpan', 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menyimpan informed consent. Error : ' . $e->getMessage()]);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            $data = InformedConsent::where('template_id', $id)->where('is_aktif',1)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus unit. Data unit tidak ditemukan.']);
            }
            
            $mapping = InformedMapping::where('template_id', $id)->where('is_aktif',1)->where('client_id', $this->clientId)->first();
            
            DB::connection('dbclient')->beginTransaction();
            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam memperbaharui data template']);
            }
            
            if($mapping) {
                $success = InformedMapping::where('template_id', $id)->where('is_aktif',1)
                    ->where('client_id', $this->clientId)
                    ->update(['is_aktif'=>false, 'updated_by'=>Auth::user()->username]);

                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam memperbaharui data item template']);
                }
            }

            DB::connection('dbclient')->commit();
            
            return response()->json(['success' => true, 'message' => 'informed consent berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data informed consent gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function deleteItem(Request $request, $id)
    {
        try {
            $data = InformedMapping::where('inform_map_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }

            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data']);
            }
            return response()->json(['success' => true, 'message' => 'informed consent item berhasil dihapus']);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data informed consent item gagal dihapus. Error :' . $e->getMessage()]);
        }
    }

    public function getInformedID($prefix)
    {
        try {
            $id = $this->clientId . '-' . $prefix . '0001';
            $maxId = InformedConsent::withTrashed()->where('client_id', $this->clientId)->where('template_id', 'LIKE', $this->clientId . '-' . $prefix . '%')->max('template_id');
            if (!$maxId) {
                $id = $this->clientId . '-' . $prefix . '0001';
            } else {
                $maxId = str_replace($this->clientId . '-' . $prefix, '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '-' . $prefix . '000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '-' . $prefix . '00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '-' . $prefix . '0' . $count;
                } else {
                    $id = $this->clientId . '-' . $prefix . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getItemID($prefix)
    {
        try {
            $id = $prefix . '-' . $prefix . '0001';
            $maxId = InformedDetail::withTrashed()->where('client_id', $this->clientId)->where('detail_id', 'LIKE', $this->clientId . '-' . $prefix . '%')->max('detail_id');
            if (!$maxId) {
                $id = $this->clientId . '-' . $prefix . '0001';
            } else {
                $maxId = str_replace($this->clientId . '-' . $prefix, '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '-' . $prefix . '000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '-' . $prefix . '00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '-' . $prefix . '0' . $count;
                } else {
                    $id = $this->clientId . '-' . $prefix . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
