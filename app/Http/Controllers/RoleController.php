<?php

namespace App\Http\Controllers;

use App\Models\Roles_type;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
    // Menampilkan daftar role
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Roles_type::all();
            return response()->json($roles); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('admin.roles.index'); // Mengembalikan daftar role dalam format JSON
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        return view('admin.roles.create');
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Roles_type::create([
            'name' => $request->name,
        ]);

        // Mengembalikan tampilan HTML setelah menyimpan role
        return redirect()->route('admin.roles')->with('success', 'Role berhasil ditambahkan');
    }

    public function show(Roles_type $role)
    {
        return response()->json($role);
    }


    // Menampilkan form untuk mengedit role
    public function edit(Roles_type $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    // Memperbarui role
    public function update(Request $request, Roles_type $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        return redirect()->route('admin.roles')->with('success', 'Roles_type berhasil diperbarui.');
    }

    // Menghapus role
    public function destroy(Roles_type $role)
    {
        $role->delete();
        return response()->json(['status' => 'success','message' => 'Role berhasil dihapus']);

        // return redirect()->route('admin.roles')->with('success', 'Roles_type berhasil dihapus.');
        // return response()->json(['message' => 'Role berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $roles = Roles_type::where('name', 'like', '%' . $search . '%')->get();
        return response()->json($roles);
    }
}
