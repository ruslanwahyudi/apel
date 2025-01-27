<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use App\Models\layanan\IdentitasLayanan;
use Illuminate\Http\Request;

class IdentitasLayananController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $identitas = IdentitasLayanan::latest()->get();
            return response()->json($identitas);
        }

        return view('layanan.identitas.index');
    }

    public function create()
    {
        return view('layanan.identitas.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'nama_field' => 'required|string|max:255',
            'tipe_field' => 'required|string|max:255',
            'required' => 'required|boolean',
        ]);

        try {
            IdentitasLayanan::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Identitas layanan berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(IdentitasLayanan $identitas)
    {
        return response()->json($identitas);
    }

    public function edit(IdentitasLayanan $identitas)
    {
        return view('layanan.identitas.edit', compact('identitas'));
    }

    public function update(Request $request, IdentitasLayanan $identitas)
    {
        $validatedData = $request->validate([
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'nama_field' => 'required|string|max:255',
            'tipe_field' => 'required|string|max:255',
            'required' => 'required|boolean',
        ]);

        try {
            $identitas->update($validatedData); 

            return response()->json([
                'success' => true,
                'message' => 'Identitas layanan berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(IdentitasLayanan $identitas)
    {
        try {
            $identitas->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Identitas layanan berhasil dihapus!'
                ]);
            }

            return redirect()->route('layanan.identitas')
                ->with('success', 'Identitas layanan berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function search($search)
    {
        $identitas = IdentitasLayanan::where('nama', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->orWhere('dasar_hukum', 'like', "%{$search}%")
            ->orWhere('prosedur', 'like', "%{$search}%")
            ->get();

        return response()->json($identitas);
    }
} 