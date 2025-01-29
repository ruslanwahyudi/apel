<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\informasi\Wisata;
use Illuminate\Http\Request;

class WisataController extends Controller
{
    public function index()
    {
        $wisata = Wisata::latest()->get();

        $data = $wisata->map(function($item) {
            return [
                'id' => $item->id,
                'nama_wisata' => $item->nama,
                'deskripsi' => $item->deskripsi,
                'lokasi' => $item->lokasi,
                'jam_operasional' => $item->jam_operasional,
                'harga_tiket' => $item->harga_tiket,
                'kontak' => $item->kontak,
                'gambar' => $item->gambar ? asset($item->gambar) : null,
                'created_at' => $item->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar wisata desa',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $wisata = Wisata::findOrFail($id);

        $data = [
            'id' => $wisata->id,
            'nama_wisata' => $wisata->nama_wisata,
            'deskripsi' => $wisata->deskripsi,
            'lokasi' => $wisata->lokasi,
            'jam_operasional' => $wisata->jam_operasional,
            'harga_tiket' => $wisata->harga_tiket,
            'kontak' => $wisata->kontak,
            'gambar' => $wisata->gambar ? asset('storage/wisata/' . $wisata->gambar) : null,
            'created_at' => $wisata->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $wisata->updated_at->format('Y-m-d H:i:s')
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail wisata',
            'data' => $data
        ]);
    }
} 