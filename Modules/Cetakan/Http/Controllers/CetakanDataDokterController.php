<?php

namespace Modules\Cetakan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\MasterData\Entities\Dokter;

class CetakanDataDokterController extends Controller
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

    public function dataDokter(Request $request, $dokterId)
    {
        try {
            $data = Dokter::where('client_id', $this->clientId)->where('dokter_id', $dokterId)->first();

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data dokter : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
