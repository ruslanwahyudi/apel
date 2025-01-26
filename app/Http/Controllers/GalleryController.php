<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(12);
        return view('informasi.gallery.index', compact('galleries'));
    }

    public function create()
    {
        return view('informasi.gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            $imagePath = $request->file('image')->store('gallery', 'public');

            Gallery::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'is_active' => $request->is_active ?? true
            ]);

            return redirect()->route('info.gallery')
                ->with('success', 'Gambar berhasil ditambahkan ke galeri!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Gallery $gallery)
    {
        return view('informasi.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true
            ];

            if ($request->hasFile('image')) {
                // Delete old image
                if ($gallery->image_path) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
                
                // Store new image
                $data['image_path'] = $request->file('image')->store('gallery', 'public');
            }

            $gallery->update($data);

            return redirect()->route('info.gallery')
                ->with('success', 'Galeri berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Gallery $gallery)
    {
        try {
            // Delete image file
            if ($gallery->image_path) {
                Storage::disk('public')->delete($gallery->image_path);
            }

            $gallery->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Galeri berhasil dihapus!'
                ]);
            }

            return redirect()->route('info.gallery')
                ->with('success', 'Galeri berhasil dihapus!');
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

    public function show(Gallery $gallery)
    {
        return view('informasi.gallery.show', compact('gallery'));
    }
} 