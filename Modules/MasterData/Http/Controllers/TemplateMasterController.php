<?php
namespace Modules\MasterData\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MasterData\Entities\TemplateMaster;
use App\Exports\TemplateMasterExport;
use Modules\MasterData\Entities\Coa;
use Modules\MasterData\Entities\Dtd;
use Modules\MasterData\Entities\Icd9;
use Modules\MasterData\Entities\Icd10;
use Modules\MasterData\Entities\UnitPelayanan;
use Modules\MasterData\Entities\RuangPelayanan;
use Modules\MasterData\Entities\BedPelayanan;
// use Modules\MasterData\Entities\Penjamin;

class TemplateMasterController extends Controller
{
    public $clientId;

    public function __construct(Request $request)
    {
        /*** check apakah header menyertakan client ID atau tidak */
        if (!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function list(Request $request)
    {
        try {
            $perPage = 10;
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
            }
            
            $query = TemplateMaster::query();
            foreach ($request->except('_token') as $key => $data) {
                if ($key !== "page" && $key !== "per_page") {
                    $query = $query->where($key, 'ILIKE', '%' . $data . '%');
                }
            }
            $data = $query->where('client_id', $this->clientId)->paginate($perPage);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ada kesalahan dalam proses menampilkan semua data mapping unit dokter', 'error' => $e->getMessage()]);
        }
    }

    public function exportCoa() 
    {
        try {
            $coa = new Coa;
            // $column = str_replace('_', ' ', $coa->getTableColumns());
            $column = $coa->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_COA.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data CoA. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportDtd() 
    {
        try {
            $dtd = new Dtd;
            // $column = str_replace('_', ' ', $dtd->getTableColumns());
            $column = $dtd->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_DTD.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data DTD. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportICD9() 
    {
        try {
            $icd9 = new Icd9;
            // $column = str_replace('_', ' ', $dtd->getTableColumns());
            $column = $icd9->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_ICD-9.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data DTD. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportICD10() 
    {
        try {
            $icd10 = new Icd10;
            // $column = str_replace('_', ' ', $dtd->getTableColumns());
            $column = $icd10->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_ICD-10.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data DTD. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportUnit() 
    {
        try {
            $unit = new UnitPelayanan;
            // $column = str_replace('_', ' ', $unit->getTableColumns());
            $column = $unit->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_UNIT.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data Unit. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportRuang() 
    {
        try {
            $ruang = new RuangPelayanan;
            // $column = str_replace('_', ' ', $ruang->getTableColumns());
            $column = $ruang->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_RUANG.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data Unit. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function exportBed() 
    {
        try {
            $bed = new BedPelayanan;
            // $column = str_replace('_', ' ', $bed->getTableColumns());
            $column = $bed->getTableColumns();
            $fixColumn = array_splice($column, 0, -5);
            return (new TemplateMasterExport($fixColumn))->download('MASTER_DATA_RUANG.xlsx');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export template master data Unit. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    // public function exportPenjamin() 
    // {
    //     try {
    //         $penjamin = new Penjamin();
    //         $column = str_replace('_', ' ', $penjamin->getTableColumns());
    //         $fixColumn = array_splice($column, 0, -5);
    //         return (new TemplateMasterExport($fixColumn ))->download('MASTER_DATA_PENJAMIN.xlsx');
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Gagal export template master data Penjamin. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
    //     }
    // }

}
