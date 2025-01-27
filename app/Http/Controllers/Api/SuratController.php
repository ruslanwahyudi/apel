<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\adm\RegisterSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SuratController extends Controller
{
    public function index()
    {
        $surat = RegisterSurat::with(['kategori_surat', 'signer', 'statusSurat'])
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Surat',
            'data' => $surat
        ]);
    }

    public function show($id)
    {
        $surat = RegisterSurat::with(['kategori_surat', 'signer', 'statusSurat'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Detail Surat',
            'data' => $surat
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_surat' => 'required|string|in:Surat Masuk,Surat Keluar',
            'kategori_surat_id' => 'required|exists:kategori_surat,id',
            'perihal' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'signer_id' => 'required|exists:users,id',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'nullable|date',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status' => 'required|string|in:1,2,3,4',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $noSurat = generateNoSurat();
        $request->merge(['nomor_surat' => $noSurat]);

        $data = $request->except('lampiran');

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::slug($noSurat) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/surat/lampiran', $filename);
            $data['lampiran'] = $filename;
        }

        $surat = RegisterSurat::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil ditambahkan',
            'data' => $surat
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_surat' => 'required|string|in:Surat Masuk,Surat Keluar',
            'perihal' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'signer_id' => 'required|exists:users,id',
            'tanggal_diterima' => 'nullable|date',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status' => 'required|string|in:1,2,3,4',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $surat = RegisterSurat::findOrFail($id);
        $data = $request->except('lampiran');

        if ($request->hasFile('lampiran')) {
            if ($surat->lampiran) {
                Storage::delete('public/surat/lampiran/' . $surat->lampiran);
            }

            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::slug($surat->nomor_surat) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/surat/lampiran', $filename);
            $data['lampiran'] = $filename;
        }

        $surat->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil diupdate',
            'data' => $surat
        ]);
    }

    public function destroy($id)
    {
        $surat = RegisterSurat::findOrFail($id);
        
        if ($surat->lampiran) {
            Storage::delete('public/surat/lampiran/' . $surat->lampiran);
        }

        $surat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil dihapus'
        ]);
    }
} 