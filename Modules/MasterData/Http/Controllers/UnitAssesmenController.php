<?php

namespace Modules\MasterData\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Modules\MasterData\Entities\UnitPelayanan;

use Modules\MasterData\Entities\InformedConsent;
use Modules\MasterData\Entities\InformedDetail;
use Modules\MasterData\Entities\InformedMapping;
use Modules\MasterData\Entities\UnitAssesmen;

class UnitAssesmenController extends Controller
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
            $keyword = '%%';
            $perPage = 20;

            if ($request->has('keyword')) {
                $keyword = '%' . $request->input('keyword') . '%';
            }
            
            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                if($perPage == 'ALL') { $perPage = UnitPelayanan::where('client_id',$this->clientId)->count(); }
            }   

            $list = UnitPelayanan::where('client_id', $this->clientId)            
                ->where('is_aktif', true)
                ->where(function($q) use ($keyword) {
                    $q->where('unit_nama', 'ILIKE', $keyword)
                    ->orWhere('unit_id','ILIKE',$keyword);
                })
                ->orderBy('unit_id', 'ASC')->paginate($perPage);
                
            foreach($list->items() as $item) {
                $item['inform'] = UnitAssesmen::leftJoin('tb_inform_template','tb_inform_template.template_id','=','tb_unit_assesmen.template_id')
                    ->where('tb_unit_assesmen.is_aktif',1)
                    ->where('tb_inform_template.is_aktif',1)
                    ->where('tb_unit_assesmen.client_id',$this->clientId)
                    ->where('tb_unit_assesmen.unit_id',$item['unit_id'])
                    ->select(
                        'tb_unit_assesmen.unit_template_id',
                        'tb_inform_template.template_id',
                        'tb_inform_template.template_nama',
                        'tb_inform_template.deskripsi'
                    )->get();
            }

            return response()->json(['success' => true, 'message' => 'OK', 'data' => $list]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan semua data unit pelayanan', 'error' => $e->getMessage()]);
        }
    }


    public function data(request $request, $unitId)
    {
        try {
            $data = UnitAssesmen::leftJoin('tb_inform_template','tb_inform_template.template_id','=','tb_unit_assesmen.template_id')
                ->where('tb_unit_assesmen.is_aktif',1)
                ->where('tb_inform_template.is_aktif',1)
                ->where('tb_unit_assesmen.unit_id',$unitId)
                ->where('tb_unit_assesmen.client_id',$this->clientId)
                ->select(
                    'tb_inform_template.template_id',
                    'tb_inform_template.template_nama',
                    'tb_inform_template.deskripsi'
                )->get();
            
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'data tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data unit pelayanan', 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = UnitAssesmen::where('is_aktif',1)->where('client_id',$this->clientId)->where('unit_id',$request->unit_id)->where('template_id',$request->template_id)->first();
            if($data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan mapping assesmen. Data sudah ada.']);
            }

            $data = new UnitAssesmen();
            $data->unit_template_id = $this->clientId.'-'.Uuid::uuid4()->getHex();
            $data->template_id = $request->template_id;
            $data->unit_id = $request->unit_id;
            $data->template_nama = $request->template_nama;
            $data->deskripsi = $request->deskripsi;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan mapping assesmen']);
            }
            
            return response()->json(['success' => true, 'message' => 'mapping unit assesmen berhasil', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan saat menambah assesmen. Error : ' . $e->getMessage()]);
        }
    }

    
    public function delete(Request $request, $id)
    {
        try {
            $data = UnitAssesmen::where('unit_template_id', $id)->where('client_id', $this->clientId)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data. Data tidak ditemukan.']);
            }

            $data->updated_by = Auth::user()->username;
            $data->is_aktif = 0;
            $success = $data->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menghapus data.']);
            }

            return response()->json(['success' => true, 'message' => 'data berhasil dinonaktifkan', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'data gagal dinonaktifkan. Error :' . $e->getMessage()]);
        }
    }

}
