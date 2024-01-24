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
use App\Models\UserProfile;
use App\Models\User;
use Modules\ManajemenUser\Entities\Invitation;

class InvitationController extends Controller
{
    public $clientId;

    public function __construct(Request $request){
        /*** check apakah header menyertakan client ID atau tidak */
        if(!$request->hasHeader('X-cid')) {
            return response()->json(['success' => false, 'message' => 'tempat kerja tidak dikenali.']);
        }
        $this->clientId = $request->header('X-cid');
    }

    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        try {
            $keyword = '%%';
            $active = 'true';
            $rowNumber = 20;

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            if($request->has('is_aktif')) {
                $active = '%'.$request->input('is_aktif').'%';
            }

            $lists = Invitation::where('tb_invitation.is_aktif','ILIKE',$active)
                ->where('client_id',$this->clientId)
                ->leftJoin('users','users.user_id','=','tb_invitation.user_id')
                ->leftJoin('tb_user_profile','tb_invitation.user_id','=','tb_user_profile.user_id')
                ->where(function($q) use ($keyword) {
                    $q->where('users.email','ILIKE',$keyword)
                    ->orWhere('users.username','ILIKE',$keyword)
                    ->orWhere('tb_user_profile.nama_lengkap','ILIKE',$keyword);
                })
                ->select(
                    'tb_invitation.invitation_id',
                    'tb_invitation.tgl_dibuat',
                    'tb_invitation.response_at',
                    'tb_invitation.expired_at',
                    'tb_invitation.dibuat_oleh',
                    'tb_invitation.status',
                    'tb_invitation.is_aktif',
                    'users.username',
                    'users.email',
                    'tb_user_profile.nama_lengkap',
                    'tb_user_profile.tempat_lahir',
                    'tb_user_profile.tanggal_lahir',
                    'tb_user_profile.no_telepon',
                    'tb_user_profile.bio_singkat',
                    'users.avatar',
                )->orderBy('users.username', 'ASC')->paginate($rowNumber);

                foreach ($lists->items() as $itm) {
                    $itm['is_selected'] = false;
                }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diambil.','error'=>$e->getMessage()]);
        }
    }

     /**
     * Display a listing of the resource.
     */
    public function searchUser(Request $request)
    {
        try {
            $keyword = '%%';
            $active = 'true';
            $rowNumber = 20;

            if($request->has('keyword')) {
                $keyword = '%'.$request->input('keyword').'%';
            }

            $lists = User::where('users.is_aktif','ILIKE',$active)
                ->where('users.user_id','<>',Auth::user()->user_id)
                ->leftJoin('tb_user_profile','users.user_id','=','tb_user_profile.user_id')
                ->where(function($q) use ($keyword) {
                    $q->where('users.email','ILIKE',$keyword)
                    ->orWhere('users.username','ILIKE',$keyword)
                    ->orWhere('tb_user_profile.nama_lengkap','ILIKE',$keyword);
                })
                ->select(
                    'users.user_id',
                    'users.username',
                    'users.email',
                    'tb_user_profile.nama_lengkap',
                    'tb_user_profile.tempat_lahir',
                    'tb_user_profile.tanggal_lahir',
                    'tb_user_profile.no_telepon',
                    'tb_user_profile.bio_singkat',
                    'users.avatar',
                )->orderBy('users.username', 'ASC')->paginate($rowNumber);

                foreach ($lists->items() as $itm) {
                    $itm['is_selected'] = false;
                }

            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diambil.','error'=>$e->getMessage()]);
        }
    }

    public function myInvitation(Request $request){
        try {
            $lists = Invitation::where('tb_invitation.is_aktif',1)
                ->where('user_id',Auth::user()->user_id)
                ->where('status','MENUNGGU')
                ->where('expired_at','>=',date('Y-m-d'))
                ->leftJoin('tb_client','tb_invitation.client_id','=','tb_client.client_id')
                ->select(
                    'tb_invitation.*',
                    'tb_client.client_nama',
                    'tb_client.client_tipe',
                    'tb_client.deskripsi',
                    'tb_client.path_logo',
                    'tb_client.url_logo',
                )->orderBy('tb_invitation.tgl_dibuat', 'ASC')->get();
            
            if(!$lists) {
                return response()->json(['success'=>true,'message'=>'tidak ada undangan bergabung','data'=>[]]);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$lists]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data tidak berhasil diambil.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request)
    {
        try {
            $currentDate = date("Y-m-d");
            $dateExpired = date('Y-m-d', strtotime($currentDate . " +30 days"));

            if($request->user_lists) {
                DB::connection('dbcentral')->beginTransaction();
                foreach($request->user_lists as $usr) {
                    //check invitation
                    $invitation = Invitation::where('username',$usr['username'])
                        ->where('email',$usr['email'])
                        ->where('user_id',$usr['user_id'])
                        ->where('client_id',$this->clientId)
                        ->where('is_aktif',1)->first();
                
                    //check user member
                    $member = ClientMember::where('user_id',$usr['user_id'])
                        ->where('client_id',$this->clientId)
                        ->where('is_aktif',1)->first();

                    if(!$member){
                        if(!$invitation) {
                            $data = new Invitation();
                            $data->invitation_id = $this->getInvitationId();
                            $data->user_id = $usr['user_id'];
                            $data->username = $usr['username'];
                            $data->client_id = $this->clientId;
                            $data->email = $usr['email'];                            
                            $data->invitation_token = date('ymdhis').Uuid::uuid4()->getHex();
                            $data->expired_at = $dateExpired;
                            $data->dibuat_oleh = Auth::user()->username;
                            $data->tgl_dibuat = date('Y-m-d H:i:s');
                            $data->status = 'MENUNGGU';
                            $data->is_aktif = true;
                            $data->created_by = Auth::user()->username;
                            $data->updated_by = Auth::user()->username;
    
                            $success = $data->save();
                            if(!$success) {
                                DB::connection('dbcentral')->rollBack();
                                return response()->json(['success' => false,'message' =>'undangan gagal dibuat.']);
                            }                           
                        }
                    } 
                }
                DB::connection('dbcentral')->commit();
                return response()->json(['success' => true,'message' =>'undangan berhasil disimpan.']);
            }
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'undangan tidak berhasil disimpan.','error'=>$e->getMessage()]);
        }
    }


    public function getInvitationId()
    {
        try {
            $id = date('Ymd').'-INV00001';
            $maxId = Invitation::withTrashed()->where('invitation_id', 'ILIKE', date('Ymd').'-INV%')->max('invitation_id');
            if (!$maxId) {
                $id = date('Ymd').'-INV00001';
            } 
            else {
                $maxId = str_replace(date('Ymd').'-INV', '', $maxId);
                $count = $maxId + 1;

                if ($count < 10) { $id = date('Ymd').'-INV0000' . $count; } 
                elseif ($count >= 10 && $count < 100) { $id = date('Ymd').'-INV000' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = date('Ymd').'-INV00' . $count; } 
                elseif ($count >= 100 && $count < 1000) { $id = date('Ymd').'-INV0' . $count; } 
                else { $id = date('Ymd').'-INV' . $count; }
            }
            return $id;
        } 
        catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Show the specified resource.
     * @param int $invitationId
     */
    public function data(Request $request, $invitationId)
    {
        try {
            $data = Invitation::where('invitation_id',$invitationId)
                ->leftJoin('users','users.user_id','=','tb_invitation.user_id')
                ->leftJoin('tb_user_profile','tb_invitation.user_id','=','tb_user_profile.user_id')
                ->select(
                    'tb_invitation.invitation_id',
                    'tb_invitation.tgl_dibuat',
                    'tb_invitation.tgl_disetujui',
                    'tb_invitation.expired_at',
                    'tb_invitation.dibuat_oleh',
                    'tb_invitation.is_aktif',
                    'users.username',
                    'users.email',
                    'tb_user_profile.nama_lengkap',
                    'tb_user_profile.tempat_lahir',
                    'tb_user_profile.tanggal_lahir',
                    'tb_user_profile.no_telepon',
                    'tb_user_profile.bio_singkat',
                    'users.avatar',
                )->first();
            
            if(!$data) {
                return response()->json(['success'=>false,'message'=>'data tidak ditemukan.']);
            }
            return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'data gagal diambil.','error'=>$e->getMessage()]);
        }
    }

    /**
     * reject the invitation.
     * @param int $invitation_id
     */
    public function accept(Request $request, $invitationId)
    {   
        try {
            $invitation = Invitation::where('invitation_id',$invitationId)
                ->where('status','MENUNGGU')
                //->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            
            if(!$invitation) {
                return response()->json(['success' => false,'message' =>'data undangan bergabung tidak ditemukan.']);
            }

            DB::connection('dbcentral')->beginTransaction();
            $success = Invitation::where('invitation_id',$invitationId)
                ->where('status','MENUNGGU')
                //->where('client_id',$this->clientId)
                ->where('is_aktif',1)->update([
                    'response_at' =>date('Y-m-d H:i:s'),
                    'updated_at' =>date('Y-m-d H:i:s'),
                    'status' => 'DITERIMA',
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false,'message' =>'data undangan bergabung gagal diterima.']);
            }

            /**tambahkan ke member client*/
            $member = new ClientMember();
            $member->user_id = $invitation->user_id;
            $member->client_id = $invitation->client_id;
            $member->tipe_member = 'MEMBER';
            $member->is_admin = false;
            $member->is_undang_gabung = true;
            $member->tgl_gabung = date('Y-m-d H:i:s');
            $member->is_aktif = true;
            $member->created_by = Auth::user()->username;
            $member->updated_by = Auth::user()->username;
            $success = $member->save();
            if(!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false,'message' =>'data member gagal dibuat.']);
            }

            DB::connection('dbcentral')->commit();

            return response()->json(['success' => true,'message' =>'data member berhasil dibuat.']);
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'undangan gagal dikonfirmasi.','error'=>$e->getMessage()]);
        }
    }

    /**
     * reject the invitation.
     * @param int $invitation_id
     */
    public function reject(Request $request, $id)
    {   
        try {
            $invitation = Invitation::where('invitation_id',$invitationId)
                ->where('status','MENUNGGU')
                //->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            
            if(!$invitation) {
                return response()->json(['success' => false,'message' =>'data undangan bergabung tidak ditemukan.']);
            }

            $success = Invitation::where('invitation_id',$invitationId)
                //->where('client_id',$this->clientId)
                ->where('status','MENUNGGU')
                ->where('is_aktif',1)->update([
                    'response_at' =>date('Y-m-d H:i:s'),
                    'updated_at' =>date('Y-m-d H:i:s'),
                    'status' => 'DITOLAK',
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                return response()->json(['success' => false,'message' =>'data undangan bergabung gagal ditolak.']);
            }

            return response()->json(['success' => true,'message' =>'data undangan berhasil ditolak.']);
        
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'penolakan undangan gagal disimpan.','error'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     */
    public function destroy(Request $request, $invitationId)
    {
        try {
            $invitation = Invitation::where('invitation_id',$invitationId)
                ->where('status','MENUNGGU')
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->first();
            
            if(!$invitation) {
                return response()->json(['success' => false,'message' =>'data undangan bergabung tidak ditemukan.']);
            }
            $success = Invitation::where('invitation_id',$invitationId)
                ->where('status','MENUNGGU')
                ->where('client_id',$this->clientId)
                ->where('is_aktif',1)->update([
                    'is_aktif' => 0,
                    'updated_at' =>date('Y-m-d H:i:s'),
                    'status' => 'DIBATALKAN',
                    'updated_by' => Auth::user()->username
                ]);
            
            if(!$success) {
                return response()->json(['success' => false,'message' =>'data undangan bergabung gagal dihapus.']);
            }

            return response()->json(['success' => true,'message' =>'data undangan berhasil dihapus.']);
            
        }
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'kesalahan dalam menghapus undangan.','error'=>$e->getMessage()]);
        }
    }
}
