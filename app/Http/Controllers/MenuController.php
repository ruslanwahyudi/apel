<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $menus = Menu::with('module')->get();
            return response()->json($menus); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('admin.menus.index'); // Mengembalikan daftar role dalam format JSON
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        $modules = Module::all();
        return view('admin.menus.create', compact('modules'));
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menus,name',
            'route' => 'required|string|max:255',
            'modul_id' => 'required|exists:modules,id',
            'no_urut' => 'required|integer',
            'icon' => 'nullable|string|max:255',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'route' => $request->route,
            'modul_id' => $request->modul_id,
            'no_urut' => $request->no_urut,
            'icon' => $request->icon,
        ]);

        // Mengembalikan tampilan HTML setelah menyimpan role
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function show(Menu $menu)
    {
        return response()->json($menu);
    }


    // Menampilkan form untuk mengedit role
    public function edit(Menu $menu)
    {
        $modules = Module::all();
        return view('admin.menus.edit', compact('menu','modules'));
    }

    // Memperbarui role
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menus,name,' . $menu->id,
            'route' => 'required|string|max:255',
            'modul_id' => 'required|exists:modules,id',
            'no_urut' => 'required|integer',
            'icon' => 'nullable|string|max:255',
        ]);

        $menu->update([
            'name' => $request->name,
            'route' => $request->route,
            'modul_id' => $request->modul_id,
            'no_urut' => $request->no_urut,
            'icon' => $request->icon,
        ]);

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil diperbarui.');
    }

    // Menghapus role
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['status' => 'success','message' => 'Menu berhasil dihapus']);

    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $menus = Menu::where('name', 'like', '%' . $search . '%')
        ->orWhereHas('module', function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->with('module')
        ->get();
        return response()->json($menus);
    }
}
