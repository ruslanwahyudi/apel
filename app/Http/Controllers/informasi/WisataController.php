<?php

namespace App\Http\Controllers\informasi;

use App\Http\Controllers\Controller;
use App\Models\informasi\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WisataController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $wisata = Wisata::latest()->get();
            return response()->json($wisata);
        }

        return view('informasi.wisata.index');
    }

    public function create()
    {
        return view('informasi.wisata.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'fasilitas' => 'nullable|string',
            'jam_buka' => 'nullable|string',
            'jam_tutup' => 'nullable|string',
            'harga_tiket' => 'nullable|numeric|min:0',
            'kontak' => 'nullable|string|max:50',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'petunjuk_arah' => 'nullable|string',
            'status' => 'required|string|max:255'
        ]);

        try {
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $nama_gambar = 'wisata-' . Str::slug($request->nama) . '-' . time() . '.' . $gambar->getClientOriginalExtension();
                $path = $gambar->storeAs('public/wisata', $nama_gambar);
                $path = str_replace('public/', 'storage/', $path);
            }

            Wisata::create([
                'nama' => $request->nama,
                'lokasi' => $request->lokasi,
                'deskripsi' => $request->deskripsi,
                'fasilitas' => $request->fasilitas,
                'jam_buka' => $request->jam_buka,
                'jam_tutup' => $request->jam_tutup,
                'harga_tiket' => $request->harga_tiket,
                'kontak' => $request->kontak,
                'gambar' => $path ?? null,
                'petunjuk_arah' => $request->petunjuk_arah,
                'status' => $request->status
            ]);

            return redirect()->route('info.wisata')
                ->with('success', 'Wisata berhasil ditambahkan!');
        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete(str_replace('storage/', 'public/', $path));
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Wisata $wisata)
    {
        return response()->json($wisata);
    }

    public function edit(Wisata $wisata)
    {
        return view('informasi.wisata.edit', compact('wisata'));
    }

    public function update(Request $request, Wisata $wisata)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'fasilitas' => 'nullable|string',
            'jam_buka' => 'nullable|string',
            'jam_tutup' => 'nullable|string',
            'harga_tiket' => 'nullable|numeric|min:0',
            'kontak' => 'nullable|string|max:50',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'petunjuk_arah' => 'nullable|string',
            'status' => 'required|string|max:255'
        ]);

        try {
            $old_gambar = $wisata->gambar;

            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $nama_gambar = 'wisata-' . Str::slug($request->nama) . '-' . time() . '.' . $gambar->getClientOriginalExtension();
                $path = $gambar->storeAs('public/wisata', $nama_gambar);
                $path = str_replace('public/', 'storage/', $path);

                if ($old_gambar) {
                    Storage::delete(str_replace('storage/', 'public/', $old_gambar));
                }
            }

            $wisata->update([
                'nama' => $request->nama,
                'lokasi' => $request->lokasi,
                'deskripsi' => $request->deskripsi,
                'fasilitas' => $request->fasilitas,
                'jam_buka' => $request->jam_buka,
                'jam_tutup' => $request->jam_tutup,
                'harga_tiket' => $request->harga_tiket,
                'kontak' => $request->kontak,
                'gambar' => $path ?? $old_gambar,
                'petunjuk_arah' => $request->petunjuk_arah,
                'status' => $request->status
            ]);

            return redirect()->route('info.wisata')
                ->with('success', 'Wisata berhasil diperbarui!');
        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete(str_replace('storage/', 'public/', $path));
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Wisata $wisata)
    {
        try {
            if ($wisata->gambar) {
                Storage::delete(str_replace('storage/', 'public/', $wisata->gambar));
            }

            $wisata->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Wisata berhasil dihapus!'
                ]);
            }

            return redirect()->route('info.wisata')
                ->with('success', 'Wisata berhasil dihapus!');
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
        $wisata = Wisata::where('nama', 'like', "%{$search}%")
            ->orWhere('lokasi', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->get();

        return response()->json($wisata);
    }
} 