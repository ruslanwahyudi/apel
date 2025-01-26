<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\blog\Kategori;
class KategoriController extends Controller
{
    // Menampilkan daftar role
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kategori = Kategori::orderBy('id', 'asc')->get();
            return response()->json($kategori); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('blog.kategori.index'); // Mengembalikan daftar role dalam format JSON
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        return view('blog.kategori.create');
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:blog_kategori,nama',
            'slug' => 'required|string|max:255|unique:blog_kategori,slug',
        ]);

        $kategori = Kategori::create([
            'nama' => $request->nama,
            'slug' => $request->slug,
        ]);

        // Mengembalikan tampilan HTML setelah menyimpan role
        return redirect()->route('blog.kategori')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show(Kategori $kategori)
    {
        return response()->json($kategori);
    }


    // Menampilkan form untuk mengedit role
    public function edit(Kategori $kategori)
    {
        return view('blog.kategori.edit', compact('kategori'));
    }

    // Memperbarui role
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:blog_kategori,nama,' . $kategori->id,
            'slug' => 'required|string|max:255|unique:blog_kategori,slug,' . $kategori->id,
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'slug' => $request->slug,
        ]);

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        return redirect()->route('blog.kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    // Menghapus role
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return response()->json(['status' => 'success','message' => 'Kategori berhasil dihapus']);

    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $kategori = Kategori::where('nama', 'like', '%' . $search . '%')->get();
        return response()->json($kategori);
    }
}
