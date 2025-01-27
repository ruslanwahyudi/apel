<?php

namespace App\Http\Controllers;

use App\Models\Roles_type;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('role')->get();
            return response()->json($users); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('admin.users.index'); // Mengembalikan daftar role dalam format JSON
    }

    public function create(){
        $roles = Roles_type::all();
        return view('admin.users.create', compact('roles'));
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles_type,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('a'),
            'role_id' => $request->role_id,
        ]);

        // Mengembalikan tampilan HTML setelah menyimpan role
        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        return response()->json($user);
    }


    // Menampilkan form untuk mengedit role
    public function edit(User $user)
    {
        $roles = Roles_type::all();
        return view('admin.users.edit', compact('user','roles'));
    }

    // Memperbarui role
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',    
            'role_id' => 'required|exists:roles_type,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            // 'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui.');
    }

    // Menghapus role
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['status' => 'success','message' => 'User berhasil dihapus']);

    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $users = User::where('name', 'like', '%' . $search . '%')->with('role')->get();
        return response()->json($users);
    } 
}
