<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use App\Models\layanan\IdentitasLayanan;
use App\Models\Layanan\IdentitasPemohon;
use App\Models\layanan\JenisLayanan;
use App\Models\Layanan\JenisPelayanan;
use Illuminate\Http\Request;

class JenisLayananController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $jenis_layanan = JenisPelayanan::with('identitasPemohon', 'syaratDokumen')->latest()->get();
            
            return response()->json($jenis_layanan);
        }

        return view('layanan.jenis.index');
    }

    public function create()
    {
        return view('layanan.jenis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        try {
            JenisLayanan::create([
                'nama_pelayanan' => $request->nama_pelayanan,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('layanan.jenis')
                ->with('success', 'Jenis layanan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(JenisPelayanan $jenis)
    {
        return response()->json($jenis);
    }

    public function edit(JenisPelayanan $jenis)
    {
        return view('layanan.jenis.edit', compact('jenis'));
    }

    public function update(Request $request, JenisPelayanan $jenis)
    {
        $request->validate([
            'nama_pelayanan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        try {
            $jenis->update([
                'nama_pelayanan' => $request->nama_pelayanan,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('layanan.jenis')
                ->with('success', 'Jenis layanan berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(JenisPelayanan $jenis)
    {
        try {
            $jenis->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis layanan berhasil dihapus!'
                ]);
            }

            return redirect()->route('layanan.jenis')
                ->with('success', 'Jenis layanan berhasil dihapus!');
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
        $jenis_layanan = JenisPelayanan::where('nama_pelayanan', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->get();

        return response()->json($jenis_layanan);
    }


} 