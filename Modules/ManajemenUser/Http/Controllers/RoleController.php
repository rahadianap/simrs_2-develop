<?php

namespace Modules\ManajemenUser\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\ManajemenUser\Entities\Role;
use Modules\ManajemenUser\Entities\RoleMenu;

use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\AppMenu;

class RoleController extends Controller
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

    public function lists(Request $request)
    {
        try {
            $perPage = 10;
            if ($request->has('per_page')) {
                $perPage = $request->get('per_page');
                if($perPage == 'ALL') {
                    $perPage = Role::where('client_id')->count();
                }
            }

            /**
             * tambahkan semua filter yang dikirim dari client
             */
            $query = Role::query();
            if($request->has('is_aktif')) {
                $query = $query->where('is_aktif', 'ILIKE', '%' . $request->get('is_aktif') . '%');
            }
            $keyword = '%%';
            if($request->has('keyword')) {
                $keyword = '%'.$request->get('keyword').'%';
            }
            foreach ($request->except(['_token','keyword']) as $key => $data) {
                if ($key !== "page" && $key !== "per_page" && $key !== "is_aktif" && $key !== "aktif") {
                    $query = $query->where($key, 'ILIKE', '%' . $data . '%');
                }
            }

            $query = $query->where(function($query) use($keyword){
                $query->where('role_nama','ILIKE',$keyword)
                ->orWhere('role_id','ILIKE',$keyword)
                ->orWhere('deskripsi','ILIKE',$keyword);
            });

            $data = $query->where('client_id', $this->clientId)->paginate($perPage);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil daftar role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function dataRole(Request $request, $id)
    {
        try {
            $data = Role::where('client_id', $this->clientId)->where('role_id', $id)->first();
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $data['menus'] = $this->getRoleMenus($id);
            return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam mengambil data role pengguna : ' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getRoleMenus($id)
    {
        try {
            $menus = RoleMenu::where('role_id', $id)
                ->where('client_id', $this->clientId)
                ->where('is_aktif', 1)
                ->with(['detail' => function ($q) {
                    $q->select(['menu_id', 'group_menu', 'menu_title', 'is_nav_header', 'menu_icon', 'menu_link']);
                }])->get();

            $buffmenus = [];
            foreach ($menus as $mn) {
                $buffmenus[] = $mn->detail;
            }
            return $buffmenus;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function createRole(Request $request)
    {
        try {
            $roleId = $this->getRoleId();
            $role = new Role();
            $role->role_id = $roleId;
            $role->role_nama = strtoupper($request->role_nama);
            $role->deskripsi = $request->deskripsi;
            $role->is_ubah_tanggal = $request->is_ubah_tanggal;
            $role->is_multi_dokter = $request->is_multi_dokter;
            $role->is_aktif = $request->is_aktif;
            $role->created_by = Auth::user()->username;
            $role->updated_by = Auth::user()->username;
            $role->client_id = $this->clientId;
            DB::connection('dbclient')->beginTransaction();
            $success = $role->save();
            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data role.']);
            }

            foreach ($request->menus as $mn) {
                if ($mn !== null) {
                    $menudata = AppMenu::where('menu_id', $mn['menu_id'])->first();
                    if ($menudata) {
                        $menu = new RoleMenu();
                        $menu->role_id = $roleId;
                        $menu->menu_id = $menudata->menu_id;
                        $menu->created_by = Auth::user()->username;
                        $menu->client_id = $this->clientId;
                        $menu->is_admin_mandatory = $menudata->is_admin_mandatory;
                        $menu->otorisasi = $menudata->otorisasi;
                        $menu->is_aktif = 1;
                        $menu->updated_by = Auth::user()->username;
                        $success = $menu->save();
                        if (!$success) {
                            DB::connection('dbclient')->rollBack();
                            return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data role menu baru']);
                        }
                    }
                }
            }

            DB::connection('dbclient')->commit();
            $role['menus'] = $this->getRoleMenus($roleId);
            return response()->json(['success' => true, 'message' => 'role berhasil di update.', 'data' => $role]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data role :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function getRoleId()
    {
        try {
            $id = $this->clientId . '.ROLE-0001';
            $maxId = Role::withTrashed()->where('role_id', 'LIKE', $this->clientId . '.ROLE-%')->max('role_id');
            if (!$maxId) {
                $id = $this->clientId . '.ROLE-0001';
            } else {
                $maxId = str_replace($this->clientId . '.ROLE-', '', $maxId);
                $count = $maxId + 1;
                if ($count < 10) {
                    $id = $this->clientId . '.ROLE-000' . $count;
                } elseif ($count >= 10 && $count < 100) {
                    $id = $this->clientId . '.ROLE-00' . $count;
                } elseif ($count >= 100 && $count < 1000) {
                    $id = $this->clientId . '.ROLE-0' . $count;
                } else {
                    $id = $this->clientId . '.ROLE-' . $count;
                }
            }
            return $id;
        } catch (\Exception $e) {
            return $this->clientId . '.ROLE-' . Uuid::uuid4()->getHex();
        }
    }

    public function updateRole(Request $request)
    {
        try {
            $roleId = $request->role_id;
            $role = Role::where('role_id', $roleId)->where('client_id', $this->clientId)->first();
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data. data tidak ditemukan.']);
            }

            // $role->role_nama = $request->role_nama;
            // $role->deskripsi = $request->deskripsi;
            // $role->is_ubah_tanggal = $request->is_ubah_tanggal;
            // $role->is_multi_dokter = $request->is_multi_dokter;
            // $role->is_aktif = $request->is_aktif;
            // $role->updated_by = Auth::user()->username;        

            DB::connection('dbclient')->beginTransaction();
            $success = Role::where('role_id', $roleId)->where('client_id', $this->clientId)->update([
                'role_nama' => $request->role_nama,
                'deskripsi' => $request->deskripsi,
                'is_multi_dokter' => $request->is_multi_dokter,
                'is_ubah_tanggal' => $request->is_ubah_tanggal,
                'is_aktif' => $request->is_aktif,
                'updated_by' => Auth::user()->username
            ]);

            if (!$success) {
                DB::connection('dbclient')->rollBack();
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam update penyimpanan data role.']);
            }

            RoleMenu::where('client_id', $this->clientId)->where('role_id', $roleId)->update(['is_aktif' => 0]);

            if (count($request->menus) > 0) {
                foreach ($request->menus as $mn) {
                    if ($mn !== null) {
                        $menudata = AppMenu::where('menu_id', $mn['menu_id'])->first();
                        if ($menudata) {
                            $menu = RoleMenu::withTrashed()->where('client_id', $this->clientId)->where('role_id', $roleId)->where('menu_id', $mn['menu_id'])->first();
                            if (!$menu) {
                                $menu = new RoleMenu();
                                $menu->role_id = $roleId;
                                $menu->menu_id = $menudata->menu_id;
                                $menu->created_by = Auth::user()->username;
                                $menu->client_id = $this->clientId;
                                $menu->is_admin_mandatory = $menudata->is_admin_mandatory;
                                $menu->otorisasi = $menudata->otorisasi;
                                $menu->is_aktif = 1;
                                $menu->updated_by = Auth::user()->username;
                                $success = $menu->save();
                            } else {
                                $success = RoleMenu::withTrashed()
                                    ->where('client_id', $this->clientId)
                                    ->where('role_id', $roleId)->where('menu_id', $mn['menu_id'])
                                    ->update(['is_aktif' => 1, 'updated_by' => Auth::user()->username, 'deleted_at' => null]);
                            }

                            if (!$success) {
                                DB::connection('dbclient')->rollBack();
                                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data role menu baru']);
                            }
                        }
                    }
                }
            }


            $isdeleted = RoleMenu::where('client_id', $this->clientId)->where('role_id', $roleId)->where('is_aktif', 0)->first();
            if ($isdeleted) {
                $success = RoleMenu::where('role_id', $roleId)->where('is_aktif', 0)->delete();
                if (!$success) {
                    DB::connection('dbclient')->rollBack();
                    return response()->json(['success' => false, 'message' => 'ada kesalahan dalam penyimpanan data (hapus archive)']);
                }
            }

            DB::connection('dbclient')->commit();

            $role = Role::where('role_id', $roleId)->where('client_id', $this->clientId)->first();
            $role['menus'] = $this->getRoleMenus($roleId);
            return response()->json(['success' => true, 'message' => 'role berhasil di update.', 'data' => $role]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal mengupdate data role :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }

    public function deleteRole(Request $request, $id)
    {
        try {
            $role = Role::where('role_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->first();
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role pengguna tidak ditemukan.']);
            }
            $success = Role::where('role_id', $id)->where('client_id', $this->clientId)->where('is_aktif', 1)->update(['is_aktif' => 0, 'updated_by' => Auth::user()->username]);
            if (!$success) {
                return response()->json(['success' => false, 'message' => 'ada kesalahan dalam menonaktifkan role']);
            }
            return response()->json(['success' => true, 'message' => 'Role pengguna berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'gagal menonaktifkan data role :' . $e->getMessage(), 'error' => $e->getMessage()]);
        }
    }
}
