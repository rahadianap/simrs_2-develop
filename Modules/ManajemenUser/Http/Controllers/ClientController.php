<?php

namespace Modules\ManajemenUser\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenUser\Entities\Client;
use Modules\ManajemenUser\Entities\ClientMember;
use Modules\ManajemenUser\Entities\Role;
use Modules\ManajemenUser\Entities\RoleMenu;
use App\Models\AppMenu;
use DB;

class ClientController extends Controller
{
    /**
     * create new client
     */
    public function create(Request $request)
    {
        try {
            $id = $this->getClientId();
            $allmenus = AppMenu::where('is_aktif',1)->where('is_nav_header',0)->select(['menu_id','otorisasi'])->get();

            DB::connection('dbcentral')->beginTransaction();
            DB::connection('dbclient')->beginTransaction();

            //save new client board
            $data = new Client();
            $data->client_id = $id;
            $data->client_nama = $request->client_nama;
            $data->client_tipe = $request->client_tipe;
            $data->deskripsi = $request->deskripsi;
            $data->kota = $request->kota;
            $data->propinsi = $request->propinsi;
            $data->kecamatan = $request->kecamatan;
            $data->kelurahan = $request->kelurahan;
            $data->kodepos = $request->kodepos;
            $data->alamat = $request->alamat;
            $data->no_telepon = $request->no_telepon;
            $data->email = $request->email;
            $data->no_ijin = $request->no_ijin;
            $data->tgl_terbit_ijin = $request->tgl_terbit_ijin;
            $data->no_whatsapp = $request->no_whatsapp;
            $data->link_instagram = $request->link_instagram;
            $data->invitation_token = Str::random(20);
            $data->admin_email = Auth::user()->email;
            $data->admin_id = Auth::user()->user_id;

            $data->path_logo = 'noimage';
            $data->url_logo = 'noimage';
            if($request->path_logo !== null) {
                $data->path_logo = $request->path_logo;
            }
            if($request->url_logo !== null) {
                $data->url_logo = $request->url_logo;
            }

            $data->loc_embed_src = $request->loc_embed_src;
            $data->loc_embed_code = $request->loc_embed_code;
            
            $data->is_aktif = 1;
            $data->created_by = Auth::user()->username;
            $data->updated_by = Auth::user()->username;
            $data->deleted_by = '';
            $success = $data->save();
    
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data(board client)']);
            }
            //add member
            $member = new ClientMember();
            $member->client_id = $id;
            $member->user_id = Auth::user()->user_id;
            $member->role_id = 'RL000-'.$id;
            $member->is_aktif = 1;
            $member->is_admin = 1;
            $member->is_undang_gabung = 1;
            $member->tgl_gabung = date('Y-m-d H:i:s');
            $member->tipe_member = 'OWNER';            
            $member->created_by = Auth::user()->username;
            $member->updated_by = Auth::user()->username;
            $success = $member->save();
    
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data(member client)']);
            }            
            //create role for superadmin
            $role = new Role();
            $role->role_id = 'RL000-'.$id;
            $role->role_nama = 'SUPERADMIN';
            $role->deskripsi = 'Auto create role pemilik tempat kerja';
            $role->is_aktif = 1;
            $role->is_multi_dokter = 1;
            $role->is_ubah_tanggal = 1;
            $role->client_id = $id;
            $role->created_by = Auth::user()->username;
            $role->updated_by = Auth::user()->username;
            $success = $role->save();
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data role superadmin']);
            }

            //adding menu for super admin
            foreach($allmenus as $mn) {
                $menu = new RoleMenu();
                $menu->role_id = 'RL000-'.$id;
                $menu->client_id = $id;
                $menu->menu_id = $mn['menu_id'];
                $menu->otorisasi = $mn['otorisasi'];
                $menu->is_aktif = 1;
                if($mn['is_admin_mandatory'] !== null){
                    $menu->is_admin_mandatory = $mn['is_admin_mandatory'];
                }
                $menu->created_by = Auth::user()->username;
                $menu->updated_by = Auth::user()->username;
                $success = $menu->save();
                if(!$success) {
                    DB::connection('dbcentral')->rollBack();
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data tempat kerja baru']);
                }
            }

            DB::connection('dbcentral')->commit();
            DB::connection('dbclient')->commit();
            return response()->json(['success' => true,'message' => 'tempat kerja baru berhasil dibuat','data'=>$data]);
            
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'tempat kerja baru tidak berhasil disimpan. Error : '.$e->getMessage() ,'error'=>$e->getMessage()]);
        }
    }

    /**
     * update client
     * perubahan data client 
     */
    public function update(Request $request)
    {
        try {
            $data = Client::where('client_id',$request->client_id)->where('admin_id',Auth::user()->user_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam update data','error'=>'data tidak ditemukan.']);
            }
            
            $data->client_nama = $request->client_nama;
            $data->client_tipe = $request->client_tipe;
            $data->deskripsi = $request->deskripsi;
            
            $data->alamat = $request->alamat;
            $data->kota = $request->kota;
            $data->propinsi = $request->propinsi;
            $data->kecamatan = $request->kecamatan;
            $data->kelurahan = $request->kelurahan;
            $data->kodepos = $request->kodepos;
            
            $data->no_telepon = $request->no_telepon;
            $data->email = $request->email;
            $data->no_whatsapp = $request->no_whatsapp;
            $data->link_instagram = $request->link_instagram;
            
            $data->no_ijin = $request->no_ijin;
            $data->tgl_terbit_ijin = $request->tgl_terbit_ijin;
            
            $data->loc_embed_src = $request->loc_embed_src;
            $data->loc_embed_code = $request->loc_embed_code;

            $data->path_logo = $request->path_logo;
            $data->url_logo = $request->url_logo;
            
            $data->is_aktif = 1;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
    
            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam penyimpanan data(client)']);
            }
            return response()->json(['success' => true,'message' => 'data berhasil diperbaharui','data'=>$data],200);            
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diperbaharui. Error : '.$e->getMessage() ,'error'=>$e->getMessage()]);
        }
    }

    /**
     * data client
     * ambil single data client
     * param : client_id
     */
    public function data(Request $request, $id)
    {
        try {
            $data = Client::where('client_id',$id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'Data tidak ditemukan','error'=>'data tidak ditemukan.']);
            }            
            if($data->admin_id == Auth::user()->user_id) {
                $data['is_admin'] = true;
            }
            
            return response()->json(['success' => true,'message' => 'Data berhasil diambil','data'=>$data ]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam pengambilan data.','error'=>$e->getMessage()]);
        }
    }

    /**
     * list client
     * ambil daftar seluruh client ataupun berdasar hasil pencarian
     * dikembalikan dalam bentuk pagination
     * 
     */
    public function list(Request $request)
    {
        try {     
            /**ambil seluruh detail client yang pengguna menjadi member didalamnya */
            $workplaces = ClientMember::where('user_id',Auth::user()->user_id)->where('is_aktif',1)
                            ->select(['client_id','is_admin'])
                            ->with(['detail'=>function($q){
                                $q->select(['alamat','client_id','client_nama','kota','client_tipe','deskripsi','path_logo','url_logo'])
                                ->where('is_aktif',1);
                            }])->get(); 

            if(!$workplaces) {
                return response()->json(['success'=>true,'message'=>'pengguna belum memiliki tempat kerja','data'=>[]]);    
            }
            
            $finaldata = [];
            $i = 0;
            foreach($workplaces as $ws) {
                if($ws->detail) {
                    $finaldata[$i] = $ws;
                    $i++;
                }
            }                                    
            return response()->json(['success'=>true,'message'=>'OK','data'=>$finaldata]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list data tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * list client
     * ambil daftar seluruh client ataupun berdasar hasil pencarian
     * dikembalikan dalam bentuk pagination
     * 
     */
    public function members(Request $request)
    {
        try {     
            if(!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'tempat kerja tidak ditemukan.']);
            }
            $clientId = $request->header('X-cid');

            $rowNumber = 10;
            $active = 'true';
            $keyword = '%%';
            
            if($request->has('per_page')){
                $rowNumber = $request->get('per_page');
                if($rowNumber == 'ALL') { $rowNumber = ClientMember::count(); }
            }

            if($request->has('keyword')){
                $keyword = '%'.$request->get('keyword').'%';
            }

            if($request->has('is_aktif')){
                $active = '%'.$request->get('is_aktif').'%';
            }

            /**ambil seluruh detail client yang pengguna menjadi member didalamnya */
            $users = DB::connection('dbcentral')->table('tb_client_member')
                        ->rightJoin('users', 'users.user_id', '=', 'tb_client_member.user_id')
                        
                        ->where('tb_client_member.client_id',$clientId)
                        ->where('tb_client_member.is_aktif','ILIKE',$active)
                        ->where(function($q) use ($keyword){
                            $q->where('username','ILIKE',$keyword)
                            ->orWhere('email','ILIKE',$keyword);
                        })
                        ->select(
                            'users.username as username',
                            'users.email as email',
                            'users.avatar as avatar',
                            'tb_client_member.role_id as role_id',
                            'tb_client_member.is_admin as is_admin',
                            'tb_client_member.tgl_gabung as tgl_gabung'
                            )
                        ->paginate($rowNumber);

            if($users) {
                 foreach($users as $user) {
                    $role = Role::where('role_id',$user->role_id)->where('is_aktif',1)->where('client_id',$clientId)->select('role_nama')->first();
                    $user->role_nama = $role->role_nama;            
                }        
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$users]);
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'list member tempat kerja tidak ditemukan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * hapus client
     * param : client_id
     */
    public function delete(Request $request, $id)
    {
        try {
            $data = Client::where('client_id',$id)->where('admin_id',Auth::user()->user_id)->first();
            if(!$data) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan tempat kerja','error'=>'data tidak ditemukan.']);
            }
            $data->is_aktif = 0;
            $data->updated_by = Auth::user()->username;
            $success = $data->save();
    
            if(!$success) {
                return response()->json(['success' => false,'message' => 'ada kesalahan dalam menonaktifkan tempat kerja.']);
            }
            return response()->json(['success' => true,'message' => 'tempat kerja berhasil dinonaktifkan','data'=>$data]); 
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'tempat kerja tidak berhasil dinonaktifkan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * logo client
     */
    public function uploadLogo(Request $request)
    {
        try {            
            $id = '';
            if($request->has('client_id')) { $id = $request->client_id; }
            
            if (!$request->hasFile('image')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.']);
            } 
            
            $path = Storage::disk('avatars')->putFile('avatars/clients', $request->file('image'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url;

            if ($id == '') {
                return response()->json(['success'=>true, 'message'=>'logo berhasil diupload', 'data'=>$data]);
            } 
            else {
                $client = Client::where('client_id',$id)->first();
                $oldLogo = $client->path_logo;
                $client->path_logo = $path;
                $client->url_logo = $path_url;
                $success = $client->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'Logo gagal diupload']);
                }                
                Storage::disk('avatars')->delete($oldLogo);
                return response()->json(['success'=>true, 'message'=>'logo berhasil diupload' ,'data'=>$data]);
            }
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam menyimpan logo.','error'=> $e->getMessage()]);
        }
    }

    

    /**
     * retrieve menu from role authenticated user
     * in workplace based on client Id
     */
    public function menus(Request $request) {
        try{
            /*** check apakah header menyertakan client ID atau tidak */
            if(!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'tempat kerja tidak ditemukan.']);
            }

            /*** ambil role ID pengguna di client ID yang dipilih.*/
            $roleId = ClientMember::select('role_id')->where('client_id',$request->header('X-cid'))
                    ->where('user_id',Auth::user()->user_id)
                    ->where('is_aktif',1)->first();
            if(!$roleId) {
                return response()->json(['success' => false, 'message' => 'role pengguna tidak ditemukan.']);
            }

             /*** ambil role menu */
             $menus = RoleMenu::select(['menu_id','role_id','client_id'])->where('role_id',$roleId->role_id)->where('client_id',$request->header('X-cid'))->where('is_aktif',1)
                ->with(['detail'=>function($q){
                    $q->select(['menu_id','group_menu','menu_title','is_nav_header','menu_icon','menu_link']);
                }])               
                ->orderBy('menu_id','ASC')
                ->get();

            /**
             * ambil header 
             */
            $group = AppMenu::select(['menu_id','group_menu','menu_title','is_nav_header','menu_icon','menu_link'])
                ->where('is_aktif',1)
                ->where('is_nav_header',1)
                ->orderBy('no_urut','ASC')
                ->get();
            if(!$group) {
                return response()->json(['success' => false, 'message' => 'menu tidak ditemukan.']);
            }
            $data = [];
            
            foreach($group as $gr) {
                $group_menu = $gr->group_menu;
                $item = [];
                foreach($menus as $menu) {
                    if($menu->detail){
                        if($menu->detail['group_menu'] == $group_menu) {
                            $item[] = $menu->detail;
                        }
                    }                    
                }
                if(count($item) > 0 ) {
                    $gr['items'] = $item;
                    $data[] = $gr;
                }
            }
            
            return response()->json(['success'=>true, 'message'=>'menu pengguna berhasil diambil' ,'data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam mengambil menu member : '.$e->getMessage(),'error'=> $e->getMessage()]);
        }
    }

    public function getClientId() 
    {
        try {
            $id = 'CL'.date('Y').'-0001';
            $maxId = Client::withTrashed()->where('client_id','LIKE','CL'.date('Y').'%')->max('client_id');
            if (!$maxId) { $id = 'CL'.date('Y').'-0001'; }
            else {
                $maxId = str_replace('CL'.date('Y').'-','',$maxId);
                $count = $maxId + 1;
                if ($count < 10) { $id = 'CL'.date('Y').'-000'.$count; } 
                elseif ($count >= 10 && $count < 100) { $id = 'CL'.date('Y').'-00'.$count; } 
                elseif ($count >= 100 && $count < 1000) { $id = 'CL'.date('Y').'-0'.$count; } 
                else { $id = 'CL'.date('Y').'-'.$count; } 
            }
            return $id;
        } catch(\Exception $e){
            return 'CL'.date('Y').'-'.Uuid::uuid4()->getHex();
        }
    }

    /**
     * retrieve all menus on client
     */
    public function clientAllMenus(Request $request) {
        try {
            /*** check apakah header menyertakan client ID atau tidak */
            if(!$request->hasHeader('X-cid')) {
                return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
            }
            
            $allmenus = AppMenu::select(['menu_id','group_menu','menu_title','is_nav_header','menu_icon','menu_link'])
                    ->where('is_aktif',1)->where('is_nav_header',1)->orderBy('menu_id','ASC')->get();

            if(!$allmenus) {
                return response()->json(['success' => false, 'message' => 'menu tempat kerja tidak ditemukan.']);
            }

            foreach($allmenus as $mn) {
                $item = AppMenu::select(['menu_id','group_menu','menu_title','is_nav_header','menu_icon','menu_link'])
                    ->where('is_aktif',1)->where('is_nav_header',0)->where('group_menu',$mn->group_menu)->get();
                $mn['items'] = $item; 
            }

            return response()->json(['success' => true, 'message' => 'menu tempat kerja ditemukan.', 'data'=>$allmenus ]);
        }        
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'ada kesalahan dalam mengambil menu member : '.$e->getMessage(),'error'=> $e->getMessage()]);
        }
    }

}
