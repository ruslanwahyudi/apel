<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use App\Models\blog\Announcement;
use App\Models\blog\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $announcements = Announcement::with('kategori')->latest()->get();
            return response()->json($announcements);
        }

        return view('blog.announcements.index');
    }

    public function create()
    {
        $categories = Kategori::all();
        return view('blog.announcements.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:blog_kategori,id',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        Announcement::create($data);

        return redirect()->route('blog.announcements')
            ->with('success', 'Pengumuman berhasil ditambahkan!');
    }

    public function edit(Announcement $announcement)
    {
        $categories = Kategori::all();
        return view('blog.announcements.edit', compact('announcement', 'categories'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:blog_kategori,id',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $data['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($data);

        return redirect()->route('blog.announcements')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy(Announcement $announcement)
    {
        try {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            
            $announcement->delete();
            return response()->json(['status' => 'success','message' => 'Pengumuman berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error','message' => 'Gagal menghapus pengumuman'], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $announcements = Announcement::with('kategori')
            ->where('title', 'like', '%' . $query . '%')
            ->latest()
            ->get();
        return response()->json($announcements);
    }
} 