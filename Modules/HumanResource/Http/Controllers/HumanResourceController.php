<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HumanResourceController extends Controller
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

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan daftar. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function data(Request $request,$jabatanId)
    {
        try {

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menampilkan data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengubah data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $jabatanId)
    {
        try {

        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getJabatanId()
    {
        try {
            $id = $this->clientId.'.POS-0001';
            $maxId = Jabatan::withTrashed()->where('client_id', $this->clientId)->where('jabatan_id','LIKE',$this->clientId.'.POS-%')->max('jabatan_id');
            if (!$maxId) { $id = $this->clientId.'.POS-0001'; }
            else {
                $maxId = str_replace($this->clientId.'.POS-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = $this->clientId.'.POS-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = $this->clientId.'.POS-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = $this->clientId.'.POS-0'.$count; } 
                else { $id = $this->clientId.'.POS-'.$count; } 
            }
            return $id;
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
