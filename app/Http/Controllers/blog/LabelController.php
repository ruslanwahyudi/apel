<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\blog\Label;
class LabelController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $label = Label::orderBy('id', 'asc')->get();
            return response()->json($label); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('blog.label.index'); // Mengembalikan daftar role dalam format JSON
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        return view('blog.label.create');
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:blog_label,nama',
            'slug' => 'required|string|max:255|unique:blog_label,slug',
        ]);

        $label = Label::create([
            'nama' => $request->nama,
            'slug' => $request->slug,
        ]);

        // Mengembalikan tampilan HTML setelah menyimpan role
        return redirect()->route('blog.label')->with('success', 'Label berhasil ditambahkan');
    }

    public function show(Label $label)
    {
        return response()->json($label);
    }


    // Menampilkan form untuk mengedit role
    public function edit(Label $label)
    {
        return view('blog.label.edit', compact('label'));
    }

    // Memperbarui role
    public function update(Request $request, Label $label)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:blog_label,nama,' . $label->id,
            'slug' => 'required|string|max:255|unique:blog_label,slug,' . $label->id,
        ]);

        $label->update([
            'nama' => $request->nama,
            'slug' => $request->slug,
        ]);

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        return redirect()->route('blog.label')->with('success', 'Label berhasil diperbarui.');
    }

    // Menghapus role
    public function destroy(Label $label)
    {
        $label->delete();
        return response()->json(['status' => 'success','message' => 'Label berhasil dihapus']);

    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $label = Label::where('nama', 'like', '%' . $search . '%')->get();
        return response()->json($label);
    }
}
