<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use Carbon\Carbon;
use Modules\Pendaftaran\Entities\Pendaftaran;

class ReportCetakanController extends Controller
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


    public function cetakanRingkasanMasukKeluar(){
        $datas = [
            
        ];

        return view('cetakan/cetakanRingkasanMasukKeluar',  compact('datas'));
    }

    // public function cetakanFormPenempatanKelasRi(){
    //     $datas = [
            
    //     ];

    //     return view('cetakan/cetakanFormPenempatanKelasRi',  compact('datas'));
    // }

    public function cetakanPendaftaranRajal(){
        $datas = [
           
        ];

        return view('cetakan/cetakanPendaftaranRajal',  compact('datas'));
    }

     public function terbilang($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->terbilang($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->terbilang($nilai/10)." puluh". $this->terbilang($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->terbilang($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->terbilang($nilai/100) . " ratus" . $this->terbilang($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->terbilang($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->terbilang($nilai/1000) . " ribu" . $this->terbilang($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->terbilang($nilai/1000000) . " juta" . $this->terbilang($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->terbilang($nilai/1000000000) . " milyar" . $this->terbilang(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->terbilang($nilai/1000000000000) . " trilyun" . $this->terbilang(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
}
