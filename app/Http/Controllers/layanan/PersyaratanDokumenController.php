<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use App\Models\layanan\JenisLayanan;
use App\Models\layanan\PersyaratanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersyaratanDokumenController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $persyaratan = JenisLayanan::with(['syaratDokumen' => function($query) {
                $query->select('id', 'jenis_pelayanan_id', 'nama_dokumen', 'deskripsi', 'required');
            }])
            ->select('id', 'nama_pelayanan')
            ->get()
            ->map(function($jenisLayanan) {
                return $jenisLayanan->syaratDokumen->map(function($syarat) use ($jenisLayanan) {
                    return [
                        'id' => $syarat->id,
                        'jenis_layanan' => [
                            'id' => $jenisLayanan->id,
                            'nama_pelayanan' => $jenisLayanan->nama_pelayanan
                        ],
                        'nama_dokumen' => $syarat->nama_dokumen,
                        'deskripsi' => $syarat->deskripsi,
                        'required' => $syarat->required
                    ];
                });
            })
            ->flatten(1);

            return response()->json($persyaratan);
        }

        return view('layanan.persyaratan.index');
    }

    public function create()
    {
        $jenisLayanan = JenisLayanan::all();
        return view('layanan.persyaratan.create', compact('jenisLayanan'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_dokumen' => 'required|string|max:255',    
            'deskripsi' => 'nullable|string',
            'required' => 'required|boolean', 
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id'
        ]);

        try {
            PersyaratanDokumen::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Persyaratan dokumen berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
    }

    public function show(PersyaratanDokumen $persyaratan)
    {
        return response()->json($persyaratan);
    }

    public function edit(PersyaratanDokumen $persyaratan)
    {
        return view('layanan.persyaratan.edit', compact('persyaratan'));
    }

    public function update(Request $request, PersyaratanDokumen $persyaratan)
    {
        $validatedData = $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'required' => 'required|boolean',
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id'
        ]);

        try {
            $persyaratan->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Persyaratan dokumen berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(PersyaratanDokumen $persyaratan)
    {
        try {
            $persyaratan->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Persyaratan dokumen berhasil dihapus!'
                ]);
            }

            return redirect()->route('layanan.persyaratan')
                ->with('success', 'Persyaratan dokumen berhasil dihapus!');
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
        $persyaratan = PersyaratanDokumen::with('jenisLayanan')
            ->where('nama_dokumen', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->get();
        
        // dd($persyaratan);


        return response()->json($persyaratan);
    }
} 