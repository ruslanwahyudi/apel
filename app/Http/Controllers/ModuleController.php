<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    //
    // Menampilkan daftar role
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $modules = Module::orderBy('no_urut', 'asc')->get();
            return response()->json($modules); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('admin.modules.index'); // Mengembalikan daftar role dalam format JSON
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        return view('admin.modules.create');
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name',
            'no_urut' => 'required|integer',
            'icon' => 'nullable|string|max:255',
        ]);

        $module = Module::create([
            'name' => $request->name,
            'no_urut' => $request->no_urut,
            'icon' => $request->icon,
        ]);

        // Mengembalikan tampilan HTML setelah menyimpan role
        return redirect()->route('admin.modules')->with('success', 'Module berhasil ditambahkan');
        // return response()->json(['status' => 'success','message' => 'Module berhasil ditambahkan']);
    }

    public function show(Module $module)
    {
        return response()->json($module);
    }


    // Menampilkan form untuk mengedit role
    public function edit(Module $module)
    {
        return view('admin.modules.edit', compact('module'));
    }

    // Memperbarui role
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name,' . $module->id,
            'no_urut' => 'required|integer',
            'icon' => 'nullable|string|max:255',
        ]);

        $module->update([
            'name' => $request->name,
            'no_urut' => $request->no_urut,
            'icon' => $request->icon,
        ]);

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        return redirect()->route('admin.modules')->with('success', 'Module berhasil diperbarui.');
    }

    // Menghapus role
    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['status' => 'success','message' => 'Module berhasil dihapus']);

    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $modules = Module::where('name', 'like', '%' . $search . '%')->get();
        return response()->json($modules);
    }
}
