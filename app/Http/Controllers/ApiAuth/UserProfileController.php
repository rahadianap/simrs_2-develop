<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function data(Request $request)
    {
        try {
            $userId = Auth::user()->user_id;
            $profile = UserProfile::where('user_id',$userId)->first();
            if(!$profile) {
                return response()->json(['success'=>false,'message'=>'profil pengguna belum pernah dibuat.']);
            }            
            return response()->json(['success'=>true,'message'=>'profil pengguna berhasil diaambil','data' => $profile]);
        }        
        catch(\Exception $e){
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data pengguna :'.$e->getMessage(), 'error' => $e->getMessage() ]);
        } 
    }

    /**
     * pembaruan data profile pengguna
     */
    public function update(Request $request)
    {
        try {
            $userId = Auth::user()->user_id;
            $profile= UserProfile::where('user_id',$userId)->first();
            if(!$profile) {
                $profile = new UserProfile();
                $profile->user_id = $userId;
                $profile->is_aktif = 1;
                $profile->created_by = Auth::user()->username;
            }
            $profile->nama_lengkap = $request->nama_lengkap;
            $profile->jenis_kelamin = $request->jenis_kelamin;
            $profile->tempat_lahir = $request->tempat_lahir;
            $profile->tanggal_lahir = $request->tanggal_lahir;
            $profile->no_telepon = $request->no_telepon;
            $profile->instagram = $request->instagram;
            $profile->nik = $request->nik;
            $profile->avatar_path = $request->avatar_path;
            $profile->avatar_url = $request->avatar_url;
            $profile->bio_singkat = $request->bio_singkat;
            $profile->updated_by = Auth::user()->username;
            
            $success = $profile->save();
            if(!$success) {
                return response()->json(['success'=>false,'message'=>'gagal memperbarui data pengguna']);
            }
        
            return response()->json(['success'=>true,'message'=>'data pengguna berhasil diperbaharui','data' => $profile]);
        }        
        catch(\Exception $e){
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam memperbarui data pengguna :'.$e->getMessage(), 'error' => $e->getMessage() ]);
        } 
    }

    public function avatar(Request $request){
        try {
            $profile = UserProfile::where('user_id',Auth::user()->user_id)->first();                       
            if (!$request->hasFile('pic_avatar')) {
                return ['success' => false, 'message' => 'File tidak ditemukan. Data tidak dapat disimpan.','html' => ''];
            } 

            $path = Storage::disk('avatars')->putFile('avatars/users', $request->file('pic_avatar'), 'public');
            $path_url = Storage::url($path);
            $data['path'] = $path;
            $data['path_url'] = $path_url;
            
            $user = Auth::user();
            $user->avatar = $path_url;
            $success = $user->save();
            if( !$success ){
                return response()->json(['success' => false, 'message' => 'avatar gagal diupload']);
            }                
            
            if(!$profile) {
                return response()->json(['success'=>true, 'message'=>'OK', 'data'=>$data]);
            }

            else {
                $profile->avatar_path = $path;
                $profile->avatar_url = $path_url;
                $success = $user->save();
                if( !$success ){
                    return response()->json(['success' => false, 'message' => 'avatar gagal diupload']);
                }
               return response()->json(['success'=>true, 'message'=>'avatar berhasil diupload' ,'data'=>$data]);
            }
        } 
        catch(\Exception $e) {
            return response()->json(['success' => false,'message' =>'avatar gagal diperbaharui.','error'=>$e->getMessage()]);
        }
    }
    

}
