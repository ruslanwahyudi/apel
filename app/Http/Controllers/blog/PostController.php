<?php

namespace App\Http\Controllers\blog;

use App\Http\Controllers\Controller;
use App\Models\blog\Imagepost;
use App\Models\blog\Kategori;
use App\Models\blog\Label;
use App\Models\blog\Labelpost;
use Illuminate\Http\Request;
use App\Models\blog\Posts;
use Auth;
use Storage;
use Str;

class PostController extends Controller
{
    //
    // Menampilkan daftar role
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $posts = Posts::with('kategori','labels','user','images')->orderBy('id', 'desc')->get();
            return response()->json($posts); // Kembalikan JSON jika permintaan melalui AJAX
        }
    
        return view('blog.posts.index'); // Mengembalikan daftar role dalam format JSON
    }

    // Menampilkan form untuk membuat role
    public function create()
    {
        $kategori = Kategori::all();
        $label = Label::all();
        return view('blog.posts.create', compact('kategori', 'label'));
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',   
            'kategori_id' => 'required|exists:blog_kategori,id',
            'label_id' => 'required|exists:blog_label,id',
            'published_at' => 'nullable|date',
        ]);

        $post = Posts::create([
            'title' => $request->title,
            'content' => $request->content,
            'kategori_id' => $request->kategori_id,
            'label_id' => json_encode($request->label_id),
            'user_id' => Auth::user()->id,
            'slug' => Str::slug($request->title),
            'published_at' => now(),
        ]);

        if($post){
            // handle label
            foreach ($request->label_id as $labelId) {
                Labelpost::create([
                    'label_id' => $labelId,
                    'post_id' => $post->id,
                ]);
            }

            // handle image
            if($request->hasFile('image')){
                
                $subPathupd = 'public/images/posts/' . $post->id;
                $subPath = 'storage/images/posts/' . $post->id;
                $counter = 0;
                $imageCount = count($request->file('image'));
                foreach ($request->file('image') as $image) {
                    $imageName = time() . '_' . $counter . '_' . uniqid().'_'.$imageCount . '.' . $image->extension();
                    $image->storeAs($subPathupd, $imageName);
                    Imagepost::create([
                        'post_id' => $post->id,
                        'image' => $subPath . '/' . $imageName,
                    ]);
                    $counter++;
                }
            }
        }

        // Mengembalikan tampilan HTML setelah menyimpan role
        // return redirect()->route('blog.posts')->with('success', 'Post berhasil ditambahkan');
        return response()->json(['status' => 'success','message' => 'Post berhasil ditambahkan']);
    }

    public function show(Posts $post)
    {
        return response()->json($post);
    }


    // Menampilkan form untuk mengedit role
    public function edit(Posts $post)
    {
        $kategori = Kategori::all();
        $label = Label::all();
        $image = Imagepost::where('post_id', $post->id)->get();
        return view('blog.posts.edit', compact('post','kategori','label','image'));
    }

    // Memperbarui role
    public function update(Request $request, Posts $post)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'content' => 'required|string',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kategori_id' => 'required|exists:blog_kategori,id',
            'label_id' => 'required|exists:blog_label,id',
            'published_at' => 'nullable|date',
        ]);
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'kategori_id' => $request->kategori_id,
            'label_id' => json_encode($request->label_id),
            'user_id' => Auth::user()->id,
            'published_at' => now(),
            'slug' => Str::slug($request->title),
        ]);

        
        if($post){
            Labelpost::where('post_id', $post->id)->delete();
            foreach ($request->label_id as $labelId) {
                Labelpost::create([
                    'label_id' => $labelId,
                    'post_id' => $post->id,
                ]);
            }

            Imagepost::where('post_id', $post->id)->delete();
            if($request->hasFile('image')){
                $subPathupd = 'public/images/posts/' . $post->id;
                $subPath = 'storage/images/posts/' . $post->id;
                $counter = 0;
                $imageCount = count($request->file('image'));
                foreach ($request->file('image') as $image) {
                    $imageName = time() . '_' . $counter . '_' . uniqid().'_'.$imageCount . '.' . $image->extension();
                    $image->storeAs($subPathupd, $imageName);
                    Imagepost::create([
                        'post_id' => $post->id,
                        'image' => $subPath . '/' . $imageName,
                    ]);
                }
            }
        }

        // return response()->json(['message' => 'Role berhasil diperbarui']);
        // return redirect()->route('blog.posts')->with('success', 'Post berhasil diperbarui.');
        return response()->json(['status' => 'Berhasil','message' => 'Post berhasil diperbarui']);
    }

    // Menghapus role
    public function destroy(Posts $post)
    {
        $image = Imagepost::where('post_id', $post->id)->get();
        foreach ($image as $img) {
            Storage::delete($img->image);
        }

        Labelpost::where('post_id', $post->id)->delete();
        Imagepost::where('post_id', $post->id)->delete();

        $post->delete();
        return response()->json(['status' => 'success','message' => 'Post berhasil dihapus']);

    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $posts = Posts::where('title', 'like', '%' . $search . '%')->get();
        return response()->json($posts);
    }
}
