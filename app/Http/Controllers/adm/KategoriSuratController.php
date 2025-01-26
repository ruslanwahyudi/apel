<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use Illuminate\Http\Request;

class KategoriSuratController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kategori = KategoriSurat::all();

            return response()->json($kategori);
        }

        return view('adm.kategori-surat.index');
    }

    public function create()
    {
        return view('adm.kategori-surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_surat,nama'
        ]);

        KategoriSurat::create($request->all());

        return redirect()
            ->route('adm.kategori-surat')
            ->with('success', 'Kategori surat berhasil ditambahkan');
    }

    public function edit(KategoriSurat $kategori)
    {
        return view('adm.kategori-surat.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriSurat $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_surat,nama,' . $kategori->id
        ]);

        $kategori->update($request->all());

        return redirect()
            ->route('adm.kategori-surat')
            ->with('success', 'Kategori surat berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $kategori = KategoriSurat::where('nama', 'like', '%' . $request->search . '%')->get();
        return response()->json($kategori);
    }

    public function destroy(KategoriSurat $kategori)
    {
        // if ($kategori->surat()->exists()) {
        //     return redirect()
        //         ->route('adm.kategori-surat')
        //         ->with('error', 'Kategori surat tidak dapat dihapus karena masih digunakan');
        // }

        $kategori->delete();

        return response()->json(['message' => 'Kategori surat berhasil dihapus']);
    }
} 