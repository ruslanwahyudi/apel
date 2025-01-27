<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriSuratController extends Controller
{
    public function index()
    {
        $kategori = KategoriSurat::latest()->get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Kategori Surat',
            'data' => $kategori
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:kategori_surat,nama'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kategori = KategoriSurat::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kategori surat berhasil ditambahkan',
            'data' => $kategori
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:kategori_surat,nama,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kategori = KategoriSurat::findOrFail($id);
        $kategori->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kategori surat berhasil diupdate',
            'data' => $kategori
        ]);
    }

    public function destroy($id)
    {
        $kategori = KategoriSurat::findOrFail($id);
        
        // Check if kategori is being used
        if ($kategori->surat()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori surat sedang digunakan, tidak dapat dihapus'
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori surat berhasil dihapus'
        ]);
    }
} 