<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class TemplateMasterExport implements FromView
{
    use Exportable;
    public $list;
    public function __construct($data){
        $this->list = $data;
    }

    public function view(): View
    {
        try {
            return view('templatemaster/templateMaster', [
                'list' => $this->list
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Template tidak ditemukan. Error Message: ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
