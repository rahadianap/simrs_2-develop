<?php

namespace Modules\Radiologi\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon;


use Modules\Radiologi\Entities\RadiologiTemplate;

class RadTemplateController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function lists(request $request)
    {
         try {           
            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            $aktif = 'true';
            if($request->has('is_aktif')) {
                $aktif = '%'.$request->input('is_aktif').'%';
            }

            $rowNumber = 20;
            if($request->has('per_page')) {
                $rowNumber = $request->input('per_page');
                if($rowNumber == 'ALL') { 
                    $rowNumber = RadiologiTemplate::count();
                }
            }
            
            $lists = RadiologiTemplate::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('rad_template_nama','ILIKE',$keyword)
                ->orderBy('created_at','ASC')->paginate($rowNumber);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);    

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan data template radiologi', 'error' => $e->getMessage()]);
        }
    }

    public function data(request $request, $templateId)
    {
        try {
            $data = RadiologiTemplate::where('client_id', $this->clientId)
                ->where('is_aktif',1)->where('rad_template_id', $templateId)
                ->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data template radiologi tidak ditemukan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan data template radiologi', 'error' => $e->getMessage()]);
        }
    }


    public function create(request $request)
    {
        try {
            $templateId = $this->getTemplateId();
            
            $data = new RadiologiTemplate();
            $data->rad_template_id = $templateId;
            $data->rad_template_nama = $request->rad_template_nama;
            $data->kesan = $request->kesan;
            $data->uraian = $request->uraian;
            $data->deskripsi = $request->deskripsi;
            $data->catatan = $request->catatan;
            $data->is_aktif = true;
            $data->client_id = $this->clientId;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'Data template radiologi tidak berhasil disimpan.']);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menyimpan data template radiologi', 'error' => $e->getMessage()]);
        }
    }

    public function update(request $request)
    {
        try {
            $templateId = $request->rad_template_id;
            
            $data = RadiologiTemplate::where('client_id',$this->clientId)->where('is_aktif',1)->where('rad_template_id',$templateId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data template radiologi tidak ditemukan.']);
            }

            $data->rad_template_nama = $request->rad_template_nama;
            $data->kesan = $request->kesan;
            $data->uraian = $request->uraian;
            $data->deskripsi = $request->deskripsi;
            $data->catatan = $request->catatan;
            $data->updated_by = Auth::user()->username;            
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'Data template radiologi tidak berhasil diubah.']);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil diubah', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses mengubah data template radiologi', 'error' => $e->getMessage()]);
        }
    }

    public function getTemplateId(){
        try {
            $id = $this->clientId.'.'.date('Ym').'-RAT0001';
            $maxId = RadiologiTemplate::withTrashed()->where('client_id', $this->clientId)
                ->where('rad_template_id', 'ILIKE', $this->clientId.'.'.date('Ym').'-RAT%')
                ->max('rad_template_id');
                
            if (!$maxId) {
                $id = $this->clientId.'.'.date('Ym').'-RAT0001';
            } 
            else {
                $maxId = str_replace($this->clientId.'.'.date('Ym').'-RAT','', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = $this->clientId.'.'.date('Ym').'-RAT000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.'.date('Ym').'-RAT00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.'.date('Ym').'-RAT0' . $count; } 
                else { $id = $this->clientId.'.'.date('Ym').'-RAT'. $count; }
            }
            return $id;
        }
        catch(\Exception $e){
            return null;
        }
    }


    public function delete(request $request,$templateId)
    {
        try {
            $data = RadiologiTemplate::where('client_id',$this->clientId)->where('is_aktif',1)->where('rad_template_id',$templateId)->first();
            if(!$data) {
                return response()->json(['success' => false, 'message' => 'Data template radiologi tidak ditemukan.']);
            }

            $data->is_aktif = false;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'Data template radiologi tidak berhasil dihapus.']);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menghapus data template radiologi', 'error' => $e->getMessage()]);
        }
    }
    

}
