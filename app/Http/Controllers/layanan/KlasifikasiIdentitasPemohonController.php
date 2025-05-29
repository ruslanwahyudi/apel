<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use App\Models\Layanan\KlasifikasiIdentitasPemohon;
use Illuminate\Http\Request;

class KlasifikasiIdentitasPemohonController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $klasifikasi = KlasifikasiIdentitasPemohon::orderBy('urutan')->get();
            return response()->json($klasifikasi);
        }

        return view('layanan.klasifikasi.index');
    }

    public function create()
    {
        return view('layanan.klasifikasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_klasifikasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer'
        ]);

        try {
            KlasifikasiIdentitasPemohon::create([
                'nama_klasifikasi' => $request->nama_klasifikasi,
                'deskripsi' => $request->deskripsi,
                'urutan' => $request->urutan ?? 0,
                'status' => $request->has('status') ? true : false
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Klasifikasi identitas pemohon berhasil ditambahkan!'
                ]);
            }

            return redirect()->route('layanan.klasifikasi')
                ->with('success', 'Klasifikasi identitas pemohon berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(KlasifikasiIdentitasPemohon $klasifikasi)
    {
        return response()->json($klasifikasi);
    }

    public function edit(KlasifikasiIdentitasPemohon $klasifikasi)
    {
        return view('layanan.klasifikasi.edit', compact('klasifikasi'));
    }

    public function update(Request $request, KlasifikasiIdentitasPemohon $klasifikasi)
    {
        $request->validate([
            'nama_klasifikasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer'
        ]);

        try {
            $klasifikasi->update([
                'nama_klasifikasi' => $request->nama_klasifikasi,
                'deskripsi' => $request->deskripsi,
                'urutan' => $request->urutan ?? 0,
                'status' => $request->has('status')
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Klasifikasi identitas pemohon berhasil diperbarui!'
                ]);
            }

            return redirect()->route('layanan.klasifikasi')
                ->with('success', 'Klasifikasi identitas pemohon berhasil diperbarui!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(KlasifikasiIdentitasPemohon $klasifikasi)
    {
        try {
            $klasifikasi->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Klasifikasi identitas pemohon berhasil dihapus!'
                ]);
            }

            return redirect()->route('layanan.klasifikasi')
                ->with('success', 'Klasifikasi identitas pemohon berhasil dihapus!');
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
}
