<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;

/**
 * notification
 */

use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerification;
use App\Notifications\EmailResetPassword;

/**
 * model
 */

use App\Models\User;
use App\Models\UserReg;
use App\Models\UserResetPassword;

class AuthUserController extends Controller
{
    /**
     * registration new user 
     * param : username, email, password
     * new user saved in temp table (tb_user_reg)
     * and will be moved to users when verification done,
     * 
     */
    public function signup(Request $request)
    {
        try {

            /**
             * proses validasi tetap diarahkan ke table users untuk mengetahui apakah
             * email dan username sudah pernah dibuat
             */
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:6|max:50|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }
            // return response()->json(['success'=>false,'message'=>'signup pass']);
            /**
             * pemeriksaan username di tb_user_reg
             * mengingat ada kemungkinan username sudah digunakan oleh orang lain
             * (menunggu verifikasi)
             * double check email tidak diperlukan mengingat sudah ada pengechekan diatas.
             */

            $usernameUsed = UserReg::select('reg_id')->where('username', $request->username)
                ->where('expired_at', '>', date('Y-m-d H:i:s'))
                ->where('is_verified', 0)->first();

            if ($usernameUsed) {
                return response()->json(['success' => false, 'message' => 'Username sudah digunakan']);
            }

            $regId = date('YmdHis') . "-" . Uuid::uuid4()->getHex();
            $verifToken = Str::random(20);
            $dateExpired = Carbon::now()->addDays(1);

            DB::connection('dbcentral')->beginTransaction();

            $userReg = new UserReg();
            $userReg->reg_id = $regId;
            $userReg->email = $request->email;
            $userReg->username = $request->username;
            $userReg->token = $verifToken;
            $userReg->password = bcrypt($request->password);
            $userReg->expired_at = $dateExpired;
            $userReg->is_verified = 0;
            $userReg->status = 'WAITING';
            $success = $userReg->save();
            if (!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false, 'message' => 'gagal mendaftarkan pengguna baru']);
            }
            /**
             * send email registration validation
             */

            Notification::route('mail', $request->email)->notify(new EmailVerification($regId, $verifToken));

            DB::connection('dbcentral')->commit();
            return response()->json(['success' => true, 'message' => 'Verifikasi akun pengguna-mu melalui tautan yang kami kirimkan ke email sebelum <br/><strong>' . $dateExpired . '</strong>']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam pembuatan user baru : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
    /**
     * proses validasi signup link.
     * jika benar maka data dari user_reg dipindahkan ke user
     * param : token dan id (reg_id)
     */
    public function signupVerification(Request $request)
    {
        try {

            $userReg = UserReg::select('reg_id', 'username', 'email', 'password')->where('reg_id', $request->id)
                ->where('token', $request->token)
                ->where('expired_at', '>=', date('Y-m-d H:i:s'))
                ->where('is_verified', 0)
                ->first();

            if (!$userReg) {
                return response()->json(['success' => false, 'message' => 'Verifikasi akun pengguna gagal. Data tautan tidak ditemukan.']);
            }

            $userId = date('YmdHis') . "-" . Uuid::uuid4()->getHex();
            $dateVerified = date('Y-m-d H:i:s');

            DB::connection('dbcentral')->beginTransaction();
            /**
             * create new user
             */
            $user = new User();
            $user->user_id = $userId;
            $user->username = $userReg->username;
            $user->email = $userReg->email;
            $user->password = $userReg->password;
            $user->phone_no = '';
            $user->verified_at = $dateVerified;
            $user->is_aktif = 1;
            $user->created_by = '';
            $user->updated_by = '';
            $success = $user->save();
            if (!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false, 'message' => 'Verifikasi akun pengguna gagal(1). Coba ulangi beberapa saat lagi.']);
            }

            $success = UserReg::where('reg_id', $request->id)
                ->orWhere('token', $request->token)
                ->update(['verified_at' => $dateVerified, 'is_verified' => 1, 'status' => 'VERIFIED', 'deleted_at' => date('Y-m-d H:i:s')]);
            if (!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false, 'message' => 'Verifikasi akun pengguna gagal(2). Coba ulangi beberapa saat lagi.']);
            }
            /**
             * closed all reg user that used same email or username
             */
            $email = $userReg->email;
            $username = $userReg->username;
            $otherExist = UserReg::where('reg_id', '<>', $request->id)
                ->where(function ($q) use ($email, $username) {
                    $q->where('email', $email)
                        ->orWhere('username', $username);
                })->first();

            if ($otherExist) {
                $success = UserReg::where('reg_id', '<>', $request->id)
                    ->where(function ($q) use ($email, $username) {
                        $q->where('email', $email)
                            ->orWhere('username', $username);
                    })->update(['verified_at' => $dateVerified, 'is_verified' => 0, 'status' => 'DROPPED', 'deleted_at' => date('Y-m-d H:i:s')]);

                if (!$success) {
                    DB::connection('dbcentral')->rollBack();
                    return response()->json(['success' => false, 'message' => 'Verifikasi akun pengguna gagal(3). Coba ulangi beberapa saat lagi.']);
                }
            }

            DB::connection('dbcentral')->commit();
            $data['username'] = $username;
            $data['email'] = $email;

            return response()->json(['success' => true, 'message' => 'Verifikasi akun pengguna berhasil dilakukan', 'data' => $userReg]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam verifikasi user : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    /**
     * handle login request
     * parameter username dan password
     * username bisa diisi dengan username atau email
     */
    public function login(Request $request)
    {
        try {
            $jsondata = json_decode($request);

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:6',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'username / password salah atau tidak sesuai', 'error' => $validator->errors()], 401);
            }

            $nameuser = $request->username;
            $user = User::where('is_aktif', 1)
                ->where(function ($q) use ($nameuser) {
                    $q->where('username', $nameuser)
                        ->orWhere('email', $nameuser);
                })->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'akun tidak dikenali']);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'username / password salah atau tidak sesuai']);
            }
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            $token->save();
            /**
             * setup data yang dikembalikan
             * untuk user tidak boleh dikembalikan semuanya
             */

            $auth['username'] = $user->username;
            $auth['email'] = $user->email;
            $auth['is_aktif'] = $user->is_aktif;

            if ($user->avatar !== null && $user->avatar !== '') {
                //$pathavatar = url('/storage/'.$user->avatar);
                $auth['pic_avatar'] = Storage::url($user->avatar);
                $auth['url_avatar'] = $user->avatar;
            } else {
                $auth['pic_avatar'] = '';
                $auth['url_avatar'] = '';
            }

            return response()->json([
                'success' => true,
                'isAuth' => true,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'auth' => $auth,
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam login user : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
    /**
     * permintaan user untuk melakukan reset password
     * param : email
     * link validasi akan dikirim via email ke alamat email
     * pencarian user berdasar alamat email.
     */
    public function resetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->where('is_aktif', 1)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Akun pengguna ' . $request->email . ' tidak ditemukan / belum diverifikasi.']);
            }

            $resetId = date('YmdHis') . Uuid::uuid4()->getHex();
            $token = Str::random(10);

            DB::connection('dbcentral')->beginTransaction();
            $reset = new UserResetPassword();
            $reset->reset_id = $resetId;
            $reset->token = $token;
            $reset->user_id = $user->user_id;
            $reset->email = $request->email;
            $reset->expired_at = Carbon::now()->addDays(7);
            $success = $reset->save();
            if (!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false, message => 'ada kesalahan dalam proses reset password.']);
            }

            /**
             * send link reset melalui email
             */
            Notification::route('mail', $request->email)->notify(new EmailResetPassword($resetId, $token));

            DB::connection('dbcentral')->commit();
            return response()->json(['success' => true, 'message' => 'Link reset password telah dibuat dan dikirimkan ke email anda.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mereset akun pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
    /**
     * ambil data link reset password
     * sebagai bagian dari verifikasi apakah link tautan masih valid
     * param : token dan reset_id
     */
    public function dataResetPassword(Request $request)
    {
        try {
            $reset = UserResetPassword::where('reset_id', $request->id)->where('token', $request->token)->first();
            if (!$reset) {
                return response()->json(['success' => false, 'message' => 'link / tautan reset password tidak ditemukan / tidak valid']);
            }
            return response()->json(['success' => true, 'message' => 'link / tautan reset password valid', 'data' => $reset]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam verifikasi tautan reset akun pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
    /**
     * eksekusi reset password
     * param : token, id, dan password baru
     * password user akan diganti dengan password baru 
     */
    public function resetVerification(Request $request)
    {
        try {
            /**
             * proses validasi tetap diarahkan ke table users untuk mengetahui apakah
             * email dan username sudah pernah dibuat
             */
            $validator = Validator::make($request->all(), [
                'token' => 'required|min:6',
                'new_password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'ada data tidak sesuai', 'error' => $validator->errors()]);
            }

            $reset = UserResetPassword::select('user_id', 'email')->where('reset_id', $request->id)->where('token', $request->token)->first();
            if (!$reset) {
                return response()->json(['success' => false, 'message' => 'proses reset password tidak berhasil']);
            }

            $user = User::where('user_id', $reset->user_id)->where('email', $reset->email)->where('is_aktif', 1)->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'proses reset password tidak berhasil. User tidak ditemukan.']);
            }

            DB::connection('dbcentral')->beginTransaction();
            //delete link reset password
            $success = UserResetPassword::where('reset_id', $request->id)->where('token', $request->token)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            if (!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false, 'message' => 'proses reset password tidak berhasil. Ada kesalahan dalam proses(1).']);
            }
            //update user password
            $success = User::where('user_id', $reset->user_id)->where('email', $reset->email)->update(['password' => bcrypt($request->new_password)]);
            if (!$success) {
                DB::connection('dbcentral')->rollBack();
                return response()->json(['success' => false, 'message' => 'proses reset password tidak berhasil. Ada kesalahan dalam proses(2).']);
            }
            DB::connection('dbcentral')->commit();
            return response()->json(['success' => true, 'message' => 'Yeayy, reset password berhasil dilakukan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam verifikasi reset akun pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
    /**
     * user logout 
     * tidak ada parameter yang dikirim.
     * proses akan menghapus token passport.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return response()->json(['success' => true, 'message' => 'user signout']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam logout pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }


    /***
     * Ubah password.
     * parameter : password dan new_password
     * apabila password sama dengan password saat ini 
     * maka akan dilanjutkan prosesnya
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|min:6',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'panjang password tidak sesuai', 'error' => $validator->errors()], 401);
            }

            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'password user salah/tidak sesuai.']);
            }

            $user->password =  bcrypt($request->new_password);
            $success = $user->save();

            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan saat memprbaharui password']);
            }
            return response()->json(['success' => true, 'message' => 'password berhasil diubah.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengubah password pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
