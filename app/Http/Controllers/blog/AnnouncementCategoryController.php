<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use App\Models\blog\AnnouncementCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementCategoryController extends Controller
{
    public function index()
    {
        $categories = AnnouncementCategory::withCount('announcements')->get();
        return view('blog.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('blog.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:announcement_categories'
        ]);

        AnnouncementCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('blog.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(AnnouncementCategory $category)
    {
        return view('blog.categories.edit', compact('category'));
    }

    public function update(Request $request, AnnouncementCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:announcement_categories,name,' . $category->id
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('blog.categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(AnnouncementCategory $category)
    {
        $category->delete();
        return redirect()->route('blog.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
} 