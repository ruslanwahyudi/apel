<?php

namespace App\Http\Controllers\informasi;

use App\Http\Controllers\Controller;
use App\Models\informasi\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $produk = Produk::latest()->get();
            return response()->json($produk);
        }

        return view('informasi.produk.index');
    }

    public function create()
    {
        return view('informasi.produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'harga' => 'nullable|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'deskripsi' => 'required|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'lokasi_penjualan' => 'required|string|max:255',
            'spesifikasi' => 'required|string'
            
        ]);

        try {
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $nama_gambar = 'produk-' . Str::slug($request->nama) . '-' . time() . '.' . $gambar->getClientOriginalExtension();
                $path = $gambar->storeAs('public/produk', $nama_gambar);
                $path = str_replace('public/', 'storage/', $path);
            }

            Produk::create([
                'nama_produk' => $request->nama_produk,
                'kategori' => $request->kategori,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'deskripsi' => $request->deskripsi,
                'gambar' => $path ?? null,
                'status' => $request->status,
                'kontak' => $request->kontak,
                'lokasi_penjualan' => $request->lokasi_penjualan,
                'spesifikasi' => $request->spesifikasi
            ]);

            return redirect()->route('info.produk')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete(str_replace('storage/', 'public/', $path));
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Produk $produk)
    {
        return response()->json($produk);
    }

    public function edit(Produk $produk)
    {
        return view('informasi.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'lokasi_penjualan' => 'required|string|max:255',
            'spesifikasi' => 'required|string'
        ]);

        try {
            $old_gambar = $produk->gambar;

            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $nama_gambar = 'produk-' . Str::slug($request->nama) . '-' . time() . '.' . $gambar->getClientOriginalExtension();
                $path = $gambar->storeAs('public/produk', $nama_gambar);
                $path = str_replace('public/', 'storage/', $path);

                if ($old_gambar) {
                    Storage::delete(str_replace('storage/', 'public/', $old_gambar));
                }
            }

            $produk->update([
                'nama_produk' => $request->nama_produk,
                'kategori' => $request->kategori,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'deskripsi' => $request->deskripsi,
                'gambar' => $path ?? $old_gambar,
                'status' => $request->status,
                'kontak' => $request->kontak,
                'lokasi_penjualan' => $request->lokasi_penjualan,
                'spesifikasi' => $request->spesifikasi
            ]);

            return redirect()->route('info.produk')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete(str_replace('storage/', 'public/', $path));
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Produk $produk)
    {
        try {
            if ($produk->gambar) {
                Storage::delete(str_replace('storage/', 'public/', $produk->gambar));
            }

            $produk->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus!'
                ]);
            }

            return redirect()->route('info.produk')
                ->with('success', 'Produk berhasil dihapus!');
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
        $produk = Produk::where('nama', 'like', "%{$search}%")
            ->orWhere('kategori', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->get();

        return response()->json($produk);
    }
} 