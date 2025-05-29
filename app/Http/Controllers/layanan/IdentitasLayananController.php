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
        \Log::info('Store identitas pemohon request', [
            'all_data' => $request->all(),
        ]);
        
        $validatedData = $request->validate([
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'klasifikasi_id' => 'nullable|exists:duk_klasifikasi_identitas_pemohon,id',
            'nama_field' => 'required|string|max:255',
            'tipe_field' => 'required|string|max:255',
            'required' => 'required|boolean',
        ]);
        
        \Log::info('Validated data', [
            'validated_data' => $validatedData,
        ]);

        try {
            // Gunakan IdentitasPemohon sebagai model karena ini untuk identitas pemohon
            $identitas = \App\Models\Layanan\IdentitasPemohon::create($validatedData);
            
            \Log::info('Identitas pemohon created', [
                'id' => $identitas->id,
                'klasifikasi_id' => $identitas->klasifikasi_id,
                'nama_field' => $identitas->nama_field,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Identitas layanan berhasil ditambahkan!',
                'data' => $identitas
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating identitas pemohon', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(IdentitasLayanan $identitas)
    {
        try {
            // Karena IdentitasLayanan dan IdentitasPemohon menggunakan tabel yang sama,
            // kita dapat mengambil instance IdentitasPemohon dengan ID yang sama
            $identitasPemohon = \App\Models\Layanan\IdentitasPemohon::with('klasifikasi')->findOrFail($identitas->id);
            
            \Log::info('Show identitas pemohon', [
                'id' => $identitasPemohon->id,
                'nama_field' => $identitasPemohon->nama_field,
                'klasifikasi_id' => $identitasPemohon->klasifikasi_id,
                'klasifikasi' => $identitasPemohon->klasifikasi
            ]);
            
            return response()->json($identitasPemohon);
        } catch (\Exception $e) {
            \Log::error('Error showing identitas pemohon', [
                'id' => $identitas->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit(IdentitasLayanan $identitas)
    {
        return view('layanan.identitas.edit', compact('identitas'));
    }

    public function update(Request $request, IdentitasLayanan $identitas)
    {
        \Log::info('Update identitas pemohon request', [
            'id' => $identitas->id,
            'all_data' => $request->all(),
        ]);
        
        $validatedData = $request->validate([
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'klasifikasi_id' => 'nullable|exists:duk_klasifikasi_identitas_pemohon,id',
            'nama_field' => 'required|string|max:255',
            'tipe_field' => 'required|string|max:255',
            'required' => 'required|boolean',
        ]);
        
        \Log::info('Validated update data', [
            'validated_data' => $validatedData,
        ]);

        try {
            // Karena IdentitasLayanan dan IdentitasPemohon menggunakan tabel yang sama,
            // kita dapat mengambil instance IdentitasPemohon dengan ID yang sama
            $identitasPemohon = \App\Models\Layanan\IdentitasPemohon::findOrFail($identitas->id);
            $identitasPemohon->update($validatedData);
            
            \Log::info('Identitas pemohon updated', [
                'id' => $identitasPemohon->id,
                'klasifikasi_id' => $identitasPemohon->klasifikasi_id,
                'nama_field' => $identitasPemohon->nama_field,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Identitas layanan berhasil diperbarui!',
                'data' => $identitasPemohon
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating identitas pemohon', [
                'id' => $identitas->id,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
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