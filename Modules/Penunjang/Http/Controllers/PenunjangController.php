<?php

namespace Modules\Penunjang\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PenunjangController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    public function index()
    {
        try {
            $lists = Operasi::where('client_id',$this->clientId)
                ->where('is_aktif','ILIKE',$aktif)
                ->where('status','ILIKE',$status)                
                ->where(function($q) use ($keyword) {
                    $q->where('unit_pengirim_id','ILIKE',$keyword)
                    ->orWhere('unit_pengirim','ILIKE',$keyword)
                    ->orWhere('nama_pasien','ILIKE',$keyword)
                    ->orWhere('no_rm','ILIKE',$keyword)
                    ->orWhere('pasien_id','ILIKE',$keyword);
                })->orderBy('tgl_operasi', 'DESC')->paginate($rowNumber);
    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);   
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam proses menampilkan daftar data', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('penunjang::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('penunjang::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('penunjang::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
