<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Module;
use App\Models\RolePrivilege;
use App\Models\Roles_type;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    //
    public function index()
    {
        $roles = Roles_type::all();
        $moduls = Module::with('menus')->get();
        // $menus = Menu::all();
        return view('admin.role_privileges.index', compact('roles', 'moduls'));
    }

    // app/Http/Controllers/PermissionController.php
    public function getPrivileges(Request $request)
    {
        $roleId = $request->query('role_id');
        $privileges = RolePrivilege::where('role_id', $roleId)->get()->keyBy('menu_id');

        return response()->json($privileges);
    }

    public function create(){
        $roles = Roles_type::all();
        $moduls = Module::with('menus')->get();
        // $menus = Menu::all();
        return view('admin.role_privileges.create', compact('roles', 'moduls'));
    }

    public function store(Request $request)
    {
        $roleId = $request->input('role_id');
        $privileges = $request->input('privileges');

        $request->validate([
            'role_id' => 'required|exists:roles_type,id',
            'privileges' => 'required|array',
        ]);

        // Hapus privileges lama untuk role ini
        RolePrivilege::where('role_id', $roleId)->delete();
        // dd($privileges);
        // Simpan privileges baru
        foreach ($privileges as $menuId => $actions) {
            $moduleId = Menu::find($menuId)->modul_id;
            RolePrivilege::create([
                'role_id' => $roleId,
                'module_id' => $moduleId,
                'menu_id' => $menuId,
                'is_visible' => isset($actions['is_visible']),
                'can_print' => isset($actions['can_print']),
                'can_create' => isset($actions['can_create']),
                'can_read' => isset($actions['can_read']),
                'can_update' => isset($actions['can_update']),
                'can_delete' => isset($actions['can_delete']),
            ]);
        }

        // return redirect()->route('admin.permissions.index')->with('success', 'Privileges updated successfully!');
    }
}
