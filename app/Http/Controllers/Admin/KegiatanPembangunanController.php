<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KegiatanPembangunan;
use App\Models\ProgresPembangunan;
use App\Models\FotoProgresPembangunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanPembangunanController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $kegiatan = KegiatanPembangunan::with(['user', 'progres'])
                        ->latest()
                        ->get();
            
            return response()->json($kegiatan);
        }

        return view('admin.pembangunan.index');
    }

    public function create()
    {
        return view('admin.pembangunan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'anggaran' => 'required|numeric',
            'sumber_dana' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'pelaksana' => 'required|string|max:255',
            'status' => 'required|in:Belum Dimulai,Dalam Pengerjaan,Selesai,Terhenti'
        ]);

        $kegiatan = KegiatanPembangunan::create([
            'user_id' => auth()->id(),
            ...$request->all()
        ]);

        return redirect()
            ->route('admin.pembangunan')
            ->with('success', 'Kegiatan pembangunan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kegiatan = KegiatanPembangunan::findOrFail($id);
        return view('admin.pembangunan.edit', compact('kegiatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'anggaran' => 'required|numeric',
            'sumber_dana' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'pelaksana' => 'required|string|max:255',
            'status' => 'required|in:Belum Dimulai,Dalam Pengerjaan,Selesai,Terhenti'
        ]);

        $kegiatan = KegiatanPembangunan::findOrFail($id);
        $kegiatan->update($request->all());

        return redirect()
            ->route('admin.pembangunan')
            ->with('success', 'Kegiatan pembangunan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kegiatan = KegiatanPembangunan::findOrFail($id);
        
        // Delete related photos
        foreach($kegiatan->progres as $progres) {
            foreach($progres->fotos as $foto) {
                Storage::delete('public/pembangunan/' . $foto->foto);
            }
        }
        
        $kegiatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan pembangunan berhasil dihapus'
        ]);
    }
} 