<?php

namespace App\Http\Controllers\informasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\informasi\Perdes;
use Auth;
use Storage;
use Str;

class PerdesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $perdes = Perdes::orderBy('id', 'desc')->get();
            return response()->json($perdes);
        }
        return view('informasi.perdes.index');
    }

    public function create()
    {
        return view('informasi.perdes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_penetapan' => 'required|date',
            'status' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:10240', // max 10MB
        ]);

        $perdes = Perdes::create([
            'nomor' => $request->nomor,
            'tahun' => $request->tahun,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_penetapan' => $request->tanggal_penetapan,
            'status' => $request->status,
        ]);

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('public/files/perdes', $fileName);
            $perdes->file = 'storage/files/perdes/' . $fileName;
            $perdes->save();
        }

        // return response()->json(['status' => 'success', 'message' => 'Peraturan Desa berhasil ditambahkan']);
        return redirect()->route('info.perdes')->with('success', 'Peraturan Desa berhasil ditambahkan');
    }

    public function show(Perdes $perdes)
    {
        return response()->json($perdes);
    }

    public function edit(Perdes $perdes)
    {
        return view('informasi.perdes.edit', compact('perdes'));
    }

    public function update(Request $request, Perdes $perdes)
    {
        $request->validate([
            'nomor' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_penetapan' => 'required|date',
            'status' => 'required|string|max:255',
            'file' => 'nullable|mimes:pdf|max:10240', // max 10MB
        ]);

        $perdes->update([
            'nomor' => $request->nomor,
            'tahun' => $request->tahun,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_penetapan' => $request->tanggal_penetapan,
            'status' => $request->status,
        ]);

        if($request->hasFile('file')) {
            // Hapus file lama jika ada
            if($perdes->file) {
                Storage::delete(str_replace('storage/', 'public/', $perdes->file));
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('public/files/perdes', $fileName);
            $perdes->file = 'storage/files/perdes/' . $fileName;
            $perdes->save();
        }

        // return response()->json(['status' => 'success', 'message' => 'Peraturan Desa berhasil diperbarui']);
        return redirect()->route('info.perdes')->with('success', 'Peraturan Desa berhasil diperbarui');
    }

    public function destroy(Perdes $perdes)
    {
        if($perdes->file) {
            Storage::delete(str_replace('storage/', 'public/', $perdes->file));
        }
        
        $perdes->delete();
        return response()->json(['status' => 'success', 'message' => 'Peraturan Desa berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $perdes = Perdes::where('judul', 'like', '%' . $search . '%')
                       ->orWhere('nomor', 'like', '%' . $search . '%')
                       ->orWhere('tahun', 'like', '%' . $search . '%')
                       ->get();
        return response()->json($perdes);
    }
} 