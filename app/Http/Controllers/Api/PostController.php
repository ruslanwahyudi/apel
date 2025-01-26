<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\blog\Posts;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Posts::with(['kategori', 'user', 'labels', 'images'])
                    ->latest()
                    ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Berita',
            'data' => $posts
        ]);
    }

    public function show($id)
    {
        $post = Posts::with(['kategori', 'user', 'labels', 'images'])
                    ->findOrFail($id);

        $post->images->transform(function($image) {
            $image->image_url = url('api/storage/' . $image->image);
            return $image;
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Detail Berita',
            'data' => $post
        ]);
    }
} 