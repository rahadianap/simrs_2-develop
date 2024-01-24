<?php

namespace Modules\ManajemenUser\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

use Ramsey\Uuid\Uuid;
use Modules\ManajemenUser\Entities\ClientMember;
use Modules\ManajemenUser\Entities\Role;
use Modules\ManajemenUser\Entities\MemberUnit;
use Modules\ManajemenUser\Entities\MemberDepo;
use Modules\MasterData\Entities\DepoUnit;
use App\Models\UserProfile;
use App\Models\User;

class ManajemenUserController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    /** Display a listing of the resource. */
    public function lists(Request $request)
    {
        try {
            $keyword = '%%';
            $active = 'true';
            $per_page = '10';
            
            if($request->has('per_page')) {
                $per_page = $request->input('per_page');
            }

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }

            $data = ClientMember::where('tb_client_member.client_id',$this->clientId)
                    ->where('tb_client_member.is_aktif','ILIKE',$active)
                    ->leftJoin('users','tb_client_member.user_id','=','users.user_id')
                    ->leftJoin('tb_user_profile','tb_client_member.user_id','=','tb_user_profile.user_id')
                    ->where(function($q) use ($keyword) {
                        $q->where('users.email','ILIKE',$keyword)
                        ->orWhere('users.username','ILIKE',$keyword)
                        ->orWhere('tb_user_profile.nama_lengkap','ILIKE',$keyword)
                        ->orWhere('tb_client_member.user_id','ILIKE',$keyword);
                    })
                    ->select(
                        'tb_client_member.tgl_gabung',
                        'users.username',
                        'users.email',
                        'tb_client_member.role_id',
                        'tb_client_member.is_aktif',
                        'tb_client_member.is_admin',
                        'tb_user_profile.nama_lengkap',
                        'tb_user_profile.tempat_lahir',
                        'tb_user_profile.tanggal_lahir',
                        'tb_user_profile.no_telepon',
                        'tb_user_profile.bio_singkat',
                        'users.avatar'
                    )
                    ->orderBy('users.username', 'ASC')->paginate($per_page);
            
            foreach($data->items() as $itm) {
                $role = Role::where('role_id',$itm['role_id'])->where('client_id',$this->clientId)->where('is_aktif',1)->first();
                if($role) {
                    $itm['role_nama'] = $role->role_nama;
                }
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function create(Request $request)
    {
        try {            
            //$id = 'USR'.date('YmdHis').Uuid::uuid4()->getHex();

            $user = User::where('email',$request->email)->first();
            if(!$user) {
                return response()->json(['success'=>false,'message'=>'data pengguna tidak ditemukan.']);
            }

            $id = $user->user_id;

            DB::connection('dbcentral')->beginTransaction();
            DB::connection('dbclient')->beginTransaction();
            /**
             * create member client
             */
            $member = new ClientMember();
            $member->user_id = $id;
            $member->client_id = $this->clientId;
            $member->role_id = $request->role_id;
            $member->tipe_member = 'MEMBER';
            $member->tgl_gabung = date('Y-m-d H:i:s');
            $member->is_admin = false;
            $member->is_undang_gabung = false;
            $member->is_aktif = true;
            $member->created_by = Auth::user()->username;
            $member->updated_by = Auth::user()->username;
            
            $success = $member->save();
            
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'data pengguna gagal disimpan.']);
            }

            /**
             * update member depo
             */
            foreach($request->depos as $dp) {
                $depo = MemberDepo::where('client_id',$this->clientId)->where('depo_id',$dp['depo_id'])->where('is_aktif',1)->first();
                if(!$depo) {
                    $depo = new MemberDepo();
                    $depo->member_depo_id = $this->clientId.Uuid::uuid4()->getHex();
                    $depo->user_id = $id;
                    $depo->depo_id = $dp['depo_id'];
                    $depo->client_id = $this->clientId;
                    $depo->depo_nama = $dp['depo_nama'];
                    $depo->is_aktif = true;
                    $depo->created_by = Auth::user()->username;
                    $depo->updated_by = Auth::user()->username;
                }
                else {
                    $depo->is_aktif = $dp['is_aktif']; 
                    $depo->updated_by = Auth::user()->username;
                }
                $success = $depo->save();
                if(!$success) {
                    DB::connection('dbcentral')->rollBack();
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data depo pengguna gagal disimpan.']);
                }
            }

            /**
             * update member unit
             */
            foreach($request->units as $dt) {
                $unit = MemberUnit::where('client_id',$this->clientId)->where('unit_id',$dt['unit_id'])->where('is_aktif',1)->first();
                if(!$unit) {
                    $unit = new MemberUnit();
                    $unit->member_unit_id = $this->clientId.Uuid::uuid4()->getHex();
                    $unit->user_id = $id;
                    $unit->unit_id = $dp['unit_id'];
                    $unit->client_id = $this->clientId;
                    $unit->unit_nama = $dp['unit_nama'];
                    $unit->is_aktif = true;
                    $unit->created_by = Auth::user()->username;
                    $unit->updated_by = Auth::user()->username;
                }
                else {
                    $unit->is_aktif = $dp['is_aktif']; 
                    $unit->updated_by = Auth::user()->username;
                }
                $success = $unit->save();
                if(!$success) {
                    DB::connection('dbcentral')->rollBack();
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success'=>false,'message'=>'data unit pengguna gagal disimpan.']);
                }
            }

            DB::connection('dbcentral')->commit();
            DB::connection('dbclient')->commit();
            
            /**
             * kembalikan data user dan member depo dan member unit
             */
            $profile = UserProfile::where('user_id',$id)->first();
            $data = ClientMember::where('user_id',$id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            $role = Role::where('role_id',$data->role_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $data['username'] = $user->username;
            $data['user_id'] = $user->user_id;
            $data['email'] = $user->email;
            $data['role_nama'] = $role->role_nama;
            
            $data['tanggal_lahir'] = $profile->tanggal_lahir;
            $data['tempat_lahir'] = $profile->tempat_lahir;
            $data['nama_lengkap'] = $profile->nama_lengkap;
            $data['jenis_kelamin'] = $profile->jenis_kelamin;
            $data['nik'] = $profile->nik;
            $data['no_telepon'] = $profile->no_telepon;

            $data['units'] = MemberUnit::where('user_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            $data['depos'] = MemberDepo::where('user_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
             
            return response()->json(['success'=>true,'message'=>'data pengguna berhasil disimpan.', 'data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil disimpan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     */
    public function userData(Request $request)
    {
        try {
            if(!$request->has('email')) {
                return response()->json(['success' => false,'message' =>'data user tidak ditemukan.']);
            }
            $email = $request->input('email');
            $user = User::where('email',$email)->first();
            $profile = UserProfile::where('user_id',$user->user_id)->first();
            $data = ClientMember::where('user_id',$user->user_id)->where('client_id',$this->clientId)->first();
            
            $data['username'] = $user->username;
            $data['user_id'] = $user->user_id;
            $data['email'] = $user->email;
            $data['role_nama'] = null;
            if($data->role_id){
                $role = Role::where('role_id',$data->role_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
                if($role) { $data['role_nama'] = $role->role_nama; }
            }
            if($profile) {
                $data['tanggal_lahir'] = $profile->tanggal_lahir;
                $data['tempat_lahir'] = $profile->tempat_lahir;
                $data['nama_lengkap'] = $profile->nama_lengkap;
                $data['jenis_kelamin'] = $profile->jenis_kelamin;            
                $data['nik'] = $profile->nik;
                $data['no_telepon'] = $profile->no_telepon;
            }
            else {
                $data['tanggal_lahir'] = null;
                $data['tempat_lahir'] = null;
                $data['nama_lengkap'] = null;
                $data['jenis_kelamin'] = null;            
                $data['nik'] = null;
                $data['no_telepon'] = null;
            }
            
            $data['units'] = MemberUnit::where('user_id',$user->user_id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
            $data['depos'] = MemberDepo::leftJoin('tb_depo','tb_depo.depo_id','=','tb_member_depo.depo_id')
                ->where('tb_member_depo.user_id',$user->user_id)
                ->where('tb_member_depo.client_id',$this->clientId)
                ->where('tb_member_depo.is_aktif',1)
                ->where('tb_depo.is_aktif',1)
                ->select('tb_member_depo.depo_id','tb_member_depo.user_id','tb_member_depo.client_id','tb_member_depo.is_aktif','tb_depo.depo_nama')
                ->get();

            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    public function userDepos(Request $request) {
        try {
            $perPage = 10;
            $keyword = '%%';

            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                if($perPage == 'ALL') { $perPage = MemberDepo::where('client_id',$this->clientId)->count(); }
            }

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            $data = MemberDepo::where('tb_member_depo.user_id',Auth::user()->user_id)
                ->where('tb_member_depo.client_id',$this->clientId)
                ->where(function($q) use($keyword){
                    $q->where('tb_member_depo.depo_id','ILIKE',$keyword)
                    ->orWhere('tb_depo.depo_nama','ILIKE',$keyword);   
                })
                ->where('tb_member_depo.is_aktif',1)
                ->where('tb_depo.is_aktif',1)
                ->leftJoin('tb_depo','tb_depo.depo_id','=','tb_member_depo.depo_id')
                ->select('tb_member_depo.depo_id','tb_depo.depo_nama')
                ->paginate($perPage);
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'daftar depo tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * pengambilan daftar unit yang berada
     * dibawah user yang sedang login
     */
    public function userUnits(Request $request) {
        try {
            $perPage = 10;
            $keyword = '%%';

            if($request->has('per_page')) {
                $perPage = $request->input('per_page');
                if($perPage == 'ALL') { 
                    $perPage = MemberUnit::where('client_id',$this->clientId)
                        ->where('user_id',Auth::user()->user_id)
                        ->count(); 
                }
            }

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }
            
            $jnsRegistrasi = '%%';
            if($request->has('jenis_registrasi')) {
                $jnsRegistrasi = '%'.$request->input('jenis_registrasi').'%';
            }
            
            $data = MemberUnit::where('tb_member_unit.user_id',Auth::user()->user_id)
                ->where('tb_member_unit.client_id',$this->clientId)
                ->leftJoin('tb_unit','tb_unit.unit_id','=','tb_member_unit.unit_id')
                ->where('tb_unit.jenis_registrasi','ILIKE',$jnsRegistrasi)
                ->where(function($q) use($keyword){
                    $q->where('tb_member_unit.unit_id','ILIKE',$keyword)
                    ->orWhere('tb_unit.unit_nama','ILIKE',$keyword);   
                })->where('tb_member_unit.is_aktif',1)
                ->where('tb_unit.is_aktif',1)
                ->select('tb_member_unit.unit_id','tb_unit.unit_nama')
                ->paginate($perPage);
            
            if($data) {
                foreach($data->items() as $item) {
                    $item['depos'] = DepoUnit::where('tb_depo_unit.client_id',$this->clientId)
                        ->where('tb_depo_unit.is_aktif',1)
                        ->join('tb_depo','tb_depo.depo_id','=','tb_depo_unit.depo_id')
                        ->join('tb_member_depo','tb_depo.depo_id','=','tb_member_depo.depo_id')
                        ->where('tb_member_depo.user_id',Auth::user()->user_id)
                        ->where('tb_depo.is_aktif',1)
                        ->where('tb_member_depo.is_aktif',1)
                        ->where('tb_depo_unit.unit_id',$item['unit_id'])
                        ->select('tb_depo_unit.*','tb_depo.depo_nama','tb_member_depo.user_id')
                        ->get();
                }
            }
            
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'daftar depo tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     */
    public function update(Request $request)
    {
        try {
            $id = $request->user_id;
            $data = ClientMember::where('client_id',$this->clientId)->where('user_id',$id)->first();
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'data pengguna gagal diubah.']);
            }
            DB::connection('dbcentral')->beginTransaction();
            DB::connection('dbclient')->beginTransaction();
            /**
             * update role member dan aktifitas
             */
            $success = ClientMember::where('client_id',$this->clientId)
                ->where('user_id',$id)
                ->update(['is_aktif'=>$request->is_aktif, 'role_id'=>$request->role_id, 'is_teknisi'=>$request->is_teknisi]);
            
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                DB::connection('dbclient')->rollBack();
                return response()->json(['success'=>false,'message'=>'data pengguna gagal diubah.']);
            }
            
            /**
             * update member depo
             */
            foreach($request->depos as $dp) {
                $depo = MemberDepo::where('client_id',$this->clientId)->where('depo_id',$dp['depo_id'])->where('user_id',$id)->where('is_aktif',1)->first();
                if(!$depo) {
                    $depo = new MemberDepo();
                    $depo->member_depo_id = $this->clientId.Uuid::uuid4()->getHex();
                    $depo->user_id = $id;
                    $depo->depo_id = $dp['depo_id'];
                    $depo->client_id = $this->clientId;
                    $depo->depo_nama = $dp['depo_nama'];
                    $depo->is_aktif = true;
                    $depo->created_by = Auth::user()->username;
                    $depo->updated_by = Auth::user()->username;
                    $success = $depo->save();
                    if(!$success) {
                        DB::connection('dbcentral')->rollBack();
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'data depo pengguna gagal diubah.']);
                    }
                }
                else {
                    $success = MemberDepo::where('client_id',$this->clientId)
                        ->where('depo_id',$dp['depo_id'])
                        ->where('is_aktif',1)->update(['is_aktif' => $dp['is_aktif'], 'updated_by' => Auth::user()->username]);

                    if(!$success) {
                        DB::connection('dbcentral')->rollBack();
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'data depo pengguna gagal diubah.']);
                    }
                }
                
            }
            
            /**
             * update member unit
             */
            foreach($request->units as $dt) {
                $unit = MemberUnit::where('client_id',$this->clientId)->where('unit_id',$dt['unit_id'])->where('user_id',$id)->where('is_aktif',1)->first();
                if(!$unit) {
                    $unit = new MemberUnit();
                    $unit->member_unit_id = $this->clientId.Uuid::uuid4()->getHex();
                    $unit->user_id = $id;
                    $unit->unit_id = $dt['unit_id'];
                    $unit->client_id = $this->clientId;
                    $unit->unit_nama = $dt['unit_nama'];
                    $unit->is_aktif = true;
                    $unit->created_by = Auth::user()->username;
                    $unit->updated_by = Auth::user()->username;
                    $success = $unit->save();
                    if(!$success) {
                        DB::connection('dbcentral')->rollBack();
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'data unit pengguna gagal diubah.']);
                    }

                }
                else {
                    $success = MemberUnit::where('client_id',$this->clientId)
                        ->where('unit_id',$dt['unit_id'])
                        ->where('is_aktif',1)->update(['is_aktif' => $dt['is_aktif'], 'updated_by' => Auth::user()->username]);

                    if(!$success) {
                        DB::connection('dbcentral')->rollBack();
                        DB::connection('dbclient')->rollBack();
                        return response()->json(['success'=>false,'message'=>'data depo pengguna gagal diubah.']);
                    }
                }
                
            }

            DB::connection('dbcentral')->commit();
            DB::connection('dbclient')->commit();
            
            /**
             * kembalikan data user dan member depo dan member unit
             */
            $user = User::where('user_id',$id)->first();
            $profile = UserProfile::where('user_id',$id)->first();
            $data = ClientMember::where('user_id',$id)->where('client_id',$this->clientId)->first();
            $role = Role::where('role_id',$data->role_id)->where('is_aktif',1)->where('client_id',$this->clientId)->first();
            
            $data['username'] = $user->username;
            $data['user_id'] = $user->user_id;
            $data['email'] = $user->email;
            $data['role_nama'] = $role->role_nama;
            
            if($profile) {
                $data['tanggal_lahir'] = $profile->tanggal_lahir;
                $data['tempat_lahir'] = $profile->tempat_lahir;
                $data['nama_lengkap'] = $profile->nama_lengkap;
                $data['jenis_kelamin'] = $profile->jenis_kelamin;
                $data['nik'] = $profile->nik;
                $data['no_telepon'] = $profile->no_telepon;
            }
            else {
                $data['tanggal_lahir'] = null;
                $data['tempat_lahir'] = null;
                $data['nama_lengkap'] = null;
                $data['jenis_kelamin'] = null;
                $data['nik'] = null;
                $data['no_telepon'] = null;            
            }
            
            $data['units'] = MemberUnit::where('user_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->orderBy('created_at')->get();
            $data['depos'] = MemberDepo::where('user_id',$id)->where('client_id',$this->clientId)->where('is_aktif',1)->get();
             
            return response()->json(['success'=>true,'message'=>'data pengguna berhasil diubah.', 'data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diubah.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     */
    public function delete(Request $request, $id)
    {
        try {

        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil dihapus.','error'=>$e->getMessage()]);
        }
    }

    public function allUserLists(Request $request) {
        try {
            $keyword = '%%';
            $active = 'true';
            $rowNumber = 20;

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            $lists = ClientMember::where('users.is_aktif','ILIKE',$active)
                ->leftJoin('tb_user_profile','users.user_id','=','tb_user_profile.user_id')
                ->where(function($q) use ($keyword) {
                    $q->where('users.email','ILIKE',$keyword)
                    ->orWhere('users.username','ILIKE',$keyword)
                    ->orWhere('tb_user_profile.nama_lengkap','ILIKE',$keyword);
                })
                ->select(
                    'users.username',
                    'users.email',
                    'tb_user_profile.nama_lengkap',
                    'tb_user_profile.tempat_lahir',
                    'tb_user_profile.tanggal_lahir',
                    'tb_user_profile.no_telepon',
                    'tb_user_profile.bio_singkat',
                    'users.avatar'
                )->orderBy('users.username', 'ASC')->paginate($rowNumber);

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diambil.','error'=>$e->getMessage()]);
        }
    }

    public function isUserDepoMember(Request $request, $depoId){
        try {
            $memberDepo = MemberDepo::where('client_id',$this->clientId)->where('is_aktif',1)
                ->where('user_id',Auth::user()->user_id)
                ->where('depo_id',$depoId)
                ->first();
            if(!$memberDepo) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otorisasi pada depo persediaan ini. Data tidak dapat ditampilkan.']);
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $memberDepo]);
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil ditampilkan.','error'=>$e->getMessage()]);
        }
    }
}
