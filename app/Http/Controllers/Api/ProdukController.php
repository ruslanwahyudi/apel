<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\informasi\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::latest()->get();

        $data = $produk->map(function($item) {
            return [
                'id' => $item->id,
                'nama_produk' => $item->nama_produk,
                'deskripsi' => $item->deskripsi,
                'harga' => $item->harga,
                'kontak' => $item->kontak,
                'gambar' => $item->gambar ? asset($item->gambar) : null,
                'created_at' => $item->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk desa',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $produk = Produk::findOrFail($id);

        $data = [
            'id' => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'deskripsi' => $produk->deskripsi,
            'harga' => $produk->harga,
            'kontak' => $produk->kontak,
            'gambar' => $produk->gambar ? asset($produk->gambar) : null,
            'created_at' => $produk->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $produk->updated_at->format('Y-m-d H:i:s')
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail produk',
            'data' => $data
        ]);
    }
} 