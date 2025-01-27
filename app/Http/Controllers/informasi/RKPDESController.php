<?php

namespace App\Http\Controllers\informasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\informasi\RKPDES;
use Auth;
use Storage;
use Str;

class RKPDESController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rkpdes = RKPDES::orderBy('id', 'desc')->get();
            return response()->json($rkpdes);
        }
        return view('informasi.rkpdes.index');
    }

    public function create()
    {
        return view('informasi.rkpdes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string|max:255',
            'tahun_anggaran' => 'required|integer',
            'program' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'anggaran' => 'required|integer',
            'sumber_dana' => 'required|string|max:255',
            'target' => 'required|string',
            'sasaran' => 'required|string',
            'status' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'file' => 'required|mimes:pdf|max:10240', // max 10MB
        ]);

        $rkpdes = RKPDES::create([
            'nomor' => $request->nomor,
            'tahun_anggaran' => $request->tahun_anggaran,
            'program' => $request->program,
            'kegiatan' => $request->kegiatan,
            'lokasi' => $request->lokasi,
            'anggaran' => $request->anggaran,
            'sumber_dana' => $request->sumber_dana,
            'target' => $request->target,
            'sasaran' => $request->sasaran,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('public/files/rkpdes', $fileName);
            $rkpdes->file = 'storage/files/rkpdes/' . $fileName;
            $rkpdes->save();
        }

        // return response()->json(['status' => 'success', 'message' => 'RKPDES berhasil ditambahkan']);
        return redirect()->route('info.rkpdes')->with('success', 'RKPDES berhasil ditambahkan');
    }

    public function show(RKPDES $rkpdes)
    {
        return response()->json($rkpdes);
    }

    public function edit(RKPDES $rkpdes)
    {
        return view('informasi.rkpdes.edit', compact('rkpdes'));
    }

    public function update(Request $request, RKPDES $rkpdes)
    {
        $request->validate([
            'nomor' => 'required|string|max:255',
            'tahun_anggaran' => 'required|integer',
            'program' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'anggaran' => 'required|numeric',
            'sumber_dana' => 'required|string|max:255',
            'target' => 'required|string',
            'sasaran' => 'required|string',
            'status' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'file' => 'nullable|mimes:pdf|max:10240', // max 10MB
        ]);

        $rkpdes->update([
            'nomor' => $request->nomor,
            'tahun_anggaran' => $request->tahun_anggaran,
            'program' => $request->program,
            'kegiatan' => $request->kegiatan,
            'lokasi' => $request->lokasi,
            'anggaran' => $request->anggaran,
            'sumber_dana' => $request->sumber_dana,
            'target' => $request->target,
            'sasaran' => $request->sasaran,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        if($request->hasFile('file')) {
            // Hapus file lama jika ada
            if($rkpdes->file) {
                Storage::delete(str_replace('storage/', 'public/', $rkpdes->file));
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('public/files/rkpdes', $fileName);
            $rkpdes->file = 'storage/files/rkpdes/' . $fileName;
            $rkpdes->save();
        }

        // return response()->json(['status' => 'success', 'message' => 'RKPDES berhasil diperbarui']);
        return redirect()->route('info.rkpdes')->with('success', 'RKPDES berhasil diperbarui');
    }

    public function destroy(RKPDES $rkpdes)
    {
        if($rkpdes->file) {
            Storage::delete(str_replace('storage/', 'public/', $rkpdes->file));
        }
        
        $rkpdes->delete();
        return response()->json(['status' => 'success', 'message' => 'RKPDES berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $rkpdes = RKPDES::where('judul', 'like', '%' . $search . '%')
                        ->orWhere('tahun', 'like', '%' . $search . '%')
                        ->get();
        return response()->json($rkpdes);
    }
} 